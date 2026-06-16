<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $productName = fake()->unique()->words(3, true);
        $productCode = fake()->unique()->bothify('PRD-####');

        return [
            'category_id' => Category::factory(),
            'sub_category_id' => SubCategory::factory(),
            'productID' => mt_rand(100, 999),
            'productCode' => $productCode,
            'productName' => Str::title($productName),
            'image' => fake()->optional()->randomElement([
                'product/product1.jpg',
                'product/product2.jpg',
            ]),
            'description' => fake()->paragraph(),
            'price' => fake()->numberBetween(100, 250) * 1000,
            'unit' => fake()->randomElement(['pcs', 'box', 'pack', 'kg']),
            'moq' => 1,
            'active' => true,
        ];
    }
}
