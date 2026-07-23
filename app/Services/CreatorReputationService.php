<?php

namespace App\Services;

use App\Models\CreatorReputation;
use App\Models\Simulation;
use App\Models\User;

class CreatorReputationService
{
    /**
     * Get or create reputation for a creator.
     */
    public function getOrCreate(int $userId): CreatorReputation
    {
        return CreatorReputation::firstOrCreate(['user_id' => $userId]);
    }

    /**
     * Update reputation score when a simulation is approved.
     */
    public function onApproved(User $user): CreatorReputation
    {
        $rep = $this->getOrCreate($user->id);
        $rep->update([
            'approved_count' => $rep->approved_count + 1,
            'total_uploads' => $rep->total_uploads + 1,
            'score' => min(100, $rep->score + 2),
        ]);

        $this->updateTier($rep);

        return $rep;
    }

    /**
     * Update reputation score when a simulation is rejected (malware).
     */
    public function onRejected(User $user): CreatorReputation
    {
        $rep = $this->getOrCreate($user->id);
        $rep->update([
            'rejected_count' => $rep->rejected_count + 1,
            'total_uploads' => $rep->total_uploads + 1,
            'score' => max(0, $rep->score - 20),
        ]);

        $this->updateTier($rep);

        return $rep;
    }

    /**
     * Update reputation score when a simulation is flagged.
     */
    public function onFlagged(User $user): CreatorReputation
    {
        $rep = $this->getOrCreate($user->id);
        $rep->update([
            'flagged_count' => $rep->flagged_count + 1,
            'score' => max(0, $rep->score - 10),
        ]);

        $this->updateTier($rep);

        return $rep;
    }

    /**
     * Update reputation score when a user report is proven.
     */
    public function onReportProven(User $user): CreatorReputation
    {
        $rep = $this->getOrCreate($user->id);
        $rep->update([
            'reports_received' => $rep->reports_received + 1,
            'score' => max(0, $rep->score - 5),
        ]);

        $this->updateTier($rep);

        return $rep;
    }

    /**
     * Determine if a creator requires manual review based on reputation score.
     */
    public function requiresManualReview(User $user): bool
    {
        $rep = $this->getOrCreate($user->id);

        return $rep->score < 50;
    }

    /**
     * Determine if a creator account should be suspended.
     */
    public function isSuspended(User $user): bool
    {
        $rep = $this->getOrCreate($user->id);

        return $rep->score < 20;
    }

    /**
     * Update the revenue tier based on simulation count and average rating.
     */
    private function updateTier(CreatorReputation $rep): void
    {
        $simCount = Simulation::where('user_id', $rep->user_id)->published()->count();
        $avgRating = Simulation::where('user_id', $rep->user_id)->published()->avg('average_rating') ?? 0;

        $tier = 'basic';

        if ($simCount >= 100 && $avgRating >= 4.7) {
            $tier = 'platinum';
        } elseif ($simCount >= 50 && $avgRating >= 4.5) {
            $tier = 'expert';
        } elseif ($simCount >= 10 && $avgRating >= 4.0) {
            $tier = 'verified';
        }

        $rep->update(['revenue_tier' => $tier]);
    }
}
