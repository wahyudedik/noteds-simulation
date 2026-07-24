<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sponsorship extends Model
{
    use HasFactory;

    protected $fillable = [
        'sponsor_id',
        'title',
        'package_type',
        'status',
        'budget',
        'spent',
        'start_date',
        'end_date',
        'positions',
        'category_filter',
        'target_impressions',
        'notes',
        'approved_by',
        'approved_at',
        'created_by',
    ];

    protected $casts = [
        'budget' => 'decimal:2',
        'spent' => 'decimal:2',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'positions' => 'array',
        'category_filter' => 'array',
        'target_impressions' => 'integer',
        'approved_at' => 'datetime',
    ];

    // ─── Relationships ────────────────────────────────────────────

    public function sponsor(): BelongsTo
    {
        return $this->belongsTo(Sponsor::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(SponsorshipInvoice::class);
    }

    public function platformAds(): HasMany
    {
        return $this->hasMany(PlatformAd::class);
    }

    // ─── Scopes ───────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeRunning($query)
    {
        return $query->where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now());
    }

    // ─── Helpers ──────────────────────────────────────────────────

    /**
     * Check if the sponsorship is currently running.
     */
    public function isCurrentlyRunning(): bool
    {
        return $this->status === 'active'
            && $this->start_date->lte(now())
            && $this->end_date->gte(now());
    }

    /**
     * Get remaining budget.
     */
    public function getRemainingBudgetAttribute(): float
    {
        return max(0, (float) $this->budget - (float) $this->spent);
    }

    /**
     * Get progress percentage (spent / budget).
     */
    public function getProgressAttribute(): float
    {
        if ($this->budget <= 0) {
            return 0;
        }

        return min(100, round(((float) $this->spent / (float) $this->budget) * 100, 1));
    }

    /**
     * Get total impressions across all linked ads.
     */
    public function getTotalImpressionsAttribute(): int
    {
        return (int) $this->platformAds()->sum('impressions');
    }

    /**
     * Get total clicks across all linked ads.
     */
    public function getTotalClicksAttribute(): int
    {
        return (int) $this->platformAds()->sum('clicks');
    }

    /**
     * Get CTR.
     */
    public function getCtrAttribute(): float
    {
        $impressions = $this->total_impressions;

        return $impressions > 0 ? round(($this->total_clicks / $impressions) * 100, 2) : 0.0;
    }

    /**
     * Get status label for display.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'Draft',
            'pending_review' => 'Menunggu Review',
            'active' => 'Aktif',
            'paused' => 'Dijeda',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            default => ucfirst($this->status),
        };
    }

    /**
     * Get status color class.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'bg-gray-100 text-gray-700',
            'pending_review' => 'bg-yellow-100 text-yellow-700',
            'active' => 'bg-green-100 text-green-700',
            'paused' => 'bg-orange-100 text-orange-700',
            'completed' => 'bg-blue-100 text-blue-700',
            'cancelled' => 'bg-red-100 text-red-700',
            default => 'bg-gray-100 text-gray-700',
        };
    }

    /**
     * Get package type label.
     */
    public function getPackageLabelAttribute(): string
    {
        return match ($this->package_type) {
            'basic' => 'Basic',
            'standard' => 'Standard',
            'premium' => 'Premium',
            'custom' => 'Custom',
            default => ucfirst($this->package_type),
        };
    }
}
