@extends('layouts.admin')

@php
    $reasonLabels = [
        'SCAM' => 'Scam', 'FAKE_LISTING' => 'Fake listing', 'NO_SHOW' => 'No-show',
        'ABUSIVE_BEHAVIOR' => 'Abusive behavior', 'OTHER' => 'Other',
    ];
@endphp

@section('content')
<div>
    <h1 class="mb-6 text-2xl font-semibold text-primary">Open reports</h1>
    @if ($reports->isEmpty())
        <p class="text-gray-500">No open reports.</p>
    @endif
    <div class="space-y-4">
        @foreach ($reports as $report)
            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <p class="font-medium text-accent">{{ $reasonLabels[$report->reason] ?? $report->reason }}</p>
                    <span class="text-xs text-gray-500">{{ $report->created_at->format('Y/m/d H:i') }}</span>
                </div>
                <p class="mt-1 text-sm text-gray-600">
                    From {{ $report->reporter->displayName() }}
                    @if ($report->targetUser) about {{ $report->targetUser->displayName() }} @endif
                    @if ($report->target_listing_id) about listing #{{ $report->target_listing_id }} @endif
                </p>
                @if ($report->details)
                    <p class="mt-2 text-sm text-ink">{{ $report->details }}</p>
                @endif
                <div class="mt-3 flex gap-2">
                    <form method="POST" action="{{ route('admin.reports.resolve', $report) }}">
                        @csrf
                        <input type="hidden" name="status" value="RESOLVED">
                        <button type="submit" class="rounded-full bg-accent px-4 py-1.5 text-sm font-medium text-white hover:opacity-90">Mark resolved</button>
                    </form>
                    <form method="POST" action="{{ route('admin.reports.resolve', $report) }}">
                        @csrf
                        <input type="hidden" name="status" value="DISMISSED">
                        <button type="submit" class="rounded-full border border-gray-300 px-4 py-1.5 text-sm text-gray-600 hover:border-accent hover:text-ink">Dismiss</button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
