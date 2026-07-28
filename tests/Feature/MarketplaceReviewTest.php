<?php

use App\Models\MarketplaceListing;
use App\Models\MarketplacePurchase;
use App\Models\MarketplaceReview;
use App\Models\Simulation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * Helper: create a listing with simulation and a completed purchase.
 */
function createReviewSetup(array $simOverrides = [], array $listingOverrides = []): array
{
    $creator = User::factory()->create(['role' => 'creator']);
    $simulation = Simulation::create(array_merge([
        'user_id' => $creator->id,
        'slug' => 'review-test-'.Str::random(6),
        'title' => 'Review Test Simulation',
        'category' => 'Matematika',
        'is_published' => true,
        'zip_path' => 'simulations/test.zip',
        'version' => '1.0.0',
        'description' => 'A test simulation for review testing.',
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
        'total_sales' => 1,
        'total_revenue' => 50000,
    ], $listingOverrides));

    $buyer = User::factory()->create();
    $purchase = MarketplacePurchase::create([
        'user_id' => $buyer->id,
        'listing_id' => $listing->id,
        'simulation_id' => $simulation->id,
        'amount' => 50000,
        'currency' => 'IDR',
        'payment_status' => 'completed',
        'snap_token' => 'mock-token',
        'midtrans_order_id' => 'MOCK-ORDER-001',
    ]);

    return [
        'creator' => $creator,
        'simulation' => $simulation,
        'listing' => $listing,
        'buyer' => $buyer,
        'purchase' => $purchase,
    ];
}

// ─── Store Review Tests ─────────────────────────────────────

it('requires authentication to store a review', function () {
    $data = createReviewSetup();

    $this->postJson(route('marketplace.reviews.store'), [
        'listing_id' => $data['listing']->id,
        'rating' => 5,
        'review_text' => 'Great simulation!',
    ])->assertUnauthorized();
});

it('requires a completed purchase to review', function () {
    $data = createReviewSetup();
    $user = User::factory()->create();

    $this->actingAs($user)
        ->postJson(route('marketplace.reviews.store'), [
            'listing_id' => $data['listing']->id,
            'rating' => 5,
            'review_text' => 'Great simulation!',
        ])->assertStatus(403);
});

it('requires a valid listing', function () {
    $data = createReviewSetup();

    $this->actingAs($data['buyer'])
        ->postJson(route('marketplace.reviews.store'), [
            'listing_id' => 99999,
            'rating' => 5,
            'review_text' => 'Great simulation!',
        ])->assertUnprocessable();
});

it('requires a valid rating between 1 and 5', function () {
    $data = createReviewSetup();

    $this->actingAs($data['buyer'])
        ->postJson(route('marketplace.reviews.store'), [
            'listing_id' => $data['listing']->id,
            'rating' => 6,
        ])->assertUnprocessable();

    $this->actingAs($data['buyer'])
        ->postJson(route('marketplace.reviews.store'), [
            'listing_id' => $data['listing']->id,
            'rating' => 0,
        ])->assertUnprocessable();
});

it('can store a review with valid data', function () {
    $data = createReviewSetup();

    $this->actingAs($data['buyer'])
        ->postJson(route('marketplace.reviews.store'), [
            'listing_id' => $data['listing']->id,
            'rating' => 5,
            'review_text' => 'Simulasi ini sangat bagus!',
        ])->assertOk()
        ->assertJsonStructure([
            'message',
            'review' => ['id', 'rating', 'review_text', 'user'],
        ]);

    $this->assertDatabaseHas('marketplace_reviews', [
        'user_id' => $data['buyer']->id,
        'listing_id' => $data['listing']->id,
        'rating' => 5,
        'review_text' => 'Simulasi ini sangat bagus!',
    ]);
});

it('can store a review without text', function () {
    $data = createReviewSetup();

    $this->actingAs($data['buyer'])
        ->postJson(route('marketplace.reviews.store'), [
            'listing_id' => $data['listing']->id,
            'rating' => 4,
        ])->assertOk();

    $this->assertDatabaseHas('marketplace_reviews', [
        'user_id' => $data['buyer']->id,
        'listing_id' => $data['listing']->id,
        'rating' => 4,
        'review_text' => null,
    ]);
});

it('prevents duplicate reviews from the same user', function () {
    $data = createReviewSetup();

    $this->actingAs($data['buyer'])
        ->postJson(route('marketplace.reviews.store'), [
            'listing_id' => $data['listing']->id,
            'rating' => 5,
        ])->assertOk();

    $this->actingAs($data['buyer'])
        ->postJson(route('marketplace.reviews.store'), [
            'listing_id' => $data['listing']->id,
            'rating' => 4,
        ])->assertStatus(422);
});

// ─── Update Review Tests ────────────────────────────────────

it('can update own review', function () {
    $data = createReviewSetup();
    $review = MarketplaceReview::create([
        'user_id' => $data['buyer']->id,
        'listing_id' => $data['listing']->id,
        'simulation_id' => $data['simulation']->id,
        'rating' => 3,
        'review_text' => 'Lumayan.',
    ]);

    $this->actingAs($data['buyer'])
        ->putJson(route('marketplace.reviews.update', $review->id), [
            'rating' => 5,
            'review_text' => 'Update: ternyata sangat bagus!',
        ])->assertOk();

    $this->assertDatabaseHas('marketplace_reviews', [
        'id' => $review->id,
        'rating' => 5,
        'review_text' => 'Update: ternyata sangat bagus!',
    ]);
});

it('prevents updating another user review', function () {
    $data = createReviewSetup();
    $review = MarketplaceReview::create([
        'user_id' => $data['buyer']->id,
        'listing_id' => $data['listing']->id,
        'simulation_id' => $data['simulation']->id,
        'rating' => 3,
        'review_text' => 'Lumayan.',
    ]);
    $otherUser = User::factory()->create();

    $this->actingAs($otherUser)
        ->putJson(route('marketplace.reviews.update', $review->id), [
            'rating' => 1,
        ])->assertForbidden();
});

// ─── Delete Review Tests ────────────────────────────────────

it('can delete own review', function () {
    $data = createReviewSetup();
    $review = MarketplaceReview::create([
        'user_id' => $data['buyer']->id,
        'listing_id' => $data['listing']->id,
        'simulation_id' => $data['simulation']->id,
        'rating' => 3,
        'review_text' => 'Lumayan.',
    ]);

    $this->actingAs($data['buyer'])
        ->deleteJson(route('marketplace.reviews.destroy', $review->id))
        ->assertOk();

    $this->assertDatabaseMissing('marketplace_reviews', ['id' => $review->id]);
});

it('allows listing owner to delete a review', function () {
    $data = createReviewSetup();
    $review = MarketplaceReview::create([
        'user_id' => $data['buyer']->id,
        'listing_id' => $data['listing']->id,
        'simulation_id' => $data['simulation']->id,
        'rating' => 3,
        'review_text' => 'Lumayan.',
    ]);

    $this->actingAs($data['creator'])
        ->deleteJson(route('marketplace.reviews.destroy', $review->id))
        ->assertOk();

    $this->assertDatabaseMissing('marketplace_reviews', ['id' => $review->id]);
});

it('prevents unrelated user from deleting a review', function () {
    $data = createReviewSetup();
    $review = MarketplaceReview::create([
        'user_id' => $data['buyer']->id,
        'listing_id' => $data['listing']->id,
        'simulation_id' => $data['simulation']->id,
        'rating' => 3,
        'review_text' => 'Lumayan.',
    ]);
    $unrelatedUser = User::factory()->create();

    $this->actingAs($unrelatedUser)
        ->deleteJson(route('marketplace.reviews.destroy', $review->id))
        ->assertForbidden();
});

// ─── Detail Page Review Section Tests ───────────────────────

it('shows review section on marketplace detail page', function () {
    $data = createReviewSetup();

    $this->get(route('marketplace.show', $data['simulation']->slug))
        ->assertOk()
        ->assertSee('Review Pembeli')
        ->assertSee('0 review');
});

it('shows reviews on marketplace detail page when exist', function () {
    $data = createReviewSetup();
    MarketplaceReview::create([
        'user_id' => $data['buyer']->id,
        'listing_id' => $data['listing']->id,
        'simulation_id' => $data['simulation']->id,
        'rating' => 5,
        'review_text' => 'Simulasi terbaik!',
    ]);

    $this->get(route('marketplace.show', $data['simulation']->slug))
        ->assertOk()
        ->assertSee('1 review')
        ->assertSee('Simulasi terbaik!')
        ->assertSee('Review Pembeli');
});

it('shows review form for purchasers who have not reviewed', function () {
    $data = createReviewSetup();

    $this->actingAs($data['buyer'])
        ->get(route('marketplace.show', $data['simulation']->slug))
        ->assertOk()
        ->assertSee('Tulis Review Anda');
});
