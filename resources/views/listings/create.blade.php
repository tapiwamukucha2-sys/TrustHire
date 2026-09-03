@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-lg">
    <h1 class="mb-6 text-2xl font-semibold text-primary">List an item to hire out</h1>
    <form method="POST" action="{{ route('listings.store') }}" class="space-y-4">
        @csrf
        <x-photo-gallery-upload />
        <x-smart-category-picker :categories="$categories" titlePlaceholder="e.g. Canon DSLR camera" />

        <label class="block text-sm text-gray-600">
            Description
            <textarea name="description" required rows="4" class="mt-1 w-full rounded border border-gray-300 px-3 py-2 focus:border-accent focus:outline-none"></textarea>
        </label>

        <div class="grid grid-cols-2 gap-4">
            <label class="block text-sm text-gray-600">
                Price
                <input type="number" name="price" min="0" step="0.01" required class="mt-1 w-full rounded border border-gray-300 px-3 py-2 focus:border-accent focus:outline-none">
            </label>
            <label class="block text-sm text-gray-600">
                Per
                <select name="price_unit" class="mt-1 w-full rounded border border-gray-300 px-3 py-2 focus:border-accent focus:outline-none">
                    <option value="hour">hour</option>
                    <option value="day">day</option>
                    <option value="week">week</option>
                </select>
            </label>
        </div>

        <label class="block text-sm text-gray-600">
            Location
            <input type="text" name="location" required class="mt-1 w-full rounded border border-gray-300 px-3 py-2 focus:border-accent focus:outline-none">
        </label>

        @error('title')<p class="text-sm text-red-600">{{ $message }}</p>@enderror

        <button type="submit" class="w-full rounded-full bg-accent px-3 py-2 font-medium text-white hover:opacity-90">Post listing</button>
    </form>
</div>
@endsection
