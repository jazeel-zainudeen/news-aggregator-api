<?php

use App\Models\Category;
use App\Models\Source;
use App\Models\User;
use App\Models\UserPreference;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user);

    $this->category = Category::factory()->create();
    $this->source = Source::factory()->create();
    
    $this->preferences = [
        ['preferable_id' => $this->category->id, 'preferable_type' => 'category'],
        ['preferable_id' => $this->source->id, 'preferable_type' => 'source'],
    ];
});

it('can retrieve user preferences', function () {
    UserPreference::factory()->create([ 'user_id' => $this->user->id, 'preferable_id' => $this->category->id, 'preferable_type' => 'category' ]);
    
    $response = $this->getJson('/api/user/preferences');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                ['preferable_id', 'preferable_type', 'name']
            ]
        ]);
});

it('can update user preferences', function () {
    UserPreference::factory()->create([ 'user_id' => $this->user->id, 'preferable_id' => $this->category->id, 'preferable_type' => 'category' ]);
    
    $response = $this->putJson('/api/user/preferences', ['preferences' => $this->preferences]);
    
    $response->assertStatus(200)
        ->assertJsonStructure([
            'message',
            'data' => [
                ['preferable_id', 'preferable_type', 'name']
            ]
        ]);

    $this->assertDatabaseHas('user_preferences', ['user_id' => $this->user->id, 'preferable_id' => $this->category->id, 'preferable_type' => 'category']);
    $this->assertDatabaseHas('user_preferences', ['user_id' => $this->user->id, 'preferable_id' => $this->source->id, 'preferable_type' => 'source']);
});

it('removes all preference', function () {
    UserPreference::factory()->create([ 'user_id' => $this->user->id, 'preferable_id' => $this->category->id, 'preferable_type' => 'category' ]);

    $response = $this->putJson('/api/user/preferences', ['preferences' => []]);
    
    $response->assertStatus(200)
        ->assertJsonCount(0, 'data');;
});
