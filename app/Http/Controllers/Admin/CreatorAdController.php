<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CreatorAd;
use App\Services\CreatorReputationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CreatorAdController extends Controller
{
    public function __construct(
        private CreatorReputationService $reputationService,
    ) {}

    /**
     * Display a listing of creator ads pending review.
     */
    public function index(Request $request): View
    {
        $query = CreatorAd::with('simulation', 'user', 'reviewer');

        $status = $request->input('status', 'pending_review');
        if ($status !== 'all') {
            $query->where('review_status', $status);
        }

        $creatorAds = $query->latest()->paginate(15)->withQueryString();

        return view('admin.creator-ads.index', compact('creatorAds', 'status'));
    }

    /**
     * Show the detail of a creator ad.
     */
    public function show(CreatorAd $creatorAd): View
    {
        $creatorAd->load('simulation', 'user', 'reviewer');

        return view('admin.creator-ads.show', compact('creatorAd'));
    }

    /**
     * Approve a creator ad.
     */
    public function approve(CreatorAd $creatorAd): RedirectResponse
    {
        $creatorAd->update([
            'review_status' => 'approved',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return redirect()->route('admin.creator-ads.show', $creatorAd)
            ->with('success', 'Iklan creator berhasil disetujui.');
    }

    /**
     * Reject a creator ad.
     */
    public function reject(Request $request, CreatorAd $creatorAd): RedirectResponse
    {
        $validated = $request->validate([
            'review_notes' => 'nullable|string|max:1000',
        ]);

        $creatorAd->update([
            'review_status' => 'rejected',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'review_notes' => $validated['review_notes'] ?? 'Ditolak oleh admin.',
        ]);

        return redirect()->route('admin.creator-ads.show', $creatorAd)
            ->with('success', 'Iklan creator ditolak.');
    }

    /**
     * Flag a creator ad as suspicious.
     */
    public function flag(Request $request, CreatorAd $creatorAd): RedirectResponse
    {
        $validated = $request->validate([
            'review_notes' => 'nullable|string|max:1000',
        ]);

        $creatorAd->update([
            'review_status' => 'flagged',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'review_notes' => $validated['review_notes'] ?? 'Mencurigakan.',
        ]);

        // Update creator reputation
        $this->reputationService->onFlagged($creatorAd->user);

        return redirect()->route('admin.creator-ads.show', $creatorAd)
            ->with('success', 'Iklan creator ditandai mencurigakan.');
    }
}
