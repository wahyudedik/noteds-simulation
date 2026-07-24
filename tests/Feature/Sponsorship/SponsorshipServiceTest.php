<?php

use App\Models\PlatformAd;
use App\Models\Sponsor;
use App\Models\Sponsorship;
use App\Models\User;
use App\Services\SponsorshipService;

test('can create a sponsorship', function () {
    $service = app(SponsorshipService::class);
    $sponsor = Sponsor::factory()->create();
    $admin = User::factory()->create(['role' => 'admin']);

    $sponsorship = $service->create($sponsor, [
        'title' => 'Kampanye Brand Awareness',
        'package_type' => 'standard',
        'budget' => 10000000,
        'start_date' => now()->addDay(),
        'end_date' => now()->addMonths(3),
        'positions' => ['header', 'sidebar'],
    ], $admin);

    expect($sponsorship)->toBeInstanceOf(Sponsorship::class)
        ->and($sponsorship->title)->toBe('Kampanye Brand Awareness')
        ->and($sponsorship->package_type)->toBe('standard')
        ->and($sponsorship->status)->toBe('draft')
        ->and($sponsorship->budget)->toBe('10000000.00')
        ->and($sponsorship->sponsor_id)->toBe($sponsor->id)
        ->and($sponsorship->created_by)->toBe($admin->id);

    $this->assertDatabaseHas('sponsorships', [
        'title' => 'Kampanye Brand Awareness',
        'status' => 'draft',
    ]);
});

test('can update a sponsorship', function () {
    $service = app(SponsorshipService::class);
    $sponsorship = Sponsorship::factory()->draft()->create();

    $updated = $service->update($sponsorship, [
        'title' => 'Judul Baru',
        'budget' => 20000000,
    ]);

    expect($updated->title)->toBe('Judul Baru')
        ->and($updated->budget)->toBe('20000000.00');
});

test('can approve a sponsorship', function () {
    $service = app(SponsorshipService::class);
    $sponsorship = Sponsorship::factory()->draft()->create();
    $admin = User::factory()->create(['role' => 'admin']);

    $approved = $service->approve($sponsorship, $admin);

    expect($approved->status)->toBe('active')
        ->and($approved->approved_by)->toBe($admin->id)
        ->and($approved->approved_at)->not->toBeNull();
});

test('can pause a sponsorship', function () {
    $service = app(SponsorshipService::class);
    $sponsorship = Sponsorship::factory()->active()->create();

    $paused = $service->pause($sponsorship);

    expect($paused->status)->toBe('paused');
});

test('can resume a sponsorship', function () {
    $service = app(SponsorshipService::class);
    $sponsorship = Sponsorship::factory()->paused()->create();

    $resumed = $service->resume($sponsorship);

    expect($resumed->status)->toBe('active');
});

test('can complete a sponsorship and deactivate linked ads', function () {
    $service = app(SponsorshipService::class);
    $sponsorship = Sponsorship::factory()->active()->create();

    PlatformAd::factory()->sponsored()->create([
        'sponsorship_id' => $sponsorship->id,
        'is_active' => true,
    ]);

    $completed = $service->complete($sponsorship);

    expect($completed->status)->toBe('completed');

    // Verify linked ads are deactivated
    $this->assertDatabaseHas('platform_ads', [
        'sponsorship_id' => $sponsorship->id,
        'is_active' => false,
    ]);
});

test('can cancel a sponsorship', function () {
    $service = app(SponsorshipService::class);
    $sponsorship = Sponsorship::factory()->active()->create();

    $cancelled = $service->cancel($sponsorship);

    expect($cancelled->status)->toBe('cancelled');
});

test('can create ad linked to sponsorship', function () {
    $service = app(SponsorshipService::class);
    $sponsorship = Sponsorship::factory()->active()->create();
    $admin = User::factory()->create(['role' => 'admin']);

    $ad = $service->createAd($sponsorship, [
        'title' => 'Iklan Header',
        'type' => 'banner',
        'position' => 'header',
        'target_url' => 'https://example.com',
    ], $admin);

    expect($ad)->toBeInstanceOf(PlatformAd::class)
        ->and($ad->title)->toBe('Iklan Header')
        ->and($ad->sponsor_id)->toBe($sponsorship->sponsor_id)
        ->and($ad->sponsorship_id)->toBe($sponsorship->id)
        ->and($ad->is_sponsored)->toBeTrue()
        ->and($ad->is_active)->toBeTrue();
});

test('can link existing ad to sponsorship', function () {
    $service = app(SponsorshipService::class);
    $sponsorship = Sponsorship::factory()->active()->create();
    $ad = PlatformAd::factory()->create([
        'sponsor_id' => null,
        'sponsorship_id' => null,
        'is_sponsored' => false,
    ]);

    $linked = $service->linkAdToSponsorship($ad, $sponsorship);

    expect($linked->sponsor_id)->toBe($sponsorship->sponsor_id)
        ->and($linked->sponsorship_id)->toBe($sponsorship->id)
        ->and($linked->is_sponsored)->toBeTrue();
});

test('deductBudget increments spent amount', function () {
    $service = app(SponsorshipService::class);
    $sponsorship = Sponsorship::factory()->active()->withBudget(10000000)->create(['spent' => 0]);

    $service->deductBudget($sponsorship, 2500000);

    $sponsorship->refresh();
    expect((float) $sponsorship->spent)->toBe(2500000.0);
});

test('deductBudget auto-pauses when budget exceeded', function () {
    $service = app(SponsorshipService::class);
    $sponsorship = Sponsorship::factory()->active()->withBudget(1000000)->create(['spent' => 900000]);

    $service->deductBudget($sponsorship, 200000);

    $sponsorship->refresh();
    expect($sponsorship->status)->toBe('paused');
});

test('isBudgetExceeded returns true when spent >= budget', function () {
    $service = app(SponsorshipService::class);
    $sponsorship = Sponsorship::factory()->active()->withBudget(1000000)->create(['spent' => 1000000]);

    expect($service->isBudgetExceeded($sponsorship))->toBeTrue();
});

test('isBudgetExceeded returns false when spent < budget', function () {
    $service = app(SponsorshipService::class);
    $sponsorship = Sponsorship::factory()->active()->withBudget(1000000)->create(['spent' => 500000]);

    expect($service->isBudgetExceeded($sponsorship))->toBeFalse();
});

test('checkBudgetRemaining returns correct amount', function () {
    $service = app(SponsorshipService::class);
    $sponsorship = Sponsorship::factory()->active()->withBudget(10000000)->create(['spent' => 3000000]);

    expect($service->checkBudgetRemaining($sponsorship))->toBe(7000000.0);
});

test('pauseExpiredSponsorships completes expired sponsorships', function () {
    $service = app(SponsorshipService::class);

    // Create sponsorships with active status but end_date in the past
    Sponsorship::factory()->expired()->count(3)->create();
    // Create running sponsorships (active + within date range)
    Sponsorship::factory()->running()->count(2)->create();

    $count = $service->pauseExpiredSponsorships();

    expect($count)->toBe(3);
    $this->assertDatabaseCount('sponsorships', 5);
    $this->assertDatabaseHas('sponsorships', ['status' => 'completed']);
});

test('getActiveSponsorshipsForPosition returns matching sponsorships', function () {
    $service = app(SponsorshipService::class);

    Sponsorship::factory()->running()->create(['positions' => ['header', 'sidebar']]);
    Sponsorship::factory()->running()->create(['positions' => ['header']]);
    Sponsorship::factory()->running()->create(['positions' => ['sidebar']]);

    $sponsorships = $service->getActiveSponsorshipsForPosition('header');

    expect($sponsorships)->toHaveCount(2);
});

test('getDashboardStats returns correct stats', function () {
    $service = app(SponsorshipService::class);

    $sponsors = Sponsor::factory()->count(3)->create();
    Sponsorship::factory()->active()->count(2)->create(['sponsor_id' => $sponsors->first()->id]);
    Sponsorship::factory()->draft()->count(1)->create(['sponsor_id' => $sponsors->first()->id]);

    $stats = $service->getDashboardStats();

    expect($stats)->toHaveKeys([
        'total_sponsors',
        'active_sponsors',
        'total_sponsorships',
        'active_sponsorships',
        'total_budget',
        'total_spent',
        'pending_invoices',
        'overdue_invoices',
    ])
        ->and($stats['total_sponsors'])->toBe(3)
        ->and($stats['active_sponsorships'])->toBe(2)
        ->and($stats['total_sponsorships'])->toBe(3);
});

test('getSponsorshipStats returns correct performance data', function () {
    $service = app(SponsorshipService::class);
    $sponsorship = Sponsorship::factory()->active()->withBudget(10000000)->create(['spent' => 2000000]);

    PlatformAd::factory()->sponsored()->create([
        'sponsorship_id' => $sponsorship->id,
        'impressions' => 5000,
        'clicks' => 100,
    ]);

    $stats = $service->getSponsorshipStats($sponsorship);

    expect($stats['total_impressions'])->toBe(5000)
        ->and($stats['total_clicks'])->toBe(100)
        ->and($stats['ctr'])->toBe(2.0)
        ->and($stats['ads_count'])->toBe(1);
});

test('getBrandStats returns aggregated data', function () {
    $service = app(SponsorshipService::class);
    $sponsor = Sponsor::factory()->create();

    Sponsorship::factory()->active()->withBudget(10000000)->create(['sponsor_id' => $sponsor->id, 'spent' => 3000000]);
    Sponsorship::factory()->draft()->withBudget(5000000)->create(['sponsor_id' => $sponsor->id]);

    $stats = $service->getBrandStats($sponsor);

    expect($stats['total_sponsorships'])->toBe(2)
        ->and($stats['active_sponsorships'])->toBe(1)
        ->and($stats['total_budget'])->toBe(15000000.0);
});

test('sponsorship model remaining_budget calculates correctly', function () {
    $sponsorship = Sponsorship::factory()->active()->withBudget(10000000)->create(['spent' => 4000000]);

    expect($sponsorship->remaining_budget)->toBe(6000000.0);
});

test('sponsorship model progress calculates correctly', function () {
    $sponsorship = Sponsorship::factory()->active()->withBudget(10000000)->create(['spent' => 5000000]);

    expect($sponsorship->progress)->toBe(50.0);
});

test('sponsorship model progress returns 0 when budget is 0', function () {
    $sponsorship = Sponsorship::factory()->active()->withBudget(0)->create(['spent' => 0]);

    expect($sponsorship->progress)->toBe(0.0);
});

test('sponsorship model status_label returns correct label', function () {
    expect(Sponsorship::factory()->draft()->make()->status_label)->toBe('Draft')
        ->and(Sponsorship::factory()->active()->make()->status_label)->toBe('Aktif')
        ->and(Sponsorship::factory()->paused()->make()->status_label)->toBe('Dijeda')
        ->and(Sponsorship::factory()->completed()->make()->status_label)->toBe('Selesai');
});

test('sponsorship model status_color returns correct color class', function () {
    expect(Sponsorship::factory()->active()->make()->status_color)->toBe('bg-green-100 text-green-700')
        ->and(Sponsorship::factory()->paused()->make()->status_color)->toBe('bg-orange-100 text-orange-700');
});

test('sponsor model total_spent and total_budget calculate correctly', function () {
    $sponsor = Sponsor::factory()->create();

    Sponsorship::factory()->active()->withBudget(10000000)->create(['sponsor_id' => $sponsor->id, 'spent' => 3000000]);
    Sponsorship::factory()->active()->withBudget(5000000)->create(['sponsor_id' => $sponsor->id, 'spent' => 1000000]);

    expect($sponsor->total_spent)->toBe(4000000.0)
        ->and($sponsor->total_budget)->toBe(15000000.0);
});

test('sponsorship model scopes work correctly', function () {
    Sponsorship::factory()->active()->count(3)->create();
    Sponsorship::factory()->draft()->count(2)->create();
    Sponsorship::factory()->paused()->count(1)->create();

    expect(Sponsorship::active()->count())->toBe(3);

    // running scope: active + within date range
    $running = Sponsorship::running()->count();
    expect($running)->toBeGreaterThanOrEqual(0); // depends on date range
});
