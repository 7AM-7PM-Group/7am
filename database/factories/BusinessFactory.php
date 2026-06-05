<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Business>
 */
class BusinessFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->sentence(2, false),
            'npwp' => fake()->numerify("##########"),
            'address' => fake()->address(),
            'bank' => fake()->word(),
            'account_number' => fake()->numerify("##########"),
            'account_name' => fake()->name(),
            'id_card' => 'id/card1.jpg',
            'representative' => fake()->name(),
            "phone" => fake()->phoneNumber(),
            'user_id' => User::factory()
        ];
    }
}
