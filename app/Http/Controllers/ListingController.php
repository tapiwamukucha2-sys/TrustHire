<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Listing;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ListingController extends Controller
{
    public function create(): View
    {
        return view('listings.create', [
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'price_unit' => 'required|in:hour,day,week',
            'location' => 'required|string|max:255',
            'photos_json' => 'nullable|string',
        ]);

        $photos = [];
        if (! empty($data['photos_json'])) {
            $decoded = json_decode($data['photos_json'], true);
            if (is_array($decoded)) {
                $photos = array_slice(array_filter($decoded, 'is_string'), 0, 5);
            }
        }

        $listing = Listing::create([
            'owner_id' => $request->user()->id,
            'category_id' => $data['category_id'],
            'title' => $data['title'],
            'description' => $data['description'],
            'price' => $data['price'],
            'price_unit' => $data['price_unit'],
            'location' => $data['location'],
            'photos' => $photos,
        ]);

        return redirect()->route('listings.show', $listing);
    }

    public function show(Listing $listing): View
    {
        $listing->load('category', 'owner');

        return view('listings.show', ['listing' => $listing]);
    }
}
