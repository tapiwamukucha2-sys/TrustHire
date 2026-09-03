<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VerificationController extends Controller
{
    public function index(): View
    {
        $pending = User::where('verification_tier', 'PENDING')->orderBy('verification_requested_at')->get();

        return view('admin.verifications', ['pending' => $pending]);
    }

    public function approve(User $user): RedirectResponse
    {
        $user->update(['verification_tier' => 'VERIFIED', 'verification_reviewed_at' => now()]);

        return back();
    }

    public function reject(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate(['reason' => 'nullable|string']);

        $user->update([
            'verification_tier' => 'REJECTED',
            'verification_reviewed_at' => now(),
            'verification_reject_reason' => $data['reason'] ?: 'Documents could not be verified.',
        ]);

        return back();
    }
}
