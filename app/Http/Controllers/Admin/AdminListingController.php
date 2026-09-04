<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Listing;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdminListingController extends Controller
{
    public function index(): View
    {
        return view('admin.listings.index', [
            'listings' => Listing::with(['category', 'owner'])->latest()->get(),
        ]);
    }

    public function edit(Listing $listing): View
    {
        return view('admin.listings.edit', [
            'listing' => $listing,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Listing $listing): RedirectResponse
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'price_unit' => 'required|string|in:hour,day,week',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|string|in:ACTIVE,INACTIVE',
            'photo' => 'nullable|image|max:8192',
        ]);

        $photos = $listing->photos ?? [];

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('listings', 'public');
            $photos = ['/storage/'.$path];
        }

        $listing->update([
            'title' => $data['title'],
            'description' => $data['description'],
            'price' => $data['price'],
            'price_unit' => $data['price_unit'],
            'category_id' => $data['category_id'],
            'status' => $data['status'],
            'photos' => $photos,
        ]);

        return redirect()->route('admin.listings.index')->with('status', 'Listing updated.');
    }

    public function destroy(Listing $listing): RedirectResponse
    {
        foreach ($listing->photos ?? [] as $photo) {
            $relative = ltrim(str_replace('/storage/', '', $photo), '/');
            Storage::disk('public')->delete($relative);
        }

        $listing->delete();

        return redirect()->route('admin.listings.index')->with('status', 'Listing deleted.');
    }
}
