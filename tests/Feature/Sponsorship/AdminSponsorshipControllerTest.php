<?php

use App\Models\Sponsor;
use App\Models\Sponsorship;
use App\Models\SponsorshipInvoice;
use App\Models\User;

test('guest cannot access sponsorship management', function () {
    $sponsorship = Sponsorship::factory()->create();

    $this->get(route('admin.sponsorships.index'))->assertRedirect('/login');
    $this->get(route('admin.sponsorships.show', $sponsorship))->assertRedirect('/login');
});

test('regular user cannot access sponsorship management', function () {
    $user = User::factory()->create(['role' => 'user']);
    $sponsorship = Sponsorship::factory()->create();

    $this->actingAs($user)->get(route('admin.sponsorships.index'))->assertForbidden();
});

test('admin can access sponsorship index page', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)->get(route('admin.sponsorships.index'))->assertOk();
});

test('admin can see sponsorships list', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    Sponsorship::factory()->count(3)->create();

    $this->actingAs($admin)->get(route('admin.sponsorships.index'))->assertOk();
});

test('admin can view create sponsorship form', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)->get(route('admin.sponsorships.create'))->assertOk();
});

test('admin can create a sponsorship', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $sponsor = Sponsor::factory()->create();

    $this->actingAs($admin)->post(route('admin.sponsorships.store'), [
        'sponsor_id' => $sponsor->id,
        'title' => 'Kampanye Baru',
        'package_type' => 'standard',
        'budget' => 10000000,
        'start_date' => now()->addDay()->format('Y-m-d'),
        'end_date' => now()->addMonths(3)->format('Y-m-d'),
        'positions' => ['header', 'sidebar'],
    ])->assertRedirect();

    $this->assertDatabaseHas('sponsorships', [
        'title' => 'Kampanye Baru',
        'status' => 'draft',
    ]);
});

test('admin can view sponsorship detail', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $sponsorship = Sponsorship::factory()->create();

    $this->actingAs($admin)->get(route('admin.sponsorships.show', $sponsorship))->assertOk();
});

test('admin can view edit sponsorship form', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $sponsorship = Sponsorship::factory()->create();

    $this->actingAs($admin)->get(route('admin.sponsorships.edit', $sponsorship))->assertOk();
});

test('admin can update a sponsorship', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $sponsor = Sponsor::factory()->create();
    $sponsorship = Sponsorship::factory()->draft()->create(['sponsor_id' => $sponsor->id]);

    $this->actingAs($admin)->put(route('admin.sponsorships.update', $sponsorship), [
        'sponsor_id' => $sponsor->id,
        'title' => 'Judul Diperbarui',
        'package_type' => 'premium',
        'budget' => 20000000,
        'start_date' => $sponsorship->start_date->format('Y-m-d'),
        'end_date' => $sponsorship->end_date->format('Y-m-d'),
        'positions' => ['header'],
    ])->assertRedirect();

    $this->assertDatabaseHas('sponsorships', [
        'id' => $sponsorship->id,
        'title' => 'Judul Diperbarui',
    ]);
});

test('admin can approve a sponsorship', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $sponsorship = Sponsorship::factory()->draft()->create();

    $this->actingAs($admin)->post(route('admin.sponsorships.approve', $sponsorship))->assertRedirect();

    $this->assertDatabaseHas('sponsorships', [
        'id' => $sponsorship->id,
        'status' => 'active',
    ]);
});

test('admin can pause a sponsorship', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $sponsorship = Sponsorship::factory()->active()->create();

    $this->actingAs($admin)->post(route('admin.sponsorships.pause', $sponsorship))->assertRedirect();

    $this->assertDatabaseHas('sponsorships', [
        'id' => $sponsorship->id,
        'status' => 'paused',
    ]);
});

test('admin can resume a sponsorship', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $sponsorship = Sponsorship::factory()->paused()->create();

    $this->actingAs($admin)->post(route('admin.sponsorships.resume', $sponsorship))->assertRedirect();

    $this->assertDatabaseHas('sponsorships', [
        'id' => $sponsorship->id,
        'status' => 'active',
    ]);
});

test('admin can complete a sponsorship', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $sponsorship = Sponsorship::factory()->active()->create();

    $this->actingAs($admin)->post(route('admin.sponsorships.complete', $sponsorship))->assertRedirect();

    $this->assertDatabaseHas('sponsorships', [
        'id' => $sponsorship->id,
        'status' => 'completed',
    ]);
});

test('admin can view invoices page', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $sponsorship = Sponsorship::factory()->create();

    $this->actingAs($admin)->get(route('admin.sponsorships.invoices', $sponsorship))->assertOk();
});

test('admin can create an invoice', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $sponsorship = Sponsorship::factory()->active()->create();

    $this->actingAs($admin)->post(route('admin.sponsorships.invoices.create', $sponsorship), [
        'amount' => 5000000,
        'due_date' => now()->addDays(14)->format('Y-m-d'),
        'notes' => 'Pembayaran pertama',
    ])->assertRedirect();

    $this->assertDatabaseHas('sponsorship_invoices', [
        'sponsorship_id' => $sponsorship->id,
        'status' => 'draft',
    ]);
});

test('admin can send an invoice', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $invoice = SponsorshipInvoice::factory()->draft()->create();

    $this->actingAs($admin)->patch(route('admin.invoices.send', $invoice))->assertRedirect();

    $this->assertDatabaseHas('sponsorship_invoices', [
        'id' => $invoice->id,
        'status' => 'sent',
    ]);
});

test('admin can mark invoice as paid', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $sponsorship = Sponsorship::factory()->active()->withBudget(10000000)->create(['spent' => 0]);
    $invoice = SponsorshipInvoice::factory()->sent()->withAmount(5000000)->create([
        'sponsorship_id' => $sponsorship->id,
    ]);

    $this->actingAs($admin)->patch(route('admin.invoices.mark-paid', $invoice), [
        'payment_method' => 'bank_transfer',
    ])->assertRedirect();

    $this->assertDatabaseHas('sponsorship_invoices', [
        'id' => $invoice->id,
        'status' => 'paid',
        'payment_method' => 'bank_transfer',
    ]);
});

test('superadmin can access sponsorship management', function () {
    $superadmin = User::factory()->create(['role' => 'superadmin']);

    $this->actingAs($superadmin)->get(route('admin.sponsorships.index'))->assertOk();
});

test('sponsorship index can filter by status', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    Sponsorship::factory()->active()->count(2)->create();
    Sponsorship::factory()->draft()->count(1)->create();

    $this->actingAs($admin)->get(route('admin.sponsorships.index', ['status' => 'active']))
        ->assertOk();
});

test('sponsorship index shows dashboard stats', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    Sponsor::factory()->count(2)->create();
    Sponsorship::factory()->active()->count(3)->create();

    $response = $this->actingAs($admin)->get(route('admin.sponsorships.index'));

    $response->assertOk();
});

test('store sponsorship validates required fields', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)->post(route('admin.sponsorships.store'), [])
        ->assertSessionHasErrors(['sponsor_id', 'title', 'budget', 'start_date', 'end_date']);
});

test('store sponsor validates required fields', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)->post(route('admin.sponsors.store'), [])
        ->assertSessionHasErrors(['company_name', 'contact_name', 'contact_email']);
});
