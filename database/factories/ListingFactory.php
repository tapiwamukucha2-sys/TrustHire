<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ListingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'owner_id' => User::factory(),
            'category_id' => Category::factory(),
            'title' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'price' => fake()->randomFloat(2, 5, 200),
            'price_unit' => fake()->randomElement(['hour', 'day', 'week']),
            'location' => fake()->city(),
            'photos' => [],
            'status' => 'ACTIVE',
        ];
    }
}
