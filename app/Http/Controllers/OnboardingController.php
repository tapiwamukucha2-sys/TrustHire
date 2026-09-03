<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OnboardingController extends Controller
{
    public function show(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        $callback = $request->query('callback', '/');

        if ($user->profile_completed_at) {
            return redirect($callback);
        }

        return view('onboarding.show', ['user' => $user, 'callback' => $callback]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'account_type' => 'required|in:INDIVIDUAL,BUSINESS',
            'location' => 'nullable|string|max:255',
            'photo_data_url' => 'nullable|string',
            'marketing_opt_in' => 'nullable|boolean',
            'age_confirmed' => 'required|accepted',
            'terms_accepted' => 'required|accepted',
        ]);

        $request->user()->update([
            'name' => $data['name'],
            'account_type' => $data['account_type'],
            'location' => $data['location'] ?? null,
            'photo_url' => $data['photo_data_url'] ?? null,
            'marketing_opt_in' => $request->boolean('marketing_opt_in'),
            'terms_accepted_at' => now(),
            'profile_completed_at' => now(),
        ]);

        return redirect($request->input('callback', '/'));
    }
}
