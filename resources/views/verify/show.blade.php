@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-md">
    <h1 class="mb-2 text-2xl font-semibold text-primary">Get verified</h1>
    <p class="mb-6 text-sm text-gray-600">Verified members get a visible trust badge on their profile and listings. Submit a photo ID and a selfie — a real person on our team reviews every submission (no automated checks yet, so this may take a little while).</p>

    @if ($user->verification_tier === 'VERIFIED')
        <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
            <x-verified-badge tier="VERIFIED" />
            <p class="mt-2 text-sm text-gray-600">You're verified. Nothing more to do here.</p>
        </div>
    @elseif ($user->verification_tier === 'PENDING')
        <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
            <x-verified-badge tier="PENDING" />
            <p class="mt-2 text-sm text-gray-600">Submitted on {{ $user->verification_requested_at?->format('Y/m/d') }}. We'll notify you once it's reviewed.</p>
        </div>
    @else
        @if ($user->verification_tier === 'REJECTED')
            <p class="mb-4 rounded border border-red-300 bg-red-50 p-3 text-sm text-red-700">
                Your last submission wasn't approved{{ $user->verification_reject_reason ? ': '.$user->verification_reject_reason : '.' }} Feel free to try again.
            </p>
        @endif
        <form method="POST" action="{{ route('verify.submit') }}" class="space-y-4">
            @csrf
            <x-single-image-upload name="id_document_url" label="Photo ID (national ID or passport)" />
            <x-single-image-upload name="selfie_url" label="A clear selfie of your face" />
            @error('id_document_url')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
            <button type="submit" class="w-full rounded-full bg-accent px-3 py-2 font-medium text-white hover:opacity-90">Submit for review</button>
        </form>
    @endif
</div>
@endsection
