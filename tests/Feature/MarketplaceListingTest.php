<?php

use App\Models\MarketplaceListing;
use App\Models\Simulation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * Helper: create a simulation for studio marketplace tests.
 */
function createStudioSimulation(array $overrides = []): Simulation
{
    $creator = User::factory()->create(['role' => 'creator']);

    return Simulation::create(array_merge([
        'user_id' => $creator->id,
        'slug' => 'studio-marketplace-'.Str::random(6),
        'title' => 'Studio Marketplace Test',
        'category' => 'Matematika',
        'is_published' => true,
        'zip_path' => 'simulations/test.zip',
        'version' => '1.0.0',
        'description' => 'A test simulation for marketplace listing testing.',
    ], $overrides, ['user_id' => $creator->id]));
}

// ─── Auth Tests ─────────────────────────────────────────────────

it('requires authentication to view marketplace settings', function () {
    $simulation = createStudioSimulation();

    $this->get(route('studio.simulations.marketplace', $simulation->slug))
        ->assertRedirect(route('login'));
});

it('requires authentication to store marketplace listing', function () {
    $simulation = createStudioSimulation();

    $this->post(route('studio.simulations.marketplace.store', $simulation->slug))
        ->assertRedirect(route('login'));
});

// ─── Authorization Tests ────────────────────────────────────────

it('prevents non-owner from accessing marketplace settings', function () {
    $simulation = createStudioSimulation();
    $otherUser = User::factory()->create();

    $this->actingAs($otherUser);
    $this->get(route('studio.simulations.marketplace', $simulation->slug))
        ->assertNotFound();
});

it('prevents non-owner from storing marketplace listing', function () {
    $simulation = createStudioSimulation();
    $otherUser = User::factory()->create();

    $this->actingAs($otherUser);
    $this->post(route('studio.simulations.marketplace.store', $simulation->slug), [
        'price' => 50000,
        'currency' => 'IDR',
        'license_type' => 'single',
    ])->assertNotFound();
});

// ─── Marketplace Settings Page Tests ────────────────────────────

it('shows marketplace settings page for simulation owner', function () {
    $simulation = createStudioSimulation();

    $this->actingAs(User::find($simulation->user_id));
    $this->get(route('studio.simulations.marketplace', $simulation->slug))
        ->assertOk()
        ->assertViewIs('studio.marketplace-settings')
        ->assertViewHas(['simulation', 'listing']);
});

it('shows existing listing data when available', function () {
    $simulation = createStudioSimulation();
    $listing = MarketplaceListing::create([
        'simulation_id' => $simulation->id,
        'user_id' => $simulation->user_id,
        'price' => 75000,
        'currency' => 'IDR',
        'license_type' => 'institutional',
        'is_active' => true,
        'demo_available' => false,
        'demo_limit_minutes' => 0,
        'total_sales' => 5,
        'total_revenue' => 375000,
    ]);

    $this->actingAs(User::find($simulation->user_id));
    $this->get(route('studio.simulations.marketplace', $simulation->slug))
        ->assertOk()
        ->assertSee('75')
        ->assertSee('Institutional');
});

// ─── Store Listing Tests ────────────────────────────────────────

it('creates a new marketplace listing', function () {
    $simulation = createStudioSimulation();

    $this->actingAs(User::find($simulation->user_id));
    $this->post(route('studio.simulations.marketplace.store', $simulation->slug), [
        'price' => 50000,
        'currency' => 'IDR',
        'license_type' => 'single',
        'demo_available' => false,
        'demo_limit_minutes' => 0,
        'is_active' => true,
    ])->assertRedirect();

    $this->assertDatabaseHas('marketplace_listings', [
        'simulation_id' => $simulation->id,
        'user_id' => $simulation->user_id,
        'price' => 50000,
        'currency' => 'IDR',
        'license_type' => 'single',
        'is_active' => true,
    ]);
});

it('updates existing marketplace listing', function () {
    $simulation = createStudioSimulation();
    MarketplaceListing::create([
        'simulation_id' => $simulation->id,
        'user_id' => $simulation->user_id,
        'price' => 50000,
        'currency' => 'IDR',
        'license_type' => 'single',
        'is_active' => true,
        'demo_available' => false,
        'demo_limit_minutes' => 0,
        'total_sales' => 0,
        'total_revenue' => 0,
    ]);

    $this->actingAs(User::find($simulation->user_id));
    $this->put(route('studio.simulations.marketplace.update', $simulation->slug), [
        'price' => 100000,
        'currency' => 'USD',
        'license_type' => 'institutional',
        'demo_available' => true,
        'demo_limit_minutes' => 15,
        'is_active' => false,
    ])->assertRedirect();

    $this->assertDatabaseHas('marketplace_listings', [
        'simulation_id' => $simulation->id,
        'price' => 100000,
        'currency' => 'USD',
        'license_type' => 'institutional',
        'is_active' => false,
        'demo_available' => true,
        'demo_limit_minutes' => 15,
    ]);
});

// ─── Validation Tests ───────────────────────────────────────────

it('validates price is required', function () {
    $simulation = createStudioSimulation();

    $this->actingAs(User::find($simulation->user_id));
    $this->post(route('studio.simulations.marketplace.store', $simulation->slug), [
        'price' => '',
        'currency' => 'IDR',
        'license_type' => 'single',
    ])->assertSessionHasErrors('price');
});

it('validates price minimum', function () {
    $simulation = createStudioSimulation();

    $this->actingAs(User::find($simulation->user_id));
    $this->post(route('studio.simulations.marketplace.store', $simulation->slug), [
        'price' => 500,
        'currency' => 'IDR',
        'license_type' => 'single',
    ])->assertSessionHasErrors('price');
});

it('validates currency is valid', function () {
    $simulation = createStudioSimulation();

    $this->actingAs(User::find($simulation->user_id));
    $this->post(route('studio.simulations.marketplace.store', $simulation->slug), [
        'price' => 50000,
        'currency' => 'EUR',
        'license_type' => 'single',
    ])->assertSessionHasErrors('currency');
});

it('validates license_type is valid', function () {
    $simulation = createStudioSimulation();

    $this->actingAs(User::find($simulation->user_id));
    $this->post(route('studio.simulations.marketplace.store', $simulation->slug), [
        'price' => 50000,
        'currency' => 'IDR',
        'license_type' => 'enterprise',
    ])->assertSessionHasErrors('license_type');
});

// ─── Remove from Marketplace Tests ──────────────────────────────

it('removes listing from marketplace', function () {
    $simulation = createStudioSimulation();
    MarketplaceListing::create([
        'simulation_id' => $simulation->id,
        'user_id' => $simulation->user_id,
        'price' => 50000,
        'currency' => 'IDR',
        'license_type' => 'single',
        'is_active' => true,
        'demo_available' => false,
        'demo_limit_minutes' => 0,
        'total_sales' => 0,
        'total_revenue' => 0,
    ]);

    $this->actingAs(User::find($simulation->user_id));
    $this->delete(route('studio.simulations.marketplace.remove', $simulation->slug))
        ->assertRedirect();

    $this->assertDatabaseMissing('marketplace_listings', [
        'simulation_id' => $simulation->id,
    ]);
});

it('prevents non-owner from removing listing', function () {
    $simulation = createStudioSimulation();
    MarketplaceListing::create([
        'simulation_id' => $simulation->id,
        'user_id' => $simulation->user_id,
        'price' => 50000,
        'currency' => 'IDR',
        'license_type' => 'single',
        'is_active' => true,
        'demo_available' => false,
        'demo_limit_minutes' => 0,
        'total_sales' => 0,
        'total_revenue' => 0,
    ]);

    $otherUser = User::factory()->create();
    $this->actingAs($otherUser);
    $this->delete(route('studio.simulations.marketplace.remove', $simulation->slug))
        ->assertNotFound();

    $this->assertDatabaseHas('marketplace_listings', [
        'simulation_id' => $simulation->id,
    ]);
});
