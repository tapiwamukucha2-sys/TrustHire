<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\SiteSettings;
use App\Models\User;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        return view('home.index', [
            'categories' => Category::orderBy('name')->get(),
            'settings' => SiteSettings::current(),
            'verifiedCount' => User::where('verification_tier', 'VERIFIED')->count(),
        ]);
    }
}
