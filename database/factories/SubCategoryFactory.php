<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SubCategory>
 */
class SubCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subCategoryName = fake()->unique()->words(2, true);

        return [
            'subCategoryID' => fake()->unique()->uuid(),
            'subCategoryName' => Str::title($subCategoryName),
            'slug' => Str::slug($subCategoryName),
        ];
    }
}
