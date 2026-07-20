<?php

use App\Models\User;
use Laravel\Socialite\Facades\Socialite;

test('google redirect returns a redirect response', function () {
    $provider = Mockery::mock('Laravel\Socialite\Two\GoogleProvider');
    $provider->shouldReceive('redirect')->andReturn(redirect('https://accounts.google.com/o/oauth2/auth'));

    Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

    $response = $this->get(route('google.login'));

    $response->assertRedirect();
});

test('new user is created and logged in via google callback', function () {
    $googleUser = (object) [
        'id' => '111058076787065481069',
        'name' => 'Wahyu Dedik',
        'email' => 'wahyudedik9@gmail.com',
    ];

    $provider = Mockery::mock('Laravel\Socialite\Two\GoogleProvider');
    $provider->shouldReceive('user')->andReturn($googleUser);

    Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

    $this->assertDatabaseMissing('users', ['email' => 'wahyudedik9@gmail.com']);

    $response = $this->get(route('google.callback'));

    $response->assertRedirect(route('dashboard', absolute: false));
    $this->assertAuthenticated();

    $user = User::where('email', 'wahyudedik9@gmail.com')->first();
    expect($user)->not->toBeNull()
        ->and($user->google_id)->toBe('111058076787065481069')
        ->and($user->password)->toBeNull();
});

test('existing user is linked and logged in via google callback', function () {
    $existingUser = User::factory()->create([
        'email' => 'wahyudedik9@gmail.com',
        'google_id' => null,
    ]);

    $googleUser = (object) [
        'id' => '111058076787065481069',
        'name' => $existingUser->name,
        'email' => $existingUser->email,
    ];

    $provider = Mockery::mock('Laravel\Socialite\Two\GoogleProvider');
    $provider->shouldReceive('user')->andReturn($googleUser);

    Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

    $response = $this->get(route('google.callback'));

    $response->assertRedirect(route('dashboard', absolute: false));
    $this->assertAuthenticated();

    $this->assertDatabaseHas('users', [
        'id' => $existingUser->id,
        'google_id' => '111058076787065481069',
    ]);
    $this->assertDatabaseCount('users', 1);
});
