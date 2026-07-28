<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('allows admin to verify a creator', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $creator = User::factory()->create(['role' => 'creator']);

    $response = $this->actingAs($admin)
        ->post(route('admin.users.verify-creator', $creator), [
            'verification_notes' => 'Verified based on quality content',
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $creator->refresh();
    expect($creator->verified_at)->not->toBeNull();
    expect($creator->verification_notes)->toBe('Verified based on quality content');
    expect($creator->isVerifiedCreator())->toBeTrue();
});

it('allows admin to verify a creator without notes', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $creator = User::factory()->create(['role' => 'creator']);

    $response = $this->actingAs($admin)
        ->post(route('admin.users.verify-creator', $creator));

    $response->assertRedirect();
    $creator->refresh();
    expect($creator->verified_at)->not->toBeNull();
    expect($creator->verification_notes)->toBeNull();
});

it('prevents verifying non-creator users', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create(['role' => 'user']);

    $response = $this->actingAs($admin)
        ->post(route('admin.users.verify-creator', $user));

    $response->assertRedirect();
    $response->assertSessionHas('error');
    $user->refresh();
    expect($user->verified_at)->toBeNull();
});

it('prevents double-verifying a creator', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $creator = User::factory()->create([
        'role' => 'creator',
        'verified_at' => now(),
    ]);

    $response = $this->actingAs($admin)
        ->post(route('admin.users.verify-creator', $creator));

    $response->assertRedirect();
    $response->assertSessionHas('error');
});

it('allows admin to revoke creator verification', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $creator = User::factory()->create([
        'role' => 'creator',
        'verified_at' => now(),
        'verification_notes' => 'Some notes',
    ]);

    $response = $this->actingAs($admin)
        ->post(route('admin.users.revoke-verification', $creator));

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $creator->refresh();
    expect($creator->verified_at)->toBeNull();
    expect($creator->verification_notes)->toBeNull();
    expect($creator->isVerifiedCreator())->toBeFalse();
});

it('prevents revoking verification from unverified creator', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $creator = User::factory()->create(['role' => 'creator']);

    $response = $this->actingAs($admin)
        ->post(route('admin.users.revoke-verification', $creator));

    $response->assertRedirect();
    $response->assertSessionHas('error');
});

it('returns correct verification badge type', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    // Platinum tier
    $platinum = User::factory()->create(['role' => 'creator', 'verified_at' => now()]);
    $platinum->reputation()->create(['revenue_tier' => 'platinum']);
    expect($platinum->verification_badge)->toBe('platinum');

    // Expert tier
    $expert = User::factory()->create(['role' => 'creator', 'verified_at' => now()]);
    $expert->reputation()->create(['revenue_tier' => 'expert']);
    expect($expert->verification_badge)->toBe('expert');

    // Default tier
    $verified = User::factory()->create(['role' => 'creator', 'verified_at' => now()]);
    expect($verified->verification_badge)->toBe('verified');

    // Unverified creator
    $unverified = User::factory()->create(['role' => 'creator']);
    expect($unverified->verification_badge)->toBeNull();
});

it('prevents unauthenticated access to verify routes', function () {
    $creator = User::factory()->create(['role' => 'creator']);

    $this->post(route('admin.users.verify-creator', $creator))->assertRedirect('/login');
    $this->post(route('admin.users.revoke-verification', $creator))->assertRedirect('/login');
});

it('prevents non-admin from verifying creators', function () {
    $user = User::factory()->create(['role' => 'user']);
    $creator = User::factory()->create(['role' => 'creator']);

    $response = $this->actingAs($user)
        ->post(route('admin.users.verify-creator', $creator));

    $response->assertForbidden();
});
