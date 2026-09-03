<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemRequestFactory extends Factory
{
    public function definition(): array
    {
        return [
            'requester_id' => User::factory(),
            'category_id' => Category::factory(),
            'title' => fake()->sentence(4),
            'description' => fake()->sentence(),
            'budget' => fake()->randomFloat(2, 10, 100),
            'location' => fake()->city(),
            'needed_from' => now()->addDays(3),
            'needed_to' => now()->addDays(4),
            'status' => 'OPEN',
        ];
    }
}
