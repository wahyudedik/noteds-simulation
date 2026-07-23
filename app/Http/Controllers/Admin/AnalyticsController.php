<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\CreatorAd;
use App\Models\PlatformAd;
use App\Models\PlatformAnalytic;
use App\Models\Simulation;
use App\Models\User;
use Illuminate\Http\Request;
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

        // Chart data
        $chartLabels = $analytics->pluck('date->format("d M")')->values();
        $chartUsers = $analytics->pluck('new_registrations')->values();
        $chartViews = $analytics->pluck('total_views')->values();
        $chartPlays = $analytics->pluck('total_plays')->values();
        $chartRevenue = $analytics->pluck('total_revenue')->values();

        return view('admin.analytics.index', compact(
            'analytics',
            'currentStats',
            'growth',
            'topCategories',
            'chartLabels',
            'chartUsers',
            'chartViews',
            'chartPlays',
            'chartRevenue',
            'period'
        ));
    }
}
