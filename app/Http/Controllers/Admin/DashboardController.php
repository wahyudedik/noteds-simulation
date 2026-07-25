<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CodeScanLog;
use App\Models\Comment;
use App\Models\CreatorAd;
use App\Models\ForumCategory;
use App\Models\ForumReply;
use App\Models\ForumThread;
use App\Models\MarketplacePurchase;
use App\Models\Payout;
use App\Models\Simulation;
use App\Models\Sponsorship;
use App\Models\User;
use App\Models\UserReport;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index(): View
    {
        $user = auth()->user();

        // Platform-wide stats for admin/superadmin
        $stats = [
            'total_users' => User::count(),
            'total_simulations' => Simulation::count(),
            'published' => Simulation::where('is_published', true)->count(),
            'draft' => Simulation::where('is_published', false)->count(),
            'total_views' => (int) Simulation::sum('view_count'),
            'total_plays' => (int) Simulation::sum('play_count'),
            'total_likes' => (int) Simulation::sum('like_count'),
            'total_bookmarks' => (int) Simulation::sum('bookmark_count'),
            'total_shares' => (int) Simulation::sum('share_count'),
            'total_comments' => Comment::count(),
            'total_forum_threads' => ForumThread::count(),
            'total_forum_replies' => ForumReply::count(),
            'total_forum_categories' => ForumCategory::count(),
            'unsolved_threads' => ForumThread::where('is_solved', false)->count(),
        ];

        // Badge counts for admin menu cards (new items since last visit to each page)
        $badges = [
            'pending_reports' => UserReport::where('status', 'pending')
                ->where('created_at', '>=', session('badge_last_seen_reports', now()->subYear()))
                ->count(),
            'pending_creator_ads' => CreatorAd::where('review_status', 'pending_review')
                ->where('created_at', '>=', session('badge_last_seen_creator_ads', now()->subYear()))
                ->count(),
            'pending_payouts' => Payout::where('status', 'pending')
                ->where('created_at', '>=', session('badge_last_seen_payouts', now()->subYear()))
                ->count(),
            'pending_sponsorships' => Sponsorship::where('status', 'pending_review')
                ->where('created_at', '>=', session('badge_last_seen_sponsorships', now()->subYear()))
                ->count(),
            'draft_simulations' => Simulation::where('is_published', false)
                ->where('created_at', '>=', session('badge_last_seen_simulations', now()->subYear()))
                ->count(),
            'unsolved_threads' => ForumThread::where('is_solved', false)
                ->where('created_at', '>=', session('badge_last_seen_forum', now()->subYear()))
                ->count(),
            'new_users_24h' => User::where('created_at', '>=', session('badge_last_seen_users', now()->subYear()))
                ->count(),
            'pending_purchases' => MarketplacePurchase::where('payment_status', 'pending')
                ->where('created_at', '>=', session('badge_last_seen_marketplace', now()->subYear()))
                ->count(),
            'new_scans_with_issues' => CodeScanLog::where('result', '!=', 'clean')
                ->where('created_at', '>=', session('badge_last_seen_scans', now()->subYear()))
                ->count(),
        ];

        $recentSimulations = Simulation::with('user')
            ->latest()
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentSimulations', 'badges'));
    }
}
