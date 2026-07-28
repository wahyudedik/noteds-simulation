<?php

use App\Models\CreatorAd;
use App\Models\CreatorReputation;
use App\Models\Simulation;
use App\Models\User;
use App\Services\PlayRevenueService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->service = app(PlayRevenueService::class);
});

/**
 * Helper: insert ad impressions in batches to avoid SQLite variable limit.
 * Uses DB::table() for reliable bulk inserts in tests.
 */
function insertImpressions(int $simulationId, int $count): void
{
    $batchSize = 100;
    $now = now()->toDateTimeString();

    for ($offset = 0; $offset < $count; $offset += $batchSize) {
        $currentBatch = min($batchSize, $count - $offset);
        $batch = [];

        for ($i = 0; $i < $currentBatch; $i++) {
            $batch[] = [
                'ad_type' => 'creator',
                'ad_id' => 1,
                'simulation_id' => $simulationId,
                'position' => 'pre-roll',
                'clicked' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('ad_impressions')->insert($batch);
    }
}

it('calculates revenue for a simulation', function () {
    $user = User::create([
        'name' => 'Creator',
        'username' => 'creator1',
        'email' => 'creator@test.com',
        'password' => bcrypt('password'),
        'role' => 'creator',
    ]);

    CreatorReputation::create([
        'user_id' => $user->id,
        'score' => 50,
        'revenue_tier' => 'basic',
        'total_revenue' => 0,
    ]);

    $simulation = Simulation::create([
        'user_id' => $user->id,
        'title' => 'Test Simulation',
        'slug' => 'test-simulation',
        'category' => 'Fisika',
        'zip_path' => 'simulations/test.zip',
        'is_published' => true,
    ]);

    insertImpressions($simulation->id, 5000);

    $revenue = $this->service->calculateRevenue($simulation);

    // 5000 impressions / 1000 * 10000 RPM * 55% (basic tier) = 27500
    expect($revenue)->toBe(27500.0);
});

it('returns higher revenue for more impressions', function () {
    $user = User::create([
        'name' => 'Creator',
        'username' => 'creator2',
        'email' => 'creator2@test.com',
        'password' => bcrypt('password'),
        'role' => 'creator',
    ]);

    CreatorReputation::create([
        'user_id' => $user->id,
        'score' => 50,
        'revenue_tier' => 'basic',
        'total_revenue' => 0,
    ]);

    $sim1 = Simulation::create([
        'user_id' => $user->id,
        'title' => 'Low Traffic Sim',
        'slug' => 'low-traffic',
        'category' => 'Fisika',
        'zip_path' => 'simulations/test.zip',
        'is_published' => true,
    ]);

    $sim2 = Simulation::create([
        'user_id' => $user->id,
        'title' => 'High Traffic Sim',
        'slug' => 'high-traffic',
        'category' => 'Kimia',
        'zip_path' => 'simulations/test.zip',
        'is_published' => true,
    ]);

    insertImpressions($sim1->id, 1000);
    insertImpressions($sim2->id, 5000);

    $revenue1 = $this->service->calculateRevenue($sim1);
    $revenue2 = $this->service->calculateRevenue($sim2);

    expect($revenue2)->toBeGreaterThan($revenue1);
});

it('returns revenue breakdown per simulation', function () {
    $user = User::create([
        'name' => 'Creator',
        'username' => 'creator3',
        'email' => 'creator3@test.com',
        'password' => bcrypt('password'),
        'role' => 'creator',
    ]);

    CreatorReputation::create([
        'user_id' => $user->id,
        'score' => 70,
        'revenue_tier' => 'verified',
        'total_revenue' => 0,
    ]);

    $sim = Simulation::create([
        'user_id' => $user->id,
        'title' => 'Breakdown Sim',
        'slug' => 'breakdown-sim',
        'category' => 'Biologi',
        'zip_path' => 'simulations/test.zip',
        'is_published' => true,
    ]);

    insertImpressions($sim->id, 3000);

    $breakdown = $this->service->getRevenueBreakdown($user);

    expect($breakdown['tier'])->toBe('verified')
        ->and($breakdown['creator_share_percent'])->toBe(65)
        ->and($breakdown['total_revenue'])->toBeGreaterThan(0)
        ->and($breakdown['simulations'])->toBeArray()
        ->and($breakdown['simulations'])->toHaveCount(1);
});

it('calculates monthly revenue', function () {
    $user = User::create([
        'name' => 'Creator',
        'username' => 'creator4',
        'email' => 'creator4@test.com',
        'password' => bcrypt('password'),
        'role' => 'creator',
    ]);

    CreatorReputation::create([
        'user_id' => $user->id,
        'score' => 50,
        'revenue_tier' => 'basic',
        'total_revenue' => 0,
    ]);

    $sim = Simulation::create([
        'user_id' => $user->id,
        'title' => 'Monthly Sim',
        'slug' => 'monthly-sim',
        'category' => 'Fisika',
        'zip_path' => 'simulations/test.zip',
        'is_published' => true,
    ]);

    insertImpressions($sim->id, 2000);

    $monthlyRevenue = $this->service->getMonthlyRevenue($user, now());

    expect($monthlyRevenue)->toBeGreaterThan(0);
});

it('handles zero impressions gracefully', function () {
    $user = User::create([
        'name' => 'Creator',
        'username' => 'creator5',
        'email' => 'creator5@test.com',
        'password' => bcrypt('password'),
        'role' => 'creator',
    ]);

    $sim = Simulation::create([
        'user_id' => $user->id,
        'title' => 'No Impressions Sim',
        'slug' => 'no-impressions',
        'category' => 'Fisika',
        'zip_path' => 'simulations/test.zip',
        'is_published' => true,
    ]);

    $revenue = $this->service->calculateRevenue($sim);

    expect($revenue)->toBe(0.0);
});

it('returns correct revenue tier share', function () {
    $user = User::create([
        'name' => 'Creator',
        'username' => 'creator6',
        'email' => 'creator6@test.com',
        'password' => bcrypt('password'),
        'role' => 'creator',
    ]);

    CreatorReputation::create([
        'user_id' => $user->id,
        'score' => 90,
        'revenue_tier' => 'platinum',
        'total_revenue' => 0,
    ]);

    $sim = Simulation::create([
        'user_id' => $user->id,
        'title' => 'Platinum Sim',
        'slug' => 'platinum-sim',
        'category' => 'Fisika',
        'zip_path' => 'simulations/test.zip',
        'is_published' => true,
    ]);

    insertImpressions($sim->id, 5000);

    $revenue = $this->service->calculateRevenue($sim);

    // 5000 impressions / 1000 * 10000 RPM * 85% (platinum) = 42500
    expect($revenue)->toBe(42500.0);
});

it('returns daily revenue data for chart', function () {
    $user = User::create([
        'name' => 'Creator',
        'username' => 'creator7',
        'email' => 'creator7@test.com',
        'password' => bcrypt('password'),
        'role' => 'creator',
    ]);

    $dailyData = $this->service->getDailyRevenue($user, 7);

    expect($dailyData)->toBeArray()
        ->and($dailyData)->toHaveCount(7);

    foreach ($dailyData as $day) {
        expect($day)->toHaveKeys(['date', 'label', 'impressions', 'gross_revenue', 'creator_revenue']);
    }
});

it('processes daily revenue for all creators', function () {
    $user = User::create([
        'name' => 'Creator',
        'username' => 'creator8',
        'email' => 'creator8@test.com',
        'password' => bcrypt('password'),
        'role' => 'creator',
    ]);

    $reputation = CreatorReputation::create([
        'user_id' => $user->id,
        'score' => 50,
        'revenue_tier' => 'basic',
        'total_revenue' => 0,
    ]);

    $sim = Simulation::create([
        'user_id' => $user->id,
        'title' => 'Process Sim',
        'slug' => 'process-sim',
        'category' => 'Fisika',
        'zip_path' => 'simulations/test.zip',
        'is_published' => true,
    ]);

    CreatorAd::create([
        'simulation_id' => $sim->id,
        'user_id' => $user->id,
        'provider' => 'adsense',
        'publisher_id' => 'pub-123',
        'ad_config' => [],
        'review_status' => 'approved',
        'is_active' => true,
        'impressions' => 5000,
        'clicks' => 100,
        'revenue' => 0,
    ]);

    $count = $this->service->processDailyRevenue();

    expect($count)->toBeGreaterThan(0);

    $reputation->refresh();
    expect($reputation->total_revenue)->toBeGreaterThan(0);
});

it('provides revenue detail page for authenticated creator', function () {
    $user = User::create([
        'name' => 'Creator',
        'username' => 'creator9',
        'email' => 'creator9@test.com',
        'password' => bcrypt('password'),
        'role' => 'creator',
    ]);

    // MustVerifyEmail checks the model attribute, not just DB column
    $user->markEmailAsVerified();

    $this->actingAs($user);

    $response = $this->get(route('studio.ads-revenue.detail'));

    $response->assertStatus(200);
});
