<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CreatorCertification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'level',
        'status',
        'criteria_met',
        'awarded_at',
        'expires_at',
        'reviewed_by',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'criteria_met' => 'array',
            'awarded_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // ─── Helpers ──────────────────────────────────────────────────

    public function getLevelLabelAttribute(): string
    {
        return match ($this->level) {
            'verified' => 'Verified Creator',
            'expert' => 'Expert Creator',
            'platinum' => 'Platinum Creator',
            default => $this->level,
        };
    }

    public function getLevelIconAttribute(): string
    {
        return match ($this->level) {
            'verified' => '✅',
            'expert' => '👑',
            'platinum' => '⭐',
            default => '🏅',
        };
    }

    public function getLevelBadgeClassAttribute(): string
    {
        return match ($this->level) {
            'verified' => 'bg-blue-100 text-blue-700',
            'expert' => 'bg-amber-100 text-amber-700',
            'platinum' => 'bg-violet-100 text-violet-700',
            default => 'bg-slate-100 text-slate-700',
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'active' => 'bg-emerald-100 text-emerald-700',
            'expired' => 'bg-amber-100 text-amber-700',
            'revoked' => 'bg-red-100 text-red-700',
            default => 'bg-slate-100 text-slate-700',
        };
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if a creator meets the criteria for a given certification level.
     */
    public static function checkCriteria(User $user, string $level): array
    {
        $simulations = $user->simulations()->published()->count();
        $avgRating = $user->simulations()->published()->avg('average_rating') ?? 0;
        $totalPlays = (int) $user->simulations()->published()->sum('play_count');
        $monthsActive = $user->created_at->diffInMonths(now());

        $requirements = match ($level) {
            'verified' => ['simulations' => 10, 'rating' => 4.0, 'plays' => 1000, 'months' => 0],
            'expert' => ['simulations' => 50, 'rating' => 4.5, 'plays' => 10000, 'months' => 6],
            'platinum' => ['simulations' => 100, 'rating' => 4.7, 'plays' => 100000, 'months' => 12],
            default => ['simulations' => 0, 'rating' => 0, 'plays' => 0, 'months' => 0],
        };

        $met = [
            'simulations_count' => $simulations,
            'avg_rating' => round((float) $avgRating, 2),
            'total_plays' => $totalPlays,
            'months_active' => $monthsActive,
        ];

        $eligible = $simulations >= $requirements['simulations']
            && $avgRating >= $requirements['rating']
            && $totalPlays >= $requirements['plays']
            && $monthsActive >= $requirements['months'];

        return [
            'eligible' => $eligible,
            'current' => $met,
            'requirements' => $requirements,
        ];
    }
}
