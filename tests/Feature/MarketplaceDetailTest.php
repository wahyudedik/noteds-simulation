<?php

use App\Models\MarketplaceListing;
use App\Models\Rating;
use App\Models\Simulation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

/**
 * Helper to create a marketplace listing with simulation and creator.
 */
function createDetailListing(array $simOverrides = [], array $listingOverrides = []): array
{
    $creator = User::factory()->create(['role' => 'creator']);
    $simulation = Simulation::create(array_merge([
        'user_id' => $creator->id,
        'title' => 'Simulasi Detail '.Str::random(6),
        'slug' => 'simulasi-detail-'.Str::random(6),
        'description' => 'Deskripsi lengkap untuk simulasi marketplace detail ini.',
        'category' => 'Fisika',
        'version' => '1.0.0',
        'zip_path' => 'simulations/test.zip',
        'thumbnail' => 'thumbnails/test.jpg',
        'is_published' => true,
        'is_featured' => false,
        'play_count' => 150,
        'view_count' => 300,
        'average_rating' => 4.5,
        'rating_count' => 2,
        'published_at' => now()->subDays(5),
    ], $simOverrides));

    $listing = MarketplaceListing::create(array_merge([
        'simulation_id' => $simulation->id,
        'user_id' => $creator->id,
        'price' => 150000,
        'currency' => 'IDR',
        'license_type' => 'single',
        'is_active' => true,
        'demo_available' => true,
        'demo_limit_minutes' => 10,
        'total_sales' => 42,
        'total_revenue' => 6300000,
    ], $listingOverrides));

    return compact('creator', 'simulation', 'listing');
}

it('shows the marketplace detail page for a valid listing', function () {
    ['simulation' => $simulation] = createDetailListing();

    $response = $this->get(route('marketplace.show', $simulation->slug));

    $response->assertStatus(200);
    $response->assertSee($simulation->title);
    $response->assertSee($simulation->description);
    $response->assertSee('Rp 150.000');
    $response->assertSee('Single License');
    $response->assertSee('Beli Sekarang');
});

it('displays the simulation thumbnail', function () {
    ['simulation' => $simulation] = createDetailListing();

    $response = $this->get(route('marketplace.show', $simulation->slug));

    $response->assertStatus(200);
    $response->assertSee('thumbnails/test.jpg');
});

it('displays creator information', function () {
    ['creator' => $creator, 'simulation' => $simulation] = createDetailListing();

    $response = $this->get(route('marketplace.show', $simulation->slug));

    $response->assertStatus(200);
    $response->assertSee($creator->name);
    $response->assertSee('Creator');
});

it('displays demo section when demo is available', function () {
    ['simulation' => $simulation] = createDetailListing([
        'slug' => 'demo-available-'.Str::random(6),
    ], [
        'demo_available' => true,
        'demo_limit_minutes' => 10,
    ]);

    $response = $this->get(route('marketplace.show', $simulation->slug));

    $response->assertStatus(200);
    $response->assertSee('Demo Gratis');
    $response->assertSee('10 menit');
    $response->assertSee('Mainkan Demo');
});

it('hides demo section when demo is not available', function () {
    ['simulation' => $simulation] = createDetailListing([
        'slug' => 'no-demo-'.Str::random(6),
    ], [
        'demo_available' => false,
    ]);

    $response = $this->get(route('marketplace.show', $simulation->slug));

    $response->assertStatus(200);
    $response->assertDontSee('Demo Gratis');
    $response->assertDontSee('Mainkan Demo');
});

it('displays rating summary with distribution', function () {
    ['simulation' => $simulation] = createDetailListing();

    // Create additional ratings for distribution
    $users = User::factory()->count(3)->create();
    foreach ($users as $i => $user) {
        Rating::create([
            'user_id' => $user->id,
            'simulation_id' => $simulation->id,
            'rating' => $i + 1, // 1, 2, 3
        ]);
    }

    $response = $this->get(route('marketplace.show', $simulation->slug));

    $response->assertStatus(200);
    $response->assertSeeHtml('Ulasan & Rating');
    $response->assertSee(number_format($simulation->average_rating, 1));
});

it('displays license type badge in sidebar', function () {
    ['simulation' => $simulation] = createDetailListing([], [
        'license_type' => 'institutional',
    ]);

    $response = $this->get(route('marketplace.show', $simulation->slug));

    $response->assertStatus(200);
    $response->assertSee('Institutional License');
});

it('displays sales and stats in sidebar', function () {
    ['simulation' => $simulation] = createDetailListing();

    $response = $this->get(route('marketplace.show', $simulation->slug));

    $response->assertStatus(200);
    $response->assertSee('Total Terjual');
    $response->assertSee('42');
    $response->assertSee('Dilihat');
    $response->assertSee('Dimainkan');
});

it('shows breadcrumb navigation', function () {
    ['simulation' => $simulation] = createDetailListing();

    $response = $this->get(route('marketplace.show', $simulation->slug));

    $response->assertStatus(200);
    $response->assertSee('Marketplace');
});

it('returns 404 for non-published simulation', function () {
    ['simulation' => $simulation] = createDetailListing([
        'is_published' => false,
    ]);

    $response = $this->get(route('marketplace.show', $simulation->slug));

    $response->assertStatus(404);
});

it('returns 404 for inactive listing', function () {
    ['simulation' => $simulation] = createDetailListing([], [
        'is_active' => false,
    ]);

    $response = $this->get(route('marketplace.show', $simulation->slug));

    $response->assertStatus(404);
});

it('returns 404 for non-existent slug', function () {
    $response = $this->get(route('marketplace.show', 'non-existent-slug'));

    $response->assertStatus(404);
});

it('displays simulation version', function () {
    ['simulation' => $simulation] = createDetailListing([
        'version' => '2.1.0',
    ]);

    $response = $this->get(route('marketplace.show', $simulation->slug));

    $response->assertStatus(200);
    $response->assertSee('v2.1.0');
});

it('displays category tag', function () {
    ['simulation' => $simulation] = createDetailListing([
        'category' => 'Kimia',
    ]);

    $response = $this->get(route('marketplace.show', $simulation->slug));

    $response->assertStatus(200);
    $response->assertSee('Kimia');
});

it('shows related simulations from same category', function () {
    ['simulation' => $simulation] = createDetailListing([
        'category' => 'Biologi',
        'slug' => 'main-bio-'.Str::random(6),
    ]);

    // Create a related listing in the same category
    $relatedCreator = User::factory()->create(['role' => 'creator']);
    $relatedSim = Simulation::create([
        'user_id' => $relatedCreator->id,
        'title' => 'Related Bio Sim',
        'slug' => 'related-bio-'.Str::random(6),
        'category' => 'Biologi',
        'version' => '1.0.0',
        'zip_path' => 'simulations/related.zip',
        'is_published' => true,
        'published_at' => now(),
    ]);
    MarketplaceListing::create([
        'simulation_id' => $relatedSim->id,
        'user_id' => $relatedCreator->id,
        'price' => 50000,
        'currency' => 'IDR',
        'license_type' => 'single',
        'is_active' => true,
    ]);

    $response = $this->get(route('marketplace.show', $simulation->slug));

    $response->assertStatus(200);
    $response->assertSee('Simulasi Sejenis');
    $response->assertSee('Related Bio Sim');
});

it('does not show related simulations from different categories', function () {
    ['simulation' => $simulation] = createDetailListing([
        'category' => 'Fisika',
        'slug' => 'fisika-main-'.Str::random(6),
    ]);

    // Create a listing in a different category
    $otherCreator = User::factory()->create(['role' => 'creator']);
    $otherSim = Simulation::create([
        'user_id' => $otherCreator->id,
        'title' => 'Chemistry Sim',
        'slug' => 'chemistry-'.Str::random(6),
        'category' => 'Kimia',
        'version' => '1.0.0',
        'zip_path' => 'simulations/chem.zip',
        'is_published' => true,
        'published_at' => now(),
    ]);
    MarketplaceListing::create([
        'simulation_id' => $otherSim->id,
        'user_id' => $otherCreator->id,
        'price' => 50000,
        'currency' => 'IDR',
        'license_type' => 'single',
        'is_active' => true,
    ]);

    $response = $this->get(route('marketplace.show', $simulation->slug));

    $response->assertStatus(200);
    $response->assertDontSee('Chemistry Sim');
});
