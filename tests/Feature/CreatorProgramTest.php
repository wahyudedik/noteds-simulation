<?php

use App\Models\CreatorApplication;
use App\Models\User;

it('shows the creator program landing page to guests', function () {
    $response = $this->get(route('become-creator-page'));

    $response->assertStatus(200);
    $response->assertSee('Noteds Creator');
    $response->assertSee('Program Monetisasi');
    $response->assertSee('Revenue Sharing');
    $response->assertSee('Jadi Kreator');
});

it('shows the creator program landing page to authenticated users', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('become-creator-page'));

    $response->assertStatus(200);
    $response->assertSee('Noteds Creator');
});

it('creates a pending creator application via POST', function () {
    $user = User::factory()->create(['role' => 'user']);

    $response = $this->actingAs($user)->post(route('become-creator'), [
        'reason' => 'Saya ingin menjadi kreator dan berbagi simulasi edukasi kepada komunitas.',
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas(CreatorApplication::class, [
        'user_id' => $user->id,
        'status' => 'pending',
    ]);
    // Role should NOT change until admin approves
    $user->refresh();
    expect($user->role)->toBe('user');
});

it('does not redirect already-creators', function () {
    $user = User::factory()->create(['role' => 'creator']);

    $response = $this->actingAs($user)->post(route('become-creator'));

    $response->assertRedirect(route('dashboard'));
});
