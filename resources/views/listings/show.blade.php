@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-lg">
    @if (!empty($listing->photos))
        <div class="mb-5 grid grid-cols-2 gap-2 sm:grid-cols-3">
            @foreach ($listing->photos as $photo)
                <img src="{{ $photo }}" alt="{{ $listing->title }} photo" class="aspect-square w-full rounded-lg border border-gray-200 object-cover">
            @endforeach
        </div>
    @endif

    <p class="text-xs uppercase tracking-wide text-accent">{{ $listing->category->name }}</p>
    <h1 class="mt-1 text-2xl font-semibold text-primary">{{ $listing->title }}</h1>
    <p class="mt-3 whitespace-pre-wrap text-gray-700">{{ $listing->description }}</p>
    <p class="mt-5 text-lg font-medium text-accent">${{ number_format($listing->price, 2) }} / {{ $listing->price_unit }}</p>
    <p class="text-sm text-gray-600">{{ $listing->location }}</p>
    <p class="mt-4 flex items-center gap-2 text-sm text-gray-600">
        Listed by {{ $listing->owner->displayName() }}
        <x-verified-badge :tier="$listing->owner->verification_tier" />
    </p>
    <p class="mt-2 text-sm text-gray-500">To hire this, post a request and reference it, or wait for the owner to respond to your open request in the same category.</p>

    @auth
        <div class="mt-4">
            <x-report-button :target-listing-id="$listing->id" label="Report this listing" />
        </div>
    @endauth
</div>
@endsection
