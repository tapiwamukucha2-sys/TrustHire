@extends('layouts.app')

@section('content')
<div>
    @include('admin._nav')
    <h1 class="mb-6 text-2xl font-semibold text-primary">Verification requests</h1>
    @if ($pending->isEmpty())
        <p class="text-gray-500">Nothing pending.</p>
    @endif
    <div class="space-y-6">
        @foreach ($pending as $user)
            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                <p class="font-medium">{{ $user->displayName() }}</p>
                <p class="text-xs text-gray-500">Submitted {{ $user->verification_requested_at?->format('Y/m/d H:i') }}</p>
                <div class="mt-3 grid grid-cols-2 gap-3">
                    @if ($user->id_document_url)
                        <img src="{{ $user->id_document_url }}" alt="ID document" class="w-full rounded border border-gray-200 object-contain">
                    @endif
                    @if ($user->selfie_url)
                        <img src="{{ $user->selfie_url }}" alt="Selfie" class="w-full rounded border border-gray-200 object-contain">
                    @endif
                </div>
                <div class="mt-4 flex gap-2">
                    <form method="POST" action="{{ route('admin.verifications.approve', $user) }}">
                        @csrf
                        <button type="submit" class="rounded-full bg-accent px-4 py-1.5 text-sm font-medium text-white hover:opacity-90">Approve</button>
                    </form>
                    <form method="POST" action="{{ route('admin.verifications.reject', $user) }}" class="flex flex-1 gap-2">
                        @csrf
                        <input type="text" name="reason" placeholder="Rejection reason (optional)" class="flex-1 rounded border border-gray-300 px-3 py-1.5 text-sm focus:border-accent focus:outline-none">
                        <button type="submit" class="rounded-full border border-gray-300 px-4 py-1.5 text-sm text-gray-600 hover:border-accent hover:text-ink">Reject</button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
