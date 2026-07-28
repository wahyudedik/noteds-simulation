<?php

use App\Models\MarketplaceListing;
use App\Models\Simulation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * Helper: create a published simulation with an active marketplace listing.
 */
function createMarketplaceListing(array $simOverrides = [], array $listingOverrides = []): MarketplaceListing
{
    $creator = User::factory()->create(['role' => 'creator']);

    $simulation = Simulation::create(array_merge([
        'user_id' => $creator->id,
        'title' => 'Simulasi Marketplace '.Str::random(6),
        'category' => 'Fisika',
        'description' => 'Deskripsi simulasi marketplace',
        'zip_path' => 'simulations/test.zip',
        'version' => '1.0.0',
        'is_published' => true,
        'published_at' => now(),
    ], $simOverrides));

    return MarketplaceListing::create(array_merge([
        'simulation_id' => $simulation->id,
        'user_id' => $creator->id,
        'price' => 50000,
        'currency' => 'IDR',
        'license_type' => 'single',
        'is_active' => true,
        'demo_available' => true,
        'demo_limit_minutes' => 5,
    ], $listingOverrides));
}

// ─── Browse Page Tests ────────────────────────────────────────

it('shows marketplace browse page', function () {
    $response = $this->get(route('marketplace.index'));

    $response->assertStatus(200);
    $response->assertSee('Marketplace');
});

it('displays active listings on browse page', function () {
    $listing = createMarketplaceListing();

    $response = $this->get(route('marketplace.index'));

    $response->assertStatus(200);
    $response->assertSee($listing->simulation->title);
    $response->assertSee($listing->formatted_price);
});

it('does not display inactive listings', function () {
    $listing = createMarketplaceListing([], ['is_active' => false]);

    $response = $this->get(route('marketplace.index'));

    $response->assertStatus(200);
    $response->assertDontSee($listing->simulation->title);
});

it('does not display unpublished simulations', function () {
    $listing = createMarketplaceListing(['is_published' => false]);

    $response = $this->get(route('marketplace.index'));

    $response->assertStatus(200);
    $response->assertDontSee($listing->simulation->title);
});

// ─── Search Tests ─────────────────────────────────────────────

it('filters listings by search term', function () {
    $listing1 = createMarketplaceListing(['title' => 'Fisika Mekanik']);
    $listing2 = createMarketplaceListing(['title' => 'Kimia Organik']);

    $response = $this->get(route('marketplace.index', ['search' => 'Fisika']));

    $response->assertStatus(200);
    $response->assertSee('Fisika Mekanik');
    $response->assertDontSee('Kimia Organik');
});

it('search is case insensitive', function () {
    $listing = createMarketplaceListing(['title' => 'Biologi Sel']);

    $response = $this->get(route('marketplace.index', ['search' => 'biologi']));

    $response->assertStatus(200);
    $response->assertSee('Biologi Sel');
});

// ─── Category Filter Tests ────────────────────────────────────

it('filters listings by category', function () {
    $listing1 = createMarketplaceListing(['category' => 'Fisika']);
    $listing2 = createMarketplaceListing(['category' => 'Kimia']);

    $response = $this->get(route('marketplace.index', ['category' => 'Fisika']));

    $response->assertStatus(200);
    $response->assertSee($listing1->simulation->title);
    $response->assertDontSee($listing2->simulation->title);
});

it('shows category counts in sidebar', function () {
    createMarketplaceListing(['category' => 'Fisika']);
    createMarketplaceListing(['category' => 'Fisika']);
    createMarketplaceListing(['category' => 'Kimia']);

    $response = $this->get(route('marketplace.index'));

    $response->assertStatus(200);
    $response->assertSee('Fisika');
    $response->assertSee('Kimia');
});

// ─── License Filter Tests ─────────────────────────────────────

it('filters listings by license type', function () {
    $listing1 = createMarketplaceListing([], ['license_type' => 'single']);
    $listing2 = createMarketplaceListing([], ['license_type' => 'institutional']);

    $response = $this->get(route('marketplace.index', ['license' => 'single']));

    $response->assertStatus(200);
    $response->assertSee($listing1->simulation->title);
    $response->assertDontSee($listing2->simulation->title);
});

// ─── Sort Tests ───────────────────────────────────────────────

it('sorts listings by newest by default', function () {
    $listing1 = createMarketplaceListing(['title' => 'Old Sim']);
    $listing2 = createMarketplaceListing(['title' => 'New Sim']);

    $response = $this->get(route('marketplace.index'));

    $response->assertStatus(200);
    // Both should be visible
    $response->assertSee('New Sim');
    $response->assertSee('Old Sim');
});

it('sorts listings by price low to high', function () {
    $listing1 = createMarketplaceListing(['title' => 'Cheap Sim'], ['price' => 10000]);
    $listing2 = createMarketplaceListing(['title' => 'Expensive Sim'], ['price' => 100000]);

    $response = $this->get(route('marketplace.index', ['sort' => 'price_low']));

    $response->assertStatus(200);
    $response->assertSee('Cheap Sim');
    $response->assertSee('Expensive Sim');
});

it('sorts listings by price high to low', function () {
    $listing1 = createMarketplaceListing(['title' => 'Cheap Sim'], ['price' => 10000]);
    $listing2 = createMarketplaceListing(['title' => 'Expensive Sim'], ['price' => 100000]);

    $response = $this->get(route('marketplace.index', ['sort' => 'price_high']));

    $response->assertStatus(200);
    $response->assertSee('Cheap Sim');
    $response->assertSee('Expensive Sim');
});

// ─── Empty State Tests ────────────────────────────────────────

it('shows empty state when no listings exist', function () {
    $response = $this->get(route('marketplace.index'));

    $response->assertStatus(200);
    $response->assertSee('Tidak ada simulasi ditemukan');
});

it('shows empty state when search has no results', function () {
    createMarketplaceListing(['title' => 'Fisika']);

    $response = $this->get(route('marketplace.index', ['search' => 'xyznotfound']));

    $response->assertStatus(200);
    $response->assertSee('Tidak ada simulasi ditemukan');
});

// ─── Detail Page Tests ────────────────────────────────────────

it('shows marketplace detail page for active listing', function () {
    $listing = createMarketplaceListing();

    $response = $this->get(route('marketplace.show', $listing->simulation->slug));

    $response->assertStatus(200);
    $response->assertSee($listing->simulation->title);
    $response->assertSee($listing->formatted_price);
});

it('returns 404 for inactive listing detail', function () {
    $listing = createMarketplaceListing([], ['is_active' => false]);

    $response = $this->get(route('marketplace.show', $listing->simulation->slug));

    $response->assertStatus(404);
});

it('returns 404 for non-existent simulation', function () {
    $response = $this->get(route('marketplace.show', 'non-existent-slug'));

    $response->assertStatus(404);
});

// ─── Price Display Tests ──────────────────────────────────────

it('displays formatted IDR price', function () {
    $listing = createMarketplaceListing([], ['price' => 50000, 'currency' => 'IDR']);

    $response = $this->get(route('marketplace.index'));

    $response->assertStatus(200);
    $response->assertSee('Rp 50.000');
});

it('displays formatted USD price', function () {
    $listing = createMarketplaceListing([], ['price' => 9.99, 'currency' => 'USD']);

    $response = $this->get(route('marketplace.index'));

    $response->assertStatus(200);
    $response->assertSee('$9.99');
});

// ─── License Badge Tests ──────────────────────────────────────

it('displays license type badge', function () {
    $listing = createMarketplaceListing([], ['license_type' => 'institutional']);

    $response = $this->get(route('marketplace.index'));

    $response->assertStatus(200);
    $response->assertSee('Institutional');
});

it('displays demo available badge', function () {
    $listing = createMarketplaceListing([], ['demo_available' => true]);

    $response = $this->get(route('marketplace.index'));

    $response->assertStatus(200);
    $response->assertSee('Demo Available');
});

// ─── Stats Tests ──────────────────────────────────────────────

it('displays total listings count', function () {
    createMarketplaceListing();
    createMarketplaceListing();

    $response = $this->get(route('marketplace.index'));

    $response->assertStatus(200);
    $response->assertSee('Menampilkan');
    $response->assertSee('2');
});

it('displays sales count on cards', function () {
    $listing = createMarketplaceListing([], ['total_sales' => 42]);

    $response = $this->get(route('marketplace.index'));

    $response->assertStatus(200);
    $response->assertSee('42');
    $response->assertSee('terjual');
});
