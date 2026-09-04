<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\View\View;

class AdminMessageController extends Controller
{
    public function index(): View
    {
        return view('admin.messages', [
            'messages' => Message::with(['sender', 'booking'])->latest()->limit(100)->get(),
        ]);
    }
}
