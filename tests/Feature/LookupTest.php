<?php

use App\Models\Category;
use App\Models\Source;
use App\Models\Author;
use function Pest\Laravel\actingAs;
use App\Models\User;

describe('Lookup API', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
    });

    test('it returns a paginated list of categories', function () {
        Category::factory()->count(15)->create();

        actingAs($this->user)->getJson('/api/lookup/categories?per_page=10')
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name'],
                ],
                'links',
                'meta',
            ]);
    });

    test('it returns a paginated list of sources', function () {
        Source::factory()->count(15)->create();

        actingAs($this->user)->getJson('/api/lookup/sources?per_page=10')
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name'],
                ],
                'links',
                'meta',
            ]);
    });

    test('it returns a paginated list of authors', function () {
        Author::factory()->count(15)->create();

        actingAs($this->user)->getJson('/api/lookup/authors?per_page=10')
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name'],
                ],
                'links',
                'meta',
            ]);
    });
});
