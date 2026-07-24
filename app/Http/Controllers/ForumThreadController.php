<?php

namespace App\Http\Controllers;

use App\Models\ForumCategory;
use App\Models\ForumThread;
use App\Models\User;
use App\Services\GamificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ForumThreadController extends Controller
{
    /**
     * Show the form for creating a new thread.
     */
    public function create(): View
    {
        $categories = ForumCategory::ordered()->get();

        return view('forum.create', compact('categories'));
    }

    /**
     * Store a newly created thread.
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string|max:10000',
            'forum_category_id' => 'required|exists:forum_categories,id',
            'simulation_id' => 'nullable|exists:simulations,id',
        ]);

        /** @var User $user */
        $user = Auth::user();

        $thread = ForumThread::create([
            'user_id' => $user->id,
            'forum_category_id' => $validated['forum_category_id'],
            'simulation_id' => $validated['simulation_id'] ?? null,
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']).'-'.Str::random(5),
            'body' => $validated['body'],
        ]);

        // Update category thread count
        ForumCategory::where('id', $thread->forum_category_id)->increment('threads_count');

        // Gamification: award points for creating a thread
        $gamification = app(GamificationService::class);
        $gamification->awardPoints($user, 'forum_thread', 'Thread baru: '.$thread->title);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'redirect' => route('forum.show', $thread->slug),
            ]);
        }

        return redirect()->route('forum.show', $thread->slug)->with('success', 'Thread berhasil dibuat!');
    }

    /**
     * Display the specified thread.
     */
    public function show(string $slug): View
    {
        $thread = ForumThread::with(['user', 'category', 'simulation', 'lastReplyUser'])
            ->where('slug', $slug)
            ->firstOrFail();

        // Increment view count — deduplicate per session
        $viewedKey = 'thread_viewed_'.$thread->id;
        if (! session()->has($viewedKey)) {
            $thread->increment('views_count');
            session()->put($viewedKey, true);
        }

        // Load replies with user
        $replies = $thread->replies()
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();

        // Get user vote if authenticated
        $userVote = null;
        if (Auth::check()) {
            $vote = $thread->votes()->where('user_id', Auth::id())->first();
            $userVote = $vote?->value;
        }

        $categories = ForumCategory::ordered()->get();

        return view('forum.show', compact('thread', 'replies', 'userVote', 'categories'));
    }

    /**
     * Show the form for editing the specified thread.
     */
    public function edit(string $slug): View
    {
        $thread = ForumThread::where('slug', $slug)->firstOrFail();

        $this->authorizeThread($thread);

        $categories = ForumCategory::ordered()->get();

        return view('forum.edit', compact('thread', 'categories'));
    }

    /**
     * Update the specified thread.
     */
    public function update(Request $request, string $slug): RedirectResponse
    {
        $thread = ForumThread::where('slug', $slug)->firstOrFail();

        $this->authorizeThread($thread);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string|max:10000',
            'forum_category_id' => 'required|exists:forum_categories,id',
        ]);

        $oldCategoryId = $thread->forum_category_id;

        $thread->update([
            'title' => $validated['title'],
            'body' => $validated['body'],
            'forum_category_id' => $validated['forum_category_id'],
        ]);

        // Update category counts if category changed
        if ($oldCategoryId !== $thread->forum_category_id) {
            ForumCategory::where('id', $oldCategoryId)->decrement('threads_count');
            ForumCategory::where('id', $thread->forum_category_id)->increment('threads_count');
        }

        return redirect()->route('forum.show', $thread->slug)->with('success', 'Thread berhasil diperbarui!');
    }

    /**
     * Remove the specified thread.
     */
    public function destroy(string $slug): RedirectResponse
    {
        $thread = ForumThread::where('slug', $slug)->firstOrFail();

        $this->authorizeThread($thread);

        $categoryId = $thread->forum_category_id;

        // Delete replies first
        $thread->replies()->delete();
        $thread->votes()->delete();
        $thread->delete();

        // Update category thread count
        ForumCategory::where('id', $categoryId)->decrement('threads_count');

        return redirect()->route('forum.index')->with('success', 'Thread berhasil dihapus!');
    }

    /**
     * Toggle lock on a thread (admin only).
     */
    public function lock(string $slug): RedirectResponse
    {
        $thread = ForumThread::where('slug', $slug)->firstOrFail();

        /** @var User $user */
        $user = Auth::user();
        if (! $user->isAdmin()) {
            abort(403);
        }

        $thread->update(['is_locked' => ! $thread->is_locked]);

        return redirect()->back()->with('success', $thread->is_locked ? 'Thread dikunci.' : 'Thread dibuka kuncinya.');
    }

    /**
     * Toggle pin on a thread (admin only).
     */
    public function pin(string $slug): RedirectResponse
    {
        $thread = ForumThread::where('slug', $slug)->firstOrFail();

        /** @var User $user */
        $user = Auth::user();
        if (! $user->isAdmin()) {
            abort(403);
        }

        $thread->update(['is_pinned' => ! $thread->is_pinned]);

        return redirect()->back()->with('success', $thread->is_pinned ? 'Thread disematkan.' : 'Thread dilepas dari sematan.');
    }

    /**
     * Check if the current user can modify the thread.
     */
    private function authorizeThread(ForumThread $thread): void
    {
        /** @var User $user */
        $user = Auth::user();

        if (! $thread->isOwnedBy($user) && ! $user->isAdmin()) {
            abort(403, 'Anda tidak memiliki akses untuk mengubah thread ini.');
        }
    }
}
