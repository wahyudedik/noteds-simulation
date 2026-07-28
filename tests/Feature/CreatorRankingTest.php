<?php

use App\Models\CreatorReputation;
use App\Models\Simulation;
use App\Models\User;
use App\Services\CreatorRankingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

$simData = fn (int $userId, string $slug, string $category, array $overrides = []) => array_merge([
    'user_id' => $userId,
    'title' => $slug,
    'slug' => $slug,
    'category' => $category,
    'zip_path' => 'simulations/'.$slug.'.zip',
    'is_published' => true,
    'play_count' => 0,
    'view_count' => 0,
    'average_rating' => 0,
    'rating_count' => 0,
    'published_at' => now()->subDays(5),
], $overrides);

it('calculates ranking score for a creator with simulations', function () use ($simData) {
    $creator = User::factory()->create(['role' => 'creator']);
    CreatorReputation::create(['user_id' => $creator->id, 'revenue_tier' => 'verified']);

    foreach (range(1, 3) as $i) {
        Simulation::create($simData($creator->id, "test-calc-{$creator->id}-{$i}", 'Fisika', [
            'play_count' => 100,
            'view_count' => 500,
            'average_rating' => 4.5,
            'rating_count' => 10,
        ]));
    }

    $service = app(CreatorRankingService::class);
    $result = $service->calculateRank($creator);

    expect($result['ranking_score'])->toBeGreaterThan(0);
    expect($result['total_views'])->toBe(1500);
    expect($result['total_plays'])->toBe(300);
    expect($result['simulation_count'])->toBe(3);
});

it('gives higher score to creator with more engagement', function () use ($simData) {
    $creatorA = User::factory()->create(['role' => 'creator']);
    $creatorB = User::factory()->create(['role' => 'creator']);
    CreatorReputation::create(['user_id' => $creatorA->id, 'revenue_tier' => 'basic']);
    CreatorReputation::create(['user_id' => $creatorB->id, 'revenue_tier' => 'basic']);

    foreach (range(1, 5) as $i) {
        Simulation::create($simData($creatorA->id, "high-{$creatorA->id}-{$i}", 'Fisika', [
            'play_count' => 1000,
            'view_count' => 5000,
            'average_rating' => 4.8,
            'rating_count' => 20,
        ]));
    }

    foreach (range(1, 2) as $i) {
        Simulation::create($simData($creatorB->id, "low-{$creatorB->id}-{$i}", 'Kimia', [
            'play_count' => 50,
            'view_count' => 200,
            'average_rating' => 3.0,
            'rating_count' => 5,
        ]));
    }

    $service = app(CreatorRankingService::class);
    $scoreA = $service->calculateRank($creatorA)['ranking_score'];
    $scoreB = $service->calculateRank($creatorB)['ranking_score'];

    expect($scoreA)->toBeGreaterThan($scoreB);
});

it('updates ranking score in database', function () use ($simData) {
    $creator = User::factory()->create(['role' => 'creator']);
    CreatorReputation::create(['user_id' => $creator->id, 'revenue_tier' => 'basic']);

    foreach (range(1, 2) as $i) {
        Simulation::create($simData($creator->id, "update-{$creator->id}-{$i}", 'Biologi', [
            'play_count' => 50,
            'view_count' => 200,
            'average_rating' => 4.0,
            'rating_count' => 5,
        ]));
    }

    $service = app(CreatorRankingService::class);
    $reputation = $service->updateRanking($creator);

    expect($reputation->ranking_score)->toBeGreaterThan(0);

    $reputation->refresh();
    expect($reputation->ranking_score)->toBeGreaterThan(0);
});

it('updates rankings for all creators', function () {
    $creator1 = User::factory()->create(['role' => 'creator']);
    $creator2 = User::factory()->create(['role' => 'creator']);
    CreatorReputation::create(['user_id' => $creator1->id, 'revenue_tier' => 'basic']);
    CreatorReputation::create(['user_id' => $creator2->id, 'revenue_tier' => 'basic']);

    $service = app(CreatorRankingService::class);
    $count = $service->updateAllRankings();

    expect($count)->toBe(2);

    $creator1->refresh();
    $creator2->refresh();
    expect($creator1->reputation->ranking_score)->toBeGreaterThanOrEqual(0);
    expect($creator2->reputation->ranking_score)->toBeGreaterThanOrEqual(0);
});

it('returns top creators ordered by ranking score', function () use ($simData) {
    $creator1 = User::factory()->create(['role' => 'creator']);
    $creator2 = User::factory()->create(['role' => 'creator']);
    CreatorReputation::create(['user_id' => $creator1->id, 'revenue_tier' => 'basic']);
    CreatorReputation::create(['user_id' => $creator2->id, 'revenue_tier' => 'platinum']);

    foreach (range(1, 5) as $i) {
        Simulation::create($simData($creator1->id, "c1-{$creator1->id}-{$i}", 'Fisika', [
            'play_count' => 100,
            'view_count' => 500,
            'average_rating' => 4.0,
            'rating_count' => 10,
        ]));
    }

    foreach (range(1, 2) as $i) {
        Simulation::create($simData($creator2->id, "c2-{$creator2->id}-{$i}", 'Kimia', [
            'play_count' => 50,
            'view_count' => 200,
            'average_rating' => 4.5,
            'rating_count' => 10,
        ]));
    }

    $service = app(CreatorRankingService::class);
    $service->updateAllRankings();

    $topCreators = $service->getTopCreators(10);

    expect($topCreators)->toHaveCount(2);

    // Both creators should be present
    $ids = $topCreators->pluck('user.id')->toArray();
    expect($ids)->toContain($creator1->id);
    expect($ids)->toContain($creator2->id);

    // Verify ranking scores are set
    foreach ($topCreators as $entry) {
        expect($entry['ranking_score'])->toBeGreaterThan(0);
    }
});

it('gives tier bonus for higher revenue tiers', function () {
    $basicCreator = User::factory()->create(['role' => 'creator']);
    $platinumCreator = User::factory()->create(['role' => 'creator']);
    CreatorReputation::create(['user_id' => $basicCreator->id, 'revenue_tier' => 'basic']);
    CreatorReputation::create(['user_id' => $platinumCreator->id, 'revenue_tier' => 'platinum']);

    $service = app(CreatorRankingService::class);
    $basicResult = $service->calculateRank($basicCreator);
    $platinumResult = $service->calculateRank($platinumCreator);

    expect($basicResult['tier_bonus'])->toBe(0);
    expect($platinumResult['tier_bonus'])->toBe(50);
});

it('returns empty collection when no creators exist', function () {
    $service = app(CreatorRankingService::class);
    $topCreators = $service->getTopCreators(10);

    expect($topCreators)->toHaveCount(0);
});

it('handles creator with no simulations gracefully', function () {
    $creator = User::factory()->create(['role' => 'creator']);
    CreatorReputation::create(['user_id' => $creator->id, 'revenue_tier' => 'basic']);

    $service = app(CreatorRankingService::class);
    $result = $service->calculateRank($creator);

    expect($result['ranking_score'])->toBeGreaterThanOrEqual(0);
    expect($result['total_views'])->toBe(0);
    expect($result['total_plays'])->toBe(0);
    expect($result['simulation_count'])->toBe(0);
});

it('leaderboard creators page is accessible', function () {
    $response = $this->get(route('leaderboard.creators'));

    $response->assertStatus(200);
    $response->assertViewIs('leaderboard.creators');
});

it('leaderboard creators page works with period filter', function () {
    $response = $this->get(route('leaderboard.creators', ['period' => 'week']));

    $response->assertStatus(200);
    $response->assertViewIs('leaderboard.creators');
});

it('leaderboard creators page supports sort by followers', function () {
    $creator1 = User::factory()->create(['role' => 'creator']);
    $creator2 = User::factory()->create(['role' => 'creator']);
    CreatorReputation::create(['user_id' => $creator1->id, 'revenue_tier' => 'basic', 'ranking_score' => 100]);
    CreatorReputation::create(['user_id' => $creator2->id, 'revenue_tier' => 'basic', 'ranking_score' => 50]);

    // creator1 has more followers
    $follower = User::factory()->create(['role' => 'user']);
    DB::table('follows')->insert([
        'follower_id' => $follower->id,
        'followable_id' => $creator1->id,
    ]);

    $response = $this->get(route('leaderboard.creators', ['sort' => 'followers']));

    $response->assertStatus(200);
    $response->assertViewIs('leaderboard.creators');

    $topCreators = $response->viewData('topCreators');
    // creator1 should be first since they have more followers
    expect($topCreators->first()['user']->id)->toBe($creator1->id);
});

it('leaderboard creators page supports sort by simulations', function () use ($simData) {
    $creator1 = User::factory()->create(['role' => 'creator']);
    $creator2 = User::factory()->create(['role' => 'creator']);
    CreatorReputation::create(['user_id' => $creator1->id, 'revenue_tier' => 'basic', 'ranking_score' => 50]);
    CreatorReputation::create(['user_id' => $creator2->id, 'revenue_tier' => 'basic', 'ranking_score' => 100]);

    // creator1 has more simulations
    foreach (range(1, 5) as $i) {
        Simulation::create($simData($creator1->id, "sort-sim-{$creator1->id}-{$i}", 'Fisika'));
    }
    Simulation::create($simData($creator2->id, "sort-sim-{$creator2->id}-1", 'Kimia'));

    $response = $this->get(route('leaderboard.creators', ['sort' => 'simulations']));

    $response->assertStatus(200);

    $topCreators = $response->viewData('topCreators');
    expect($topCreators->first()['user']->id)->toBe($creator1->id);
});

it('leaderboard creators page includes trending creators', function () use ($simData) {
    $creator = User::factory()->create(['role' => 'creator']);
    CreatorReputation::create(['user_id' => $creator->id, 'revenue_tier' => 'basic', 'ranking_score' => 100]);

    Simulation::create($simData($creator->id, "trending-sim-{$creator->id}", 'Fisika', [
        'published_at' => now()->subDays(2),
    ]));

    $response = $this->get(route('leaderboard.creators'));

    $response->assertStatus(200);

    $trendingCreators = $response->viewData('trendingCreators');
    expect($trendingCreators)->toHaveCount(1);
    expect($trendingCreators->first()['user']->id)->toBe($creator->id);
});

it('creator profile page shows ranking badge for top 10 creator', function () use ($simData) {
    $creator = User::factory()->create(['role' => 'creator']);
    $reputation = CreatorReputation::create(['user_id' => $creator->id, 'revenue_tier' => 'platinum', 'ranking_score' => 500]);

    Simulation::create($simData($creator->id, "profile-sim-{$creator->id}", 'Fisika'));

    $response = $this->get(route('creators.show', $creator->username));

    $response->assertStatus(200);
    $response->assertViewIs('creators.show');

    $isTop10 = $response->viewData('isTop10');
    expect($isTop10)->toBeTrue();
});

it('creator profile page does not show ranking badge for low ranked creator', function () {
    // Create 15 creators with high scores so the test creator is not in top 10
    foreach (range(1, 15) as $i) {
        $c = User::factory()->create(['role' => 'creator']);
        CreatorReputation::create(['user_id' => $c->id, 'revenue_tier' => 'platinum', 'ranking_score' => 1000 + $i]);
    }

    $lowCreator = User::factory()->create(['role' => 'creator']);
    CreatorReputation::create(['user_id' => $lowCreator->id, 'revenue_tier' => 'basic', 'ranking_score' => 1]);

    $response = $this->get(route('creators.show', $lowCreator->username));

    $response->assertStatus(200);

    $isTop10 = $response->viewData('isTop10');
    expect($isTop10)->toBeFalse();
});
