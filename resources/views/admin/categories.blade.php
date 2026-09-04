@extends('layouts.admin')

@section('content')
<div class="max-w-lg">
    <h1 class="mb-6 text-2xl font-semibold text-primary">Categories</h1>

    @if (request('saved'))
        <p class="mb-4 rounded border border-gray-200 bg-white p-3 text-sm text-accent shadow-sm">Saved.</p>
    @endif

    <form method="POST" action="{{ route('admin.categories.store') }}" class="mb-6 flex gap-2">
        @csrf
        <input type="text" name="name" required placeholder="New category name" class="flex-1 rounded border border-gray-300 px-3 py-2 focus:border-accent focus:outline-none">
        <button type="submit" class="rounded-full bg-accent px-4 py-2 text-sm font-medium text-white hover:opacity-90">Add</button>
    </form>
    @error('name')<p class="mb-4 text-sm text-red-600">{{ $message }}</p>@enderror

    <div class="space-y-3">
        @foreach ($categories as $category)
            <div data-testid="category-row" class="flex items-center gap-3 rounded-lg border border-gray-200 bg-white p-3 shadow-sm">
                <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="flex flex-1 gap-2">
                    @csrf
                    <input type="text" name="name" value="{{ $category->name }}" class="flex-1 rounded border border-gray-300 px-2 py-1 text-sm focus:border-accent focus:outline-none">
                    <button type="submit" class="text-xs text-accent underline">Save</button>
                </form>
                <span class="text-xs text-gray-500">{{ $category->listings_count }} listings &middot; {{ $category->item_requests_count }} requests</span>
                <form method="POST" action="{{ route('admin.categories.destroy', $category) }}">
                    @csrf
                    <button type="submit" class="text-xs text-gray-500 hover:text-red-600">Delete</button>
                </form>
            </div>
        @endforeach
    </div>
    @error('category')<p class="mt-4 text-sm text-red-600">{{ $message }}</p>@enderror
</div>
@endsection
