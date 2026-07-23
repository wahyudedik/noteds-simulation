<?php

namespace App\Http\Controllers;

use App\Models\CreatorPaymentSetting;
use App\Models\Payout;
use App\Models\User;
use App\Services\PayoutService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PayoutController extends Controller
{
    public function __construct(
        private PayoutService $payoutService,
    ) {}

    /**
     * Show payout dashboard for creator.
     */
    public function index(): View
    {
        /** @var User $user */
        $user = Auth::user();

        $availableBalance = $this->payoutService->getAvailableBalance($user);
        $totalPaid = $this->payoutService->getTotalPaid($user);
        $minPayout = $this->payoutService->getMinPayout();

        $payouts = Payout::where('user_id', $user->id)
            ->latest()
            ->paginate(10);

        $paymentSettings = CreatorPaymentSetting::where('user_id', $user->id)->first();

        return view('studio.payouts', compact('availableBalance', 'totalPaid', 'minPayout', 'payouts', 'paymentSettings'));
    }

    /**
     * Show payment settings form.
     */
    public function paymentSettings(): View
    {
        /** @var User $user */
        $user = Auth::user();

        $settings = CreatorPaymentSetting::firstOrCreate(
            ['user_id' => $user->id],
            ['preferred_method' => 'bank_transfer']
        );

        return view('studio.payment-settings', compact('settings'));
    }

    /**
     * Update payment settings.
     */
    public function updatePaymentSettings(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'preferred_method' => 'required|in:bank_transfer,paypal,midtrans',
            'bank_name' => 'nullable|string|max:100',
            'account_number' => 'nullable|string|max:100',
            'account_holder' => 'nullable|string|max:100',
            'paypal_email' => 'nullable|email|max:255',
        ]);

        /** @var User $user */
        $user = Auth::user();

        CreatorPaymentSetting::updateOrCreate(
            ['user_id' => $user->id],
            $validated
        );

        return redirect()->route('studio.payment-settings')
            ->with('success', 'Pengaturan pembayaran berhasil diupdate.');
    }

    /**
     * Request a payout.
     */
    public function requestPayout(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'amount' => 'required|numeric|min:500000',
        ]);

        $payout = $this->payoutService->requestPayout($user, [
            'amount' => $validated['amount'],
        ]);

        if (! $payout) {
            return back()->with('error', 'Saldo tidak mencukupi untuk payout. Minimum Rp 500.000.');
        }

        return redirect()->route('studio.payouts')
            ->with('success', 'Permintaan payout berhasil diajukan. Menunggu review admin.');
    }
}
