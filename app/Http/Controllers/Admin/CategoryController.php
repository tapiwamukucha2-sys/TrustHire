<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::withCount('listings', 'itemRequests')->orderBy('name')->get();

        return view('admin.categories', ['categories' => $categories]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(['name' => 'required|string|max:255']);
        $slug = Str::slug($data['name']);

        if (Category::where('slug', $slug)->exists()) {
            return back()->withErrors(['name' => 'A category with that name already exists.']);
        }

        Category::create(['name' => $data['name'], 'slug' => $slug]);

        return redirect()->route('admin.categories.index', ['saved' => 1]);
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $data = $request->validate(['name' => 'required|string|max:255']);

        $category->update(['name' => $data['name']]);

        return redirect()->route('admin.categories.index', ['saved' => 1]);
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->listings()->exists() || $category->itemRequests()->exists()) {
            return back()->withErrors(['category' => "Can't delete a category that still has listings or requests. Rename it instead."]);
        }

        $category->delete();

        return redirect()->route('admin.categories.index');
    }
}
