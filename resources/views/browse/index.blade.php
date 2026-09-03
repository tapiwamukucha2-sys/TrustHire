@extends('layouts.app')

@section('content')
<div>
    @if ($location)
        <p class="mb-4 text-sm text-gray-600">
            Showing results near <span class="font-medium text-primary">{{ $location }}</span> &middot;
            <a href="{{ route('browse', ['tab' => $tab, 'category' => $categorySlug]) }}" class="text-accent hover:underline">clear</a>
        </p>
    @endif

    <div class="mb-8 flex gap-6 border-b text-sm">
        <a href="{{ route('browse', ['tab' => 'listings', 'category' => $categorySlug, 'location' => $location]) }}" class="pb-3 {{ $tab === 'listings' ? 'border-b-2 border-accent font-semibold text-accent' : 'text-gray-500' }}">Available to hire</a>
        <a href="{{ route('browse', ['tab' => 'requests', 'category' => $categorySlug, 'location' => $location]) }}" class="pb-3 {{ $tab === 'requests' ? 'border-b-2 border-accent font-semibold text-accent' : 'text-gray-500' }}">People looking to hire</a>
    </div>

    <div class="mb-8 flex flex-wrap gap-2 text-sm">
        <a href="{{ route('browse', ['tab' => $tab, 'location' => $location]) }}" class="rounded-full border px-3 py-1 {{ !$categorySlug ? 'border-accent bg-accent text-white' : 'border-gray-300 text-gray-600 hover:border-accent' }}">All</a>
        @foreach ($categories as $category)
            <a href="{{ route('browse', ['tab' => $tab, 'category' => $category->slug, 'location' => $location]) }}" class="rounded-full border px-3 py-1 {{ $categorySlug === $category->slug ? 'border-accent bg-accent text-white' : 'border-gray-300 text-gray-600 hover:border-accent' }}">{{ $category->name }}</a>
        @endforeach
    </div>

    @if ($tab === 'listings')
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($listings as $listing)
                <a href="{{ route('listings.show', $listing) }}" class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm hover:border-accent">
                    @if ($listing->firstPhoto())
                        <img src="{{ $listing->firstPhoto() }}" alt="{{ $listing->title }}" class="aspect-video w-full object-cover">
                    @else
                        <div class="flex aspect-video w-full items-center justify-center bg-gray-50 text-xs text-gray-400">No photo</div>
                    @endif
                    <div class="p-4">
                        <p class="text-xs uppercase tracking-wide text-accent">{{ $listing->category->name }}</p>
                        <h3 class="mt-1 text-lg font-semibold text-primary">{{ $listing->title }}</h3>
                        <p class="text-sm text-gray-600">{{ $listing->location }}</p>
                        <p class="mt-2 font-medium text-accent">${{ number_format($listing->price, 2) }} / {{ $listing->price_unit }}</p>
                    </div>
                </a>
            @empty
                <p class="text-gray-500">No listings yet.</p>
            @endforelse
        </div>
    @else
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($requests as $itemRequest)
                <a href="{{ route('requests.show', $itemRequest) }}" class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm hover:border-accent">
                    <p class="text-xs uppercase tracking-wide text-accent">{{ $itemRequest->category->name }}</p>
                    <h3 class="mt-1 text-lg font-semibold text-primary">{{ $itemRequest->title }}</h3>
                    <p class="text-sm text-gray-600">{{ $itemRequest->location }}</p>
                    @if ($itemRequest->budget)
                        <p class="mt-2 font-medium text-accent">Budget: ${{ number_format($itemRequest->budget, 2) }}</p>
                    @endif
                </a>
            @empty
                <p class="text-gray-500">No requests yet.</p>
            @endforelse
        </div>
    @endif
</div>
@endsection
