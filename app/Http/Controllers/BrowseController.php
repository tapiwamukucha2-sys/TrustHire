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
        $priceMin = $request->query('price_min');
        $priceMax = $request->query('price_max');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        $verifiedOnly = $request->boolean('verified_only');

        $listings = collect();
        $requests = collect();

        if ($tab === 'listings') {
            $listings = Listing::query()
                ->where('status', 'ACTIVE')
                ->when($categorySlug, fn ($q) => $q->whereHas('category', fn ($q2) => $q2->where('slug', $categorySlug)))
                ->when($location, fn ($q) => $q->where('location', 'like', "%{$location}%"))
                ->when($priceMin !== null && $priceMin !== '', fn ($q) => $q->where('price', '>=', $priceMin))
                ->when($priceMax !== null && $priceMax !== '', fn ($q) => $q->where('price', '<=', $priceMax))
                ->when($verifiedOnly, fn ($q) => $q->whereHas('owner', fn ($q2) => $q2->where('verification_tier', 'VERIFIED')))
                ->when($dateFrom && $dateTo, fn ($q) => $q->whereDoesntHave('offers', function ($q2) use ($dateFrom, $dateTo) {
                    $q2->whereHas('booking', function ($q3) use ($dateFrom, $dateTo) {
                        $q3->whereIn('status', ['CONFIRMED', 'COMPLETED'])
                            ->where('start_date', '<', $dateTo)
                            ->where('end_date', '>', $dateFrom);
                    });
                }))
                ->with('category', 'owner')
                ->latest()
                ->get();
        } else {
            $requests = ItemRequest::query()
                ->where('status', 'OPEN')
                ->when($categorySlug, fn ($q) => $q->whereHas('category', fn ($q2) => $q2->where('slug', $categorySlug)))
                ->when($location, fn ($q) => $q->where('location', 'like', "%{$location}%"))
                ->when($priceMin !== null && $priceMin !== '', fn ($q) => $q->where('budget', '>=', $priceMin))
                ->when($priceMax !== null && $priceMax !== '', fn ($q) => $q->where('budget', '<=', $priceMax))
                ->when($verifiedOnly, fn ($q) => $q->whereHas('requester', fn ($q2) => $q2->where('verification_tier', 'VERIFIED')))
                ->with('category', 'requester')
                ->latest()
                ->get();
        }

        return view('browse.index', [
            'tab' => $tab,
            'categorySlug' => $categorySlug,
            'location' => $location,
            'priceMin' => $priceMin,
            'priceMax' => $priceMax,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'verifiedOnly' => $verifiedOnly,
            'categories' => Category::orderBy('name')->get(),
            'listings' => $listings,
            'requests' => $requests,
        ]);
    }
}
