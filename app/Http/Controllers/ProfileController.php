<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(Request $request): View
    {
        $user = $request->user();

        $listings = $user->listings()->latest()->get();
        $requests = $user->itemRequests()->latest()->get();
        $bookings = $user->bookingsAsLender()->with('itemRequest')->get()
            ->merge($user->bookingsAsRenter()->with('itemRequest')->get())
            ->sortByDesc('created_at')
            ->values();
        $reviewsReceived = $user->reviewsReceived()->with('author')->get();

        $avgRating = $reviewsReceived->isNotEmpty() ? round($reviewsReceived->avg('rating'), 1) : null;

        return view('profile.show', compact('user', 'listings', 'requests', 'bookings', 'reviewsReceived', 'avgRating'));
    }
}
