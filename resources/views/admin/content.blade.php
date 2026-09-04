@extends('layouts.app')

@section('content')
<div class="max-w-lg">
    @include('admin._nav')
    <h1 class="mb-2 text-2xl font-semibold text-primary">Site content</h1>
    <p class="mb-6 text-sm text-gray-600">Edit the homepage hero text and an optional site-wide announcement banner — changes go live immediately, no code deploy needed.</p>

    @if (request('saved'))
        <p class="mb-4 rounded border border-gray-200 bg-white p-3 text-sm text-accent shadow-sm">Saved.</p>
    @endif

    <form method="POST" action="{{ route('admin.content.update') }}" enctype="multipart/form-data" class="space-y-4">
        @csrf
        <label class="block text-sm text-gray-600">
            Hero headline
            <input type="text" name="hero_headline" required value="{{ old('hero_headline', $settings->hero_headline) }}" class="mt-1 w-full rounded border border-gray-300 px-3 py-2 focus:border-accent focus:outline-none">
        </label>
        <label class="block text-sm text-gray-600">
            Hero subtext
            <textarea name="hero_subtext" required rows="3" class="mt-1 w-full rounded border border-gray-300 px-3 py-2 focus:border-accent focus:outline-none">{{ old('hero_subtext', $settings->hero_subtext) }}</textarea>
        </label>

        <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
            <p class="mb-3 text-sm font-medium text-primary">Hero background photos</p>
            <p class="mb-4 text-xs text-gray-500">The homepage hero slowly crossfades between these two photos. Upload your own to replace them.</p>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <img src="{{ $settings->heroImage1Url() }}" alt="Hero photo 1" class="mb-2 h-28 w-full rounded object-cover">
                    <label class="block text-xs text-gray-600">
                        Photo 1
                        <input type="file" name="hero_image_1" accept="image/*" class="mt-1 block w-full text-xs">
                    </label>
                </div>
                <div>
                    <img src="{{ $settings->heroImage2Url() }}" alt="Hero photo 2" class="mb-2 h-28 w-full rounded object-cover">
                    <label class="block text-xs text-gray-600">
                        Photo 2
                        <input type="file" name="hero_image_2" accept="image/*" class="mt-1 block w-full text-xs">
                    </label>
                </div>
            </div>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="announcement_enabled" @checked($settings->announcement_enabled)>
                Show announcement banner site-wide
            </label>
            <textarea name="announcement_text" rows="2" placeholder="e.g. TrustHire is in early access — thanks for trying it out!" class="mt-3 w-full rounded border border-gray-300 px-3 py-2 focus:border-accent focus:outline-none">{{ old('announcement_text', $settings->announcement_text) }}</textarea>
        </div>
        <button type="submit" class="w-full rounded-full bg-accent px-3 py-2 font-medium text-white hover:opacity-90">Save changes</button>
    </form>
</div>
@endsection
