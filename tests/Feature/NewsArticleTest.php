<?php

use App\Models\Category;
use App\Models\NewsArticle;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

test('guest cannot access articles API', function () {
    $this->getJson('/api/articles')->assertUnauthorized();
});

test('authenticated user can list articles', function () {
    Sanctum::actingAs(User::factory()->create());
    NewsArticle::factory()->count(5)->create();
    
    $response = $this->getJson('/api/articles');
    $response->assertOk()->assertJsonStructure(['data']);
});

test('articles API supports filtering by search', function () {
    Sanctum::actingAs(User::factory()->create());
    NewsArticle::factory()->create(['title' => 'Unique Test Article']);
    NewsArticle::factory()->create(['title' => 'Test Article']);
    
    $response = $this->getJson('/api/articles?search=Unique');
    $response->assertOk()->assertJsonCount(0, 'data');
});

test('articles API supports filtering by category', function () {
    Sanctum::actingAs(User::factory()->create());
    $newsArticle1 = NewsArticle::factory()->create();
    NewsArticle::factory()->create();
    
    $response = $this->getJson('/api/articles?category=' . $newsArticle1->category_id);
    $response->assertOk()->assertJsonCount(1, 'data');
});

test('user can view a specific article', function () {
    Sanctum::actingAs(User::factory()->create());
    $article = NewsArticle::factory()->create();
    
    $this->getJson("/api/articles/{$article->id}")->assertOk()->assertJson(['data' => ['id' => $article->id]]);
});

test('returns 404 for non-existing article', function () {
    Sanctum::actingAs(User::factory()->create());
    
    $this->getJson('/api/articles/999')->assertNotFound();
});

test('authenticated user can get personalized feed', function () {
    Sanctum::actingAs($user = User::factory()->create());

    $category = Category::factory()->create();
    
    // Simulate user preferences
    $user->preferences()->create(['preferable_type' => 'category', 'preferable_id' => $category->id]);
    
    NewsArticle::factory()->create(['category_id' => $category->id]);
    NewsArticle::factory()->create();
    
    $response = $this->getJson('/api/articles/personalized');
    $response->assertOk()->assertJsonCount(1, 'data');
});
