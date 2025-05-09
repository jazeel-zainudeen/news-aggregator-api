<?php

namespace Database\Factories;

use App\Models\Author;
use App\Models\Category;
use App\Models\Source;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserPreference>
 */
class UserPreferenceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $preferableTypes = [Source::class, Category::class, Author::class];

        $preferableType = fake()->randomElement($preferableTypes);
        $preferable = $preferableType::factory()->create();

        return [
            'user_id' => User::factory(),
            'preferable_id' => $preferable->id,
            'preferable_type' => $preferableType,
        ];
    }
}
