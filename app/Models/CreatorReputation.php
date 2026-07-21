<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CreatorReputation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'score',
        'total_uploads',
        'approved_count',
        'rejected_count',
        'flagged_count',
        'reports_received',
        'revenue_tier',
        'total_revenue',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'integer',
            'total_uploads' => 'integer',
            'approved_count' => 'integer',
            'rejected_count' => 'integer',
            'flagged_count' => 'integer',
            'reports_received' => 'integer',
            'total_revenue' => 'decimal:2',
        ];
    }

    /**
     * Get the user (creator).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the revenue share percentage based on tier.
     */
    public function getRevenueSharePercentage(): int
    {
        return match ($this->revenue_tier) {
            'basic' => 55,
            'verified' => 65,
            'expert' => 75,
            'platinum' => 85,
            default => 55,
        };
    }

    /**
     * Get tier label in Indonesian.
     */
    public function getTierLabelAttribute(): string
    {
        return match ($this->revenue_tier) {
            'basic' => 'Dasar',
            'verified' => 'Terverifikasi',
            'expert' => 'Ahli',
            'platinum' => 'Platinum',
            default => 'Dasar',
        };
    }
}
