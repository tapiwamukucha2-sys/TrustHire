@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-lg">
    <h1 class="mb-6 text-2xl font-semibold text-primary">Post what you need</h1>
    <form method="POST" action="{{ route('requests.store') }}" class="space-y-4">
        @csrf
        <x-smart-category-picker :categories="$categories" titlePlaceholder="e.g. Need a lawn mower" />

        <label class="block text-sm text-gray-600">
            Description
            <textarea name="description" required rows="4" class="mt-1 w-full rounded border border-gray-300 px-3 py-2 focus:border-accent focus:outline-none"></textarea>
        </label>

        <label class="block text-sm text-gray-600">
            Budget (optional)
            <input type="number" name="budget" min="0" step="0.01" class="mt-1 w-full rounded border border-gray-300 px-3 py-2 focus:border-accent focus:outline-none">
        </label>

        <label class="block text-sm text-gray-600">
            Location
            <input type="text" name="location" required class="mt-1 w-full rounded border border-gray-300 px-3 py-2 focus:border-accent focus:outline-none">
        </label>

        <div class="grid grid-cols-2 gap-4">
            <label class="block text-sm text-gray-600">
                Needed from
                <input type="date" name="needed_from" required class="mt-1 w-full rounded border border-gray-300 px-3 py-2 focus:border-accent focus:outline-none">
            </label>
            <label class="block text-sm text-gray-600">
                Needed to
                <input type="date" name="needed_to" required class="mt-1 w-full rounded border border-gray-300 px-3 py-2 focus:border-accent focus:outline-none">
            </label>
        </div>

        @error('needed_to')<p class="text-sm text-red-600">{{ $message }}</p>@enderror

        <button type="submit" class="w-full rounded-full bg-accent px-3 py-2 font-medium text-white hover:opacity-90">Post request</button>
    </form>
</div>
@endsection
