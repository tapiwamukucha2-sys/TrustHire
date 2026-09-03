@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-lg">
    <p class="text-xs uppercase tracking-wide text-accent">{{ $itemRequest->category->name }}</p>
    <h1 class="mt-1 text-2xl font-semibold text-primary">{{ $itemRequest->title }}</h1>
    <p class="mt-3 whitespace-pre-wrap text-gray-700">{{ $itemRequest->description }}</p>
    @if ($itemRequest->budget)
        <p class="mt-3 font-medium text-accent">Budget: ${{ number_format($itemRequest->budget, 2) }}</p>
    @endif
    <p class="mt-1 text-sm text-gray-600">{{ $itemRequest->location }}</p>
    <p class="text-sm text-gray-600">Needed {{ $itemRequest->needed_from->format('Y/m/d') }} &ndash; {{ $itemRequest->needed_to->format('Y/m/d') }}</p>
    <p class="mt-4 flex flex-wrap items-center gap-2 text-sm text-gray-600">
        Requested by {{ $itemRequest->requester->displayName() }}
        <x-verified-badge :tier="$itemRequest->requester->verification_tier" />
        &middot; Status: {{ $itemRequest->status }}
    </p>

    @auth
        @if (auth()->id() !== $itemRequest->requester_id)
            <div class="mt-2">
                <x-report-button :target-user-id="$itemRequest->requester_id" label="Report this user" />
            </div>
        @endif

        @php($myOffer = $itemRequest->offers->firstWhere('lender_id', auth()->id()))
        @if (auth()->id() !== $itemRequest->requester_id && $itemRequest->status === 'OPEN' && !$myOffer)
            <form method="POST" action="{{ route('offers.store') }}" class="mt-6 space-y-4 rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                @csrf
                <input type="hidden" name="item_request_id" value="{{ $itemRequest->id }}">
                <h2 class="text-lg font-semibold text-primary">Send an offer</h2>
                <label class="block text-sm text-gray-600">
                    Your price
                    <input type="number" name="price" min="0" step="0.01" required class="mt-1 w-full rounded border border-gray-300 px-3 py-2 focus:border-accent focus:outline-none">
                </label>
                <label class="block text-sm text-gray-600">
                    Message (optional)
                    <textarea name="message" rows="3" class="mt-1 w-full rounded border border-gray-300 px-3 py-2 focus:border-accent focus:outline-none"></textarea>
                </label>
                <button type="submit" class="w-full rounded-full bg-accent px-3 py-2 font-medium text-white hover:opacity-90">Send offer</button>
            </form>
        @endif
    @endauth

    @error('offer')<p class="mt-4 text-sm text-red-600">{{ $message }}</p>@enderror

    @if ($itemRequest->offers->isNotEmpty())
        <div class="mt-6">
            <h2 class="mb-3 text-lg font-semibold text-primary">Offers ({{ $itemRequest->offers->count() }})</h2>
            <div class="space-y-3">
                @foreach ($itemRequest->offers as $offer)
                    <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                        <div class="flex items-center justify-between">
                            <p class="font-medium text-accent">${{ number_format($offer->price, 2) }}</p>
                            <span class="text-xs uppercase tracking-wide text-gray-500">{{ $offer->status }}</span>
                        </div>
                        <p class="flex items-center gap-2 text-sm text-gray-600">
                            from {{ $offer->lender->displayName() }}
                            <x-verified-badge :tier="$offer->lender->verification_tier" />
                        </p>
                        @if ($offer->message)
                            <p class="mt-1 text-sm text-gray-700">{{ $offer->message }}</p>
                        @endif
                        @auth
                            @if (auth()->id() === $itemRequest->requester_id && $offer->status === 'PENDING' && $itemRequest->status === 'OPEN')
                                <form method="POST" action="{{ route('offers.accept', $offer) }}" class="mt-3">
                                    @csrf
                                    <button type="submit" class="rounded-full bg-accent px-4 py-1.5 text-sm font-medium text-white hover:opacity-90">Accept offer</button>
                                </form>
                            @endif
                        @endauth
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
