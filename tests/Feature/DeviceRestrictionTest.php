<?php

use App\Models\User;
use App\Actions\AuthenticateLoginAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->user = User::factory()->create([
        'group' => 'user',
        'password' => Hash::make('password'),
        'device_token' => null,
    ]);
});

test('new device token is registered on first login', function () {
    expect($this->user->device_token)->toBeNull();

    $request = new Request([
        'email' => $this->user->email,
        'password' => 'password',
        'device_token' => 'test-device-uuid-123',
    ]);

    $auth = new AuthenticateLoginAttempt();
    $result = $auth($request);

    expect($result)->not->toBeNull();
    $this->user->refresh();
    expect($this->user->device_token)->toBe('test-device-uuid-123');
});

test('same device token allows login', function () {
    $this->user->update(['device_token' => 'registered-device-uuid']);

    $request = new Request([
        'email' => $this->user->email,
        'password' => 'password',
        'device_token' => 'registered-device-uuid',
    ]);

    $auth = new AuthenticateLoginAttempt();
    $result = $auth($request);

    expect($result)->not->toBeNull();
    expect($result->id)->toBe($this->user->id);
});

test('different device token throws validation exception', function () {
    $this->user->update(['device_token' => 'original-device-uuid']);

    $request = new Request([
        'email' => $this->user->email,
        'password' => 'password',
        'device_token' => 'different-device-uuid',
    ]);

    $auth = new AuthenticateLoginAttempt();

    $this->expectException(\Illuminate\Validation\ValidationException::class);
    $this->expectExceptionMessage('Perangkat ini tidak terdaftar');

    $auth($request);
});

test('admin can reset user device token', function () {
    $admin = User::factory()->create(['group' => 'admin']);
    $this->user->update(['device_token' => 'old-device-uuid']);

    actingAs($admin);
    $this->user->update(['device_token' => null]);

    $this->user->refresh();
    expect($this->user->device_token)->toBeNull();
});

test('login without device token works when no token is registered', function () {
    expect($this->user->device_token)->toBeNull();

    $request = new Request([
        'email' => $this->user->email,
        'password' => 'password',
        'device_token' => null,
    ]);

    $auth = new AuthenticateLoginAttempt();
    $result = $auth($request);

    expect($result)->not->toBeNull();
});

test('wrong password returns null regardless of device token', function () {
    $this->user->update(['device_token' => 'some-device']);

    $request = new Request([
        'email' => $this->user->email,
        'password' => 'wrong-password',
        'device_token' => 'some-device',
    ]);

    $auth = new AuthenticateLoginAttempt();
    $result = $auth($request);

    expect($result)->toBeNull();
});
