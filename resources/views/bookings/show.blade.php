@extends('layouts.app')

@php
    $counterparty = $booking->counterparty(auth()->id());
    $myReview = $booking->reviews->firstWhere('author_id', auth()->id());
    $paymentLabels = [
        'ECOCASH' => 'EcoCash', 'ONEMONEY' => 'OneMoney', 'TELECASH' => 'Telecash',
        'PAYNOW' => 'Paynow', 'BANK_TRANSFER' => 'Bank Transfer', 'CASH' => 'Cash', 'OTHER' => 'Other',
    ];
@endphp

@section('content')
<div class="mx-auto max-w-lg">
    <p class="text-xs uppercase tracking-wide text-accent">{{ $booking->itemRequest->category->name }}</p>
    <h1 class="mt-1 text-2xl font-semibold text-primary">{{ $booking->itemRequest->title }}</h1>
    <p class="mt-2 flex flex-wrap items-center gap-2 text-sm text-gray-600">
        With {{ $counterparty->displayName() }}
        <x-verified-badge :tier="$counterparty->verification_tier" />
        &middot; Status: {{ $booking->status }}
    </p>
    <p class="text-sm text-gray-600">{{ $booking->start_date->format('Y/m/d') }} &ndash; {{ $booking->end_date->format('Y/m/d') }}</p>
    <div class="mt-2">
        <x-report-button :target-user-id="$counterparty->id" label="Report this user" />
    </div>

    <div class="mt-6 rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold text-primary">Payment</h2>
            <span class="font-medium text-accent">${{ number_format($booking->offer->price, 2) }}</span>
        </div>
        <p class="mt-1 text-xs text-gray-500">TrustHire doesn't process payments yet — coordinate and pay directly, then track it here.</p>

        @if ($booking->paid_at)
            <p class="mt-3 text-sm text-gray-700">
                Marked paid via <span class="font-medium text-accent">{{ $paymentLabels[$booking->payment_method] ?? $booking->payment_method }}</span> on {{ $booking->paid_at->format('Y/m/d') }}.
            </p>
        @else
            <form method="POST" action="{{ route('bookings.payment-method', $booking) }}" class="mt-3 flex flex-wrap gap-2">
                @csrf
                @foreach ($paymentLabels as $value => $label)
                    <button type="submit" name="payment_method" value="{{ $value }}" class="rounded-full border px-3 py-1 text-xs {{ $booking->payment_method === $value ? 'border-accent bg-accent text-white' : 'border-gray-300 text-gray-600 hover:border-accent' }}">{{ $label }}</button>
                @endforeach
            </form>
            @if ($booking->payment_method)
                <form method="POST" action="{{ route('bookings.mark-paid', $booking) }}" class="mt-3">
                    @csrf
                    <button type="submit" class="rounded-full bg-accent px-4 py-1.5 text-sm font-medium text-white hover:opacity-90">Mark as paid ({{ $paymentLabels[$booking->payment_method] }})</button>
                </form>
            @endif
        @endif
    </div>

    @if ($booking->status === 'CONFIRMED')
        <form method="POST" action="{{ route('bookings.complete', $booking) }}" class="mt-4">
            @csrf
            <button type="submit" class="rounded-full bg-accent px-4 py-1.5 text-sm font-medium text-white hover:opacity-90">Mark as completed</button>
        </form>
    @endif

    <div class="mt-8">
        <h2 class="mb-3 text-lg font-semibold text-primary">Messages</h2>
        <div class="max-h-80 space-y-2 overflow-y-auto rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
            @forelse ($booking->messages as $message)
                <div class="{{ $message->sender_id === auth()->id() ? 'text-right' : 'text-left' }}">
                    <p class="inline-block rounded-lg px-3 py-1.5 text-sm {{ $message->sender_id === auth()->id() ? 'bg-accent text-white' : 'border border-gray-200 text-gray-700' }}">{{ $message->body }}</p>
                </div>
            @empty
                <p class="text-sm text-gray-500">No messages yet.</p>
            @endforelse
        </div>
        <form method="POST" action="{{ route('bookings.messages.store', $booking) }}" class="mt-3 flex gap-2">
            @csrf
            <input type="text" name="body" required placeholder="Write a message..." class="flex-1 rounded border border-gray-300 px-3 py-2 text-sm focus:border-accent focus:outline-none">
            <button type="submit" class="rounded-full bg-accent px-4 py-2 text-sm font-medium text-white hover:opacity-90">Send</button>
        </form>
    </div>

    @if ($booking->status === 'COMPLETED')
        <div class="mt-8">
            <h2 class="mb-3 text-lg font-semibold text-primary">{{ $myReview ? 'Your review' : 'Rate '.$counterparty->displayName() }}</h2>
            @if ($myReview)
                <p class="rounded-lg border border-gray-200 bg-white p-4 text-sm text-gray-700 shadow-sm">
                    <span class="font-medium text-accent">{{ $myReview->rating }}/5</span> &mdash; {{ $myReview->comment }}
                </p>
            @else
                <form method="POST" action="{{ route('bookings.review', $booking) }}" class="space-y-3 rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                    @csrf
                    <label class="block text-sm text-gray-600">
                        Rating
                        <select name="rating" required class="mt-1 w-full rounded border border-gray-300 px-3 py-2 focus:border-accent focus:outline-none">
                            @for ($n = 5; $n >= 1; $n--)
                                <option value="{{ $n }}">{{ $n }} star{{ $n > 1 ? 's' : '' }}</option>
                            @endfor
                        </select>
                    </label>
                    <textarea name="comment" rows="3" placeholder="Optional comment" class="w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-accent focus:outline-none"></textarea>
                    <button type="submit" class="rounded-full bg-accent px-4 py-2 text-sm font-medium text-white hover:opacity-90">Submit review</button>
                </form>
            @endif
        </div>
    @endif
</div>
@endsection
