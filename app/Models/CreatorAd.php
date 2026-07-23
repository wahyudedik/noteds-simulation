<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CreatorAd extends Model
{
    use HasFactory;

    protected $fillable = [
        'simulation_id',
        'user_id',
        'provider',
        'publisher_id',
        'ad_config',
        'code_snippet',
        'review_status',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
        'scan_result',
        'sandbox_result',
        'is_active',
        'impressions',
        'clicks',
        'revenue',
    ];

    protected $casts = [
        'ad_config' => 'array',
        'scan_result' => 'array',
        'sandbox_result' => 'array',
        'reviewed_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    // ─── Relationships ────────────────────────────────────────────

    public function simulation(): BelongsTo
    {
        return $this->belongsTo(Simulation::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function impressionsLog()
    {
        return $this->hasMany(AdImpression::class, 'ad_id')
            ->where('ad_type', 'creator');
    }

    // ─── Scopes ───────────────────────────────────────────────────

    public function scopePendingReview($query)
    {
        return $query->where('review_status', 'pending_review');
    }

    public function scopeApproved($query)
    {
        return $query->where('review_status', 'approved');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->approved();
    }

    // ─── Helpers ──────────────────────────────────────────────────

    /**
     * Check if the ad is approved and active.
     */
    public function isVisible(): bool
    {
        return $this->is_active && $this->review_status === 'approved';
    }
}
