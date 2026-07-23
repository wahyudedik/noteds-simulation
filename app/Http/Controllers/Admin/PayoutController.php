<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payout;
use App\Services\PayoutService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PayoutController extends Controller
{
    public function __construct(
        private PayoutService $payoutService,
    ) {}

    /**
     * Display a listing of payout requests.
     */
    public function index(Request $request): View
    {
        $query = Payout::with('user', 'reviewer');

        $status = $request->input('status', 'pending');
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $payouts = $query->latest()->paginate(15)->withQueryString();
        $stats = $this->payoutService->getStats();

        return view('admin.payouts.index', compact('payouts', 'status', 'stats'));
    }

    /**
     * Display the specified payout.
     */
    public function show(Payout $payout): View
    {
        $payout->load('user', 'reviewer');

        return view('admin.payouts.show', compact('payout'));
    }

    /**
     * Approve a payout request.
     */
    public function approve(Request $request, Payout $payout): RedirectResponse
    {
        $validated = $request->validate([
            'review_notes' => 'nullable|string|max:500',
        ]);

        $this->payoutService->approve($payout, $validated['review_notes'] ?? 'Disetujui oleh admin.');

        return redirect()->route('admin.payouts.show', $payout)
            ->with('success', 'Payout berhasil disetujui.');
    }

    /**
     * Mark a payout as paid.
     */
    public function markPaid(Request $request, Payout $payout): RedirectResponse
    {
        $validated = $request->validate([
            'review_notes' => 'nullable|string|max:500',
            'proof' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $proofPath = null;
        if ($request->hasFile('proof')) {
            $proofPath = $request->file('proof')->store('payout-proofs', 'public');
        }

        $this->payoutService->markAsPaid($payout, $proofPath, $validated['review_notes'] ?? 'Pembayaran selesai.');

        return redirect()->route('admin.payouts.show', $payout)
            ->with('success', 'Payout berhasil ditandai sebagai sudah dibayar.');
    }

    /**
     * Reject a payout request.
     */
    public function reject(Request $request, Payout $payout): RedirectResponse
    {
        $validated = $request->validate([
            'review_notes' => 'nullable|string|max:500',
        ]);

        $this->payoutService->reject($payout, $validated['review_notes'] ?? 'Ditolak oleh admin.');

        return redirect()->route('admin.payouts.show', $payout)
            ->with('success', 'Payout berhasil ditolak.');
    }
}
