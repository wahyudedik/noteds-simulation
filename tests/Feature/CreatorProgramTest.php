<?php

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

it('allows authenticated user to become a creator via POST', function () {
    $user = User::factory()->create(['role' => 'user']);

    $response = $this->actingAs($user)->post(route('become-creator'));

    $response->assertRedirect();
    $user->refresh();
    expect($user->role)->toBe('creator');
});

it('does not redirect already-creators', function () {
    $user = User::factory()->create(['role' => 'creator']);

    $response = $this->actingAs($user)->post(route('become-creator'));

    $response->assertRedirect(route('dashboard'));
});
