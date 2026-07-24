<?php

namespace App\Http\Controllers;

use App\Models\PlayHistory;
use App\Models\SeoSetting;
use App\Models\Simulation;
use App\Models\Tag;
use App\Models\User;
use App\Services\GamificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use ZipArchive;

class SimulationController extends Controller
{
    /**
     * Display the explore/discover page with categories and curated simulations.
     */
    public function explore(Request $request): View
    {
        $categories = Simulation::published()
            ->selectRaw('category, count(*) as count')
            ->groupBy('category')
            ->orderBy('count', 'desc')
            ->get();

        // Filter by category if provided
        $activeCategory = $request->input('category');

        // Filter by tag if provided
        $activeTag = $request->input('tag');
        $tags = Tag::has('simulations', '>', 0)
            ->withCount('simulations')
            ->orderByDesc('simulations_count')
            ->take(20)
            ->get();

        // Shared scope: apply category + tag filters
        $applyFilters = fn ($q) => $q
            ->when($activeCategory, fn ($q2) => $q2->where('category', $activeCategory))
            ->when($activeTag, fn ($q2) => $q2->whereHas('tagModels', fn ($q3) => $q3->where('tags.slug', $activeTag)));

        // Trending time period filter
        $trendingPeriod = $request->input('trending', 'all');
        $trendingPeriods = [
            'today' => 'Hari Ini',
            'week' => 'Minggu Ini',
            'month' => 'Bulan Ini',
            'year' => 'Tahun Ini',
            'all' => 'Semua',
        ];

        $trendingQuery = Simulation::published()->tap($applyFilters);

        // Apply trending time filter
        $trendingQuery->where('published_at', '>=', match ($trendingPeriod) {
            'today' => now()->startOfDay(),
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            'year' => now()->startOfYear(),
            default => now()->subYears(100),
        });

        $trending = $trendingQuery
            ->orderByDesc('play_count')
            ->take(12)
            ->get();

        // Featured simulations (always all-time)
        $featured = Simulation::published()
            ->tap($applyFilters)
            ->orderByDesc('play_count')
            ->take(6)
            ->get();

        // Recently added
        $recent = Simulation::published()
            ->tap($applyFilters)
            ->latest('published_at')
            ->take(12)
            ->get();

        // Highest rated
        $topRated = Simulation::published()
            ->tap($applyFilters)
            ->withAvg('ratings', 'rating')
            ->orderByDesc('ratings_avg_rating')
            ->take(12)
            ->get();

        // "For You" — personalized recommendations based on play history
        $forYou = collect();
        $user = $request->user();
        if ($user) {
            $topCategories = PlayHistory::where('user_id', $user->id)
                ->with('simulation')
                ->get()
                ->pluck('simulation.category')
                ->filter()
                ->countBy()
                ->sortDesc()
                ->keys()
                ->take(3);

            if ($topCategories->isNotEmpty()) {
                $playedIds = $user->playHistory()->pluck('simulation_id');
                $forYou = Simulation::published()
                    ->whereIn('category', $topCategories)
                    ->whereNotIn('id', $playedIds)
                    ->with('user')
                    ->orderByDesc('average_rating')
                    ->take(12)
                    ->get();
            }
        }

        // Fallback: if no personalized results, show highest rated not yet in other sections
        if ($forYou->isEmpty()) {
            $excludeIds = $featured->pluck('id')
                ->merge($trending->pluck('id'))
                ->merge($topRated->pluck('id'))
                ->unique()
                ->toArray();

            $forYou = Simulation::published()
                ->tap($applyFilters)
                ->whereNotIn('id', $excludeIds)
                ->with('user')
                ->withAvg('ratings', 'rating')
                ->orderByDesc('ratings_avg_rating')
                ->take(12)
                ->get();
        }

        return view('simulations.explore', compact(
            'categories',
            'activeCategory',
            'tags',
            'activeTag',
            'featured',
            'trending',
            'recent',
            'topRated',
            'forYou',
            'trendingPeriod',
            'trendingPeriods',
        ));
    }

    /**
     * AJAX search endpoint for live search suggestions.
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->input('q', '');

        if (strlen($query) < 2) {
            return response()->json(['results' => []]);
        }

        $results = Simulation::published()
            ->search($query)
            ->orderByDesc('play_count')
            ->take(8)
            ->get()
            ->map(fn ($sim) => [
                'id' => $sim->id,
                'title' => $sim->title,
                'slug' => $sim->slug,
                'category' => $sim->category,
                'formatted_play_count' => $sim->formatted_play_count,
                'thumbnail' => $sim->thumbnail ? asset('storage/'.$sim->thumbnail) : null,
            ]);

        return response()->json(['results' => $results]);
    }

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

        // Trending simulations — filter by period (day, week, month, year)
        $trendingPeriod = $request->input('period', 'week');
        $trendingQuery = Simulation::published();

        $trendingQuery->where('published_at', '>=', match ($trendingPeriod) {
            'day' => now()->subDay(),
            'week' => now()->subWeek(),
            'month' => now()->subMonth(),
            'year' => now()->subYear(),
            default => now()->subWeek(),
        });

        $trending = $trendingQuery
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

        // Discovered for You — based on play history
        $discovered = collect();
        $user = $request->user();
        if ($user) {
            $topCategories = PlayHistory::where('user_id', $user->id)
                ->with('simulation')
                ->get()
                ->pluck('simulation.category')
                ->filter()
                ->countBy()
                ->sortDesc()
                ->keys()
                ->take(3);

            if ($topCategories->isNotEmpty()) {
                $playedSimulationIds = $user->playHistory()->pluck('simulation_id');
                $discovered = Simulation::published()
                    ->whereIn('category', $topCategories)
                    ->whereNotIn('id', $playedSimulationIds)
                    ->with('user')
                    ->orderByDesc('average_rating')
                    ->take(12)
                    ->get();
            }
        }

        // Fallback: if no history or no results, show highest rated
        if ($discovered->isEmpty()) {
            $discovered = Simulation::published()
                ->with('user')
                ->orderByDesc('average_rating')
                ->take(12)
                ->get();
        }

        // Search
        $search = $request->input('search');
        $searchResults = null;
        if ($search) {
            $searchResults = Simulation::published()
                ->search($search)
                ->orderByDesc('play_count')
                ->paginate(20);
        }

        return view('landing', compact('categories', 'trending', 'latest', 'popular', 'discovered', 'search', 'searchResults'));
    }

    /**
     * Display a single simulation.
     */
    public function show(Request $request, string $slug): View
    {
        $simulation = Simulation::published()
            ->where('slug', $slug)
            ->with(['user' => function ($q) {
                $q->withCount([
                    'simulations as published_simulations_count' => fn ($sq) => $sq->published(),
                    'followers',
                ]);
            }])
            ->firstOrFail();

        // Increment view count with deduplication:
        // - Logged-in users: keyed by user ID (survives session expiry)
        // - Guests: keyed by IP hash
        // Cooldown: 5 minutes between duplicate views
        $cooldownMinutes = 5;
        if (Auth::check()) {
            $viewedKey = 'sim_viewed_user_'.Auth::id().'_'.$simulation->id;
        } else {
            $viewedKey = 'sim_viewed_ip_'.md5($request->ip()).'_'.$simulation->id;
        }

        $lastViewed = session($viewedKey);
        if (! $lastViewed || now()->diffInMinutes($lastViewed) > $cooldownMinutes) {
            $simulation->increment('view_count');
            session()->put($viewedKey, now());

            // Track traffic source
            $source = $this->detectTrafficSource($request);
            $this->trackTrafficSource($simulation->id, $source, 'view');
        }

        // Load comments (top-level only, with replies)
        $comments = $simulation->comments()
            ->topLevel()
            ->with('user', 'replies.user')
            ->pinnedFirst()
            ->get();

        // Check user interactions (if authenticated)
        $user = Auth::user();
        $isBookmarked = $simulation->isBookmarkedBy($user);
        $isFavorited = $simulation->isFavoritedBy($user);
        $favoriteCount = $simulation->favorites()->count();
        $ratingModel = $simulation->getRatingBy($user);
        $userRating = $ratingModel ? $ratingModel->rating : 0;
        $userReactions = $simulation->getReactionsBy($user)->pluck('type')->toArray();
        $isFollowing = false;
        $isFollowingSimulation = false;
        $userCollections = collect();

        if ($user instanceof User) {
            $isFollowing = $simulation->user_id !== $user->id
                ? $user->isFollowing($simulation->user)
                : false;
            $isFollowingSimulation = $user->isFollowingSimulation($simulation);
            $userCollections = $user->collections()->withCount('simulations')->get();
        }

        $reactionCounts = $simulation->reaction_counts;

        // Related simulations: same category + matching tags, ordered by rating then play_count
        $simTags = $simulation->tags_array;
        $related = Simulation::published()
            ->where('id', '!=', $simulation->id)
            ->where(function ($q) use ($simulation, $simTags) {
                // Priority 1: same category
                $q->where('category', $simulation->category);
                // Priority 2: matching tags
                if (! empty($simTags)) {
                    $q->orWhere(function ($q2) use ($simTags) {
                        foreach ($simTags as $tag) {
                            $q2->orWhere('tags', 'like', "%{$tag}%");
                        }
                    });
                }
            })
            ->orderByDesc('average_rating')
            ->orderByDesc('play_count')
            ->paginate(8);

        // Load SEO settings for this simulation
        $seoSetting = SeoSetting::findByKey('simulation:'.$simulation->slug);

        return view('simulations.show', compact(
            'simulation',
            'related',
            'comments',
            'isBookmarked',
            'isFavorited',
            'favoriteCount',
            'userRating',
            'userReactions',
            'isFollowing',
            'isFollowingSimulation',
            'reactionCounts',
            'userCollections',
            'seoSetting',
        ));
    }

    /**
     * Increment play count (called via AJAX when user plays simulation).
     */
    public function play(Request $request, string $slug)
    {
        $simulation = Simulation::published()
            ->where('slug', $slug)
            ->firstOrFail();

        // Deduplication: only count 1 play per 30 seconds per session
        $playKey = 'sim_played_'.$simulation->id;
        $lastPlayed = session($playKey);
        $cooldownSeconds = 30;

        if (! $lastPlayed || now()->diffInSeconds($lastPlayed) > $cooldownSeconds) {
            $simulation->increment('play_count');
            session()->put($playKey, now());

            // Track traffic source for play
            $source = $this->detectTrafficSource($request);
            $this->trackTrafficSource($simulation->id, $source, 'play');
        }

        // Record play history for authenticated users
        $user = Auth::user();
        if ($user instanceof User) {
            PlayHistory::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'simulation_id' => $simulation->id,
                ],
                [
                    'duration_seconds' => 0,
                    'completed' => false,
                ]
            );

            // Gamification: award play points + check badges
            $gamification = app(GamificationService::class);
            $gamification->awardPoints($user, 'play', 'Memainkan: '.$simulation->title);
            $gamification->checkBadges($user);
        }

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

        // Derive extract path from zip_path (e.g. "simulations/1/slug.zip" → "simulations/1/slug")
        $extractPath = $this->getExtractPath($simulation);

        // Auto-extract if not yet extracted
        if (! is_dir($extractPath)) {
            $this->extractSimulation($simulation, $extractPath);
        }

        // Verify extraction succeeded
        if (! is_dir($extractPath)) {
            abort(404, 'Simulasi belum di-extract atau file tidak ditemukan.');
        }

        // Default to index.html when no specific path is requested
        // (prevents serving a directory path which causes FileNotFoundException)
        if ($path === '') {
            $path = 'index.html';
        }

        $filePath = $extractPath.'/'.$path;

        // If file not found at expected path, search one level deep in subdirectories
        // This handles ZIPs that contain a subdirectory (e.g. "simulation/index.html")
        if ($path !== '' && ! file_exists($filePath) && is_dir($extractPath)) {
            $subdirs = glob($extractPath.'/*', GLOB_ONLYDIR);
            foreach ($subdirs as $subdir) {
                $candidate = $subdir.'/'.$path;
                if (file_exists($candidate)) {
                    $filePath = $candidate;
                    break;
                }
            }
        }

        // Security: prevent directory traversal
        // Use basename comparison as fallback when realpath() fails (e.g. symlinks)
        $realExtractPath = realpath($extractPath);
        $realFilePath = realpath($filePath);

        if ($realExtractPath !== false && $realFilePath !== false) {
            // Both paths resolved — verify containment
            if (! str_starts_with($realFilePath, $realExtractPath.DIRECTORY_SEPARATOR) && $realFilePath !== $realExtractPath) {
                abort(403, 'Access denied.');
            }
        } else {
            // realpath() failed — fall back to string-based containment check
            $normExtract = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $extractPath);
            $normFile = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $filePath);
            if (! str_starts_with($normFile, $normExtract.DIRECTORY_SEPARATOR)) {
                abort(403, 'Access denied.');
            }
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
     * Get the extract directory path for a simulation.
     * Derives from zip_path: "simulations/1/slug.zip" → storage/app/public/simulations/1/slug
     */
    private function getExtractPath(Simulation $simulation): string
    {
        // Primary: derive from zip_path on public disk
        if ($simulation->zip_path) {
            $publicExtract = Storage::disk('public')->path(rtrim(dirname($simulation->zip_path), '/\\').'/'.$simulation->slug);
            if (is_dir($publicExtract)) {
                return $publicExtract;
            }

            return $publicExtract;
        }

        // Fallback: legacy path
        return storage_path('app/simulations/'.$simulation->slug.'/extracted');
    }

    /**
     * Extract simulation ZIP to the target directory.
     * Checks model zip_path first, then legacy paths.
     */
    private function extractSimulation(Simulation $simulation, string $extractPath): void
    {
        $slug = $simulation->slug;
        $zipFile = null;

        // Primary: use zip_path from model (public disk)
        if ($simulation->zip_path) {
            $zipFile = Storage::disk('public')->path($simulation->zip_path);
            if (! file_exists($zipFile)) {
                $zipFile = null;
            }
        }

        // Fallback 1: new path structure (simulations disk)
        if ($zipFile === null) {
            $simDir = storage_path('app/simulations/'.$slug);
            $zipFile = $this->findZipInDirectory($simDir);
        }

        // Fallback 2: legacy path (private/simulations)
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
    public function category(Request $request, string $category): View
    {
        $sort = $request->input('sort', 'popular');

        $simulations = Simulation::published()
            ->where('category', $category)
            ->when($sort === 'newest', fn ($q) => $q->latest('published_at'))
            ->when($sort === 'rating', fn ($q) => $q->withAvg('ratings', 'rating')->orderByDesc('ratings_avg_rating'))
            ->when($sort === 'popular', fn ($q) => $q->orderByDesc('play_count'))
            ->paginate(20)
            ->appends(['sort' => $sort]);

        return view('simulations.category', compact('simulations', 'category', 'sort'));
    }

    /**
     * Detect traffic source from request headers.
     */
    private function detectTrafficSource(Request $request): string
    {
        $referer = $request->header('referer', '');
        $path = $request->path();

        // Check if accessed via embed
        if (str_contains($path, 'embed')) {
            return 'embed';
        }

        // Check referer for search engines
        if (preg_match('/google\.|bing\.|yahoo\.|duckduckgo\./i', $referer)) {
            return 'search';
        }

        // Check referer for social media
        if (preg_match('/facebook\.|twitter\.|x\.com|instagram\.|linkedin\.|reddit\.|t\.me|wa\.me|web\.whatsapp/i', $referer)) {
            return 'social';
        }

        // Check if there's a referer (referral from another site)
        if (! empty($referer) && ! str_contains($referer, $request->getHost())) {
            return 'referral';
        }

        return 'direct';
    }

    /**
     * Track a traffic source event.
     */
    private function trackTrafficSource(int $simulationId, string $source, string $metricType): void
    {
        DB::table('traffic_sources')->updateOrInsert(
            [
                'simulation_id' => $simulationId,
                'source' => $source,
                'metric_type' => $metricType,
                'date' => now()->toDateString(),
            ],
            [
                'count' => DB::raw('COALESCE(`count`, 0) + 1'),
            ]
        );
    }
}
