<?php

namespace App\Http\Controllers;

use App\Models\MarketplaceListing;
use App\Models\MarketplacePurchase;
use App\Services\MarketplacePaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class MarketplacePaymentController extends Controller
{
    public function __construct(
        private MarketplacePaymentService $paymentService,
    ) {}

    /**
     * Process checkout for a marketplace listing.
     */
    public function checkout(Request $request, MarketplaceListing $listing)
    {
        $user = $request->user();

        // Check if user owns this listing
        if ($listing->user_id === $user->id) {
            return redirect()->route('marketplace.show', $listing->simulation->slug)
                ->with('error', 'Anda tidak dapat membeli simulasi sendiri.');
        }

        // Check if already purchased
        if ($listing->isPurchasedBy($user)) {
            return redirect()->route('marketplace.show', $listing->simulation->slug)
                ->with('error', 'Anda sudah membeli simulasi ini.');
        }

        $result = $this->paymentService->createTransaction($listing, $user);

        if (isset($result['error'])) {
            return redirect()->route('marketplace.show', $listing->simulation->slug)
                ->with('error', $result['error']);
        }

        return view('marketplace.checkout', [
            'listing' => $listing,
            'simulation' => $listing->simulation,
            'purchase' => MarketplacePurchase::find($result['purchase_id']),
            'snap_token' => $result['snap_token'],
            'client_key' => config('midtrans.client_key'),
            'is_mock' => $result['is_mock'] ?? false,
        ]);
    }

    /**
     * Show payment success page.
     */
    public function success(Request $request): View
    {
        $orderId = $request->query('order_id');

        $purchase = MarketplacePurchase::where('midtrans_order_id', $orderId)
            ->where('user_id', $request->user()->id)
            ->with(['listing', 'simulation'])
            ->firstOrFail();

        return view('marketplace.success', [
            'purchase' => $purchase,
            'simulation' => $purchase->simulation,
        ]);
    }

    /**
     * Handle Midtrans callback (webhook).
     */
    public function callback(Request $request)
    {
        $payload = $request->all();

        Log::info('Midtrans callback received', ['order_id' => $payload['order_id'] ?? 'unknown']);

        $this->paymentService->handleCallback($payload);

        return response()->json(['status' => 'ok']);
    }

    /**
     * Show purchase history.
     */
    public function history(Request $request): View
    {
        $purchases = MarketplacePurchase::where('user_id', $request->user()->id)
            ->with(['listing', 'simulation'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('marketplace.history', [
            'purchases' => $purchases,
        ]);
    }
}
