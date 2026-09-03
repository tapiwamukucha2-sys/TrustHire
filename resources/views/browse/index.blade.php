@extends('layouts.app')

@section('content')
<div>
    <div class="mb-8 flex gap-6 border-b text-sm">
        <a href="{{ route('browse', array_merge(request()->query(), ['tab' => 'listings'])) }}" class="pb-3 {{ $tab === 'listings' ? 'border-b-2 border-accent font-semibold text-accent' : 'text-gray-500' }}">Available to hire</a>
        <a href="{{ route('browse', array_merge(request()->query(), ['tab' => 'requests'])) }}" class="pb-3 {{ $tab === 'requests' ? 'border-b-2 border-accent font-semibold text-accent' : 'text-gray-500' }}">People looking to hire</a>
    </div>

    <div class="mb-6 flex flex-wrap gap-2 text-sm">
        <a href="{{ route('browse', array_merge(request()->query(), ['category' => null])) }}" class="rounded-full border px-3 py-1 {{ !$categorySlug ? 'border-accent bg-accent text-white' : 'border-gray-300 text-gray-600 hover:border-accent' }}">All</a>
        @foreach ($categories as $category)
            <a href="{{ route('browse', array_merge(request()->query(), ['category' => $category->slug])) }}" class="rounded-full border px-3 py-1 {{ $categorySlug === $category->slug ? 'border-accent bg-accent text-white' : 'border-gray-300 text-gray-600 hover:border-accent' }}">{{ $category->name }}</a>
        @endforeach
    </div>

    <form method="GET" action="{{ route('browse') }}" class="mb-8 flex flex-wrap items-end gap-3 rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
        <input type="hidden" name="tab" value="{{ $tab }}">
        <input type="hidden" name="category" value="{{ $categorySlug }}">

        <label class="text-sm text-gray-600">
            Location
            <input type="text" name="location" value="{{ $location }}" placeholder="City or area" class="mt-1 block w-36 rounded border border-gray-300 px-2 py-1.5 text-sm focus:border-accent focus:outline-none">
        </label>

        <label class="text-sm text-gray-600">
            {{ $tab === 'listings' ? 'Min price' : 'Min budget' }}
            <input type="number" name="price_min" value="{{ $priceMin }}" min="0" step="0.01" class="mt-1 block w-24 rounded border border-gray-300 px-2 py-1.5 text-sm focus:border-accent focus:outline-none">
        </label>

        <label class="text-sm text-gray-600">
            {{ $tab === 'listings' ? 'Max price' : 'Max budget' }}
            <input type="number" name="price_max" value="{{ $priceMax }}" min="0" step="0.01" class="mt-1 block w-24 rounded border border-gray-300 px-2 py-1.5 text-sm focus:border-accent focus:outline-none">
        </label>

        @if ($tab === 'listings')
            <label class="text-sm text-gray-600">
                Available from
                <input type="date" name="date_from" value="{{ $dateFrom }}" class="mt-1 block rounded border border-gray-300 px-2 py-1.5 text-sm focus:border-accent focus:outline-none">
            </label>
            <label class="text-sm text-gray-600">
                Available to
                <input type="date" name="date_to" value="{{ $dateTo }}" class="mt-1 block rounded border border-gray-300 px-2 py-1.5 text-sm focus:border-accent focus:outline-none">
            </label>
        @endif

        <label class="flex items-center gap-2 pb-1.5 text-sm text-gray-600">
            <input type="checkbox" name="verified_only" value="1" @checked($verifiedOnly)>
            Verified only
        </label>

        <button type="submit" class="rounded-full bg-accent px-4 py-1.5 text-sm font-medium text-white hover:opacity-90">Apply filters</button>
        <a href="{{ route('browse', ['tab' => $tab]) }}" class="pb-1.5 text-sm text-gray-500 hover:text-accent">Clear</a>
    </form>

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
                        <h3 class="mt-1 flex items-center gap-2 text-lg font-semibold text-primary">
                            {{ $listing->title }}
                            <x-verified-badge :tier="$listing->owner->verification_tier" />
                        </h3>
                        <p class="text-sm text-gray-600">{{ $listing->location }}</p>
                        <p class="mt-2 font-medium text-accent">${{ number_format($listing->price, 2) }} / {{ $listing->price_unit }}</p>
                    </div>
                </a>
            @empty
                <p class="text-gray-500">No listings match these filters.</p>
            @endforelse
        </div>
    @else
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($requests as $itemRequest)
                <a href="{{ route('requests.show', $itemRequest) }}" class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm hover:border-accent">
                    <p class="text-xs uppercase tracking-wide text-accent">{{ $itemRequest->category->name }}</p>
                    <h3 class="mt-1 flex items-center gap-2 text-lg font-semibold text-primary">
                        {{ $itemRequest->title }}
                        <x-verified-badge :tier="$itemRequest->requester->verification_tier" />
                    </h3>
                    <p class="text-sm text-gray-600">{{ $itemRequest->location }}</p>
                    @if ($itemRequest->budget)
                        <p class="mt-2 font-medium text-accent">Budget: ${{ number_format($itemRequest->budget, 2) }}</p>
                    @endif
                </a>
            @empty
                <p class="text-gray-500">No requests match these filters.</p>
            @endforelse
        </div>
    @endif
</div>
@endsection
