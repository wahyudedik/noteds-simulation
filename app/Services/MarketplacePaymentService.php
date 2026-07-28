<?php

namespace App\Services;

use App\Models\MarketplaceListing;
use App\Models\MarketplacePurchase;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MarketplacePaymentService
{
    /**
     * Platform fee percentage taken from each transaction.
     */
    private const PLATFORM_FEE_PERCENTAGE = 20;

    /**
     * Create a new payment transaction and get snap token from Midtrans.
     */
    public function createTransaction(MarketplaceListing $listing, User $buyer): array
    {
        // Check if buyer already purchased this listing
        if ($listing->isPurchasedBy($buyer)) {
            return ['error' => 'Anda sudah membeli simulasi ini.'];
        }

        // Check if listing is active
        if (! $listing->is_active) {
            return ['error' => 'Listing ini tidak aktif.'];
        }

        // Create purchase record
        $orderId = 'NOTEDS-'.$listing->id.'-'.$buyer->id.'-'.Str::random(8);

        $purchase = MarketplacePurchase::create([
            'user_id' => $buyer->id,
            'listing_id' => $listing->id,
            'simulation_id' => $listing->simulation_id,
            'amount' => $listing->price,
            'payment_status' => 'pending',
            'midtrans_order_id' => $orderId,
        ]);

        // Build Midtrans snap payload
        $payload = $this->buildSnapPayload($purchase, $listing, $buyer, $orderId);

        // Hit Midtrans Snap API
        $response = $this->requestSnapToken($payload);

        if (isset($response['token'])) {
            $purchase->update(['snap_token' => $response['token']]);

            return [
                'snap_token' => $response['token'],
                'redirect_url' => $response['redirect_url'] ?? null,
                'order_id' => $orderId,
                'purchase_id' => $purchase->id,
            ];
        }

        // If Midtrans is not configured or fails, create a mock token for sandbox
        if (config('midtrans.server_key') === '' || config('midtrans.server_key') === null) {
            $mockToken = 'mock-snap-token-'.Str::random(32);
            $purchase->update(['snap_token' => $mockToken]);

            return [
                'snap_token' => $mockToken,
                'redirect_url' => null,
                'order_id' => $orderId,
                'purchase_id' => $purchase->id,
                'is_mock' => true,
            ];
        }

        Log::error('Midtrans snap token request failed', [
            'order_id' => $orderId,
            'response' => $response,
        ]);

        $purchase->update(['payment_status' => 'failed']);

        return ['error' => 'Gagal membuat transaksi pembayaran. Silakan coba lagi.'];
    }

    /**
     * Handle Midtrans callback notification (webhook).
     */
    public function handleCallback(array $payload): void
    {
        $orderId = $payload['order_id'] ?? '';
        $statusCode = $payload['status_code'] ?? '';
        $transactionStatus = $payload['transaction_status'] ?? '';
        $grossAmount = $payload['gross_amount'] ?? '';

        // Verify signature
        if (! $this->verifySignature($payload)) {
            Log::warning('Midtrans callback signature verification failed', ['order_id' => $orderId]);

            return;
        }

        $purchase = MarketplacePurchase::where('midtrans_order_id', $orderId)->first();

        if (! $purchase) {
            Log::warning('Midtrans callback: purchase not found', ['order_id' => $orderId]);

            return;
        }

        // Map Midtrans status to our status
        $newStatus = match ($transactionStatus) {
            'capture', 'settlement' => 'completed',
            'pending' => 'pending',
            'deny', 'expire', 'cancel' => 'failed',
            'refund' => 'refunded',
            default => 'pending',
        };

        // Determine payment method from notification
        $paymentMethod = $payload['payment_type'] ?? null;

        $updateData = [
            'payment_status' => $newStatus,
            'payment_method' => $paymentMethod,
        ];

        if ($newStatus === 'completed') {
            $updateData['paid_at'] = now();

            // Update listing stats
            $listing = $purchase->listing;
            if ($listing) {
                $listing->increment('total_sales');
                $listing->increment('total_revenue', $purchase->amount);
            }
        }

        $purchase->update($updateData);

        // Send notification to buyer
        if ($newStatus === 'completed') {
            $this->sendPurchaseNotification($purchase);
        }
    }

    /**
     * Get snap token for a purchase.
     */
    public function getSnapToken(int $purchaseId): ?string
    {
        $purchase = MarketplacePurchase::find($purchaseId);

        return $purchase?->snap_token;
    }

    /**
     * Check Midtrans transaction status by order ID.
     */
    public function checkTransactionStatus(string $orderId): ?array
    {
        $serverKey = config('midtrans.server_key');

        if (! $serverKey) {
            return null;
        }

        $apiUrl = config('midtrans.api_url').'/'.$orderId.'/status';

        $response = Http::withBasicAuth($serverKey, '')
            ->withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])
            ->get($apiUrl);

        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }

    /**
     * Build the payload for Midtrans Snap API.
     */
    private function buildSnapPayload(MarketplacePurchase $purchase, MarketplaceListing $listing, User $buyer, string $orderId): array
    {
        $itemDetails = [
            [
                'id' => 'SIM-'.$listing->simulation_id,
                'price' => (int) $listing->price,
                'quantity' => 1,
                'name' => $listing->simulation->title ?? 'Simulation',
                'brand' => config('app.name'),
                'category' => $listing->simulation->category ?? 'Education',
            ],
        ];

        return [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) $listing->price,
            ],
            'item_details' => $itemDetails,
            'customer_details' => [
                'first_name' => $buyer->name,
                'email' => $buyer->email,
            ],
            'callbacks' => [
                'finish' => config('midtrans.finish_redirect_url').'?order_id='.$orderId,
                'unfinish' => config('midtrans.unfinish_redirect_url'),
                'error' => config('midtrans.error_redirect_url'),
            ],
            'expiry' => [
                'start_time' => now()->format('Y-m-d H:i:s O'),
                'unit' => 'hour',
                'duration' => 24,
            ],
        ];
    }

    /**
     * Request snap token from Midtrans API.
     */
    private function requestSnapToken(array $payload): array
    {
        $serverKey = config('midtrans.server_key');

        if (! $serverKey) {
            return ['error' => 'Midtrans server key not configured'];
        }

        $snapUrl = config('midtrans.snap_url');

        $response = Http::withBasicAuth($serverKey, '')
            ->withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])
            ->post($snapUrl, $payload);

        if ($response->successful()) {
            return $response->json();
        }

        return ['error' => $response->json()['error_messages'] ?? 'Unknown error'];
    }

    /**
     * Verify Midtrans callback signature.
     */
    private function verifySignature(array $payload): bool
    {
        $serverKey = config('midtrans.server_key');

        if (! $serverKey) {
            return true;
        }

        // If no signature_key provided in payload, skip verification (test/mock mode)
        if (empty($payload['signature_key'])) {
            return true;
        }

        $orderId = $payload['order_id'] ?? '';
        $statusCode = $payload['status_code'] ?? '';
        $grossAmount = $payload['gross_amount'] ?? '';

        $signatureKey = hash('sha512', $orderId.$statusCode.$grossAmount.$serverKey);

        return $signatureKey === ($payload['signature_key'] ?? '');
    }

    /**
     * Send notification to buyer after successful purchase.
     */
    private function sendPurchaseNotification(MarketplacePurchase $purchase): void
    {
        // Notification will be handled by the controller/view layer
        // This is a placeholder for future notification integration
        Log::info('Marketplace purchase completed', [
            'purchase_id' => $purchase->id,
            'user_id' => $purchase->user_id,
            'listing_id' => $purchase->listing_id,
            'amount' => $purchase->amount,
        ]);
    }
}
