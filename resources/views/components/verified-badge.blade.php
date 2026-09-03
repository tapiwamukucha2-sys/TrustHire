@props(['tier'])

@if ($tier === 'VERIFIED')
    <span class="inline-flex items-center gap-1 rounded-full border border-accent/40 bg-accent/10 px-2 py-0.5 text-xs font-medium text-accent">
        <x-trust-shield class="h-3.5 w-3.5" color="currentColor" />
        Verified
    </span>
@elseif ($tier === 'PENDING')
    <span class="inline-flex items-center gap-1 rounded-full border border-gray-300 px-2 py-0.5 text-xs text-gray-500">
        Verification pending
    </span>
@endif
