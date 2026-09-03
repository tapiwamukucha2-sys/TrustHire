<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Listing;
use App\Models\SiteSettings;
use App\Models\User;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $categories = Category::orderBy('name')->get();

        $recentListings = Listing::where('status', 'ACTIVE')
            ->with('category')
            ->latest()
            ->limit(24)
            ->get();

        $featuredByCategory = $recentListings
            ->groupBy(fn (Listing $listing) => $listing->category->name)
            ->map(fn ($listings) => $listings->take(4))
            ->sortKeys();

        return view('home.index', [
            'categories' => $categories,
            'settings' => SiteSettings::current(),
            'verifiedCount' => User::where('verification_tier', 'VERIFIED')->count(),
            'featuredByCategory' => $featuredByCategory,
        ]);
    }
}
