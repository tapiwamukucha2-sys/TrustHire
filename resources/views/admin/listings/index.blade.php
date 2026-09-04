@extends('layouts.admin')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <h1 class="text-2xl font-semibold text-primary">Listings</h1>
    <a href="{{ route('listings.create') }}" class="rounded-full bg-accent px-4 py-2 text-sm font-medium text-white hover:opacity-90">+ Add listing</a>
</div>

<div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
    <table class="w-full text-left text-sm">
        <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-500">
            <tr>
                <th class="px-4 py-3">Photo</th>
                <th class="px-4 py-3">Title</th>
                <th class="px-4 py-3">Category</th>
                <th class="px-4 py-3">Owner</th>
                <th class="px-4 py-3">Price</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse ($listings as $listing)
                <tr>
                    <td class="px-4 py-3">
                        @if ($listing->firstPhoto())
                            <img src="{{ $listing->firstPhoto() }}" alt="" class="h-12 w-16 rounded object-cover">
                        @else
                            <div class="flex h-12 w-16 items-center justify-center rounded bg-gray-100 text-[10px] text-gray-400">No photo</div>
                        @endif
                    </td>
                    <td class="px-4 py-3 font-medium text-primary">{{ $listing->title }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $listing->category->name }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $listing->owner->displayName() }}</td>
                    <td class="px-4 py-3 text-gray-600">${{ number_format($listing->price, 2) }} / {{ $listing->price_unit }}</td>
                    <td class="px-4 py-3">
                        <span class="rounded-full px-2 py-0.5 text-xs {{ $listing->status === 'ACTIVE' ? 'bg-accent/10 text-accent' : 'bg-gray-100 text-gray-500' }}">{{ $listing->status }}</span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('admin.listings.edit', $listing) }}" class="text-accent hover:underline">Edit</a>
                        <form method="POST" action="{{ route('admin.listings.destroy', $listing) }}" class="inline" onsubmit="return confirm('Delete this listing?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="ml-3 text-red-500 hover:underline">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="px-4 py-6 text-center text-gray-400">No listings yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
