<?php

use App\Models\User;

beforeEach(function () {
    $this->userData = [
        'name' => 'John Doe',
        'email' => 'john.doe@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ];
});

it('can register a new user', function () {
    $response = $this->postJson('/api/register', $this->userData);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'message',
            'access_token',
            'token_type',
        ]);

    $this->assertDatabaseHas('users', [
        'email' => $this->userData['email'],
    ]);
});

it('returns validation error for missing fields', function () {
    $response = $this->postJson('/api/register', []);
    
    $response->assertStatus(422)
        ->assertJsonStructure(['errors']);
});

it('returns validation error for duplicate email', function () {
    User::factory()->create([ 'email' => $this->userData['email'] ]);

    $response = $this->postJson('/api/register', $this->userData);

    $response->assertStatus(422)
        ->assertJsonStructure(['errors' => ['email']]);
});

it('returns validation error for weak password', function () {
    $response = $this->postJson('/api/register', array_merge($this->userData, [
        'password' => '123',
        'password_confirmation' => '123',
    ]));

    $response->assertStatus(422)
        ->assertJsonStructure(['errors' => ['password']]);
});
