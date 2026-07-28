<?php

use App\Models\AffiliateLink;
use App\Models\CreatorReputation;
use App\Models\Simulation;
use App\Models\User;
use App\Services\AffiliateService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/** Helper: create a published simulation owned by a creator. */
function createPublishedSimulation(User $creator, array $overrides = []): Simulation
{
    $simData = array_merge([
        'title' => 'Simulasi Test',
        'slug' => 'simulasi-test-'.Str::random(6),
        'description' => 'Deskripsi simulasi test',
        'category' => 'fisika',
        'zip_path' => 'simulations/test.zip',
        'thumbnail' => 'thumbnails/test.jpg',
        'user_id' => $creator->id,
        'is_published' => true,
        'is_featured' => false,
        'version' => '1.0.0',
    ], $overrides);

    return Simulation::create($simData);
}

/** Helper: create a creator user (verified). */
function createCreator(array $overrides = []): User
{
    $user = User::factory()->create(array_merge([
        'role' => 'creator',
        'username' => 'creator-'.Str::random(6),
    ], $overrides));

    CreatorReputation::create([
        'user_id' => $user->id,
        'score' => 50,
        'revenue_tier' => 'basic',
        'ranking_score' => 0,
    ]);

    return $user;
}

/** Helper: create a regular user (verified). */
function createRegularUser(array $overrides = []): User
{
    return User::factory()->create(array_merge([
        'role' => 'user',
        'username' => 'user-'.Str::random(6),
    ], $overrides));
}

/*
|--------------------------------------------------------------------------
| Affiliate System Tests
|--------------------------------------------------------------------------
*/

it('allows creator to view affiliate page', function () {
    $creator = createCreator();

    $this->actingAs($creator)
        ->get(route('studio.affiliate'))
        ->assertOk();
});

it('prevents non-creator from viewing affiliate page', function () {
    $user = createRegularUser();

    $this->actingAs($user)
        ->get(route('studio.affiliate'))
        ->assertForbidden();
});

it('allows creator to generate affiliate link for own simulation', function () {
    $creator = createCreator();
    $sim = createPublishedSimulation($creator);

    $this->actingAs($creator)
        ->post(route('studio.affiliate.generate'), ['simulation_id' => $sim->id])
        ->assertRedirect(route('studio.affiliate'));

    $this->assertDatabaseHas('affiliate_links', [
        'user_id' => $creator->id,
        'simulation_id' => $sim->id,
    ]);
});

it('prevents creator from generating affiliate link for other creator simulation', function () {
    $creator1 = createCreator();
    $creator2 = createCreator();
    $sim = createPublishedSimulation($creator2);

    $this->actingAs($creator1)
        ->post(route('studio.affiliate.generate'), ['simulation_id' => $sim->id])
        ->assertForbidden();
});

it('allows creator to delete own affiliate link', function () {
    $creator = createCreator();
    $sim = createPublishedSimulation($creator);

    $link = AffiliateLink::create([
        'user_id' => $creator->id,
        'simulation_id' => $sim->id,
        'code' => 'testcode1',
    ]);

    $this->actingAs($creator)
        ->delete(route('studio.affiliate.destroy', $link))
        ->assertRedirect(route('studio.affiliate'));

    $this->assertDatabaseMissing('affiliate_links', ['id' => $link->id]);
});

it('prevents creator from deleting other creator affiliate link', function () {
    $creator1 = createCreator();
    $creator2 = createCreator();
    $sim = createPublishedSimulation($creator2);

    $link = AffiliateLink::create([
        'user_id' => $creator2->id,
        'simulation_id' => $sim->id,
        'code' => 'testcode2',
    ]);

    $this->actingAs($creator1)
        ->delete(route('studio.affiliate.destroy', $link))
        ->assertForbidden();

    $this->assertDatabaseHas('affiliate_links', ['id' => $link->id]);
});

it('tracks click on affiliate link and redirects to simulation', function () {
    $creator = createCreator();
    $sim = createPublishedSimulation($creator);

    $link = AffiliateLink::create([
        'user_id' => $creator->id,
        'simulation_id' => $sim->id,
        'code' => 'tracktest',
        'clicks' => 0,
    ]);

    $this->get(route('affiliate.track', 'tracktest'))
        ->assertRedirect(route('simulations.show', $sim->slug));

    $this->assertDatabaseHas('affiliate_links', [
        'code' => 'tracktest',
        'clicks' => 1,
    ]);
});

it('returns 404 for invalid affiliate code', function () {
    $this->get(route('affiliate.track', 'nonexistent'))
        ->assertNotFound();
});

it('displays affiliate stats on the page', function () {
    $creator = createCreator();
    $sim = createPublishedSimulation($creator);

    AffiliateLink::create([
        'user_id' => $creator->id,
        'simulation_id' => $sim->id,
        'code' => 'statcode1',
        'clicks' => 42,
        'conversions' => 3,
    ]);

    $response = $this->actingAs($creator)
        ->get(route('studio.affiliate'));

    $response->assertOk()
        ->assertSee('42')
        ->assertSee('3')
        ->assertSee('Link Afiliasi');
});

it('shows simulation dropdown for generating affiliate links', function () {
    $creator = createCreator();
    $sim = createPublishedSimulation($creator, ['title' => 'Simulasi Unik ABC']);

    $this->actingAs($creator)
        ->get(route('studio.affiliate'))
        ->assertSee('Simulasi Unik ABC')
        ->assertSee('Buat Link Afiliasi Baru');
});

it('prevents self-referral on conversion tracking', function () {
    $creator = createCreator();
    $sim = createPublishedSimulation($creator);

    $link = AffiliateLink::create([
        'user_id' => $creator->id,
        'simulation_id' => $sim->id,
        'code' => 'selftest',
        'clicks' => 5,
    ]);

    $result = app(AffiliateService::class)->trackConversion('selftest', $creator, 100000);

    expect($result)->toBeNull();

    $this->assertDatabaseMissing('affiliate_conversions', [
        'affiliate_link_id' => $link->id,
    ]);
});

it('tracks conversion with correct commission', function () {
    $creator = createCreator();
    $buyer = User::create([
        'name' => 'Buyer',
        'email' => 'buyer@test.com',
        'password' => bcrypt('password'),
        'role' => 'user',
        'username' => 'buyer-user',
    ]);
    $sim = createPublishedSimulation($creator);

    $link = AffiliateLink::create([
        'user_id' => $creator->id,
        'simulation_id' => $sim->id,
        'code' => 'comtest',
        'clicks' => 10,
    ]);

    $conversion = app(AffiliateService::class)->trackConversion('comtest', $buyer, 100000);

    expect($conversion)->not->toBeNull()
        ->and((float) $conversion->commission)->toBe(10000.0);

    $this->assertDatabaseHas('affiliate_conversions', [
        'affiliate_link_id' => $link->id,
        'buyer_user_id' => $buyer->id,
        'amount' => 100000,
        'commission' => 10000,
    ]);

    $this->assertDatabaseHas('affiliate_links', [
        'code' => 'comtest',
        'conversions' => 1,
    ]);
});

it('returns correct creator affiliate stats', function () {
    $creator = createCreator();
    $sim = createPublishedSimulation($creator);

    AffiliateLink::create([
        'user_id' => $creator->id,
        'simulation_id' => $sim->id,
        'code' => 'statlink1',
        'clicks' => 100,
        'conversions' => 5,
    ]);

    $stats = app(AffiliateService::class)->getCreatorStats($creator);

    expect($stats['total_links'])->toBe(1)
        ->and($stats['total_clicks'])->toBe(100)
        ->and($stats['total_conversions'])->toBe(5);
});
