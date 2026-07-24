<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserProfileController extends Controller
{
    /**
     * Display the user's profile with tabs.
     */
    public function index(Request $request, ?string $tab = null): View
    {
        $tab ??= 'bookmarks';

        $user = $request->user();
        $validTabs = ['bookmarks', 'history', 'following', 'collections', 'forum'];
        $activeTab = in_array($tab, $validTabs) ? $tab : 'bookmarks';

        $data = match ($activeTab) {
            'bookmarks' => $this->getBookmarks($user),
            'history' => $this->getHistory($user),
            'following' => $this->getFollowing($user),
            'collections' => $this->getCollections($user),
            'forum' => $this->getForumThreads($user),
            default => [],
        };

        $stats = [
            'bookmarks' => $user->bookmarks()->count(),
            'simulations_played' => $user->playHistory()->count(),
            'following' => $user->following()->count(),
            'followers' => $user->followers()->count(),
            'comments' => $user->comments()->count(),
            'collections' => $user->collections()->count(),
            'forum_threads' => $user->forumThreads()->count(),
            'forum_replies' => $user->forumReplies()->count(),
        ];

        return view('user-profile.index', compact('user', 'activeTab', 'data', 'stats'));
    }

    /**
     * Get user's bookmarked simulations.
     */
    private function getBookmarks($user): array
    {
        $bookmarks = $user->bookmarks()
            ->with('simulation.user')
            ->latest()
            ->paginate(12);

        return ['bookmarks' => $bookmarks];
    }

    /**
     * Get user's play history.
     */
    private function getHistory($user): array
    {
        $history = $user->playHistory()
            ->with('simulation.user')
            ->latest()
            ->paginate(12);

        return ['history' => $history];
    }

    /**
     * Get users that the current user is following.
     */
    private function getFollowing($user): array
    {
        $followingUserIds = $user->following()->pluck('followable_id');
        $following = User::whereIn('id', $followingUserIds)
            ->withCount('simulations')
            ->with('simulations')
            ->latest()
            ->paginate(12);

        return ['following' => $following];
    }

    /**
     * Get user's collections.
     */
    private function getCollections($user): array
    {
        $collections = $user->collections()
            ->withCount('simulations')
            ->latest()
            ->paginate(12);

        return ['collections' => $collections];
    }

    /**
     * Get user's forum threads and replies.
     */
    private function getForumThreads($user): array
    {
        $threads = $user->forumThreads()
            ->with('category')
            ->latest()
            ->paginate(10);

        $replies = $user->forumReplies()
            ->with('thread.category')
            ->latest()
            ->paginate(10);

        return [
            'forum_threads' => $threads,
            'forum_replies' => $replies,
        ];
    }
}
