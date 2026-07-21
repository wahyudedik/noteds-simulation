<?php

namespace App\Http\Controllers;

use App\Models\Bookmark;
use App\Models\Comment;
use App\Models\CreatorReputation;
use App\Models\Follow;
use App\Models\Notification;
use App\Models\Rating;
use App\Models\Reaction;
use App\Models\Share;
use App\Models\Simulation;
use App\Models\SimulationAnalytic;
use App\Models\SimulationVersion;
use App\Models\Tag;
use App\Models\User;
use App\Services\SecurityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class StudioController extends Controller
{
    /**
     * Studio Dashboard — creator overview with stats & charts.
     */
    public function dashboard(): View
    {
        /** @var User $user */
        $user = Auth::user();

        $simulations = Simulation::where('user_id', $user->id);
        $publishedCount = (clone $simulations)->published()->count();
        $draftCount = (clone $simulations)->where('is_published', false)->count();
        $totalSimulations = $publishedCount + $draftCount;

        // Aggregate stats from all creator's simulations
        $simIds = Simulation::where('user_id', $user->id)->pluck('id');

        $totalViews = Simulation::whereIn('id', $simIds)->sum('view_count');
        $totalPlays = Simulation::whereIn('id', $simIds)->sum('play_count');
        $totalLikes = Reaction::whereIn('simulation_id', $simIds)->count();
        $totalBookmarks = Bookmark::whereIn('simulation_id', $simIds)->count();
        $totalShares = Share::whereIn('simulation_id', $simIds)->count();
        $totalComments = Comment::whereIn('simulation_id', $simIds)->count();
        $totalFollowers = Follow::where('followable_id', $user->id)
            ->where('followable_type', User::class)
            ->count();

        // Trend data (last 7 days)
        $trendDays = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $dayLabel = now()->subDays($i)->format('D');
            $trendDays->push([
                'date' => $date,
                'label' => $dayLabel,
                'views' => (int) SimulationAnalytic::whereIn('simulation_id', $simIds)
                    ->where('date', $date)->sum('views'),
                'plays' => (int) SimulationAnalytic::whereIn('simulation_id', $simIds)
                    ->where('date', $date)->sum('plays'),
            ]);
        }

        // Top simulations by play count
        $topSimulations = Simulation::where('user_id', $user->id)
            ->orderByDesc('play_count')
            ->limit(5)
            ->get(['id', 'title', 'play_count', 'view_count', 'slug']);

        // Recent comments on creator's simulations
        $recentComments = Comment::whereIn('simulation_id', $simIds)
            ->with('user', 'simulation')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('studio.dashboard', compact(
            'totalSimulations',
            'publishedCount',
            'draftCount',
            'totalViews',
            'totalPlays',
            'totalLikes',
            'totalBookmarks',
            'totalShares',
            'totalComments',
            'totalFollowers',
            'trendDays',
            'topSimulations',
            'recentComments',
        ));
    }

    /**
     * List all simulations owned by the creator.
     */
    public function simulations(Request $request): View
    {
        /** @var User $user */
        $user = Auth::user();
        $status = $request->input('status', 'all');

        $query = Simulation::where('user_id', $user->id)->with('tagModels');

        if ($status === 'published') {
            $query->published();
        } elseif ($status === 'draft') {
            $query->where('is_published', false);
        }

        $simulations = $query->orderByDesc('updated_at')->paginate(12)->withQueryString();

        return view('studio.simulations', compact('simulations', 'status'));
    }

    /**
     * Show create form for new simulation.
     */
    public function create(): View
    {
        return view('studio.create');
    }

    /**
     * Store a new simulation (upload zip).
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'category' => 'required|string|max:100',
            'subcategory' => 'nullable|string|max:100',
            'simulation_zip' => 'required|file|mimes:zip|max:51200',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'tags' => 'nullable|string|max:500',
            'is_published' => 'nullable|boolean',
        ]);

        /** @var User $user */
        $user = Auth::user();

        // Handle zip upload
        $zipFile = $request->file('simulation_zip');
        $slug = Str::slug($validated['title']);
        $extractPath = 'simulations/'.$user->id.'/'.$slug;

        // Store zip
        $zipPath = $zipFile->storeAs('simulations/'.$user->id, $slug.'.zip', 'public');

        // Extract zip
        $fullExtractPath = Storage::disk('public')->path($extractPath);
        $zip = new \ZipArchive;
        if ($zip->open(Storage::disk('public')->path('simulations/'.$user->id.'/'.$slug.'.zip')) === true) {
            $zip->extractTo($fullExtractPath);
            $zip->close();
        }

        // Read manifest if exists
        $manifestPath = $fullExtractPath.'/manifest.json';
        $entryPoint = 'index.html';
        if (file_exists($manifestPath)) {
            $manifest = json_decode(file_get_contents($manifestPath), true);
            $entryPoint = $manifest['entryPoint'] ?? 'index.html';
        }

        // Handle thumbnail
        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        // Create simulation
        $simulation = Simulation::create([
            'user_id' => $user->id,
            'title' => $validated['title'],
            'slug' => $slug,
            'description' => $validated['description'] ?? '',
            'category' => $validated['category'],
            'subcategory' => $validated['subcategory'] ?? null,
            'zip_path' => $zipPath,
            'extract_path' => $extractPath,
            'entry_point' => $entryPoint,
            'thumbnail' => $thumbnailPath,
            'is_published' => $request->boolean('is_published', false),
            'published_at' => $request->boolean('is_published') ? now() : null,
        ]);

        // Handle tags
        if (! empty($validated['tags'])) {
            $tagNames = array_map('trim', explode(',', $validated['tags']));
            foreach ($tagNames as $tagName) {
                if (empty($tagName)) {
                    continue;
                }

                // Truncate tag name to fit DB column (max 255 chars)
                $tagName = Str::limit($tagName, 255, '');
                $tagSlug = Str::limit(Str::slug($tagName), 255, '');

                $tag = Tag::firstOrCreate(
                    ['slug' => $tagSlug],
                    ['name' => $tagName]
                );
                $simulation->tagModels()->attach($tag->id);
            }
        }

        // Auto-scan (Layer 1) — run security scan on uploaded ZIP
        $security = app(SecurityService::class);
        $zipFullPath = Storage::disk('public')->path('simulations/'.$user->id.'/'.$slug.'.zip');
        $scanResult = $security->autoScan($simulation, $zipFullPath);

        // If auto-scan rejects, auto-pend the simulation
        if ($scanResult->result === 'reject' && $simulation->is_published) {
            $simulation->update(['status' => 'pending']);
        }

        // Update creator reputation
        $reputation = CreatorReputation::firstOrCreate(['user_id' => $user->id]);
        $reputation->update(['total_uploads' => $reputation->total_uploads + 1]);

        return redirect()->route('studio.simulations')
            ->with('success', $simulation->is_published ? 'Simulasi berhasil dipublikasikan!' : 'Simulasi berhasil disimpan sebagai draft.');
    }

    /**
     * Show edit form for a simulation.
     */
    public function edit(string $slug): View
    {
        /** @var User $user */
        $user = Auth::user();
        $simulation = Simulation::where('user_id', $user->id)
            ->where('slug', $slug)
            ->with('tagModels')
            ->firstOrFail();

        return view('studio.edit', compact('simulation'));
    }

    /**
     * Update a simulation.
     */
    public function update(Request $request, string $slug): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $simulation = Simulation::where('user_id', $user->id)
            ->where('slug', $slug)
            ->firstOrFail();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'category' => 'required|string|max:100',
            'subcategory' => 'nullable|string|max:100',
            'simulation_zip' => 'nullable|file|mimes:zip|max:51200',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'tags' => 'nullable|string|max:500',
            'is_published' => 'nullable|boolean',
        ]);

        $data = [
            'title' => $validated['title'],
            'description' => $validated['description'] ?? '',
            'category' => $validated['category'],
            'subcategory' => $validated['subcategory'] ?? null,
            'is_published' => $request->boolean('is_published', false),
        ];

        // Handle new zip upload
        if ($request->hasFile('simulation_zip')) {
            $zipFile = $request->file('simulation_zip');
            $newSlug = Str::slug($validated['title']);
            $extractPath = 'simulations/'.$user->id.'/'.$newSlug;

            $zipFile->storeAs('simulations/'.$user->id, $newSlug.'.zip', 'public');

            $fullExtractPath = Storage::disk('public')->path($extractPath);
            $zip = new \ZipArchive;
            if ($zip->open(Storage::disk('public')->path('simulations/'.$user->id.'/'.$newSlug.'.zip')) === true) {
                $zip->extractTo($fullExtractPath);
                $zip->close();
            }

            $manifestPath = $fullExtractPath.'/manifest.json';
            $entryPoint = 'index.html';
            if (file_exists($manifestPath)) {
                $manifest = json_decode(file_get_contents($manifestPath), true);
                $entryPoint = $manifest['entryPoint'] ?? 'index.html';
            }

            // Save old version before updating
            SimulationVersion::create([
                'simulation_id' => $simulation->id,
                'version' => $simulation->version ?? '1.0.0',
                'zip_path' => $simulation->zip_path,
                'changelog' => 'Versi sebelum update',
            ]);

            $data['zip_path'] = 'simulations/'.$user->id.'/'.$newSlug.'.zip';
            $data['extract_path'] = $extractPath;
            $data['entry_point'] = $entryPoint;
            $data['version'] = $this->bumpVersion($simulation->version ?? '1.0.0');
        }

        // Handle thumbnail
        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        // Set published_at if publishing for the first time
        if ($request->boolean('is_published') && ! $simulation->is_published) {
            $data['published_at'] = now();
        }

        $simulation->update($data);

        // Sync tags
        if ($request->has('tags')) {
            $tagNames = array_map('trim', explode(',', $request->input('tags', '')));
            $tagIds = [];
            foreach ($tagNames as $tagName) {
                if (empty($tagName)) {
                    continue;
                }

                // Truncate tag name to fit DB column (max 255 chars)
                $tagName = Str::limit($tagName, 255, '');
                $tagSlug = Str::limit(Str::slug($tagName), 255, '');

                $tag = Tag::firstOrCreate(
                    ['slug' => $tagSlug],
                    ['name' => $tagName]
                );
                $tagIds[] = $tag->id;
            }
            $simulation->tagModels()->sync($tagIds);
        }

        return redirect()->route('studio.simulations')
            ->with('success', 'Simulasi berhasil diupdate.');
    }

    /**
     * Delete a simulation.
     */
    public function destroy(string $slug): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $simulation = Simulation::where('user_id', $user->id)
            ->where('slug', $slug)
            ->firstOrFail();

        // Delete files
        if ($simulation->zip_path) {
            Storage::disk('public')->delete($simulation->zip_path);
        }
        if ($simulation->extract_path) {
            $path = Storage::disk('public')->path($simulation->extract_path);
            if (is_dir($path)) {
                $this->deleteDirectory($path);
            }
        }

        $simulation->delete();

        return redirect()->route('studio.simulations')
            ->with('success', 'Simulasi berhasil dihapus.');
    }

    /**
     * Show version history for a simulation.
     */
    public function versions(string $slug): View
    {
        /** @var User $user */
        $user = Auth::user();
        $simulation = Simulation::where('user_id', $user->id)
            ->where('slug', $slug)
            ->firstOrFail();

        $versions = SimulationVersion::where('simulation_id', $simulation->id)
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('studio.versions', compact('simulation', 'versions'));
    }

    /**
     * Show analytics for a specific simulation.
     */
    public function analytics(string $slug): View
    {
        /** @var User $user */
        $user = Auth::user();
        $simulation = Simulation::where('user_id', $user->id)
            ->where('slug', $slug)
            ->firstOrFail();

        // Last 30 days analytics
        $dailyAnalytics = SimulationAnalytic::where('simulation_id', $simulation->id)
            ->where('date', '>=', now()->subDays(30))
            ->orderBy('date')
            ->get();

        // Reaction breakdown
        $reactions = Reaction::where('simulation_id', $simulation->id)
            ->select('type', DB::raw('count(*) as count'))
            ->groupBy('type')
            ->get();

        // Rating distribution
        $ratingDistribution = Rating::where('simulation_id', $simulation->id)
            ->select('rating', DB::raw('count(*) as count'))
            ->groupBy('rating')
            ->orderBy('rating')
            ->get();

        return view('studio.analytics', compact('simulation', 'dailyAnalytics', 'reactions', 'ratingDistribution'));
    }

    // ========== Comments Moderation ==========

    /**
     * List all comments on creator's simulations with filters.
     */
    public function comments(Request $request): View
    {
        /** @var User $user */
        $user = Auth::user();
        $filter = $request->input('filter', 'all');

        $simIds = Simulation::where('user_id', $user->id)->pluck('id');
        $query = Comment::whereIn('simulation_id', $simIds)->with('user', 'simulation');

        if ($filter === 'replied') {
            $query->has('replies');
        } elseif ($filter === 'unreplied') {
            $query->doesntHave('replies');
        } elseif ($filter === 'reported') {
            $query->where('is_reported', true);
        } elseif ($filter === 'pinned') {
            $query->where('is_pinned', true);
        }

        $comments = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        return view('studio.comments', compact('comments', 'filter'));
    }

    /**
     * Reply to a comment from Studio.
     */
    public function replyComment(Request $request, int $commentId): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $parentComment = Comment::findOrFail($commentId);

        // Verify the comment belongs to creator's simulation
        $simulation = Simulation::where('user_id', $user->id)
            ->where('id', $parentComment->simulation_id)
            ->firstOrFail();

        $validated = $request->validate([
            'body' => 'required|string|max:2000',
        ]);

        $reply = Comment::create([
            'user_id' => $user->id,
            'simulation_id' => $simulation->id,
            'parent_id' => $parentComment->id,
            'body' => $validated['body'],
        ]);

        // Notify the original commenter
        if ($parentComment->user_id !== $user->id) {
            Notification::create([
                'user_id' => $parentComment->user_id,
                'type' => 'comment_reply',
                'title' => 'Balasan dari Kreator',
                'body' => $user->name.' membalas komentar Anda di '.$simulation->title,
                'data' => [
                    'simulation_slug' => $simulation->slug,
                    'comment_id' => $reply->id,
                    'url' => route('simulations.show', $simulation->slug).'#comment-'.$reply->id,
                ],
            ]);
        }

        return redirect()->route('studio.comments')
            ->with('success', 'Balasan berhasil dikirim.');
    }

    /**
     * Toggle pin/unpin a comment.
     */
    public function togglePinComment(int $commentId): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $comment = Comment::findOrFail($commentId);

        // Verify ownership
        Simulation::where('user_id', $user->id)
            ->where('id', $comment->simulation_id)
            ->firstOrFail();

        $comment->update(['is_pinned' => ! $comment->is_pinned]);

        return redirect()->route('studio.comments')
            ->with('success', $comment->is_pinned ? 'Komentar disematkan.' : 'Komentar dilepas dari sematan.');
    }

    /**
     * Delete a comment from Studio.
     */
    public function destroyComment(int $commentId): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $comment = Comment::findOrFail($commentId);

        // Verify ownership
        Simulation::where('user_id', $user->id)
            ->where('id', $comment->simulation_id)
            ->firstOrFail();

        $comment->delete();

        return redirect()->route('studio.comments')
            ->with('success', 'Komentar berhasil dihapus.');
    }

    // ========== Followers ==========

    /**
     * List all followers of the creator.
     */
    public function followers(): View
    {
        /** @var User $user */
        $user = Auth::user();

        $followers = Follow::where('followable_id', $user->id)
            ->where('followable_type', User::class)
            ->with('follower')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('studio.followers', compact('followers'));
    }

    // ========== Settings ==========

    /**
     * Show studio settings form.
     */
    public function settings(): View
    {
        /** @var User $user */
        $user = Auth::user();

        return view('studio.settings', compact('user'));
    }

    /**
     * Update studio settings (creator profile).
     */
    public function updateSettings(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'bio' => 'nullable|string|max:1000',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = [
            'name' => $validated['name'],
            'bio' => $validated['bio'] ?? null,
        ];

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($data);

        return redirect()->route('studio.settings')
            ->with('success', 'Pengaturan berhasil diupdate.');
    }

    // ========== Helper Methods ==========

    /**
     * Bump version number (patch version).
     */
    private function bumpVersion(string $version): string
    {
        $parts = explode('.', $version);
        $parts[2] = ($parts[2] ?? 0) + 1;

        return implode('.', $parts);
    }

    /**
     * Recursively delete a directory.
     */
    private function deleteDirectory(string $path): void
    {
        if (! is_dir($path)) {
            return;
        }

        $items = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($items as $item) {
            $item->isDir() ? rmdir($item->getRealPath()) : unlink($item->getRealPath());
        }

        rmdir($path);
    }
}
