<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\ItemRequest;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Booking>
 */
class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'item_request_id' => ItemRequest::factory(),
            'offer_id' => Offer::factory(),
            'lender_id' => User::factory(),
            'renter_id' => User::factory(),
            'start_date' => now()->addDays(3),
            'end_date' => now()->addDays(5),
            'status' => 'CONFIRMED',
        ];
    }
}
