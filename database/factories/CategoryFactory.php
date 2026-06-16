<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categoryName = fake()->unique()->words(2, true);

        return [
            'categoryID' => fake()->unique()->numberBetween(1000, 9999),
            'categoryName' => Str::title($categoryName),
            'slug' => Str::slug($categoryName),
            'active' => true,
        ];
    }
}
