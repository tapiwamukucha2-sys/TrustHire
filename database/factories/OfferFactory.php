<?php

namespace Database\Factories;

use App\Models\ItemRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OfferFactory extends Factory
{
    public function definition(): array
    {
        return [
            'item_request_id' => ItemRequest::factory(),
            'listing_id' => null,
            'lender_id' => User::factory(),
            'price' => fake()->randomFloat(2, 5, 100),
            'message' => null,
            'status' => 'PENDING',
        ];
    }
}
