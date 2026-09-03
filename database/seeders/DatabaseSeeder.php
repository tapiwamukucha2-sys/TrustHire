<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $categories = [
            'Machinery',
            'Electronics',
            'Tools',
            'Cameras',
            'Garden Equipment',
            'Event Gear',
            'Vehicles',
            'Fashion & Luxury',
            'Real Estate & Spaces',
            'Agricultural Equipment',
            'Outdoor & Recreational',
        ];

        foreach ($categories as $name) {
            Category::firstOrCreate(['slug' => Str::slug($name)], ['name' => $name]);
        }
    }
}
