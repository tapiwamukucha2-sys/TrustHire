<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportAdminController extends Controller
{
    public function index(): View
    {
        $reports = Report::where('status', 'OPEN')
            ->with('reporter', 'targetUser')
            ->orderBy('created_at')
            ->get();

        return view('admin.reports', ['reports' => $reports]);
    }

    public function resolve(Request $request, Report $report): RedirectResponse
    {
        $data = $request->validate(['status' => 'required|in:RESOLVED,DISMISSED']);

        $report->update(['status' => $data['status']]);

        return back();
    }
}
