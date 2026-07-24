<?php

use App\Models\Sponsor;
use App\Models\User;

test('guest cannot access sponsor management', function () {
    $sponsor = Sponsor::factory()->create();

    $this->get(route('admin.sponsors.index'))->assertRedirect('/login');
    $this->get(route('admin.sponsors.show', $sponsor))->assertRedirect('/login');
    $this->get(route('admin.sponsors.create'))->assertRedirect('/login');
});

test('regular user cannot access sponsor management', function () {
    $user = User::factory()->create(['role' => 'user']);
    $sponsor = Sponsor::factory()->create();

    $this->actingAs($user)->get(route('admin.sponsors.index'))->assertForbidden();
    $this->actingAs($user)->get(route('admin.sponsors.show', $sponsor))->assertForbidden();
});

test('admin can access sponsor index page', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)->get(route('admin.sponsors.index'))->assertOk();
});

test('admin can see sponsors list', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    Sponsor::factory()->count(3)->create();

    $this->actingAs($admin)->get(route('admin.sponsors.index'))->assertOk();
});

test('admin can view create sponsor form', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)->get(route('admin.sponsors.create'))->assertOk();
});

test('admin can create a sponsor', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)->post(route('admin.sponsors.store'), [
        'company_name' => 'PT Teknologi Maju',
        'contact_name' => 'Budi Santoso',
        'contact_email' => 'budi@teknologi-maju.com',
        'contact_phone' => '08123456789',
        'industry' => 'Technology',
        'website_url' => 'https://teknologi-maju.com',
        'notes' => 'Partner strategis',
    ])->assertRedirect();

    $this->assertDatabaseHas('sponsors', [
        'company_name' => 'PT Teknologi Maju',
        'contact_name' => 'Budi Santoso',
    ]);
});

test('admin can view sponsor detail', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $sponsor = Sponsor::factory()->create();

    $this->actingAs($admin)->get(route('admin.sponsors.show', $sponsor))->assertOk();
});

test('admin can view edit sponsor form', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $sponsor = Sponsor::factory()->create();

    $this->actingAs($admin)->get(route('admin.sponsors.edit', $sponsor))->assertOk();
});

test('admin can update a sponsor', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $sponsor = Sponsor::factory()->create(['company_name' => 'PT Lama']);

    $this->actingAs($admin)->put(route('admin.sponsors.update', $sponsor), [
        'company_name' => 'PT Baru',
        'contact_name' => $sponsor->contact_name,
        'contact_email' => $sponsor->contact_email,
        'industry' => 'Education',
    ])->assertRedirect();

    $this->assertDatabaseHas('sponsors', [
        'id' => $sponsor->id,
        'company_name' => 'PT Baru',
    ]);
});

test('admin can view sponsor report', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $sponsor = Sponsor::factory()->create();

    $this->actingAs($admin)->get(route('admin.sponsors.report', $sponsor))->assertOk();
});

test('superadmin can access sponsor management', function () {
    $superadmin = User::factory()->create(['role' => 'superadmin']);

    $this->actingAs($superadmin)->get(route('admin.sponsors.index'))->assertOk();
});

test('sponsor index can filter by search', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    Sponsor::factory()->create(['company_name' => 'PT Maju Jaya']);
    Sponsor::factory()->create(['company_name' => 'CV Berkah']);

    $this->actingAs($admin)->get(route('admin.sponsors.index', ['search' => 'Maju']))
        ->assertOk();
});
