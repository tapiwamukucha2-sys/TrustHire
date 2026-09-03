<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VerifyController extends Controller
{
    public function show(Request $request): View
    {
        return view('verify.show', ['user' => $request->user()]);
    }

    public function submit(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'id_document_url' => 'required|string',
            'selfie_url' => 'required|string',
        ]);

        $request->user()->update([
            'id_document_url' => $data['id_document_url'],
            'selfie_url' => $data['selfie_url'],
            'verification_tier' => 'PENDING',
            'verification_requested_at' => now(),
            'verification_reject_reason' => null,
        ]);

        return redirect()->route('verify.show');
    }
}
