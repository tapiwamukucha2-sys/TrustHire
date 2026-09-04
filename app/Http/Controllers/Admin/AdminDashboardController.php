<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\ItemRequest;
use App\Models\Listing;
use App\Models\Message;
use App\Models\Report;
use App\Models\User;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'stats' => [
                'listings' => Listing::count(),
                'requests' => ItemRequest::count(),
                'bookings' => Booking::count(),
                'users' => User::count(),
                'verifiedUsers' => User::where('verification_tier', 'VERIFIED')->count(),
                'pendingVerifications' => User::where('verification_tier', 'PENDING')->count(),
                'openReports' => Report::where('status', 'OPEN')->count(),
                'messages' => Message::count(),
            ],
        ]);
    }
}
