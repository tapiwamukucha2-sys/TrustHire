<?php

use App\Models\User;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;

function mockGoogleUser(string $id, string $email, string $name): void
{
    $socialiteUser = (new SocialiteUser)->setRaw([])->map([
        'id' => $id,
        'email' => $email,
        'name' => $name,
        'nickname' => $name,
        'avatar' => 'https://example.com/avatar.jpg',
    ]);

    $provider = Mockery::mock(Provider::class);
    $provider->shouldReceive('stateless')->andReturnSelf();
    $provider->shouldReceive('user')->andReturn($socialiteUser);

    Socialite::shouldReceive('driver')->with('google')->andReturn($provider);
}

test('a new Google user is created and logged in, then sent to onboarding', function () {
    config(['services.google.client_id' => 'test-client-id']);
    mockGoogleUser('google-123', 'newperson@example.com', 'New Person');

    $response = $this->get('/auth/google/callback');

    $response->assertRedirect(route('onboarding.show', ['callback' => '/']));
    $this->assertAuthenticated();

    $user = User::where('email', 'newperson@example.com')->first();
    expect($user)->not->toBeNull();
    expect($user->google_id)->toBe('google-123');
    expect($user->name)->toBe('New Person');
});

test('an existing email/password user is linked to Google by matching email', function () {
    $existing = User::factory()->create(['email' => 'existing@example.com', 'google_id' => null]);

    config(['services.google.client_id' => 'test-client-id']);
    mockGoogleUser('google-456', 'existing@example.com', 'Existing Person');

    $this->get('/auth/google/callback');

    $this->assertAuthenticatedAs($existing->fresh());
    expect($existing->fresh()->google_id)->toBe('google-456');
});

test('a returning Google user is matched by google_id even if their email changed', function () {
    $existing = User::factory()->create(['email' => 'old@example.com', 'google_id' => 'google-789']);

    config(['services.google.client_id' => 'test-client-id']);
    mockGoogleUser('google-789', 'new@example.com', 'Existing Person');

    $this->get('/auth/google/callback');

    $this->assertAuthenticatedAs($existing->fresh());
});

test('redirecting to Google when it is not configured sends the user back with a message', function () {
    config(['services.google.client_id' => '']);

    $response = $this->get('/auth/google/redirect');

    $response->assertRedirect(route('login'));
    $response->assertSessionHas('status', 'Google sign-in is not configured yet.');
});
