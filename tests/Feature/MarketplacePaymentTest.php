n<?php

use App\Models\MarketplaceListing;
use App\Models\MarketplacePurchase;
use App\Models\Simulation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * Helper: create a listing with simulation for checkout tests.
 */
function createPaymentListing(array $simOverrides = [], array $listingOverrides = []): array
{
    $creator = User::factory()->create(['role' => 'creator']);
    $simulation = Simulation::create(array_merge([
        'user_id' => $creator->id,
        'slug' => 'payment-test-'.Str::random(6),
        'title' => 'Payment Test Simulation',
        'category' => 'Matematika',
        'is_published' => true,
        'zip_path' => 'simulations/test.zip',
        'version' => '1.0.0',
        'description' => 'A test simulation for payment testing.',
    ], $simOverrides));

    $listing = MarketplaceListing::create(array_merge([
        'simulation_id' => $simulation->id,
        'user_id' => $creator->id,
        'price' => 50000,
        'currency' => 'IDR',
        'license_type' => 'single',
        'is_active' => true,
        'demo_available' => false,
        'demo_limit_minutes' => 0,
        'total_sales' => 0,
        'total_revenue' => 0,
    ], $listingOverrides));

    return ['creator' => $creator, 'simulation' => $simulation, 'listing' => $listing];
}

beforeEach(function () {
    // Enable mock mode by clearing server key so service generates mock tokens
    config(['midtrans.server_key' => '']);
    config(['midtrans.client_key' => 'mock-client-key']);
});

// ─── Checkout Route Tests ───────────────────────────────────────

it('requires authentication to checkout', function () {
    $data = createPaymentListing();

    $this->post(route('marketplace.checkout', $data['listing']->id))
        ->assertRedirect(route('login'));
});

it('shows checkout page for valid listing', function () {
    $data = createPaymentListing();
    $buyer = User::factory()->create();

    $this->actingAs($buyer);
    $this->post(route('marketplace.checkout', $data['listing']->id))
        ->assertOk()
        ->assertViewIs('marketplace.checkout');
});

it('passes snap token to checkout view', function () {
    $data = createPaymentListing();
    $buyer = User::factory()->create();

    $this->actingAs($buyer);
    $this->post(route('marketplace.checkout', $data['listing']->id))
        ->assertOk()
        ->assertViewHas(['snap_token', 'client_key', 'purchase']);
});

it('creates purchase record on checkout', function () {
    $data = createPaymentListing();
    $buyer = User::factory()->create();

    $this->actingAs($buyer);
    $this->post(route('marketplace.checkout', $data['listing']->id));

    $this->assertDatabaseHas('marketplace_purchases', [
        'user_id' => $buyer->id,
        'listing_id' => $data['listing']->id,
        'payment_status' => 'pending',
    ]);
});

it('prevents creator from buying own listing', function () {
    $data = createPaymentListing();

    $this->actingAs($data['creator']);
    $this->post(route('marketplace.checkout', $data['listing']->id))
        ->assertRedirect()
        ->assertSessionHas('error', 'Anda tidak dapat membeli simulasi sendiri.');
});

it('prevents duplicate purchase', function () {
    $data = createPaymentListing();
    $buyer = User::factory()->create();

    MarketplacePurchase::create([
        'user_id' => $buyer->id,
        'listing_id' => $data['listing']->id,
        'simulation_id' => $data['simulation']->id,
        'amount' => $data['listing']->price,
        'payment_status' => 'completed',
    ]);

    $this->actingAs($buyer);
    $this->post(route('marketplace.checkout', $data['listing']->id))
        ->assertRedirect()
        ->assertSessionHas('error', 'Anda sudah membeli simulasi ini.');
});

it('prevents checkout on inactive listing', function () {
    $data = createPaymentListing([], ['is_active' => false]);
    $buyer = User::factory()->create();

    $this->actingAs($buyer);
    $this->post(route('marketplace.checkout', $data['listing']->id))
        ->assertRedirect()
        ->assertSessionHas('error', 'Listing ini tidak aktif.');
});

// ─── Success Page Tests ─────────────────────────────────────────

it('requires authentication to view success page', function () {
    $this->get(route('marketplace.success', ['order_id' => 'TEST-123']))
        ->assertRedirect(route('login'));
});

it('shows success page for valid purchase', function () {
    $data = createPaymentListing();
    $buyer = User::factory()->create();

    MarketplacePurchase::create([
        'user_id' => $buyer->id,
        'listing_id' => $data['listing']->id,
        'simulation_id' => $data['simulation']->id,
        'amount' => $data['listing']->price,
        'payment_status' => 'completed',
        'midtrans_order_id' => 'NOTEDS-TEST-SUCCESS',
    ]);

    $this->actingAs($buyer);
    $this->get(route('marketplace.success', ['order_id' => 'NOTEDS-TEST-SUCCESS']))
        ->assertOk()
        ->assertViewIs('marketplace.success')
        ->assertViewHas('purchase')
        ->assertViewHas('simulation');
});

it('returns 404 for non-existent order', function () {
    $buyer = User::factory()->create();

    $this->actingAs($buyer);
    $this->get(route('marketplace.success', ['order_id' => 'NON-EXISTENT']))
        ->assertNotFound();
});

// ─── Purchase History Tests ─────────────────────────────────────

it('requires authentication to view history', function () {
    $this->get(route('marketplace.history'))
        ->assertRedirect(route('login'));
});

it('shows purchase history page', function () {
    $buyer = User::factory()->create();

    $this->actingAs($buyer);
    $this->get(route('marketplace.history'))
        ->assertOk()
        ->assertViewIs('marketplace.history')
        ->assertViewHas('purchases');
});

it('displays purchases in history', function () {
    $data = createPaymentListing();
    $buyer = User::factory()->create();

    MarketplacePurchase::create([
        'user_id' => $buyer->id,
        'listing_id' => $data['listing']->id,
        'simulation_id' => $data['simulation']->id,
        'amount' => $data['listing']->price,
        'payment_status' => 'completed',
        'midtrans_order_id' => 'NOTEDS-HIST-001',
    ]);

    $this->actingAs($buyer);
    $this->get(route('marketplace.history'))
        ->assertOk()
        ->assertSee($data['simulation']->title);
});

it('shows empty state when no purchases', function () {
    $buyer = User::factory()->create();

    $this->actingAs($buyer);
    $this->get(route('marketplace.history'))
        ->assertOk()
        ->assertSeeHtml('Belum ada pembelian');
});

// ─── Callback Tests ─────────────────────────────────────────────

it('handles midtrans callback successfully', function () {
    $data = createPaymentListing();
    $buyer = User::factory()->create();

    $purchase = MarketplacePurchase::create([
        'user_id' => $buyer->id,
        'listing_id' => $data['listing']->id,
        'simulation_id' => $data['simulation']->id,
        'amount' => $data['listing']->price,
        'payment_status' => 'pending',
        'midtrans_order_id' => 'NOTEDS-CB-001',
    ]);

    $this->postJson(route('marketplace.callback'), [
        'order_id' => 'NOTEDS-CB-001',
        'status_code' => '200',
        'transaction_status' => 'settlement',
        'gross_amount' => $data['listing']->price,
        'payment_type' => 'bank_transfer',
        'server_key' => '',
        'signature_key' => '',
    ])->assertOk();

    $purchase->refresh();
    expect($purchase->payment_status)->toBe('completed');
    expect($purchase->paid_at)->not->toBeNull();
    expect($purchase->payment_method)->toBe('bank_transfer');
});

it('updates listing stats on successful callback', function () {
    $data = createPaymentListing();
    $buyer = User::factory()->create();

    MarketplacePurchase::create([
        'user_id' => $buyer->id,
        'listing_id' => $data['listing']->id,
        'simulation_id' => $data['simulation']->id,
        'amount' => $data['listing']->price,
        'payment_status' => 'pending',
        'midtrans_order_id' => 'NOTEDS-CB-002',
    ]);

    $this->postJson(route('marketplace.callback'), [
        'order_id' => 'NOTEDS-CB-002',
        'status_code' => '200',
        'transaction_status' => 'settlement',
        'gross_amount' => $data['listing']->price,
        'payment_type' => 'bank_transfer',
        'server_key' => '',
        'signature_key' => '',
    ]);

    $data['listing']->refresh();
    expect($data['listing']->total_sales)->toBe(1);
    expect((float) $data['listing']->total_revenue)->toBe((float) $data['listing']->price);
});

it('handles pending callback status', function () {
    $data = createPaymentListing();
    $buyer = User::factory()->create();

    $purchase = MarketplacePurchase::create([
        'user_id' => $buyer->id,
        'listing_id' => $data['listing']->id,
        'simulation_id' => $data['simulation']->id,
        'amount' => $data['listing']->price,
        'payment_status' => 'pending',
        'midtrans_order_id' => 'NOTEDS-CB-003',
    ]);

    $this->postJson(route('marketplace.callback'), [
        'order_id' => 'NOTEDS-CB-003',
        'status_code' => '202',
        'transaction_status' => 'pending',
        'gross_amount' => $data['listing']->price,
        'server_key' => '',
        'signature_key' => '',
    ])->assertOk();

    $purchase->refresh();
    expect($purchase->payment_status)->toBe('pending');
});

it('handles failed callback status', function () {
    $data = createPaymentListing();
    $buyer = User::factory()->create();

    $purchase = MarketplacePurchase::create([
        'user_id' => $buyer->id,
        'listing_id' => $data['listing']->id,
        'simulation_id' => $data['simulation']->id,
        'amount' => $data['listing']->price,
        'payment_status' => 'pending',
        'midtrans_order_id' => 'NOTEDS-CB-004',
    ]);

    $this->postJson(route('marketplace.callback'), [
        'order_id' => 'NOTEDS-CB-004',
        'status_code' => '202',
        'transaction_status' => 'deny',
        'gross_amount' => $data['listing']->price,
        'server_key' => '',
        'signature_key' => '',
    ])->assertOk();

    $purchase->refresh();
    expect($purchase->payment_status)->toBe('failed');
});

it('handles non-existent order in callback gracefully', function () {
    $this->postJson(route('marketplace.callback'), [
        'order_id' => 'NOT-EXIST-001',
        'status_code' => '200',
        'transaction_status' => 'settlement',
        'gross_amount' => 50000,
        'server_key' => '',
        'signature_key' => '',
    ])->assertOk();
});

// ─── Show Page Form Action Tests ────────────────────────────────

it('show page has checkout form action for logged-in users', function () {
    $data = createPaymentListing();
    $buyer = User::factory()->create();

    $this->actingAs($buyer);
    $this->get(route('marketplace.show', $data['simulation']->slug))
        ->assertOk()
        ->assertSee(route('marketplace.checkout', $data['listing']->id));
});

// ─── Config Tests ───────────────────────────────────────────────

it('has midtrans config loaded', function () {
    expect(config('midtrans.client_key'))->not->toBeNull();
    expect(config('midtrans.is_production'))->toBe(false);
    expect(config('midtrans.platform_fee_percentage'))->toBe(20);
});

it('midtrans config has correct snap url for sandbox', function () {
    config(['midtrans.is_production' => false]);
    expect(config('midtrans.snap_url'))->toContain('sandbox');
});

it('midtrans config has correct api url for sandbox', function () {
    config(['midtrans.is_production' => false]);
    expect(config('midtrans.api_url'))->toContain('sandbox');
});
