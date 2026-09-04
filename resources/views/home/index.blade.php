@extends('layouts.app')

@section('content')
<div class="space-y-24">
    {{-- Hero --}}
    <section class="reveal relative overflow-hidden rounded-2xl text-center shadow-xl">
        <div class="absolute inset-0">
            <img src="{{ $settings->heroImage1Url() }}" alt="" class="hero-fade-a absolute inset-0 h-full w-full object-cover">
            <img src="{{ $settings->heroImage2Url() }}" alt="" class="hero-fade-b absolute inset-0 h-full w-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-b from-primary/60 via-primary/50 to-[#0B3A1F]/70"></div>
        </div>

        <div class="relative z-10 px-6 py-16 sm:py-24">
            <span class="hero-text-shadow inline-block rounded-full border border-white/30 bg-black/20 px-4 py-1 text-xs uppercase tracking-wide text-accent-light">A local, verified hiring marketplace</span>
            <h1 class="hero-text-shadow mx-auto mt-6 max-w-2xl font-serif text-5xl font-semibold leading-tight text-white sm:text-6xl">
                {{ $settings->hero_headline }}
            </h1>
            <p class="hero-text-shadow mx-auto mt-5 max-w-xl text-lg text-white/95">{{ $settings->hero_subtext }}</p>

            <form action="{{ route('browse') }}" method="get" class="mx-auto mt-8 flex max-w-md gap-2">
                <input type="text" name="location" placeholder="Enter your city or area" class="flex-1 rounded-full border-0 bg-white px-4 py-3 text-sm shadow-lg transition focus:outline-none focus:ring-2 focus:ring-accent-light">
                <button type="submit" class="rounded-full bg-accent px-5 py-3 text-sm font-medium text-white shadow-lg transition hover:scale-105 hover:opacity-90">Search</button>
            </form>

            <div class="mx-auto mt-5 flex max-w-xl flex-wrap justify-center gap-2 text-xs">
                @foreach ($categories->take(6) as $category)
                    <a href="{{ route('browse', ['category' => $category->slug]) }}" class="rounded-full border border-white/30 px-3 py-1 text-white/90 transition hover:border-accent-light hover:text-accent-light">{{ $category->name }}</a>
                @endforeach
            </div>

            @if ($verifiedCount > 0)
                <p class="mt-6 text-sm text-white/80">
                    <span class="font-semibold text-accent-light">{{ $verifiedCount }}</span> verified {{ Str::plural('member', $verifiedCount) }} on TrustHire
                </p>
            @endif
        </div>
    </section>

    {{-- Themed visual strip: property, agreements, garden, tools --}}
    <section class="reveal -mt-16">
        <div class="mx-auto grid max-w-3xl grid-cols-2 gap-4 sm:grid-cols-4">
            <div class="rounded-xl border border-gray-200 bg-white p-5 text-center shadow-lg transition hover:-translate-y-1 hover:shadow-xl">
                <svg viewBox="0 0 24 24" fill="none" class="mx-auto h-8 w-8 text-accent"><path d="M3 11 12 4l9 7" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/><path d="M5 10v9a1 1 0 0 0 1 1h4v-6h4v6h4a1 1 0 0 0 1-1v-9" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg>
                <p class="mt-2 text-xs font-medium text-primary">Property &amp; Spaces</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-5 text-center shadow-lg transition hover:-translate-y-1 hover:shadow-xl">
                <svg viewBox="0 0 24 24" fill="none" class="mx-auto h-8 w-8 text-accent"><path d="M3 12h3l2.5-2.5 3 3L15 9l3 3h3" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/><path d="M7 16h2M15 16h2" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                <p class="mt-2 text-xs font-medium text-primary">Trusted Agreements</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-5 text-center shadow-lg transition hover:-translate-y-1 hover:shadow-xl">
                <svg viewBox="0 0 24 24" fill="none" class="mx-auto h-8 w-8 text-accent"><path d="M9 3 6 6m9-3 3 3M6 6 3 9l12 12 3-3L6 6Z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <p class="mt-2 text-xs font-medium text-primary">Garden Equipment</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-5 text-center shadow-lg transition hover:-translate-y-1 hover:shadow-xl">
                <svg viewBox="0 0 24 24" fill="none" class="mx-auto h-8 w-8 text-accent"><path d="M14.7 6.3a4 4 0 0 0-5.4 5.4L3 18l3 3 6.3-6.3a4 4 0 0 0 5.4-5.4l-2.6 2.6-2-2 2.6-2.6Z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <p class="mt-2 text-xs font-medium text-primary">Tools &amp; Machinery</p>
            </div>
        </div>
    </section>

    {{-- Featured listings, grouped by category --}}
    @if ($featuredByCategory->isNotEmpty())
        <section class="reveal">
            <div class="flex items-center justify-between">
                <h2 class="font-serif text-2xl font-semibold text-primary sm:text-3xl">Featured on TrustHire</h2>
                <a href="{{ route('browse') }}" class="text-sm text-accent hover:underline">Browse all &rarr;</a>
            </div>
            <div class="mt-8 space-y-10">
                @foreach ($featuredByCategory as $categoryName => $categoryListings)
                    <div>
                        <h3 class="mb-3 text-sm font-semibold uppercase tracking-wide text-accent">{{ $categoryName }}</h3>
                        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                            @foreach ($categoryListings as $listing)
                                <a href="{{ route('listings.show', $listing) }}" class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm transition hover:-translate-y-1 hover:border-accent hover:shadow-md">
                                    @if ($listing->firstPhoto())
                                        <img src="{{ $listing->firstPhoto() }}" alt="{{ $listing->title }}" class="aspect-video w-full object-cover">
                                    @else
                                        <div class="flex aspect-video w-full items-center justify-center bg-gray-50 text-xs text-gray-400">No photo</div>
                                    @endif
                                    <div class="p-3">
                                        <h4 class="truncate text-sm font-semibold text-primary">{{ $listing->title }}</h4>
                                        <p class="mt-1 text-sm font-medium text-accent">${{ number_format($listing->price, 2) }} / {{ $listing->price_unit }}</p>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Why choose us --}}
    <section class="reveal">
        <h2 class="text-center font-serif text-2xl font-semibold text-primary sm:text-3xl">Why Choose TrustHire?</h2>
        <p class="mx-auto mt-2 max-w-xl text-center text-gray-600">Verification and direct negotiation, built into how the platform works.</p>
        <div class="mt-10 grid gap-6 sm:grid-cols-3">
            <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                <img src="{{ $settings->trustImage1Url() }}" alt="" class="h-36 w-full object-cover">
                <div class="p-6 text-center">
                    <h3 class="text-lg font-semibold text-primary">Verified Community</h3>
                    <p class="mt-2 text-sm text-gray-600">Every member starts with phone verification. ID and selfie checks are rolling out for high-value hires.</p>
                </div>
            </div>
            <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                <img src="{{ $settings->trustImage2Url() }}" alt="" class="h-36 w-full object-cover">
                <div class="p-6 text-center">
                    <h3 class="text-lg font-semibold text-primary">Direct Negotiation</h3>
                    <p class="mt-2 text-sm text-gray-600">Post what you need or have, and negotiate price directly. No bidding wars, no platform commission.</p>
                </div>
            </div>
            <div class="rounded-lg border border-gray-200 bg-white p-6 text-center shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                <x-trust-shield class="mx-auto h-8 w-8" color="#15803D" />
                <h3 class="mt-4 text-lg font-semibold text-primary">Local & Personal</h3>
                <p class="mt-2 text-sm text-gray-600">Built for hiring within your own community — from and to people you can actually meet.</p>
            </div>
        </div>
    </section>

    {{-- How it works --}}
    <section id="how-it-works" class="reveal">
        <h2 class="text-center font-serif text-2xl font-semibold text-primary sm:text-3xl">How it Works</h2>
        <p class="mx-auto mt-2 max-w-xl text-center text-gray-600">Simple on both sides of the hire.</p>
        <div class="mt-10 grid gap-10 md:grid-cols-2">
            <div>
                <h3 class="mb-5 text-center text-sm font-semibold uppercase tracking-wide text-accent">For Renters</h3>
                <ol class="space-y-5">
                    @foreach ([
                        ['Post what you need', 'Tell us the item, your budget, and your dates. Verified lenders nearby respond with offers.'],
                        ['Compare offers', "Review price, availability, and the lender's rating, then accept the offer that works for you."],
                        ['Chat & arrange pickup', 'Message your lender directly in-app to sort out handoff details.'],
                        ['Hire & review', 'Use the item, mark the booking complete, and leave a rating for the next person.'],
                    ] as $i => [$stepTitle, $stepBody])
                        <li class="flex gap-4">
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-accent text-sm font-semibold text-white">{{ $i + 1 }}</span>
                            <div><p class="font-medium text-primary">{{ $stepTitle }}</p><p class="text-sm text-gray-600">{{ $stepBody }}</p></div>
                        </li>
                    @endforeach
                </ol>
            </div>
            <div>
                <h3 class="mb-5 text-center text-sm font-semibold uppercase tracking-wide text-accent">For Lenders</h3>
                <ol class="space-y-5">
                    @foreach ([
                        ['List your item', 'Add a title, description, and your price per hour, day, or week.'],
                        ['Receive requests', 'Browse open requests in your category, or wait for renters to find your listing.'],
                        ['Send an offer', 'Quote your price directly on a renter\'s request — no bidding wars, no commission cut.'],
                        ['Get hired & rated', 'Confirm the booking, hand off the item, and build your reputation with every hire.'],
                    ] as $i => [$stepTitle, $stepBody])
                        <li class="flex gap-4">
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-accent text-sm font-semibold text-white">{{ $i + 1 }}</span>
                            <div><p class="font-medium text-primary">{{ $stepTitle }}</p><p class="text-sm text-gray-600">{{ $stepBody }}</p></div>
                        </li>
                    @endforeach
                </ol>
            </div>
        </div>
    </section>

    {{-- Trust & safety --}}
    <section class="reveal rounded-lg border border-gray-200 bg-white p-8 shadow-sm sm:p-10">
        <h2 class="text-center font-serif text-2xl font-semibold text-primary">Your Safety is Our Priority</h2>
        <p class="mx-auto mt-2 max-w-xl text-center text-gray-600">Trust is the reason TrustHire exists — here's what's live today, and what's coming next.</p>
        <div class="mt-8 grid gap-6 sm:grid-cols-3">
            <div><h3 class="font-medium text-accent">Phone-Verified Identity</h3><p class="mt-1 text-sm text-gray-600">Every member logs in with a verified phone number before posting or hiring.</p></div>
            <div><h3 class="font-medium text-accent">In-App Messaging</h3><p class="mt-1 text-sm text-gray-600">Coordinate pickup and details without sharing personal contact details upfront.</p></div>
            <div><h3 class="font-medium text-accent">Ratings After Every Hire</h3><p class="mt-1 text-sm text-gray-600">Both sides rate each other, building a public track record over time.</p></div>
        </div>
        <p class="mt-6 text-center text-xs text-gray-500">Coming soon: ID + selfie verification for high-value items, and optional damage-deposit holds. Read more on our <a href="{{ route('trust-safety') }}" class="text-accent hover:underline">Trust &amp; Safety page</a>.</p>
    </section>

    {{-- Categories --}}
    <section class="reveal">
        <div class="flex items-center justify-between">
            <h2 class="font-serif text-2xl font-semibold text-primary sm:text-3xl">Browse Categories</h2>
            <a href="{{ route('browse') }}" class="text-sm text-accent hover:underline">View all &rarr;</a>
        </div>
        <div class="mt-8 grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6">
            @foreach ($categories as $category)
                <a href="{{ route('browse', ['category' => $category->slug]) }}" class="rounded-lg border border-gray-200 bg-white p-4 text-center text-sm font-medium shadow-sm transition hover:-translate-y-1 hover:border-accent hover:text-accent hover:shadow-md">{{ $category->name }}</a>
            @endforeach
        </div>
    </section>

    {{-- CTA --}}
    <section class="reveal rounded-lg bg-gradient-to-br from-primary to-[#0B3A1F] px-8 py-12 text-center text-white sm:py-16">
        <h2 class="font-serif text-2xl font-semibold sm:text-3xl">Ready to Get Started?</h2>
        <p class="mx-auto mt-2 max-w-md text-sm text-white/70">Join TrustHire and start hiring — or earning — from your local community today.</p>
        <div class="mt-6 flex flex-wrap justify-center gap-3">
            <a href="{{ route('login') }}" class="rounded-full bg-accent px-5 py-2.5 text-sm font-medium text-white shadow-md transition hover:scale-105 hover:opacity-90">Create Free Account</a>
            <a href="{{ route('browse') }}" class="rounded-full border border-white/40 px-5 py-2.5 text-sm font-medium text-white transition hover:border-white">Browse Equipment</a>
        </div>
    </section>
</div>
@endsection
