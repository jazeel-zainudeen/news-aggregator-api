<?php

namespace Database\Factories;

use App\Models\Author;
use App\Models\Category;
use App\Models\Source;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\NewsArticle>
 */
class NewsArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'source_id' => Source::factory(),
            'author_id' => Author::factory(),
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'published_at' => fake()->dateTime(),
            'url_to_image' => fake()->imageUrl(),
            'content' => fake()->paragraph(),
        ];
    }
}
