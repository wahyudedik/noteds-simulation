<?php

namespace App\Services;

use App\Models\AffiliateConversion;
use App\Models\AffiliateLink;
use App\Models\Simulation;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class AffiliateService
{
    private const COMMISSION_RATE = 0.10; // 10%

    /**
     * Generate an affiliate link for a creator's simulation.
     */
    public function generateLink(User $creator, Simulation $simulation): AffiliateLink
    {
        return AffiliateLink::updateOrCreate(
            ['user_id' => $creator->id, 'simulation_id' => $simulation->id],
            ['code' => $this->generateUniqueCode()]
        );
    }

    /**
     * Track a click on an affiliate link.
     */
    public function trackClick(string $code): ?AffiliateLink
    {
        $link = AffiliateLink::where('code', $code)->first();

        if (! $link) {
            return null;
        }

        $link->increment('clicks');

        return $link;
    }

    /**
     * Track a conversion (purchase) from an affiliate link.
     */
    public function trackConversion(string $code, User $buyer, float $amount): ?AffiliateConversion
    {
        $link = AffiliateLink::where('code', $code)->first();

        if (! $link) {
            return null;
        }

        // Don't allow self-referral
        if ($link->user_id === $buyer->id) {
            return null;
        }

        $commission = round($amount * self::COMMISSION_RATE, 2);

        $link->increment('conversions');
        $link->increment('clicks', 0); // ensure clicks column exists

        return AffiliateConversion::create([
            'affiliate_link_id' => $link->id,
            'buyer_user_id' => $buyer->id,
            'amount' => $amount,
            'commission' => $commission,
        ]);
    }

    /**
     * Get the commission rate.
     */
    public function getCommissionRate(): float
    {
        return self::COMMISSION_RATE;
    }

    /**
     * Get all affiliate links for a creator.
     */
    public function getCreatorLinks(User $creator): Collection
    {
        return AffiliateLink::where('user_id', $creator->id)
            ->with(['simulation', 'affiliateConversions'])
            ->withCount('affiliateConversions')
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Get stats for a creator's affiliate links.
     */
    public function getCreatorStats(User $creator): array
    {
        $links = $this->getCreatorLinks($creator);

        return [
            'total_links' => $links->count(),
            'total_clicks' => $links->sum('clicks'),
            'total_conversions' => $links->sum('conversions'),
            'total_commission' => (float) AffiliateConversion::whereHas('affiliateLink', fn ($q) => $q->where('user_id', $creator->id))->sum('commission'),
        ];
    }

    /**
     * Find affiliate link by code.
     */
    public function findByCode(string $code): ?AffiliateLink
    {
        return AffiliateLink::where('code', $code)->first();
    }

    /**
     * Generate a unique affiliate code.
     */
    private function generateUniqueCode(): string
    {
        do {
            $code = strtolower(Str::random(8));
        } while (AffiliateLink::where('code', $code)->exists());

        return $code;
    }
}
