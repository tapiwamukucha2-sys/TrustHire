<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ItemRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ItemRequestController extends Controller
{
    public function create(): View
    {
        return view('requests.create', [
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'budget' => 'nullable|numeric|min:0',
            'location' => 'required|string|max:255',
            'needed_from' => 'required|date',
            'needed_to' => 'required|date|after_or_equal:needed_from',
        ]);

        $itemRequest = ItemRequest::create([
            'requester_id' => $request->user()->id,
            'category_id' => $data['category_id'],
            'title' => $data['title'],
            'description' => $data['description'],
            'budget' => $data['budget'] ?? null,
            'location' => $data['location'],
            'needed_from' => $data['needed_from'],
            'needed_to' => $data['needed_to'],
        ]);

        return redirect()->route('requests.show', $itemRequest);
    }

    public function show(ItemRequest $itemRequest): View
    {
        $itemRequest->load('category', 'requester', 'offers.lender');

        return view('requests.show', ['itemRequest' => $itemRequest]);
    }
}
