<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Sponsor extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name',
        'contact_name',
        'contact_email',
        'contact_phone',
        'logo_path',
        'website_url',
        'industry',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ─── Relationships ────────────────────────────────────────────

    public function sponsorships(): HasMany
    {
        return $this->hasMany(Sponsorship::class);
    }

    public function platformAds(): HasMany
    {
        return $this->hasMany(PlatformAd::class);
    }

    public function activeSponsorships(): HasMany
    {
        return $this->hasMany(Sponsorship::class)->where('status', 'active');
    }

    // ─── Helpers ──────────────────────────────────────────────────

    /**
     * Get the logo URL or null.
     */
    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo_path ? Storage::disk('public')->url($this->logo_path) : null;
    }

    /**
     * Get total spent across all sponsorships.
     */
    public function getTotalSpentAttribute(): float
    {
        return (float) $this->sponsorships()->sum('spent');
    }

    /**
     * Get total budget across all sponsorships.
     */
    public function getTotalBudgetAttribute(): float
    {
        return (float) $this->sponsorships()->sum('budget');
    }
}
