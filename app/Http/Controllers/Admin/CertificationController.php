<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CreatorCertification;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CertificationController extends Controller
{
    /**
     * Display all certifications with eligibility checks.
     */
    public function index(Request $request): View
    {
        $query = CreatorCertification::with(['user', 'reviewer']);

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('level')) {
            $query->where('level', $request->input('level'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('user', fn ($q) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%"));
        }

        $certifications = $query->latest()->paginate(15)->withQueryString();

        // Check eligible creators who don't have certification yet
        $eligibleCreators = $this->getEligibleCreators();

        $totalCerts = CreatorCertification::count();
        $activeCerts = CreatorCertification::where('status', 'active')->count();

        return view('admin.certifications.index', compact(
            'certifications',
            'eligibleCreators',
            'totalCerts',
            'activeCerts'
        ));
    }

    /**
     * Award certification to a creator.
     */
    public function award(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'level' => 'required|in:verified,expert,platinum',
            'notes' => 'nullable|string',
        ]);

        $user = User::findOrFail($validated['user_id']);

        // Check criteria
        $criteria = CreatorCertification::checkCriteria($user, $validated['level']);

        if (! $criteria['eligible']) {
            return back()->withErrors(['error' => 'Kreator belum memenuhi kriteria sertifikasi.']);
        }

        // Check if already has this level
        $existing = CreatorCertification::where('user_id', $user->id)
            ->where('level', $validated['level'])
            ->where('status', 'active')
            ->exists();

        if ($existing) {
            return back()->withErrors(['error' => 'Kreator sudah memiliki sertifikasi ini.']);
        }

        CreatorCertification::create([
            'user_id' => $user->id,
            'level' => $validated['level'],
            'status' => 'active',
            'criteria_met' => $criteria['current'],
            'awarded_at' => now(),
            'expires_at' => match ($validated['level']) {
                'verified' => now()->addYear(),
                'expert' => now()->addMonths(6),
                'platinum' => null,
            },
            'reviewed_by' => auth()->id(),
            'notes' => $validated['notes'] ?? null,
        ]);

        return back()->with('success', "Sertifikasi {$validated['level']} berhasil diberikan ke {$user->name}.");
    }

    /**
     * Revoke a certification.
     */
    public function revoke(CreatorCertification $certification): RedirectResponse
    {
        $certification->update([
            'status' => 'revoked',
            'reviewed_by' => auth()->id(),
        ]);

        return back()->with('success', 'Sertifikasi berhasil dicabut.');
    }

    /**
     * Get creators eligible for certification.
     */
    private function getEligibleCreators(): array
    {
        $creators = User::where('role', 'creator')
            ->withCount(['simulations as published_count' => fn ($q) => $q->published()])
            ->having('published_count', '>', 0)
            ->get();

        $eligible = [];
        foreach ($creators as $creator) {
            $hasVerified = CreatorCertification::where('user_id', $creator->id)
                ->where('level', 'verified')
                ->where('status', 'active')
                ->exists();

            $hasExpert = CreatorCertification::where('user_id', $creator->id)
                ->where('level', 'expert')
                ->where('status', 'active')
                ->exists();

            $hasPlatinum = CreatorCertification::where('user_id', $creator->id)
                ->where('level', 'platinum')
                ->where('status', 'active')
                ->exists();

            // Check from highest to lowest
            if (! $hasPlatinum) {
                $criteria = CreatorCertification::checkCriteria($creator, 'platinum');
                if ($criteria['eligible']) {
                    $eligible[] = ['user' => $creator, 'level' => 'platinum', 'criteria' => $criteria];

                    continue;
                }
            }

            if (! $hasExpert) {
                $criteria = CreatorCertification::checkCriteria($creator, 'expert');
                if ($criteria['eligible']) {
                    $eligible[] = ['user' => $creator, 'level' => 'expert', 'criteria' => $criteria];

                    continue;
                }
            }

            if (! $hasVerified) {
                $criteria = CreatorCertification::checkCriteria($creator, 'verified');
                if ($criteria['eligible']) {
                    $eligible[] = ['user' => $creator, 'level' => 'verified', 'criteria' => $criteria];
                }
            }
        }

        return $eligible;
    }
}
