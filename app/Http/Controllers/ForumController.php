<?php

namespace App\Http\Controllers;

use App\Models\ForumCategory;
use App\Models\ForumReply;
use App\Models\ForumThread;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ForumController extends Controller
{
    /**
     * Display the forum index page with categories and latest threads.
     */
    public function index(Request $request): View
    {
        $categories = ForumCategory::ordered()->get();
        $sort = $request->input('sort', 'latest');

        $query = ForumThread::with(['user', 'category', 'lastReplyUser'])
            ->withCount('replies');

        $query = match ($sort) {
            'popular' => $query->popular(),
            'unanswered' => $query->unanswered(),
            default => $query->latest(),
        };

        $threads = $query->paginate(15)->withQueryString();

        $stats = [
            'total_threads' => ForumThread::count(),
            'total_replies' => ForumReply::count(),
            'total_users' => ForumThread::distinct('user_id')->count('user_id'),
        ];

        return view('forum.index', compact('categories', 'threads', 'stats', 'sort'));
    }

    /**
     * Display threads filtered by category.
     */
    public function category(string $slug, Request $request): View
    {
        $category = ForumCategory::where('slug', $slug)->firstOrFail();
        $sort = $request->input('sort', 'latest');

        $query = $category->threads()
            ->with(['user', 'lastReplyUser'])
            ->withCount('replies');

        $query = match ($sort) {
            'popular' => $query->popular(),
            'unanswered' => $query->unanswered(),
            default => $query->latest(),
        };

        $threads = $query->paginate(15)->withQueryString();
        $categories = ForumCategory::ordered()->get();

        return view('forum.category', compact('category', 'threads', 'categories', 'sort'));
    }
}
