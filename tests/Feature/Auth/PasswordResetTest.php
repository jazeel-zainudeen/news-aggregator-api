<?php

use App\Models\User;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->user = User::factory()->create([
        'email' => 'john.doe@example.com',
    ]);
});

it('can send a password reset link', function () {
    $response = $this->postJson('/api/password/forgot', [
        'email' => $this->user->email,
    ]);

    $response->assertStatus(200)
        ->assertJson(['message' => 'Password reset link sent']);
});

it('returns validation error for non-existing email', function () {
    $response = $this->postJson('/api/password/forgot', [
        'email' => 'nonexistent@example.com',
    ]);

    $response->assertStatus(422);
});

it('can reset password with valid token', function () {
    $token = Password::createToken($this->user);

    $response = $this->postJson('/api/password/reset', [
        'token' => $token,
        'email' => $this->user->email,
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ]);

    $response->assertStatus(200)
        ->assertJson(['message' => 'Password reset successfully']);

    expect(Hash::check('newpassword123', $this->user->fresh()->password))->toBeTrue();
});

it('fails password reset with invalid token', function () {
    $response = $this->postJson('/api/password/reset', [
        'token' => 'invalid-token',
        'email' => $this->user->email,
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ]);

    $response->assertStatus(422)
        ->assertJson(['message' => 'Invalid token or email']);
});
