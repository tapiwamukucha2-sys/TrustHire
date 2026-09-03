@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-lg space-y-10">
    <div class="flex items-center gap-3">
        @if ($user->photo_url)
            <img src="{{ $user->photo_url }}" alt="" class="h-9 w-9 rounded-full object-cover">
        @else
            <x-trust-shield class="h-9 w-9" color="#EC0C8C" />
        @endif
        <div>
            <h1 class="flex items-center gap-2 text-2xl font-semibold text-primary">
                {{ $user->displayName() }}
                <x-verified-badge :tier="$user->verification_tier" />
            </h1>
            <p class="text-xs uppercase tracking-wide text-gray-500">
                {{ $user->account_type === 'BUSINESS' ? 'Business account' : 'Individual account' }}
                @if ($user->location) &middot; {{ $user->location }} @endif
            </p>
            @if ($avgRating)
                <p class="text-sm text-accent">{{ $avgRating }}★ &middot; {{ $reviewsReceived->count() }} review{{ $reviewsReceived->count() === 1 ? '' : 's' }}</p>
            @endif
            @if ($user->verification_tier === 'BASIC')
                <a href="{{ route('verify.show') }}" class="mt-1 inline-block text-xs text-accent underline">Get verified</a>
            @elseif ($user->verification_tier === 'REJECTED')
                <a href="{{ route('verify.show') }}" class="mt-1 inline-block text-xs text-red-600 underline">Verification rejected — try again</a>
            @endif
        </div>
    </div>

    <section>
        <h2 class="mb-3 border-b pb-2 text-lg font-semibold text-primary">Your listings</h2>
        @forelse ($listings as $listing)
            <p><a href="{{ route('listings.show', $listing) }}" class="text-sm text-ink underline decoration-accent underline-offset-4 hover:text-accent">{{ $listing->title }}</a></p>
        @empty
            <p class="text-sm text-gray-500">None yet.</p>
        @endforelse
    </section>

    <section>
        <h2 class="mb-3 border-b pb-2 text-lg font-semibold text-primary">Your requests</h2>
        @forelse ($requests as $itemRequest)
            <p><a href="{{ route('requests.show', $itemRequest) }}" class="text-sm text-ink underline decoration-accent underline-offset-4 hover:text-accent">{{ $itemRequest->title }}</a> <span class="text-xs text-gray-500">({{ $itemRequest->status }})</span></p>
        @empty
            <p class="text-sm text-gray-500">None yet.</p>
        @endforelse
    </section>

    <section>
        <h2 class="mb-3 border-b pb-2 text-lg font-semibold text-primary">Your bookings</h2>
        @forelse ($bookings as $booking)
            <p><a href="{{ route('bookings.show', $booking) }}" class="text-sm text-ink underline decoration-accent underline-offset-4 hover:text-accent">{{ $booking->itemRequest->title }}</a> <span class="text-xs text-gray-500">({{ $booking->status }})</span></p>
        @empty
            <p class="text-sm text-gray-500">None yet.</p>
        @endforelse
    </section>

    <section>
        <h2 class="mb-3 border-b pb-2 text-lg font-semibold text-primary">Reviews about you</h2>
        @forelse ($reviewsReceived as $review)
            <p class="text-sm text-ink"><strong class="text-accent">{{ $review->rating }}★</strong> from {{ $review->author->displayName() }}@if ($review->comment) &mdash; {{ $review->comment }} @endif</p>
        @empty
            <p class="text-sm text-gray-500">None yet.</p>
        @endforelse
    </section>
</div>
@endsection
