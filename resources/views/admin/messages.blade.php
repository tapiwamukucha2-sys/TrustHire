@extends('layouts.admin')

@section('content')
<h1 class="mb-2 text-2xl font-semibold text-primary">Messages</h1>
<p class="mb-6 text-sm text-gray-600">In-app chat between renters and lenders happens per booking. This is a read-only feed of the most recent messages for oversight.</p>

<div class="space-y-3">
    @forelse ($messages as $message)
        <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
            <div class="flex items-center justify-between text-xs text-gray-400">
                <span>{{ $message->sender->displayName() }} &middot; Booking #{{ $message->booking_id }}</span>
                <span>{{ $message->created_at->diffForHumans() }}</span>
            </div>
            <p class="mt-2 text-sm text-gray-700">{{ $message->body }}</p>
        </div>
    @empty
        <p class="text-sm text-gray-400">No messages yet.</p>
    @endforelse
</div>
@endsection
