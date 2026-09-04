@extends('layouts.admin')

@section('content')
<h1 class="mb-6 text-2xl font-semibold text-primary">Dashboard</h1>

<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
    @foreach ([
        ['Listings', $stats['listings'], route('admin.listings.index')],
        ['Requests', $stats['requests'], route('admin.listings.index')],
        ['Bookings', $stats['bookings'], route('admin.listings.index')],
        ['Users', $stats['users'], route('admin.users.index')],
        ['Verified Users', $stats['verifiedUsers'], route('admin.verifications.index')],
        ['Pending Verifications', $stats['pendingVerifications'], route('admin.verifications.index')],
        ['Open Reports', $stats['openReports'], route('admin.reports.index')],
        ['Messages', $stats['messages'], route('admin.messages.index')],
    ] as [$label, $value, $link])
        <a href="{{ $link }}" class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
            <p class="text-sm text-gray-500">{{ $label }}</p>
            <p class="mt-1 text-3xl font-semibold text-primary">{{ $value }}</p>
        </a>
    @endforeach
</div>

<div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
    <a href="{{ route('admin.content.show') }}" class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
        <p class="font-medium text-primary">Site Content & Hero Slides</p>
        <p class="mt-1 text-sm text-gray-500">Edit the homepage headline, hero background photos, and the site-wide announcement banner.</p>
    </a>
    <a href="{{ route('admin.listings.index') }}" class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
        <p class="font-medium text-primary">Manage Listings</p>
        <p class="mt-1 text-sm text-gray-500">Edit titles, prices, photos, and status for any listing on the platform.</p>
    </a>
    <a href="{{ route('admin.categories.index') }}" class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
        <p class="font-medium text-primary">Categories</p>
        <p class="mt-1 text-sm text-gray-500">Add, rename, or remove listing categories.</p>
    </a>
</div>
@endsection
