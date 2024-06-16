<?php

use Laravel\Fortify\Features;
use Laravel\Jetstream\Jetstream;

use function Pest\Laravel\post;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
})->skip(function () {
    return !Features::enabled(Features::registration());
}, 'Registration support is not enabled.');

test('registration screen cannot be rendered if support is disabled', function () {
    $response = $this->get('/register');

    $response->assertStatus(404);
})->skip(function () {
    return Features::enabled(Features::registration());
}, 'Registration support is enabled.');

test('new users can register', function () {
    $response = post('/register', [
        'name' => 'Test User',
        'nip' => '1234',
        'email' => 'test@example.com',
        'phone' => '123',
        'gender' => 'male',
        'address' => 'abc',
        'city' => 'abc',
        'password' => 'password',
        'password_confirmation' => 'password',
        'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature(),
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect('/dashboard');
})->skip(function () {
    return !Features::enabled(Features::registration());
}, 'Registration support is not enabled.');
