<?php

use App\Models\MarketplaceListing;
use App\Models\MarketplacePurchase;
use App\Models\Simulation;
use App\Models\User;
use App\Services\PayoutService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    config(['midtrans.server_key' => '']);
    config(['midtrans.platform_fee_percentage' => 20]);
});

/**
 * Helper: create creator with marketplace sales.
 */
function createPayoutSetup(float $saleAmount = 100000): array
{
    $creator = User::factory()->create(['role' => 'creator']);
    $simulation = Simulation::create([
        'user_id' => $creator->id,
        'slug' => 'payout-test-'.Str::random(6),
        'title' => 'Payout Test Simulation',
        'category' => 'Matematika',
        'is_published' => true,
        'zip_path' => 'simulations/test.zip',
        'version' => '1.0.0',
        'description' => 'A test simulation for payout testing.',
    ]);

    $listing = MarketplaceListing::create([
        'simulation_id' => $simulation->id,
        'user_id' => $creator->id,
        'price' => $saleAmount,
        'currency' => 'IDR',
        'license_type' => 'single',
        'is_active' => true,
        'demo_available' => false,
        'demo_limit_minutes' => 0,
        'total_sales' => 1,
        'total_revenue' => $saleAmount,
    ]);

    $buyer = User::factory()->create();
    MarketplacePurchase::create([
        'user_id' => $buyer->id,
        'listing_id' => $listing->id,
        'simulation_id' => $simulation->id,
        'amount' => $saleAmount,
        'currency' => 'IDR',
        'payment_status' => 'completed',
        'snap_token' => 'mock-token',
        'midtrans_order_id' => 'MOCK-ORDER-001',
    ]);

    return ['creator' => $creator, 'simulation' => $simulation, 'listing' => $listing, 'buyer' => $buyer];
}

// ─── PayoutService Marketplace Earnings Tests ───────────────

it('calculates marketplace earnings after platform fee', function () {
    $data = createPayoutSetup(100000);
    $service = app(PayoutService::class);

    $earnings = $service->getMarketplaceEarnings($data['creator']);

    // 100000 * 80% = 80000
    expect($earnings)->toBe(80000.0);
});

it('calculates earnings breakdown correctly', function () {
    $data = createPayoutSetup(200000);
    $service = app(PayoutService::class);

    $breakdown = $service->getEarningsBreakdown($data['creator']);

    expect($breakdown['marketplace_gross_sales'])->toBe(200000.0);
    expect($breakdown['marketplace_platform_fee'])->toBe(40000.0); // 20%
    expect($breakdown['marketplace_earnings'])->toBe(160000.0); // 80%
    expect($breakdown['total_net'])->toBe(160000.0);
});

it('does not count pending purchases in earnings', function () {
    $data = createPayoutSetup(100000);
    $service = app(PayoutService::class);

    // Add a pending purchase
    MarketplacePurchase::create([
        'user_id' => $data['buyer']->id,
        'listing_id' => $data['listing']->id,
        'simulation_id' => $data['simulation']->id,
        'amount' => 50000,
        'currency' => 'IDR',
        'payment_status' => 'pending',
        'snap_token' => 'mock-token-2',
        'midtrans_order_id' => 'MOCK-ORDER-002',
    ]);

    $earnings = $service->getMarketplaceEarnings($data['creator']);

    // Only the completed purchase counts: 100000 * 80% = 80000
    expect($earnings)->toBe(80000.0);
});

it('includes marketplace earnings in pending balance', function () {
    $data = createPayoutSetup(100000);
    $service = app(PayoutService::class);

    $balance = $service->getPendingBalance($data['creator']);

    // Only marketplace earnings (no ad revenue): 100000 * 80% = 80000
    expect($balance)->toBe(80000.0);
});

it('includes marketplace earnings in available balance', function () {
    $data = createPayoutSetup(100000);
    $service = app(PayoutService::class);

    $available = $service->getAvailableBalance($data['creator']);

    expect($available)->toBe(80000.0);
});

// ─── Payout Page Tests ──────────────────────────────────────

it('requires authentication to view payout page', function () {
    $this->get(route('studio.payouts'))
        ->assertRedirect(route('login'));
});

it('shows payout page with earnings breakdown', function () {
    $data = createPayoutSetup(100000);

    $this->actingAs($data['creator'])
        ->get(route('studio.payouts'))
        ->assertOk()
        ->assertSee('Rincian Pendapatan')
        ->assertSee('Pendapatan Marketplace')
        ->assertSee('Pendapatan Iklan')
        ->assertSee('Total Pendapatan Bersih')
        ->assertSee('80.000'); // 100000 * 80% = 80000
});

it('shows platform fee info when there are sales', function () {
    $data = createPayoutSetup(100000);

    $this->actingAs($data['creator'])
        ->get(route('studio.payouts'))
        ->assertOk()
        ->assertSee('Penjualan kotor')
        ->assertSee('Platform fee')
        ->assertSee('100.000'); // gross sales
});

// ─── Platform Fee Config Tests ──────────────────────────────

it('respects different platform fee percentages', function () {
    config(['midtrans.platform_fee_percentage' => 30]);
    $data = createPayoutSetup(100000);
    $service = app(PayoutService::class);

    $earnings = $service->getMarketplaceEarnings($data['creator']);

    // 100000 * 70% = 70000
    expect($earnings)->toBe(70000.0);
});

it('handles zero platform fee', function () {
    config(['midtrans.platform_fee_percentage' => 0]);
    $data = createPayoutSetup(100000);
    $service = app(PayoutService::class);

    $earnings = $service->getMarketplaceEarnings($data['creator']);

    // 100000 * 100% = 100000
    expect($earnings)->toBe(100000.0);
});
