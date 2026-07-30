<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdNetworkSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'network',
        'display_name',
        'is_enabled',
        'is_active',
        'publisher_id',
        'site_id',
        'script_tag',
        'ads_txt_entry',
        'allow_banner',
        'allow_native',
        'allow_interstitial',
        'allow_popunder',
        'allow_video',
        'ad_unit_slots',
        'estimated_rpm',
        'total_impressions',
        'total_clicks',
        'total_revenue',
        'notes',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'is_active' => 'boolean',
        'allow_banner' => 'boolean',
        'allow_native' => 'boolean',
        'allow_interstitial' => 'boolean',
        'allow_popunder' => 'boolean',
        'allow_video' => 'boolean',
        'ad_unit_slots' => 'array',
        'estimated_rpm' => 'decimal:2',
        'total_impressions' => 'integer',
        'total_clicks' => 'integer',
        'total_revenue' => 'decimal:2',
    ];

    /**
     * All supported ad networks with their display names.
     */
    public const NETWORKS = [
        'adsense' => 'Google AdSense',
        'monetag' => 'Monetag',
        'propellerads' => 'PropellerAds',
        'media_net' => 'Media.net',
        'adsterra' => 'Adsterra',
        'ezoic' => 'Ezoic',
    ];

    /**
     * Safe ad positions (no pop-under, no interstitial by default).
     */
    public const SAFE_POSITIONS = [
        'header',
        'sidebar',
        'footer',
        'in_content',
        'feed_sponsored',
    ];

    /**
     * Get all enabled networks.
     */
    public function scopeEnabled($query)
    {
        return $query->where('is_enabled', true)->where('is_active', true);
    }

    /**
     * Get a specific network setting.
     */
    public static function getForNetwork(string $network): ?self
    {
        return static::where('network', $network)->first();
    }

    /**
     * Get all enabled network script tags for <head> injection.
     */
    public static function getEnabledScriptTags(): array
    {
        return static::enabled()
            ->whereNotNull('script_tag')
            ->pluck('script_tag', 'network')
            ->toArray();
    }

    /**
     * Get all ads.txt entries for enabled networks.
     */
    public static function getAdsTxtEntries(): array
    {
        return static::enabled()
            ->whereNotNull('ads_txt_entry')
            ->pluck('ads_txt_entry', 'network')
            ->toArray();
    }

    /**
     * Get the CTR for this network.
     */
    public function getCtrAttribute(): float
    {
        if ($this->total_impressions === 0) {
            return 0.0;
        }

        return round(($this->total_clicks / $this->total_impressions) * 100, 2);
    }

    /**
     * Get ad slot/zone ID for a specific position.
     */
    public function getSlotForPosition(string $position): ?string
    {
        $slots = $this->ad_unit_slots ?? [];

        return $slots[$position] ?? null;
    }
}
