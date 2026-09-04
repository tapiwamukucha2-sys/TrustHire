@props(['name' => 'password', 'required' => true, 'minlength' => null, 'class' => ''])

<div x-data="{ show: false }" class="relative">
    <input
        :type="show ? 'text' : 'password'"
        name="{{ $name }}"
        @if ($required) required @endif
        @if ($minlength) minlength="{{ $minlength }}" @endif
        {{ $attributes->merge(['class' => 'w-full rounded border border-gray-300 px-3 py-2 pr-10 focus:border-accent focus:outline-none '.$class]) }}
    >
    <button
        type="button"
        @click="show = !show"
        tabindex="-1"
        class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-400 hover:text-gray-600"
        :aria-label="show ? 'Hide password' : 'Show password'"
    >
        <svg x-show="!show" viewBox="0 0 24 24" fill="none" class="h-5 w-5"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.6"/></svg>
        <svg x-show="show" x-cloak viewBox="0 0 24 24" fill="none" class="h-5 w-5"><path d="M3 3l18 18M10.6 10.6a3 3 0 0 0 4.24 4.24M9.36 5.6A10.8 10.8 0 0 1 12 5c6.5 0 10 7 10 7a13.4 13.4 0 0 1-3.15 3.9M6.5 6.5C4 8.2 2 12 2 12s3.5 7 10 7c1.4 0 2.65-.32 3.75-.82" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
    </button>
</div>
