<?php

namespace App\Http\Controllers;

use App\Models\Simulation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use ZipArchive;

class SimulationController extends Controller
{
    /**
     * Display the landing page with simulation feed.
     */
    public function index(Request $request): View
    {
        $categories = Simulation::published()
            ->selectRaw('category, count(*) as count')
            ->groupBy('category')
            ->orderBy('count', 'desc')
            ->get();

        // Trending simulations (most played in last 7 days)
        $trending = Simulation::published()
            ->orderByDesc('play_count')
            ->take(12)
            ->get();

        // Latest simulations
        $latest = Simulation::published()
            ->latest('published_at')
            ->take(12)
            ->get();

        // Most popular all time
        $popular = Simulation::published()
            ->orderByDesc('view_count')
            ->take(12)
            ->get();

        // Search
        $search = $request->input('search');
        $searchResults = null;
        if ($search) {
            $searchResults = Simulation::published()
                ->search($search)
                ->orderByDesc('play_count')
                ->paginate(20);
        }

        return view('landing', compact('categories', 'trending', 'latest', 'popular', 'search', 'searchResults'));
    }

    /**
     * Display a single simulation.
     */
    public function show(string $slug): View
    {
        $simulation = Simulation::published()
            ->where('slug', $slug)
            ->with('user')
            ->firstOrFail();

        // Increment view count
        $simulation->increment('view_count');

        // Load comments (top-level only, with replies)
        $comments = $simulation->comments()
            ->topLevel()
            ->with('user', 'replies.user')
            ->pinnedFirst()
            ->get();

        // Check user interactions (if authenticated)
        $user = Auth::user();
        $isBookmarked = $simulation->isBookmarkedBy($user);
        $ratingModel = $simulation->getRatingBy($user);
        $userRating = $ratingModel ? $ratingModel->rating : 0;
        $userReactions = $simulation->getReactionsBy($user)->pluck('type')->toArray();
        $isFollowing = $user && $simulation->user_id !== $user->id
            ? $user->isFollowing($simulation->user)
            : false;
        $reactionCounts = $simulation->reaction_counts;

        // Related simulations (same category)
        $related = Simulation::published()
            ->where('id', '!=', $simulation->id)
            ->where('category', $simulation->category)
            ->orderByDesc('play_count')
            ->take(8)
            ->get();

        return view('simulations.show', compact(
            'simulation',
            'related',
            'comments',
            'isBookmarked',
            'userRating',
            'userReactions',
            'isFollowing',
            'reactionCounts',
        ));
    }

    /**
     * Increment play count (called via AJAX when user plays simulation).
     */
    public function play(string $slug)
    {
        $simulation = Simulation::published()
            ->where('slug', $slug)
            ->firstOrFail();

        $simulation->increment('play_count');

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'play_count' => $simulation->fresh()->play_count,
            ]);
        }

        return redirect()->route('simulations.show', $slug);
    }

    /**
     * Serve a file from the extracted simulation directory.
     * Used by iframe to load simulation assets (HTML, CSS, JS, images).
     * Auto-extracts ZIP on first request if not yet extracted.
     */
    public function serve(string $slug, string $path = '')
    {
        $simulation = Simulation::published()
            ->where('slug', $slug)
            ->firstOrFail();

        $extractPath = storage_path('app/simulations/'.$slug.'/extracted');

        // Auto-extract if not yet extracted
        if (! is_dir($extractPath)) {
            $this->extractSimulation($slug, $extractPath);
        }

        $filePath = $extractPath.'/'.$path;

        // Security: prevent directory traversal
        $realExtractPath = realpath($extractPath);
        $realFilePath = realpath($filePath);

        if ($realExtractPath === false || $realFilePath === false || ! str_starts_with($realFilePath, $realExtractPath)) {
            abort(403, 'Access denied.');
        }

        if (! file_exists($filePath)) {
            abort(404, 'File not found.');
        }

        // Determine content type
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $mimes = [
            'css' => 'text/css',
            'js' => 'application/javascript',
            'svg' => 'image/svg+xml',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'html' => 'text/html',
            'htm' => 'text/html',
            'json' => 'application/json',
        ];

        $mimeType = $mimes[$extension] ?? (mime_content_type($filePath) ?: 'application/octet-stream');

        return response()->file($filePath, [
            'Content-Type' => $mimeType,
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }

    /**
     * Extract simulation ZIP to the target directory.
     * Checks both new path (simulations disk) and legacy path (private/simulations).
     */
    private function extractSimulation(string $slug, string $extractPath): void
    {
        // Try new path: storage/app/simulations/{slug}/*.zip
        $simDir = storage_path('app/simulations/'.$slug);
        $zipFile = $this->findZipInDirectory($simDir);

        // Fallback: legacy path storage/app/private/simulations/{slug}/*.zip
        if ($zipFile === null) {
            $legacyDir = storage_path('app/private/simulations/'.$slug);
            $zipFile = $this->findZipInDirectory($legacyDir);
        }

        if ($zipFile === null) {
            abort(404, 'Simulation package not found.');
        }

        $zip = new ZipArchive;
        if ($zip->open($zipFile) === true) {
            // Ensure extract directory exists
            if (! is_dir($extractPath)) {
                mkdir($extractPath, 0775, true);
            }
            $zip->extractTo($extractPath);
            $zip->close();
        } else {
            abort(500, 'Failed to extract simulation package.');
        }
    }

    /**
     * Find the first .zip file in a directory.
     */
    private function findZipInDirectory(string $directory): ?string
    {
        if (! is_dir($directory)) {
            return null;
        }

        $files = glob($directory.'/*.zip');

        return $files[0] ?? null;
    }

    /**
     * Display simulations by category.
     */
    public function category(string $category): View
    {
        $simulations = Simulation::published()
            ->where('category', $category)
            ->orderByDesc('play_count')
            ->paginate(20);

        return view('simulations.category', compact('simulations', 'category'));
    }
}
