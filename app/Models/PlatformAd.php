<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlatformAd extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'type',
        'position',
        'content',
        'image_path',
        'video_path',
        'target_url',
        'adsense_publisher_id',
        'adsense_ad_slot',
        'category_filter',
        'weight',
        'is_active',
        'start_date',
        'end_date',
        'impressions',
        'clicks',
        'revenue',
        'created_by',
        'sponsor_id',
        'sponsorship_id',
        'is_sponsored',
        'sponsored_label',
    ];

    protected $casts = [
        'category_filter' => 'array',
        'is_active' => 'boolean',
        'is_sponsored' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'impressions' => 'integer',
        'clicks' => 'integer',
        'revenue' => 'decimal:2',
    ];

    // ─── Relationships ────────────────────────────────────────────

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function sponsor(): BelongsTo
    {
        return $this->belongsTo(Sponsor::class);
    }

    public function sponsorship(): BelongsTo
    {
        return $this->belongsTo(Sponsorship::class);
    }

    public function impressionsLog()
    {
        return $this->hasMany(AdImpression::class, 'ad_id')
            ->where('ad_type', 'platform');
    }

    // ─── Scopes ───────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', now());
            });
    }

    public function scopeForPosition($query, string $position)
    {
        return $query->where('position', $position);
    }

    // ─── Helpers ──────────────────────────────────────────────────

    /**
     * Get the Click-Through Rate (CTR).
     */
    public function getCtrAttribute(): float
    {
        if ($this->impressions === 0) {
            return 0.0;
        }

        return round(($this->clicks / $this->impressions) * 100, 2);
    }

    /**
     * Check if the ad is currently running.
     */
    public function isCurrentlyRunning(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->start_date && $this->start_date->isFuture()) {
            return false;
        }

        if ($this->end_date && $this->end_date->isPast()) {
            return false;
        }

        return true;
    }
}
