<?php

namespace App\Services;

use App\Models\AdImpression;
use App\Models\PlatformAd;
use App\Models\Sponsor;
use App\Models\Sponsorship;
use App\Models\SponsorshipInvoice;
use App\Models\User;
use Illuminate\Support\Collection;

class SponsorshipService
{
    // ─── Sponsorship CRUD ─────────────────────────────────────────

    /**
     * Create a new sponsorship.
     */
    public function create(Sponsor $sponsor, array $data, User $createdBy): Sponsorship
    {
        return Sponsorship::create([
            'sponsor_id' => $sponsor->id,
            'title' => $data['title'],
            'package_type' => $data['package_type'] ?? 'basic',
            'status' => 'draft',
            'budget' => $data['budget'] ?? 0,
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'positions' => $data['positions'] ?? [],
            'category_filter' => $data['category_filter'] ?? null,
            'target_impressions' => $data['target_impressions'] ?? null,
            'notes' => $data['notes'] ?? null,
            'created_by' => $createdBy->id,
        ]);
    }

    /**
     * Update a sponsorship.
     */
    public function update(Sponsorship $sponsorship, array $data): Sponsorship
    {
        $sponsorship->update($data);

        return $sponsorship->fresh();
    }

    /**
     * Approve a sponsorship.
     */
    public function approve(Sponsorship $sponsorship, User $admin): Sponsorship
    {
        $sponsorship->update([
            'status' => 'active',
            'approved_by' => $admin->id,
            'approved_at' => now(),
        ]);

        return $sponsorship->fresh();
    }

    /**
     * Pause a sponsorship.
     */
    public function pause(Sponsorship $sponsorship): Sponsorship
    {
        $sponsorship->update(['status' => 'paused']);

        return $sponsorship->fresh();
    }

    /**
     * Resume a paused sponsorship.
     */
    public function resume(Sponsorship $sponsorship): Sponsorship
    {
        $sponsorship->update(['status' => 'active']);

        return $sponsorship->fresh();
    }

    /**
     * Mark a sponsorship as completed.
     */
    public function complete(Sponsorship $sponsorship): Sponsorship
    {
        $sponsorship->update(['status' => 'completed']);

        // Deactivate all linked ads
        $sponsorship->platformAds()->update(['is_active' => false]);

        return $sponsorship->fresh();
    }

    /**
     * Cancel a sponsorship.
     */
    public function cancel(Sponsorship $sponsorship): Sponsorship
    {
        $sponsorship->update(['status' => 'cancelled']);

        // Deactivate all linked ads
        $sponsorship->platformAds()->update(['is_active' => false]);

        return $sponsorship->fresh();
    }

    // ─── Ad Management ────────────────────────────────────────────

    /**
     * Create an ad linked to a sponsorship.
     */
    public function createAd(Sponsorship $sponsorship, array $data, User $createdBy): PlatformAd
    {
        $ad = PlatformAd::create([
            'title' => $data['title'],
            'type' => $data['type'] ?? 'banner',
            'position' => $data['position'],
            'content' => $data['content'] ?? null,
            'image_path' => $data['image_path'] ?? null,
            'video_path' => $data['video_path'] ?? null,
            'target_url' => $data['target_url'] ?? null,
            'category_filter' => $data['category_filter'] ?? null,
            'weight' => $data['weight'] ?? 1,
            'is_active' => true,
            'start_date' => $sponsorship->start_date,
            'end_date' => $sponsorship->end_date,
            'created_by' => $createdBy->id,
            'sponsor_id' => $sponsorship->sponsor_id,
            'sponsorship_id' => $sponsorship->id,
            'is_sponsored' => true,
            'sponsored_label' => $data['sponsored_label'] ?? 'Sponsored',
        ]);

        return $ad;
    }

    /**
     * Link an existing ad to a sponsorship.
     */
    public function linkAdToSponsorship(PlatformAd $ad, Sponsorship $sponsorship): PlatformAd
    {
        $ad->update([
            'sponsor_id' => $sponsorship->sponsor_id,
            'sponsorship_id' => $sponsorship->id,
            'is_sponsored' => true,
        ]);

        return $ad->fresh();
    }

    // ─── Performance Tracking ─────────────────────────────────────

    /**
     * Get stats for a specific sponsorship.
     */
    public function getSponsorshipStats(Sponsorship $sponsorship): array
    {
        $impressions = (int) $sponsorship->platformAds()->sum('impressions');
        $clicks = (int) $sponsorship->platformAds()->sum('clicks');
        $ctr = $impressions > 0 ? round(($clicks / $impressions) * 100, 2) : 0;

        return [
            'total_impressions' => $impressions,
            'total_clicks' => $clicks,
            'ctr' => $ctr,
            'budget' => (float) $sponsorship->budget,
            'spent' => (float) $sponsorship->spent,
            'remaining' => $sponsorship->remaining_budget,
            'progress' => $sponsorship->progress,
            'ads_count' => $sponsorship->platformAds()->count(),
            'active_ads_count' => $sponsorship->platformAds()->active()->count(),
        ];
    }

    /**
     * Get aggregated stats for a brand (all sponsorships).
     */
    public function getBrandStats(Sponsor $sponsor): array
    {
        $sponsorships = $sponsor->sponsorships;
        $ads = $sponsor->platformAds();

        $impressions = (int) $ads->sum('impressions');
        $clicks = (int) $ads->sum('clicks');

        return [
            'total_sponsorships' => $sponsorships->count(),
            'active_sponsorships' => $sponsorships->where('status', 'active')->count(),
            'total_budget' => $sponsorships->sum('budget'),
            'total_spent' => $sponsorships->sum('spent'),
            'total_impressions' => $impressions,
            'total_clicks' => $clicks,
            'ctr' => $impressions > 0 ? round(($clicks / $impressions) * 100, 2) : 0,
        ];
    }

    /**
     * Get daily performance for a sponsorship.
     */
    public function getDailyPerformance(Sponsorship $sponsorship, int $days = 30): Collection
    {
        $adIds = $sponsorship->platformAds()->pluck('id');

        if ($adIds->isEmpty()) {
            return collect();
        }

        return AdImpression::where('ad_type', 'platform')
            ->whereIn('ad_id', $adIds)
            ->where('created_at', '>=', now()->subDays($days))
            ->selectRaw('DATE(created_at) as date')
            ->selectRaw('count(*) as impressions')
            ->selectRaw('sum(clicked) as clicks')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    // ─── Budget Management ────────────────────────────────────────

    /**
     * Deduct budget when an impression or click is recorded.
     */
    public function deductBudget(Sponsorship $sponsorship, float $amount): void
    {
        $sponsorship->increment('spent', $amount);

        // Auto-pause if budget exceeded
        if ($sponsorship->fresh()->spent >= $sponsorship->budget && $sponsorship->budget > 0) {
            $this->pause($sponsorship);
        }
    }

    /**
     * Get remaining budget.
     */
    public function checkBudgetRemaining(Sponsorship $sponsorship): float
    {
        return max(0, (float) $sponsorship->budget - (float) $sponsorship->spent);
    }

    /**
     * Check if budget is exceeded.
     */
    public function isBudgetExceeded(Sponsorship $sponsorship): bool
    {
        return $sponsorship->budget > 0 && (float) $sponsorship->spent >= (float) $sponsorship->budget;
    }

    // ─── Auto-Management ──────────────────────────────────────────

    /**
     * Pause sponsorships that have passed their end date.
     */
    public function pauseExpiredSponsorships(): int
    {
        $expired = Sponsorship::active()
            ->where('end_date', '<', now())
            ->get();

        $count = 0;
        foreach ($expired as $sponsorship) {
            $this->complete($sponsorship);
            $count++;
        }

        return $count;
    }

    /**
     * Get active sponsorships for a specific ad position.
     */
    public function getActiveSponsorshipsForPosition(string $position): Collection
    {
        return Sponsorship::running()
            ->whereJsonContains('positions', $position)
            ->with('sponsor')
            ->get();
    }

    // ─── Dashboard Stats ──────────────────────────────────────────

    /**
     * Get overview stats for admin dashboard.
     */
    public function getDashboardStats(): array
    {
        return [
            'total_sponsors' => Sponsor::count(),
            'active_sponsors' => Sponsor::where('is_active', true)->count(),
            'total_sponsorships' => Sponsorship::count(),
            'active_sponsorships' => Sponsorship::active()->count(),
            'total_budget' => (float) Sponsorship::sum('budget'),
            'total_spent' => (float) Sponsorship::sum('spent'),
            'pending_invoices' => SponsorshipInvoice::where('status', 'sent')->count(),
            'overdue_invoices' => SponsorshipInvoice::overdue()->count(),
        ];
    }
}
