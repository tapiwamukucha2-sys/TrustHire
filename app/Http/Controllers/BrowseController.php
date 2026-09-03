<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ItemRequest;
use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BrowseController extends Controller
{
    public function __invoke(Request $request): View
    {
        $tab = $request->query('tab') === 'requests' ? 'requests' : 'listings';
        $categorySlug = $request->query('category');
        $location = $request->query('location');

        $listings = collect();
        $requests = collect();

        if ($tab === 'listings') {
            $listings = Listing::query()
                ->where('status', 'ACTIVE')
                ->when($categorySlug, fn ($q) => $q->whereHas('category', fn ($q2) => $q2->where('slug', $categorySlug)))
                ->when($location, fn ($q) => $q->where('location', 'like', "%{$location}%"))
                ->with('category')
                ->latest()
                ->get();
        } else {
            $requests = ItemRequest::query()
                ->where('status', 'OPEN')
                ->when($categorySlug, fn ($q) => $q->whereHas('category', fn ($q2) => $q2->where('slug', $categorySlug)))
                ->when($location, fn ($q) => $q->where('location', 'like', "%{$location}%"))
                ->with('category')
                ->latest()
                ->get();
        }

        return view('browse.index', [
            'tab' => $tab,
            'categorySlug' => $categorySlug,
            'location' => $location,
            'categories' => Category::orderBy('name')->get(),
            'listings' => $listings,
            'requests' => $requests,
        ]);
    }
}
