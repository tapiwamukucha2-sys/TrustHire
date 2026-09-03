<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    private const REASONS = ['SCAM', 'FAKE_LISTING', 'NO_SHOW', 'ABUSIVE_BEHAVIOR', 'OTHER'];

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'target_user_id' => 'nullable|exists:users,id',
            'target_listing_id' => 'nullable|exists:listings,id',
            'reason' => 'required|in:'.implode(',', self::REASONS),
            'details' => 'nullable|string',
        ]);

        if (empty($data['target_user_id']) && empty($data['target_listing_id'])) {
            return back()->withErrors(['report' => 'Nothing to report.']);
        }

        Report::create([
            'reporter_id' => $request->user()->id,
            'target_user_id' => $data['target_user_id'] ?? null,
            'target_listing_id' => $data['target_listing_id'] ?? null,
            'reason' => $data['reason'],
            'details' => $data['details'] ?? null,
        ]);

        return back()->with('status', 'Thanks — our team will review this.');
    }
}
