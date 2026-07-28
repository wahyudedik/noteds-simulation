<?php

namespace App\Services;

use App\Models\CreatorReputation;
use App\Models\Simulation;
use App\Models\User;
use Illuminate\Support\Collection;

class CreatorRankingService
{
    /**
     * Weighted scoring formula for creator ranking.
     *
     * Score = (total_views × 0.1) + (total_plays × 0.2) + (avg_rating × 20)
     *       + (followers × 0.5) + (simulation_count × 2) + (badge_bonus)
     */
    public function calculateRank(User $creator): array
    {
        // Aggregate stats from published simulations
        $simStats = Simulation::where('user_id', $creator->id)
            ->published()
            ->selectRaw('
                COALESCE(SUM(view_count), 0) as total_views,
                COALESCE(SUM(play_count), 0) as total_plays,
                COALESCE(AVG(average_rating), 0) as avg_rating,
                COALESCE(AVG(rating_count), 0) as avg_rating_count,
                COUNT(*) as simulation_count
            ')
            ->first();

        // Follower count
        $followerCount = $creator->followers()->count();

        // Badge bonus
        $badgeCount = $creator->badges()->count();
        $badgeBonus = $badgeCount * 5;

        // Revenue tier bonus
        $reputation = $creator->reputation;
        $tierBonus = match ($reputation?->revenue_tier ?? 'basic') {
            'platinum' => 50,
            'expert' => 30,
            'verified' => 15,
            default => 0,
        };

        // Calculate weighted score
        $score = ($simStats->total_views * 0.1)
            + ($simStats->total_plays * 0.2)
            + ($simStats->avg_rating * 20)
            + ($followerCount * 0.5)
            + ($simStats->simulation_count * 2)
            + $badgeBonus
            + $tierBonus;

        return [
            'user_id' => $creator->id,
            'ranking_score' => round($score, 2),
            'total_views' => (int) $simStats->total_views,
            'total_plays' => (int) $simStats->total_plays,
            'avg_rating' => round((float) $simStats->avg_rating, 2),
            'follower_count' => $followerCount,
            'simulation_count' => (int) $simStats->simulation_count,
            'badge_count' => $badgeCount,
            'tier_bonus' => $tierBonus,
        ];
    }

    /**
     * Update ranking score for a single creator.
     */
    public function updateRanking(User $creator): CreatorReputation
    {
        $rankData = $this->calculateRank($creator);

        return CreatorReputation::updateOrCreate(
            ['user_id' => $creator->id],
            ['ranking_score' => $rankData['ranking_score']]
        );
    }

    /**
     * Update rankings for all creators in batch.
     */
    public function updateAllRankings(): int
    {
        $creators = User::where('role', 'creator')
            ->with('reputation')
            ->get();

        $updated = 0;

        foreach ($creators as $creator) {
            $this->updateRanking($creator);
            $updated++;
        }

        return $updated;
    }

    /**
     * Get top creators with configurable sort.
     *
     * @param  string  $sortBy  ranking|followers|simulations|rating
     * @param  string  $period  all|month|week
     */
    public function getTopCreators(int $limit = 10, string $sortBy = 'ranking', string $period = 'all'): Collection
    {
        $query = User::where('role', 'creator')
            ->whereHas('reputation')
            ->with(['reputation', 'badges'])
            ->withCount([
                'simulations as published_count' => fn ($q) => $q->published(),
                'followers',
            ])
            ->leftJoin('creator_reputations', 'users.id', '=', 'creator_reputations.user_id');

        // Period filter — only include creators active in the given period
        if ($period !== 'all') {
            $since = match ($period) {
                'week' => now()->subWeek(),
                'month' => now()->subMonth(),
                default => null,
            };

            if ($since) {
                $query->whereHas('simulations', fn ($q) => $q->published()->where('published_at', '>=', $since));
            }
        }

        // Sort — for rating, use a subquery to compute avg rating from simulations
        $query = match ($sortBy) {
            'followers' => $query->orderByDesc('followers_count'),
            'simulations' => $query->orderByDesc('published_count'),
            'rating' => $query->orderByRaw('(SELECT COALESCE(AVG(average_rating), 0) FROM simulations WHERE simulations.user_id = users.id AND simulations.is_published = 1) DESC'),
            default => $query->orderByDesc('creator_reputations.ranking_score'),
        };

        return $query->limit($limit)
            ->get()
            ->map(function (User $creator) {
                $stats = $this->calculateRank($creator);

                return [
                    'user' => $creator,
                    'ranking_score' => $creator->reputation->ranking_score ?? 0,
                    'total_views' => $stats['total_views'],
                    'total_plays' => $stats['total_plays'],
                    'avg_rating' => $stats['avg_rating'],
                    'follower_count' => $creator->followers_count,
                    'simulation_count' => $creator->published_count,
                    'badge_count' => $stats['badge_count'],
                ];
            });
    }

    /**
     * Get trending creators (active in recent period).
     */
    public function getTrendingCreators(int $limit = 5, string $period = 'week'): Collection
    {
        $since = match ($period) {
            'day' => now()->subDay(),
            'week' => now()->subWeek(),
            'month' => now()->subMonth(),
            default => now()->subWeek(),
        };

        return User::where('role', 'creator')
            ->whereHas('reputation')
            ->with(['reputation', 'badges'])
            ->withCount([
                'simulations as published_count' => fn ($q) => $q->published(),
                'followers',
            ])
            ->join('creator_reputations', 'users.id', '=', 'creator_reputations.user_id')
            ->whereHas('simulations', fn ($q) => $q->published()->where('published_at', '>=', $since))
            ->orderByDesc('creator_reputations.ranking_score')
            ->limit($limit)
            ->get()
            ->map(function (User $creator) {
                return [
                    'user' => $creator,
                    'ranking_score' => $creator->reputation->ranking_score,
                    'follower_count' => $creator->followers_count,
                    'simulation_count' => $creator->published_count,
                ];
            });
    }
}
