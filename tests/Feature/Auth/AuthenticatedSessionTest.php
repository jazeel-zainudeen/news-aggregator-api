<?php

test('user can log in with valid credentials', function () {
    $user = \App\Models\User::factory()->create([
        'password' => bcrypt('password123'),
    ]);

    $response = $this->postJson('/api/login', [
        'email' => $user->email,
        'password' => 'password123',
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure(['access_token', 'token_type']);
});

test('user cannot log in with invalid credentials', function () {
    $user = \App\Models\User::factory()->create([
        'password' => bcrypt('password123'),
    ]);

    $response = $this->postJson('/api/login', [
        'email' => $user->email,
        'password' => 'wrongpassword',
    ]);

    $response->assertStatus(401)
        ->assertJson(['message' => 'Invalid credentials']);
});

test('user cannot log in with non-existing email', function () {
    $response = $this->postJson('/api/login', [
        'email' => 'nonexistent@example.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(401)
        ->assertJson(['message' => 'Invalid credentials']);
});

test('authenticated user can log out', function () {
    $user = \App\Models\User::factory()->create();
    $token = $user->createToken('auth_token')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
        ->deleteJson('/api/logout');

    $response->assertStatus(200)
        ->assertJson(['message' => 'Logged out successfully']);

    $this->assertDatabaseMissing('personal_access_tokens', [
        'tokenable_id' => $user->id,
    ]);
});

test('unauthenticated user cannot log out', function () {
    $response = $this->deleteJson('/api/logout');

    $response->assertStatus(401)
        ->assertJson(['message' => 'Unauthenticated.']);
});
