@extends('layouts.admin')

@section('content')
<div class="max-w-lg">
    <h1 class="mb-6 text-2xl font-semibold text-primary">Edit listing</h1>

    <form method="POST" action="{{ route('admin.listings.update', $listing) }}" enctype="multipart/form-data" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            @if ($listing->firstPhoto())
                <img src="{{ $listing->firstPhoto() }}" alt="" class="mb-2 h-40 w-full rounded object-cover">
            @endif
            <label class="block text-sm text-gray-600">
                Replace photo
                <input type="file" name="photo" accept="image/*" class="mt-1 block w-full text-xs">
            </label>
        </div>

        <label class="block text-sm text-gray-600">
            Title
            <input type="text" name="title" required value="{{ old('title', $listing->title) }}" class="mt-1 w-full rounded border border-gray-300 px-3 py-2 focus:border-accent focus:outline-none">
        </label>

        <label class="block text-sm text-gray-600">
            Description
            <textarea name="description" required rows="3" class="mt-1 w-full rounded border border-gray-300 px-3 py-2 focus:border-accent focus:outline-none">{{ old('description', $listing->description) }}</textarea>
        </label>

        <label class="block text-sm text-gray-600">
            Category
            <select name="category_id" required class="mt-1 w-full rounded border border-gray-300 px-3 py-2 focus:border-accent focus:outline-none">
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected($listing->category_id === $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
        </label>

        <div class="grid grid-cols-2 gap-3">
            <label class="block text-sm text-gray-600">
                Price
                <input type="number" step="0.01" min="0" name="price" required value="{{ old('price', $listing->price) }}" class="mt-1 w-full rounded border border-gray-300 px-3 py-2 focus:border-accent focus:outline-none">
            </label>
            <label class="block text-sm text-gray-600">
                Per
                <select name="price_unit" required class="mt-1 w-full rounded border border-gray-300 px-3 py-2 focus:border-accent focus:outline-none">
                    @foreach (['hour', 'day', 'week'] as $unit)
                        <option value="{{ $unit }}" @selected($listing->price_unit === $unit)>{{ ucfirst($unit) }}</option>
                    @endforeach
                </select>
            </label>
        </div>

        <label class="block text-sm text-gray-600">
            Status
            <select name="status" required class="mt-1 w-full rounded border border-gray-300 px-3 py-2 focus:border-accent focus:outline-none">
                <option value="ACTIVE" @selected($listing->status === 'ACTIVE')>Active</option>
                <option value="INACTIVE" @selected($listing->status === 'INACTIVE')>Inactive</option>
            </select>
        </label>

        <div class="flex gap-3">
            <button type="submit" class="rounded-full bg-accent px-4 py-2 text-sm font-medium text-white hover:opacity-90">Save changes</button>
            <a href="{{ route('admin.listings.index') }}" class="rounded-full border border-gray-300 px-4 py-2 text-sm text-gray-600 hover:border-accent hover:text-accent">Cancel</a>
        </div>
    </form>
</div>
@endsection
