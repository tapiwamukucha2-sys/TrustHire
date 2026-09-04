@extends('layouts.admin')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <h1 class="text-2xl font-semibold text-primary">Users</h1>
    <a href="{{ route('admin.verifications.index') }}" class="text-sm text-accent hover:underline">Pending verifications &rarr;</a>
</div>

<div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
    <table class="w-full text-left text-sm">
        <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-500">
            <tr>
                <th class="px-4 py-3">Name</th>
                <th class="px-4 py-3">Contact</th>
                <th class="px-4 py-3">Verification</th>
                <th class="px-4 py-3">Listings</th>
                <th class="px-4 py-3">Requests</th>
                <th class="px-4 py-3">Joined</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse ($users as $user)
                <tr>
                    <td class="px-4 py-3 font-medium text-primary">
                        {{ $user->displayName() }}
                        @if ($user->is_admin)
                            <span class="ml-1 rounded-full bg-primary/10 px-2 py-0.5 text-[10px] uppercase text-primary">Admin</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $user->phone ?? $user->email ?? '—' }}</td>
                    <td class="px-4 py-3">
                        @php
                            $tierColors = ['VERIFIED' => 'bg-accent/10 text-accent', 'PENDING' => 'bg-yellow-100 text-yellow-700', 'REJECTED' => 'bg-red-100 text-red-600'];
                        @endphp
                        <span class="rounded-full px-2 py-0.5 text-xs {{ $tierColors[$user->verification_tier] ?? 'bg-gray-100 text-gray-500' }}">{{ $user->verification_tier ?? 'BASIC' }}</span>
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $user->listings_count }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $user->item_requests_count }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $user->created_at->format('Y/m/d') }}</td>
                    <td class="px-4 py-3 text-right">
                        @if ($user->verification_tier === 'PENDING')
                            <form method="POST" action="{{ route('admin.verifications.approve', $user) }}" class="inline">
                                @csrf
                                <button type="submit" class="text-accent hover:underline">Verify</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="px-4 py-6 text-center text-gray-400">No users yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
