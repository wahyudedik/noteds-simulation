<?php

namespace App\Services;

use App\Models\CreatorAd;
use App\Models\CreatorPaymentSetting;
use App\Models\CreatorReputation;
use App\Models\Payout;
use App\Models\User;

class PayoutService
{
    /**
     * Minimum payout threshold in IDR.
     */
    private const MIN_PAYOUT_IDR = 500_000;

    /**
     * Minimum payout threshold in USD.
     */
    private const MIN_PAYOUT_USD = 50;

    /**
     * Check if a creator is eligible for payout.
     */
    public function isEligible(User $user): bool
    {
        $reputation = CreatorReputation::where('user_id', $user->id)->first();
        if (! $reputation) {
            return false;
        }

        // Creator must have approved ads
        $totalRevenue = CreatorAd::where('user_id', $user->id)
            ->where('review_status', 'approved')
            ->sum('revenue');

        return $totalRevenue >= self::MIN_PAYOUT_IDR;
    }

    /**
     * Get the pending balance for a creator.
     */
    public function getPendingBalance(User $user): float
    {
        return (float) CreatorAd::where('user_id', $user->id)
            ->where('review_status', 'approved')
            ->sum('revenue');
    }

    /**
     * Get the total paid amount for a creator.
     */
    public function getTotalPaid(User $user): float
    {
        return (float) Payout::where('user_id', $user->id)
            ->where('status', 'paid')
            ->sum('amount');
    }

    /**
     * Get the available balance (total revenue - total paid).
     */
    public function getAvailableBalance(User $user): float
    {
        $totalRevenue = $this->getPendingBalance($user);
        $totalPaid = $this->getTotalPaid($user);

        return max(0, $totalRevenue - $totalPaid);
    }

    /**
     * Create a payout request for a creator.
     */
    public function requestPayout(User $user, array $data): ?Payout
    {
        $available = $this->getAvailableBalance($user);

        if ($available < self::MIN_PAYOUT_IDR) {
            return null;
        }

        $paymentSetting = CreatorPaymentSetting::where('user_id', $user->id)->first();

        return Payout::create([
            'user_id' => $user->id,
            'amount' => min($available, $data['amount'] ?? $available),
            'method' => $data['method'] ?? ($paymentSetting?->preferred_method ?? 'bank_transfer'),
            'bank_name' => $data['bank_name'] ?? $paymentSetting?->bank_name,
            'account_number' => $data['account_number'] ?? $paymentSetting?->account_number,
            'account_holder' => $data['account_holder'] ?? $paymentSetting?->account_holder,
            'paypal_email' => $data['paypal_email'] ?? $paymentSetting?->paypal_email,
            'currency' => $data['currency'] ?? 'IDR',
            'status' => 'pending',
        ]);
    }

    /**
     * Approve a payout request.
     */
    public function approve(Payout $payout, ?string $notes = null): bool
    {
        if (! $payout->canBeApproved()) {
            return false;
        }

        $payout->update([
            'status' => 'approved',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'review_notes' => $notes,
        ]);

        return true;
    }

    /**
     * Mark a payout as paid.
     */
    public function markAsPaid(Payout $payout, ?string $proofPath = null, ?string $notes = null): bool
    {
        if (! $payout->canBePaid()) {
            return false;
        }

        $payout->update([
            'status' => 'paid',
            'paid_at' => now(),
            'proof_path' => $proofPath,
            'review_notes' => $notes ?? $payout->review_notes,
        ]);

        return true;
    }

    /**
     * Reject a payout request.
     */
    public function reject(Payout $payout, ?string $notes = null): bool
    {
        if (! in_array($payout->status, ['pending', 'processing'])) {
            return false;
        }

        $payout->update([
            'status' => 'rejected',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'review_notes' => $notes,
        ]);

        return true;
    }

    /**
     * Get payout statistics for admin dashboard.
     */
    public function getStats(): array
    {
        return [
            'total_pending' => Payout::pending()->sum('amount'),
            'total_paid' => Payout::paid()->sum('amount'),
            'total_approved' => Payout::approved()->sum('amount'),
            'pending_count' => Payout::pending()->count(),
            'paid_count' => Payout::paid()->count(),
            'total_creators' => CreatorReputation::count(),
        ];
    }

    /**
     * Get minimum payout threshold.
     */
    public function getMinPayout(): float
    {
        return self::MIN_PAYOUT_IDR;
    }
}
