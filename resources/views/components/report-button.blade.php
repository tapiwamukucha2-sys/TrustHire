@props(['targetUserId' => null, 'targetListingId' => null, 'label' => 'Report'])

<div x-data="{ open: false }">
    <button type="button" x-show="!open" @click="open = true" class="text-xs text-gray-500 underline hover:text-accent">{{ $label }}</button>
    <form x-show="open" x-cloak method="POST" action="{{ route('reports.store') }}" class="mt-2 space-y-2 rounded border border-gray-200 bg-white p-3 text-sm">
        @csrf
        @if ($targetUserId)
            <input type="hidden" name="target_user_id" value="{{ $targetUserId }}">
        @endif
        @if ($targetListingId)
            <input type="hidden" name="target_listing_id" value="{{ $targetListingId }}">
        @endif
        <select name="reason" required class="w-full rounded border border-gray-300 px-2 py-1">
            <option value="">Reason...</option>
            <option value="SCAM">Scam</option>
            <option value="FAKE_LISTING">Fake listing</option>
            <option value="NO_SHOW">No-show</option>
            <option value="ABUSIVE_BEHAVIOR">Abusive behavior</option>
            <option value="OTHER">Other</option>
        </select>
        <textarea name="details" rows="2" placeholder="Details (optional)" class="w-full rounded border border-gray-300 px-2 py-1"></textarea>
        <div class="flex gap-2">
            <button type="submit" class="rounded-full bg-accent px-3 py-1 text-xs font-medium text-white">Submit</button>
            <button type="button" @click="open = false" class="text-xs text-gray-500">Cancel</button>
        </div>
    </form>
</div>
