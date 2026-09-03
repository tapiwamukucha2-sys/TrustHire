<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoListingSeeder extends Seeder
{
    public function run(): void
    {
        $owner = User::firstOrCreate(
            ['phone' => '+263770000000'],
            ['name' => 'Demo Seller', 'profile_completed_at' => now(), 'terms_accepted_at' => now()]
        );

        // Clear out any previously-seeded demo listings whose titles no longer
        // match this list, so re-seeding doesn't leave stale/mismatched rows.
        Listing::where('owner_id', $owner->id)->delete();

        $items = [
            ['Cameras', 'Vintage Film Camera', 15, 'day', 'camera.jpg'],
            ['Vehicles', 'Classic Car for Photoshoots & Events', 60, 'day', 'car.jpg'],
            ['Electronics', 'Gaming Console Controller', 5, 'day', 'controller.jpg'],
            ['Electronics', 'Laptop & Desk Accessories Set', 18, 'day', 'laptop.jpg'],
            ['Fashion & Luxury', 'Statement Heels for Photoshoot Hire', 10, 'day', 'heels.jpg'],
            ['Fashion & Luxury', 'Formal Event Accessories Set', 12, 'day', 'accessories.jpg'],
        ];

        foreach ($items as [$categoryName, $title, $price, $unit, $photo]) {
            $category = Category::where('name', $categoryName)->first();
            if (! $category) {
                continue;
            }

            Listing::firstOrCreate(
                ['owner_id' => $owner->id, 'title' => $title],
                [
                    'category_id' => $category->id,
                    'description' => "{$title}, well maintained and ready to hire. (Demo listing — placeholder photo, not the actual item.)",
                    'price' => $price,
                    'price_unit' => $unit,
                    'location' => 'Harare',
                    'photos' => ['/storage/demo/'.$photo],
                    'status' => 'ACTIVE',
                ]
            );
        }
    }
}
