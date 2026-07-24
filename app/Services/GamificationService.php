<?php

namespace App\Services;

use App\Models\Badge;
use App\Models\User;
use App\Models\UserBadge;
use App\Models\UserPointsLog;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class GamificationService
{
    /** Level thresholds: level => points required */
    private const LEVELS = [
        1 => 0,
        2 => 100,
        3 => 500,
        4 => 1500,
        5 => 5000,
        6 => 15000,
        7 => 50000,
    ];

    /** Level titles */
    private const LEVEL_TITLES = [
        1 => 'Pemula',
        2 => 'Penjelajah',
        3 => 'Investigator',
        4 => 'Peneliti',
        5 => 'Ahli',
        6 => 'Master',
        7 => 'Legenda',
    ];

    /** Points awarded per action */
    private const POINTS = [
        'play' => 10,
        'comment' => 5,
        'reaction' => 2,
        'streak_bonus' => 5,
        'upload_simulation' => 25,
        'first_upload' => 50,
        'follow_creator' => 3,
        'bookmark' => 2,
        'share' => 3,
        'forum_thread' => 10,
        'forum_reply' => 5,
        'forum_vote_given' => 1,
        'forum_best_answer' => 15,
    ];

    /**
     * Award points to a user for a specific action.
     */
    public function awardPoints(User $user, string $type, string $description = ''): UserPointsLog
    {
        $points = self::POINTS[$type] ?? 10;

        // Streak bonus
        if ($type === 'play') {
            $this->updateStreak($user);
            $streakBonus = (int) floor($user->streak_count / 7) * 5;
            if ($streakBonus > 0) {
                $points += $streakBonus;
                $description .= $description ? " (+{$streakBonus} streak bonus)" : "Streak bonus: +{$streakBonus}";
            }
        }

        return UserPointsLog::create([
            'user_id' => $user->id,
            'points' => $points,
            'type' => $type,
            'description' => $description ?: ucfirst(str_replace('_', ' ', $type)),
        ]);
    }

    /**
     * Update user streak based on daily activity.
     */
    public function updateStreak(User $user): void
    {
        $today = Carbon::today()->toDateString();
        $lastActive = $user->last_active_date?->toDateString();

        if ($lastActive === $today) {
            // Already active today, no change
            return;
        }

        $yesterday = Carbon::yesterday()->toDateString();

        if ($lastActive === $yesterday) {
            // Consecutive day — increment streak
            $user->increment('streak_count');
        } else {
            // Streak broken — reset to 1
            $user->update(['streak_count' => 1]);
        }

        $user->update(['last_active_date' => $today]);
    }

    /**
     * Get user's total points.
     */
    public function getTotalPoints(User $user): int
    {
        return (int) UserPointsLog::where('user_id', $user->id)->sum('points');
    }

    /**
     * Get user's current level number.
     */
    public function getLevel(User $user): int
    {
        $points = $this->getTotalPoints($user);
        $level = 1;

        foreach (self::LEVELS as $levelNum => $required) {
            if ($points >= $required) {
                $level = $levelNum;
            } else {
                break;
            }
        }

        return $level;
    }

    /**
     * Get level title.
     */
    public function getLevelTitle(int $level): string
    {
        return self::LEVEL_TITLES[$level] ?? 'Legenda';
    }

    /**
     * Get progress to next level (0-100%).
     */
    public function getLevelProgress(User $user): array
    {
        $points = $this->getTotalPoints($user);
        $currentLevel = $this->getLevel($user);
        $currentLevelPoints = self::LEVELS[$currentLevel] ?? 0;

        if ($currentLevel >= 7) {
            return [
                'current_level' => 7,
                'next_level' => 7,
                'current_points' => $points,
                'required_points' => $points,
                'progress' => 100,
                'title' => $this->getLevelTitle(7),
            ];
        }

        $nextLevel = $currentLevel + 1;
        $nextLevelPoints = self::LEVELS[$nextLevel];
        $rangePoints = $nextLevelPoints - $currentLevelPoints;
        $earnedInLevel = $points - $currentLevelPoints;
        $progress = $rangePoints > 0 ? min((int) round(($earnedInLevel / $rangePoints) * 100), 100) : 0;

        return [
            'current_level' => $currentLevel,
            'next_level' => $nextLevel,
            'current_points' => $points,
            'required_points' => $nextLevelPoints,
            'progress' => $progress,
            'title' => $this->getLevelTitle($currentLevel),
            'next_title' => $this->getLevelTitle($nextLevel),
        ];
    }

    /**
     * Check and award badges for a user.
     */
    public function checkBadges(User $user): array
    {
        $awarded = [];
        $badges = Badge::all();

        foreach ($badges as $badge) {
            if ($user->badges()->where('badge_id', $badge->id)->exists()) {
                continue;
            }

            if ($this->meetsCriteria($user, $badge)) {
                UserBadge::create([
                    'user_id' => $user->id,
                    'badge_id' => $badge->id,
                    'earned_at' => now(),
                ]);

                if ($badge->points_reward > 0) {
                    $this->awardPoints($user, 'badge', 'Badge earned: '.$badge->name);
                }

                $awarded[] = $badge;
            }
        }

        return $awarded;
    }

    /**
     * Check if user meets badge criteria.
     */
    private function meetsCriteria(User $user, Badge $badge): bool
    {
        $criteria = $badge->criteria;

        return match ($criteria['type'] ?? 'unknown') {
            'total_plays' => $user->playHistory()->count() >= ($criteria['value'] ?? 0),
            'total_comments' => $user->comments()->count() >= ($criteria['value'] ?? 0),
            'total_simulations' => $user->simulations()->count() >= ($criteria['value'] ?? 0),
            'total_bookmarks' => $user->bookmarks()->count() >= ($criteria['value'] ?? 0),
            'total_followers' => $user->followers()->count() >= ($criteria['value'] ?? 0),
            'streak' => $user->streak_count >= ($criteria['value'] ?? 0),
            'total_points' => $this->getTotalPoints($user) >= ($criteria['value'] ?? 0),
            'level' => $this->getLevel($user) >= ($criteria['value'] ?? 0),
            default => false,
        };
    }

    /**
     * Get leaderboard (top users by points).
     */
    public function getLeaderboard(string $period = 'all', int $limit = 20): Collection
    {
        $query = UserPointsLog::select('user_id', DB::raw('SUM(points) as total_points'))
            ->groupBy('user_id')
            ->orderByDesc('total_points')
            ->limit($limit);

        if ($period === 'week') {
            $query->where('created_at', '>=', now()->subWeek());
        } elseif ($period === 'month') {
            $query->where('created_at', '>=', now()->subMonth());
        }

        $topUsers = $query->get();

        return $topUsers->map(function ($entry) {
            $user = User::find($entry->user_id);
            if (! $user) {
                return null;
            }

            return [
                'user' => $user,
                'points' => (int) $entry->total_points,
                'level' => $this->getLevel($user),
                'level_title' => $this->getLevelTitle($this->getLevel($user)),
                'streak' => $user->streak_count,
            ];
        })->filter();
    }

    /**
     * Get default badges seed data.
     */
    public static function getDefaultBadges(): array
    {
        return [
            [
                'name' => 'Pemula Aktif',
                'description' => 'Memainkan simulasi pertama kali',
                'icon' => '(rocket)',
                'category' => 'achievement',
                'criteria' => ['type' => 'total_plays', 'value' => 1],
                'points_reward' => 10,
            ],
            [
                'name' => 'Komentator',
                'description' => 'Menulis 10 komentar',
                'icon' => '(talk)',
                'category' => 'social',
                'criteria' => ['type' => 'total_comments', 'value' => 10],
                'points_reward' => 25,
            ],
            [
                'name' => 'Pencinta Simulasi',
                'description' => 'Memainkan 25 simulasi berbeda',
                'icon' => '<3',
                'category' => 'achievement',
                'criteria' => ['type' => 'total_plays', 'value' => 25],
                'points_reward' => 50,
            ],
            [
                'name' => 'Kreator Pemula',
                'description' => 'Mengupload simulasi pertama',
                'icon' => '(art)',
                'category' => 'creator',
                'criteria' => ['type' => 'total_simulations', 'value' => 1],
                'points_reward' => 50,
            ],
            [
                'name' => 'Kreator Aktif',
                'description' => 'Mengupload 5 simulasi',
                'icon' => '(trophy)',
                'category' => 'creator',
                'criteria' => ['type' => 'total_simulations', 'value' => 5],
                'points_reward' => 100,
            ],
            [
                'name' => 'Penjelajah Setia',
                'description' => 'Streak 7 hari berturut-turut',
                'icon' => '(fire)',
                'category' => 'streak',
                'criteria' => ['type' => 'streak', 'value' => 7],
                'points_reward' => 75,
            ],
            [
                'name' => 'Master Streak',
                'description' => 'Streak 30 hari berturut-turut',
                'icon' => '(lightning)',
                'category' => 'streak',
                'criteria' => ['type' => 'streak', 'value' => 30],
                'points_reward' => 200,
            ],
            [
                'name' => 'Populer',
                'description' => 'Mencapai 10 followers',
                'icon' => '(people)',
                'category' => 'social',
                'criteria' => ['type' => 'total_followers', 'value' => 10],
                'points_reward' => 50,
            ],
            [
                'name' => 'Super Populer',
                'description' => 'Mencapai 100 followers',
                'icon' => '(star)',
                'category' => 'social',
                'criteria' => ['type' => 'total_followers', 'value' => 100],
                'points_reward' => 200,
            ],
            [
                'name' => 'Kolektor',
                'description' => 'Mencapai 500 poin total',
                'icon' => '(diamond)',
                'category' => 'milestone',
                'criteria' => ['type' => 'total_points', 'value' => 500],
                'points_reward' => 0,
            ],
            [
                'name' => 'Legend',
                'description' => 'Mencapai Level 7',
                'icon' => '(crown)',
                'category' => 'milestone',
                'criteria' => ['type' => 'level', 'value' => 7],
                'points_reward' => 500,
            ],
        ];
    }
}
