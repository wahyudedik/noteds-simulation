<?php

namespace App\Services;

use App\Models\CreatorAd;
use App\Models\CreatorReputation;

class AdRevenueService
{
    /**
     * Revenue sharing tiers: creator_share / platform_share.
     */
    private const REVENUE_TIERS = [
        'basic' => ['creator' => 55, 'platform' => 45],
        'verified' => ['creator' => 65, 'platform' => 35],
        'expert' => ['creator' => 75, 'platform' => 25],
        'platinum' => ['creator' => 85, 'platform' => 15],
    ];

    /**
     * Minimum payout threshold in IDR.
     */
    private const MIN_PAYOUT_IDR = 500_000;

    /**
     * Get the revenue share percentages for a creator based on their tier.
     */
    public function getRevenueShare(string $tier): array
    {
        return self::REVENUE_TIERS[$tier] ?? self::REVENUE_TIERS['basic'];
    }

    /**
     * Calculate estimated revenue for a creator based on ad impressions.
     * Uses a simple RPM (Revenue Per Mille) estimation.
     */
    public function calculateEstimatedRevenue(int $creatorId, float $rpm = 10000): array
    {
        $totalImpressions = CreatorAd::where('user_id', $creatorId)
            ->where('is_active', true)
            ->sum('id'); // This is a placeholder — in production, sum from ad_impressions

        // Get creator's reputation tier
        $reputation = CreatorReputation::where('user_id', $creatorId)->first();
        $tier = $reputation?->revenue_tier ?? 'basic';
        $share = $this->getRevenueShare($tier);

        // Estimate: RPM = revenue per 1000 impressions
        $estimatedRevenue = ($totalImpressions / 1000) * $rpm;
        $creatorShare = $estimatedRevenue * ($share['creator'] / 100);
        $platformShare = $estimatedRevenue * ($share['platform'] / 100);

        return [
            'tier' => $tier,
            'total_impressions' => $totalImpressions,
            'estimated_revenue' => round($estimatedRevenue, 2),
            'creator_share_percent' => $share['creator'],
            'platform_share_percent' => $share['platform'],
            'creator_revenue' => round($creatorShare, 2),
            'platform_revenue' => round($platformShare, 2),
            'min_payout' => self::MIN_PAYOUT_IDR,
            'is_payout_ready' => $creatorShare >= self::MIN_PAYOUT_IDR,
        ];
    }

    /**
     * Get all revenue tiers with their requirements.
     */
    public function getTiers(): array
    {
        return [
            'basic' => [
                'name' => 'Basic',
                'creator_share' => 55,
                'platform_share' => 45,
                'requirements' => 'Creator baru',
            ],
            'verified' => [
                'name' => 'Verified',
                'creator_share' => 65,
                'platform_share' => 35,
                'requirements' => '10+ simulasi, rating >= 4.0',
            ],
            'expert' => [
                'name' => 'Expert',
                'creator_share' => 75,
                'platform_share' => 25,
                'requirements' => '50+ simulasi, rating >= 4.5',
            ],
            'platinum' => [
                'name' => 'Platinum',
                'creator_share' => 85,
                'platform_share' => 15,
                'requirements' => '100+ simulasi, rating >= 4.7',
            ],
        ];
    }
}
