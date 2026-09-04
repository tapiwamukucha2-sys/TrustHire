<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function index(): View
    {
        return view('admin.users', [
            'users' => User::withCount(['listings', 'itemRequests'])->latest()->get(),
        ]);
    }
}
