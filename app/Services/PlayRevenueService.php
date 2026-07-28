<?php

namespace App\Services;

use App\Models\AdImpression;
use App\Models\CreatorAd;
use App\Models\CreatorReputation;
use App\Models\Simulation;
use App\Models\User;
use Carbon\Carbon;

class PlayRevenueService
{
    /**
     * Default RPM (Revenue Per Mille) — revenue per 1000 impressions.
     */
    private const DEFAULT_RPM = 10000.0;

    /**
     * Revenue share tiers (creator / platform).
     */
    private const REVENUE_TIERS = [
        'basic' => ['creator' => 55, 'platform' => 45],
        'verified' => ['creator' => 65, 'platform' => 35],
        'expert' => ['creator' => 75, 'platform' => 25],
        'platinum' => ['creator' => 85, 'platform' => 15],
    ];

    /**
     * Calculate total revenue for a specific simulation based on ad impressions.
     */
    public function calculateRevenue(Simulation $simulation, float $rpm = self::DEFAULT_RPM): float
    {
        $impressions = AdImpression::where('simulation_id', $simulation->id)
            ->where('ad_type', 'creator')
            ->count();

        // Also count impressions from creator_ads linked to this simulation
        $creatorAdImpressions = CreatorAd::where('simulation_id', $simulation->id)
            ->where('review_status', 'approved')
            ->sum('impressions');

        $totalImpressions = $impressions + $creatorAdImpressions;

        // Get creator tier for revenue share
        $reputation = CreatorReputation::where('user_id', $simulation->user_id)->first();
        $tier = $reputation?->revenue_tier ?? 'basic';
        $share = self::REVENUE_TIERS[$tier] ?? self::REVENUE_TIERS['basic'];

        $grossRevenue = ($totalImpressions / 1000) * $rpm;

        return round($grossRevenue * ($share['creator'] / 100), 2);
    }

    /**
     * Get revenue breakdown per simulation for a creator.
     */
    public function getRevenueBreakdown(User $creator, float $rpm = self::DEFAULT_RPM): array
    {
        $simulations = Simulation::where('user_id', $creator->id)
            ->where('is_published', true)
            ->get();

        $reputation = CreatorReputation::where('user_id', $creator->id)->first();
        $tier = $reputation?->revenue_tier ?? 'basic';
        $share = self::REVENUE_TIERS[$tier] ?? self::REVENUE_TIERS['basic'];

        $breakdown = [];
        $totalRevenue = 0;

        foreach ($simulations as $sim) {
            $adImpressions = AdImpression::where('simulation_id', $sim->id)
                ->where('ad_type', 'creator')
                ->count();

            $creatorAdImpressions = CreatorAd::where('simulation_id', $sim->id)
                ->where('review_status', 'approved')
                ->sum('impressions');

            $totalImpressions = $adImpressions + $creatorAdImpressions;
            $grossRevenue = ($totalImpressions / 1000) * $rpm;
            $creatorRevenue = round($grossRevenue * ($share['creator'] / 100), 2);

            $totalRevenue += $creatorRevenue;

            $breakdown[] = [
                'simulation_id' => $sim->id,
                'title' => $sim->title,
                'slug' => $sim->slug,
                'impressions' => $totalImpressions,
                'gross_revenue' => round($grossRevenue, 2),
                'creator_revenue' => $creatorRevenue,
                'platform_revenue' => round($grossRevenue - $creatorRevenue, 2),
                'play_count' => $sim->play_count,
                'view_count' => $sim->view_count,
            ];
        }

        // Sort by creator_revenue descending
        usort($breakdown, fn ($a, $b) => $b['creator_revenue'] <=> $a['creator_revenue']);

        return [
            'tier' => $tier,
            'tier_label' => match ($tier) {
                'platinum' => 'Platinum',
                'expert' => 'Expert',
                'verified' => 'Verified',
                default => 'Basic',
            },
            'creator_share_percent' => $share['creator'],
            'platform_share_percent' => $share['platform'],
            'total_revenue' => round($totalRevenue, 2),
            'simulations' => $breakdown,
        ];
    }

    /**
     * Get monthly revenue for a creator.
     */
    public function getMonthlyRevenue(User $creator, Carbon $month, float $rpm = self::DEFAULT_RPM): float
    {
        $startDate = $month->copy()->startOfMonth();
        $endDate = $month->copy()->endOfMonth();

        $simulationIds = Simulation::where('user_id', $creator->id)
            ->pluck('id');

        $impressions = AdImpression::whereIn('simulation_id', $simulationIds)
            ->where('ad_type', 'creator')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $creatorAdImpressions = CreatorAd::whereIn('simulation_id', $simulationIds)
            ->where('review_status', 'approved')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('impressions');

        $totalImpressions = $impressions + $creatorAdImpressions;

        $reputation = CreatorReputation::where('user_id', $creator->id)->first();
        $tier = $reputation?->revenue_tier ?? 'basic';
        $share = self::REVENUE_TIERS[$tier] ?? self::REVENUE_TIERS['basic'];

        $grossRevenue = ($totalImpressions / 1000) * $rpm;

        return round($grossRevenue * ($share['creator'] / 100), 2);
    }

    /**
     * Get daily revenue data for chart display (last N days).
     */
    public function getDailyRevenue(User $creator, int $days = 30, float $rpm = self::DEFAULT_RPM): array
    {
        $simulationIds = Simulation::where('user_id', $creator->id)
            ->pluck('id');

        $reputation = CreatorReputation::where('user_id', $creator->id)->first();
        $tier = $reputation?->revenue_tier ?? 'basic';
        $share = self::REVENUE_TIERS[$tier] ?? self::REVENUE_TIERS['basic'];

        $dailyData = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $startOfDay = $date->copy()->startOfDay();
            $endOfDay = $date->copy()->endOfDay();

            $impressions = AdImpression::whereIn('simulation_id', $simulationIds)
                ->where('ad_type', 'creator')
                ->whereBetween('created_at', [$startOfDay, $endOfDay])
                ->count();

            $creatorAdImpressions = CreatorAd::whereIn('simulation_id', $simulationIds)
                ->where('review_status', 'approved')
                ->whereBetween('created_at', [$startOfDay, $endOfDay])
                ->sum('impressions');

            $totalImpressions = $impressions + $creatorAdImpressions;
            $grossRevenue = ($totalImpressions / 1000) * $rpm;
            $creatorRevenue = round($grossRevenue * ($share['creator'] / 100), 2);

            $dailyData[] = [
                'date' => $date->format('Y-m-d'),
                'label' => $date->format('d M'),
                'impressions' => $totalImpressions,
                'gross_revenue' => round($grossRevenue, 2),
                'creator_revenue' => $creatorRevenue,
            ];
        }

        return $dailyData;
    }

    /**
     * Process daily revenue calculation — update creator_ads.revenue and creator_reputations.total_revenue.
     */
    public function processDailyRevenue(): int
    {
        $updatedCount = 0;

        // Get all creators with approved ads
        $creatorUserIds = CreatorAd::where('review_status', 'approved')
            ->distinct()
            ->pluck('user_id');

        $creators = User::whereIn('id', $creatorUserIds)->get();

        foreach ($creators as $creator) {
            $breakdown = $this->getRevenueBreakdown($creator);
            $totalRevenue = $breakdown['total_revenue'];

            // Update creator_reputations.total_revenue
            CreatorReputation::where('user_id', $creator->id)
                ->update(['total_revenue' => $totalRevenue]);

            // Update individual creator_ads.revenue based on their simulation impressions
            $creatorAds = CreatorAd::where('user_id', $creator->id)
                ->where('review_status', 'approved')
                ->get();

            foreach ($creatorAds as $ad) {
                $simImpressions = AdImpression::where('simulation_id', $ad->simulation_id)
                    ->where('ad_type', 'creator')
                    ->count();

                $totalImpressions = $simImpressions + $ad->impressions;
                $reputation = CreatorReputation::where('user_id', $creator->id)->first();
                $tier = $reputation?->revenue_tier ?? 'basic';
                $share = self::REVENUE_TIERS[$tier] ?? self::REVENUE_TIERS['basic'];

                $grossRevenue = ($totalImpressions / 1000) * self::DEFAULT_RPM;
                $ad->update([
                    'revenue' => round($grossRevenue * ($share['creator'] / 100), 2),
                ]);
            }

            $updatedCount++;
        }

        return $updatedCount;
    }
}
