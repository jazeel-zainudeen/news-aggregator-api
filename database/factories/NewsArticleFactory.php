<?php

namespace Database\Factories;

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
            'category' => $this->faker->randomElements(['business', 'entertainment', 'general', 'health', 'science', 'sports', 'technology']),
            'source' => $this->faker->companySuffix(),
            'author' => $this->faker->name(),
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'published_at' => $this->faker->dateTime(),
            'url_to_image' => $this->faker->imageUrl(),
            'content' => $this->faker->paragraph(),
        ];
    }
}
