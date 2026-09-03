@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-lg">
    <h1 class="text-2xl font-semibold text-primary">Trust &amp; Safety</h1>
    <p class="mt-4 text-gray-600">Trust is the reason TrustHire exists. Here's what's live today, and what's on the roadmap.</p>

    <div class="mt-8 space-y-6">
        <div>
            <h2 class="font-medium text-accent">Phone-Verified Identity</h2>
            <p class="mt-1 text-sm text-gray-600">Every member logs in with a verified phone number (or email) before posting or hiring.</p>
        </div>
        <div>
            <h2 class="font-medium text-accent">ID &amp; Selfie Verification</h2>
            <p class="mt-1 text-sm text-gray-600">Members can submit a photo ID and a selfie for manual review by our team, earning a visible "Verified" badge shown on their profile and listings.</p>
        </div>
        <div>
            <h2 class="font-medium text-accent">In-App Messaging</h2>
            <p class="mt-1 text-sm text-gray-600">Coordinate pickup and details without sharing personal contact details upfront.</p>
        </div>
        <div>
            <h2 class="font-medium text-accent">Ratings After Every Hire</h2>
            <p class="mt-1 text-sm text-gray-600">Both sides rate each other after every completed booking, building a public track record over time.</p>
        </div>
        <div>
            <h2 class="font-medium text-accent">Report a User or Listing</h2>
            <p class="mt-1 text-sm text-gray-600">Anyone can flag a scam, a fake listing, a no-show, or abusive behavior — every report is reviewed by our team.</p>
        </div>
    </div>

    <p class="mt-8 rounded border border-gray-200 bg-gray-50 p-4 text-sm text-gray-600">
        Coming soon: automated ID/liveness verification, damage-deposit holds, and in-app payment escrow. Today, verification is manual and payments happen off-platform — see our <a href="{{ route('pricing') }}" class="text-accent hover:underline">Pricing</a> page for details.
    </p>
</div>
@endsection
