<?php

namespace App\Services;

use App\Models\AdImpression;
use App\Models\PlatformAd;
use Illuminate\Support\Collection;

class AdService
{
    /**
     * Get a random active ad for a specific position, weighted by the ad's weight.
     */
    public function getAdForPosition(string $position, ?int $excludeId = null): ?PlatformAd
    {
        $query = PlatformAd::active()->forPosition($position);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        $ads = $query->get();

        if ($ads->isEmpty()) {
            return null;
        }

        // Weighted random selection
        $totalWeight = $ads->sum('weight');
        if ($totalWeight <= 0) {
            return $ads->random();
        }

        $random = mt_rand(1, $totalWeight);
        $cumulative = 0;

        foreach ($ads as $ad) {
            $cumulative += $ad->weight;
            if ($random <= $cumulative) {
                return $ad;
            }
        }

        return $ads->last();
    }

    /**
     * Record an impression for a platform ad.
     */
    public function recordImpression(
        PlatformAd $ad,
        ?int $simulationId = null,
        ?int $userId = null,
        string $position = '',
    ): AdImpression {
        $ad->increment('impressions');

        return AdImpression::create([
            'ad_type' => 'platform',
            'ad_id' => $ad->id,
            'simulation_id' => $simulationId,
            'user_id' => $userId,
            'position' => $position,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Record a click for a platform ad.
     */
    public function recordClick(PlatformAd $ad, ?int $simulationId = null, ?int $userId = null): AdImpression
    {
        $ad->increment('clicks');

        return AdImpression::create([
            'ad_type' => 'platform',
            'ad_id' => $ad->id,
            'simulation_id' => $simulationId,
            'user_id' => $userId,
            'position' => '',
            'clicked' => true,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Get ad stats summary for admin dashboard.
     */
    public function getStats(): array
    {
        $totalAds = PlatformAd::count();
        $activeAds = PlatformAd::active()->count();
        $totalImpressions = PlatformAd::sum('impressions');
        $totalClicks = PlatformAd::sum('clicks');
        $totalRevenue = PlatformAd::sum('revenue');
        $ctr = $totalImpressions > 0 ? round(($totalClicks / $totalImpressions) * 100, 2) : 0;

        return [
            'total_ads' => $totalAds,
            'active_ads' => $activeAds,
            'total_impressions' => (int) $totalImpressions,
            'total_clicks' => (int) $totalClicks,
            'total_revenue' => (float) $totalRevenue,
            'ctr' => $ctr,
        ];
    }

    /**
     * Get daily ad performance for the last N days.
     */
    public function getDailyPerformance(int $days = 30): Collection
    {
        return AdImpression::where('ad_type', 'platform')
            ->where('created_at', '>=', now()->subDays($days))
            ->selectRaw('DATE(created_at) as date')
            ->selectRaw('count(*) as impressions')
            ->selectRaw('sum(clicked) as clicks')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }
}
