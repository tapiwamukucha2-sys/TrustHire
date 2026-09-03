<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContentController extends Controller
{
    public function show(): View
    {
        return view('admin.content', ['settings' => SiteSettings::current()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'hero_headline' => 'required|string|max:255',
            'hero_subtext' => 'required|string',
            'announcement_text' => 'nullable|string',
            'announcement_enabled' => 'nullable|boolean',
        ]);

        $settings = SiteSettings::current();
        $settings->update([
            'hero_headline' => $data['hero_headline'],
            'hero_subtext' => $data['hero_subtext'],
            'announcement_text' => $data['announcement_text'] ?? null,
            'announcement_enabled' => $request->boolean('announcement_enabled'),
        ]);

        return redirect()->route('admin.content.show', ['saved' => 1]);
    }
}
