<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdImpression;
use App\Models\CreatorAd;
use App\Models\PlatformAd;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdAnalyticsController extends Controller
{
    /**
     * Display the ad analytics dashboard.
     */
    public function index(Request $request): View
    {
        $period = $request->input('period', '30');
        $startDate = now()->subDays((int) $period)->toDateString();

        // ─── Platform Ads Summary ─────────────────────────────────
        $platformAds = PlatformAd::all();
        $platformImpressions = AdImpression::where('ad_type', 'platform')
            ->where('created_at', '>=', $startDate)
            ->count();
        $platformClicks = AdImpression::where('ad_type', 'platform')
            ->where('created_at', '>=', $startDate)
            ->where('clicked', true)
            ->count();
        $platformCtr = $platformImpressions > 0
            ? round(($platformClicks / $platformImpressions) * 100, 2)
            : 0;
        $platformRevenue = $platformAds->sum('revenue');

        // ─── Creator Ads Summary ──────────────────────────────────
        $creatorAds = CreatorAd::all();
        $creatorImpressions = AdImpression::where('ad_type', 'creator')
            ->where('created_at', '>=', $startDate)
            ->count();
        $creatorClicks = AdImpression::where('ad_type', 'creator')
            ->where('created_at', '>=', $startDate)
            ->where('clicked', true)
            ->count();
        $creatorCtr = $creatorImpressions > 0
            ? round(($creatorClicks / $creatorImpressions) * 100, 2)
            : 0;
        $creatorRevenue = $creatorAds->sum('revenue');

        // ─── Top Performing Ads ───────────────────────────────────
        $topPlatformAds = PlatformAd::orderByDesc('impressions')
            ->take(10)
            ->get()
            ->map(function ($ad) {
                $ctr = $ad->impressions > 0
                    ? round(($ad->clicks / $ad->impressions) * 100, 2)
                    : 0;

                return [
                    'id' => $ad->id,
                    'title' => $ad->title,
                    'type' => $ad->type,
                    'position' => $ad->position,
                    'impressions' => $ad->impressions,
                    'clicks' => $ad->clicks,
                    'ctr' => $ctr,
                    'revenue' => $ad->revenue,
                    'is_active' => $ad->is_active,
                ];
            });

        $topCreatorAds = CreatorAd::where('review_status', 'approved')
            ->orderByDesc('impressions')
            ->take(10)
            ->with('user', 'simulation')
            ->get()
            ->map(function ($ad) {
                $ctr = $ad->impressions > 0
                    ? round(($ad->clicks / $ad->impressions) * 100, 2)
                    : 0;

                return [
                    'id' => $ad->id,
                    'title' => $ad->title ?? $ad->simulation->title,
                    'creator' => $ad->user->name,
                    'provider' => $ad->provider,
                    'impressions' => $ad->impressions,
                    'clicks' => $ad->clicks,
                    'ctr' => $ctr,
                    'revenue' => $ad->revenue,
                ];
            });

        // ─── Daily Ad Impressions Chart Data ──────────────────────
        $dailyImpressions = AdImpression::where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, ad_type, COUNT(*) as count, SUM(clicked) as clicks')
            ->groupBy('date', 'ad_type')
            ->orderBy('date')
            ->get()
            ->groupBy('date');

        $chartLabels = collect();
        $chartPlatformImpressions = collect();
        $chartCreatorImpressions = collect();
        $chartPlatformClicks = collect();
        $chartCreatorClicks = collect();

        foreach ($dailyImpressions as $date => $records) {
            $chartLabels->push($date);
            $platform = $records->where('ad_type', 'platform')->first();
            $creator = $records->where('ad_type', 'creator')->first();
            $chartPlatformImpressions->push($platform?->count ?? 0);
            $chartCreatorImpressions->push($creator?->count ?? 0);
            $chartPlatformClicks->push($platform?->clicks ?? 0);
            $chartCreatorClicks->push($creator?->clicks ?? 0);
        }

        return view('admin.ad-analytics.index', compact(
            'period',
            'platformImpressions',
            'platformClicks',
            'platformCtr',
            'platformRevenue',
            'creatorImpressions',
            'creatorClicks',
            'creatorCtr',
            'creatorRevenue',
            'topPlatformAds',
            'topCreatorAds',
            'chartLabels',
            'chartPlatformImpressions',
            'chartCreatorImpressions',
            'chartPlatformClicks',
            'chartCreatorClicks',
        ));
    }
}
