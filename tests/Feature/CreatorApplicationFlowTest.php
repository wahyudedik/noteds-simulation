<?php

use App\Models\CreatorApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('allows user to submit creator application', function () {
    $user = User::factory()->create(['role' => 'user']);

    $response = $this->actingAs($user)
        ->post(route('become-creator'), [
            'reason' => 'Saya ingin membuat simulasi interaktif untuk pendidikan anak Indonesia.',
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('status');

    $this->assertDatabaseHas('creator_applications', [
        'user_id' => $user->id,
        'status' => 'pending',
    ]);
});

it('prevents creator from submitting application', function () {
    $creator = User::factory()->create(['role' => 'creator']);

    $response = $this->actingAs($creator)
        ->post(route('become-creator'), [
            'reason' => 'Saya ingin membuat simulasi interaktif.',
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('status');
    $this->assertDatabaseCount('creator_applications', 0);
});

it('prevents duplicate pending applications', function () {
    $user = User::factory()->create(['role' => 'user']);

    // First application
    $this->actingAs($user)->post(route('become-creator'), [
        'reason' => 'Pertama kali mengajukan menjadi kreator.',
    ]);

    // Second application (should be blocked)
    $response = $this->actingAs($user)
        ->post(route('become-creator'), [
            'reason' => 'Kedua kali mengajukan menjadi kreator.',
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('error');
    $this->assertDatabaseCount('creator_applications', 1);
});

it('validates reason minimum length', function () {
    $user = User::factory()->create(['role' => 'user']);

    $response = $this->actingAs($user)
        ->post(route('become-creator'), [
            'reason' => 'Short',
        ]);

    $response->assertSessionHasErrors('reason');
});

it('allows user to cancel pending application', function () {
    $user = User::factory()->create(['role' => 'user']);

    $application = CreatorApplication::create([
        'user_id' => $user->id,
        'reason' => 'Saya ingin menjadi kreator.',
        'status' => 'pending',
    ]);

    $response = $this->actingAs($user)
        ->post(route('cancel-application'));

    $response->assertRedirect();
    $response->assertSessionHas('status');

    $application->refresh();
    expect($application->status)->toBe('rejected');
    expect($application->review_notes)->toBe('Dibatalkan oleh pengguna');
});

it('allows admin to approve creator application', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create(['role' => 'user']);

    $application = CreatorApplication::create([
        'user_id' => $user->id,
        'reason' => 'Saya ingin membuat simulasi interaktif untuk pendidikan.',
        'status' => 'pending',
    ]);

    $response = $this->actingAs($admin)
        ->post(route('admin.creators.applications.approve', $application));

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $application->refresh();
    $user->refresh();

    expect($application->status)->toBe('approved');
    expect($application->reviewed_by)->toBe($admin->id);
    expect($user->role)->toBe('creator');
    $this->assertDatabaseHas('creator_reputations', ['user_id' => $user->id]);
});

it('allows admin to reject creator application', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create(['role' => 'user']);

    $application = CreatorApplication::create([
        'user_id' => $user->id,
        'reason' => 'Saya ingin menjadi kreator.',
        'status' => 'pending',
    ]);

    $response = $this->actingAs($admin)
        ->post(route('admin.creators.applications.reject', $application), [
            'review_notes' => 'Alasan belum cukup memadai.',
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $application->refresh();
    expect($application->status)->toBe('rejected');
    expect($application->review_notes)->toBe('Alasan belum cukup memadai.');
});

it('prevents approving already processed application', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create(['role' => 'user']);

    $application = CreatorApplication::create([
        'user_id' => $user->id,
        'reason' => 'Saya ingin menjadi kreator.',
        'status' => 'approved',
        'reviewed_by' => $admin->id,
        'reviewed_at' => now(),
    ]);

    $response = $this->actingAs($admin)
        ->post(route('admin.creators.applications.approve', $application));

    $response->assertRedirect();
    $response->assertSessionHas('error');
});

it('prevents non-admin from approving applications', function () {
    $user = User::factory()->create(['role' => 'user']);
    $applicant = User::factory()->create(['role' => 'user']);

    $application = CreatorApplication::create([
        'user_id' => $applicant->id,
        'reason' => 'Saya ingin menjadi kreator.',
        'status' => 'pending',
    ]);

    $response = $this->actingAs($user)
        ->post(route('admin.creators.applications.approve', $application));

    $response->assertForbidden();
});

it('allows admin to view pending applications', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    CreatorApplication::create([
        'user_id' => User::factory()->create(['role' => 'user'])->id,
        'reason' => 'Alasan pertama.',
        'status' => 'pending',
    ]);

    $response = $this->actingAs($admin)
        ->get(route('admin.creators.applications'));

    $response->assertOk();
});

it('prevents unauthenticated access to application routes', function () {
    $this->post(route('become-creator'))->assertRedirect('/login');
    $this->post(route('cancel-application'))->assertRedirect('/login');
});
