<?php

use App\Models\OtpCode;
use App\Models\User;

test('phone OTP flow logs a new user in and sends them to onboarding', function () {
    $phone = '+263771111111';

    $this->post('/otp/send', ['phone' => $phone])->assertRedirect();

    $code = OtpCode::where('phone', $phone)->latest('id')->first()->code;

    $response = $this->post('/otp/verify', ['phone' => $phone, 'code' => $code]);
    $response->assertRedirect(route('onboarding.show', ['callback' => '/']));

    $this->assertAuthenticated();
    expect(User::where('phone', $phone)->exists())->toBeTrue();
});

test('an invalid OTP code is rejected', function () {
    $phone = '+263772222222';
    $this->post('/otp/send', ['phone' => $phone]);

    $response = $this->post('/otp/verify', ['phone' => $phone, 'code' => '000000']);

    $response->assertSessionHasErrors('code');
    $this->assertGuest();
});

test('OTP requests are rate limited after 5 attempts', function () {
    $phone = '+263773333333';

    for ($i = 0; $i < 5; $i++) {
        $this->post('/otp/send', ['phone' => $phone]);
    }

    $response = $this->post('/otp/send', ['phone' => $phone]);

    $response->assertSessionHasErrors('phone');
});

test('email signup creates an account and logs in', function () {
    $response = $this->post('/register', [
        'email' => 'test@example.com',
        'password' => 'Str0ng!Pass',
        'password_confirmation' => 'Str0ng!Pass',
    ]);

    $response->assertRedirect(route('onboarding.show', ['callback' => '/']));
    $this->assertAuthenticated();
    expect(User::where('email', 'test@example.com')->exists())->toBeTrue();
});

test('email signup rejects a weak password', function () {
    $response = $this->post('/register', [
        'email' => 'weak@example.com',
        'password' => 'weakpass',
        'password_confirmation' => 'weakpass',
    ]);

    $response->assertSessionHasErrors('password');
    $this->assertGuest();
});

test('email signup rejects a duplicate email', function () {
    User::factory()->create(['email' => 'dupe@example.com']);

    $response = $this->post('/register', [
        'email' => 'dupe@example.com',
        'password' => 'Str0ng!Pass',
        'password_confirmation' => 'Str0ng!Pass',
    ]);

    $response->assertSessionHasErrors('email');
});

test('a user can log in with email and password', function () {
    User::factory()->create(['email' => 'login@example.com', 'password' => bcrypt('Str0ng!Pass')]);

    $response = $this->post('/login/email', [
        'email' => 'login@example.com',
        'password' => 'Str0ng!Pass',
    ]);

    $response->assertRedirect(route('onboarding.show', ['callback' => '/']));
    $this->assertAuthenticated();
});

test('a guest is redirected to login from a protected page', function () {
    $response = $this->get('/profile');

    $response->assertRedirect(route('login'));
});

test('onboarding completes the profile and redirects to the callback', function () {
    $user = User::factory()->create(['profile_completed_at' => null]);

    $response = $this->actingAs($user)->post('/onboarding', [
        'name' => 'Jane Doe',
        'account_type' => 'INDIVIDUAL',
        'age_confirmed' => '1',
        'terms_accepted' => '1',
        'callback' => '/',
    ]);

    $response->assertRedirect('/');
    expect($user->fresh()->name)->toBe('Jane Doe');
    expect($user->fresh()->profile_completed_at)->not->toBeNull();
});

test('an already-onboarded user is redirected away from onboarding', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/onboarding');

    $response->assertRedirect('/');
});
