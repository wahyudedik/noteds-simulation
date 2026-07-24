<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\CreatorAd;
use App\Models\CreatorReputation;
use App\Models\Payout;
use App\Models\PlatformAd;
use App\Models\PlatformAnalytic;
use App\Models\Simulation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AnalyticsController extends Controller
{
    /**
     * Display the platform analytics dashboard.
     */
    public function index(Request $request): View
    {
        $period = $request->input('period', '30');
        $startDate = now()->subDays((int) $period)->toDateString();

        // Collect current data if not exists for today
        PlatformAnalytic::collectForDate();

        // Historical analytics
        $analytics = PlatformAnalytic::where('date', '>=', $startDate)
            ->orderBy('date')
            ->get();

        // Current totals
        $currentStats = [
            'total_users' => User::count(),
            'total_simulations' => Simulation::count(),
            'published_simulations' => Simulation::where('is_published', true)->count(),
            'total_views' => Simulation::sum('view_count'),
            'total_plays' => Simulation::sum('play_count'),
            'total_comments' => Comment::count(),
            'total_ad_revenue' => CreatorAd::where('review_status', 'approved')->sum('revenue')
                + PlatformAd::sum('revenue'),
        ];

        // Period comparison
        $previousStart = now()->subDays((int) $period * 2)->toDateString();
        $previousEnd = now()->subDays((int) $period)->toDateString();
        $previousStats = PlatformAnalytic::whereBetween('date', [$previousStart, $previousEnd])->first();

        $growth = [];
        if ($previousStats && $previousStats->total_users > 0) {
            $growth['users'] = $currentStats['total_users'] - $previousStats->total_users;
            $growth['simulations'] = $currentStats['total_simulations'] - $previousStats->total_simulations;
            $growth['views'] = $currentStats['total_views'] - $previousStats->total_views;
            $growth['plays'] = $currentStats['total_plays'] - $previousStats->total_plays;
        }

        // Top categories
        $topCategories = Simulation::published()
            ->selectRaw('category, COUNT(*) as count, SUM(view_count) as total_views')
            ->groupBy('category')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        return view('admin.analytics.index', compact(
            'analytics',
            'currentStats',
            'growth',
            'topCategories',
            'period',
        ));
    }

    /**
     * Display user analytics breakdown.
     */
    public function users(Request $request): View
    {
        $period = $request->input('period', '30');
        $startDate = now()->subDays((int) $period)->toDateString();

        // Registration trends
        $registrations = User::where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Role breakdown
        $roleBreakdown = User::select('role', DB::raw('count(*) as count'))
            ->groupBy('role')
            ->get();

        // Top creators by simulation count
        $topCreators = User::where('role', 'creator')
            ->withCount('simulations')
            ->orderByDesc('simulations_count')
            ->limit(10)
            ->get(['id', 'name', 'email', 'created_at']);

        // User activity (plays, comments)
        $totalUsers = User::count();
        $activeUsers = User::whereHas('playHistory', fn ($q) => $q->where('created_at', '>=', $startDate))->count();
        $totalPlays = Simulation::sum('play_count');
        $totalComments = Comment::count();

        return view('admin.analytics.users', compact(
            'registrations',
            'roleBreakdown',
            'topCreators',
            'totalUsers',
            'activeUsers',
            'totalPlays',
            'totalComments',
            'period'
        ));
    }

    /**
     * Display revenue analytics breakdown.
     */
    public function revenue(Request $request): View
    {
        $period = $request->input('period', '30');
        $startDate = now()->subDays((int) $period)->toDateString();

        // Creator ad revenue
        $creatorAdRevenue = CreatorAd::where('review_status', 'approved')
            ->where('created_at', '>=', $startDate)
            ->sum('revenue');

        // Platform ad revenue
        $platformAdRevenue = PlatformAd::where('created_at', '>=', $startDate)
            ->sum('revenue');

        // Total revenue
        $totalRevenue = $creatorAdRevenue + $platformAdRevenue;

        // Revenue by creator tier
        $revenueByTier = CreatorReputation::selectRaw('revenue_tier, COUNT(*) as creators, SUM(total_revenue) as total')
            ->groupBy('revenue_tier')
            ->get();

        // Top earning simulations
        $topEarningSimulations = Simulation::published()
            ->whereHas('creatorAds', fn ($q) => $q->where('review_status', 'approved'))
            ->withSum('creatorAds', 'revenue')
            ->orderByDesc('creator_ads_sum_revenue')
            ->limit(10)
            ->get(['id', 'title', 'slug', 'play_count']);

        // Payout stats
        $totalPaid = Payout::where('status', 'paid')
            ->where('paid_at', '>=', $startDate)
            ->sum('amount');
        $pendingPayouts = Payout::where('status', 'pending')->sum('amount');

        return view('admin.analytics.revenue', compact(
            'creatorAdRevenue',
            'platformAdRevenue',
            'totalRevenue',
            'revenueByTier',
            'topEarningSimulations',
            'totalPaid',
            'pendingPayouts',
            'period'
        ));
    }
}
