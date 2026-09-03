<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'TrustHire — hire local, hire verified' }}</title>
    <meta name="description" content="A peer-to-peer local hiring marketplace for tools, electronics, vehicles, event gear, and more — hire local, hire verified.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="min-h-screen bg-white text-ink antialiased">
    @php($settings = \App\Models\SiteSettings::current())

    @if ($settings->announcement_enabled && $settings->announcement_text)
        <div class="bg-gradient-to-r from-primary to-accent px-4 py-2 text-center text-sm font-medium text-white">
            {{ $settings->announcement_text }}
        </div>
    @endif

    <header class="sticky top-0 z-30 bg-primary/95 text-white shadow-lg backdrop-blur" x-data="{ mobileOpen: false, accountOpen: false }">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-4">
            <a href="{{ route('home') }}" class="flex items-center gap-2 font-serif text-2xl font-semibold tracking-wide">
                <x-trust-shield class="h-8 w-8" />
                TrustHire
            </a>

            <nav class="hidden items-center gap-6 text-sm sm:flex">
                <a href="{{ route('home') }}" class="transition hover:text-accent-light">Home</a>
                <a href="{{ route('browse') }}" class="transition hover:text-accent-light">Browse</a>
                <a href="{{ route('requests.create') }}" class="transition hover:text-accent-light">Post a request</a>
                <a href="{{ route('listings.create') }}" class="transition hover:text-accent-light">List an item</a>

                @auth
                    <div class="relative" @click.outside="accountOpen = false">
                        <button type="button" @click="accountOpen = !accountOpen" class="flex items-center gap-1 rounded-full border border-white/30 px-3 py-1.5 transition hover:border-white">
                            <span>{{ auth()->user()->name ?? 'Account' }}</span>
                            <span class="transition-transform" :class="{ 'rotate-180': accountOpen }">&#9662;</span>
                        </button>
                        <div x-show="accountOpen"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 -translate-y-1 scale-95"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             x-cloak class="absolute right-0 mt-2 w-48 origin-top-right rounded-md bg-white py-2 text-ink shadow-xl ring-1 ring-black/5">
                            <a href="{{ route('verify.show') }}" class="block px-4 py-2 text-sm hover:bg-gray-50">Get verified</a>
                            @if (auth()->user()->is_admin)
                                <a href="{{ route('admin.verifications.index') }}" class="block px-4 py-2 text-sm hover:bg-gray-50">Admin</a>
                            @endif
                            <a href="{{ route('profile.show') }}" class="block px-4 py-2 text-sm hover:bg-gray-50">Profile</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full px-4 py-2 text-left text-sm hover:bg-gray-50">Log out</button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="rounded-full bg-accent px-4 py-1.5 font-medium text-white transition hover:opacity-90">Log in</a>
                @endauth
            </nav>

            <button type="button" class="sm:hidden" @click="mobileOpen = !mobileOpen" aria-label="Toggle menu">
                <span class="block h-0.5 w-6 bg-white transition-transform" :class="{ 'translate-y-1.5 rotate-45': mobileOpen }"></span>
                <span class="mt-1.5 block h-0.5 w-6 bg-white transition-transform" :class="{ '-translate-y-1.5 -rotate-45': mobileOpen }"></span>
            </button>
        </div>

        <div x-show="mobileOpen"
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-100"
             x-cloak class="border-t border-white/10 bg-primary px-4 py-4 sm:hidden">
            <div class="flex flex-col gap-3 text-sm">
                <a href="{{ route('home') }}">Home</a>
                <a href="{{ route('browse') }}">Browse</a>
                <a href="{{ route('requests.create') }}">Post a request</a>
                <a href="{{ route('listings.create') }}">List an item</a>
                @auth
                    <a href="{{ route('verify.show') }}">Get verified</a>
                    @if (auth()->user()->is_admin)
                        <a href="{{ route('admin.verifications.index') }}">Admin</a>
                    @endif
                    <a href="{{ route('profile.show') }}">Profile</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-left">Log out</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="font-medium">Log in</a>
                @endauth
            </div>
        </div>

        <div class="h-[3px] bg-gradient-to-r from-accent via-accent-light to-accent"></div>
    </header>

    <main class="mx-auto max-w-6xl px-4 py-10">
        @if (session('status'))
            <div class="mb-6 rounded border border-accent/30 bg-accent/5 p-3 text-sm text-primary">{{ session('status') }}</div>
        @endif
        @yield('content')
    </main>

    <footer class="mt-16 bg-gradient-to-b from-primary to-[#0B3A1F] text-white/80">
        <div class="h-1 bg-gradient-to-r from-accent via-accent-light to-accent"></div>
        <div class="mx-auto grid max-w-6xl gap-10 px-4 py-16 sm:grid-cols-2 md:grid-cols-5 md:divide-x md:divide-white/10">
            <div class="md:col-span-2 md:pr-6">
                <p class="flex items-center gap-2 font-serif text-2xl font-semibold text-white">
                    <x-trust-shield class="h-7 w-7" />
                    TrustHire
                </p>
                <p class="mt-3 max-w-xs text-sm">A peer-to-peer local hiring marketplace for tools, electronics, vehicles, event gear, and more. Hire local, hire verified.</p>
            </div>
            <div class="md:px-6">
                <h3 class="border-b border-accent-light/40 pb-2 text-sm font-semibold uppercase tracking-wide text-accent-light">Quick Links</h3>
                <ul class="mt-3 space-y-2 text-sm">
                    <li><a href="{{ route('home') }}" class="transition hover:text-accent-light">Home</a></li>
                    <li><a href="{{ route('home') }}#how-it-works" class="transition hover:text-accent-light">How It Works</a></li>
                    <li><a href="{{ route('browse') }}" class="transition hover:text-accent-light">Browse</a></li>
                    <li><a href="{{ route('verify.show') }}" class="transition hover:text-accent-light">Get Verified</a></li>
                </ul>
            </div>
            <div class="md:px-6">
                <h3 class="border-b border-accent-light/40 pb-2 text-sm font-semibold uppercase tracking-wide text-accent-light">Support</h3>
                <ul class="mt-3 space-y-2 text-sm">
                    <li><a href="{{ route('trust-safety') }}" class="transition hover:text-accent-light">Trust & Safety</a></li>
                    <li><a href="{{ route('pricing') }}" class="transition hover:text-accent-light">Pricing</a></li>
                    <li><a href="{{ route('disputes') }}" class="transition hover:text-accent-light">Dispute Resolution</a></li>
                    <li><a href="{{ route('help') }}" class="transition hover:text-accent-light">Help Center</a></li>
                    <li><a href="{{ route('terms') }}" class="transition hover:text-accent-light">Terms and Conditions</a></li>
                    <li><a href="{{ route('privacy') }}" class="transition hover:text-accent-light">Privacy Policy</a></li>
                    <li><a href="{{ route('faq') }}" class="transition hover:text-accent-light">FAQs</a></li>
                </ul>
            </div>
            <div class="md:pl-6">
                <h3 class="border-b border-accent-light/40 pb-2 text-sm font-semibold uppercase tracking-wide text-accent-light">Contact Us</h3>
                <ul class="mt-3 space-y-2 text-sm">
                    <li>+263 77 665 1578</li>
                    <li>hello@trusthire.co.zw</li>
                </ul>
            </div>
        </div>
        <div class="border-t border-white/10 py-4 text-center text-xs">
            &copy; {{ date('Y') }} TrustHire. Peer-to-peer local hiring marketplace.
        </div>
    </footer>

    <a href="https://wa.me/263776651578?text={{ urlencode('Hi TrustHire, I have a question.') }}" target="_blank" rel="noopener noreferrer" class="fixed bottom-6 right-6 z-40 flex h-12 w-12 items-center justify-center rounded-full bg-[#25D366] text-white shadow-lg transition hover:scale-105 hover:opacity-90" aria-label="Chat on WhatsApp">
        <svg viewBox="0 0 24 24" fill="currentColor" class="h-6 w-6"><path d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5.1-1.3A10 10 0 1 0 12 2Zm0 18.2a8.1 8.1 0 0 1-4.2-1.2l-.3-.2-3 .8.8-2.9-.2-.3A8.2 8.2 0 1 1 12 20.2Zm4.5-6.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1-.2.2-.7.8-.8.9-.2.2-.3.2-.5.1a6.7 6.7 0 0 1-2-1.2 7.4 7.4 0 0 1-1.4-1.7c-.1-.2 0-.4.1-.5l.4-.4c.1-.1.2-.3.2-.4.1-.2 0-.3 0-.5l-.7-1.7c-.2-.4-.4-.4-.6-.4h-.5c-.2 0-.5.1-.7.3a2.5 2.5 0 0 0-.8 1.9c0 1.1.8 2.2.9 2.3.1.2 1.6 2.5 4 3.4.6.3 1 .4 1.4.5.6.2 1.1.2 1.5.1.5-.1 1.5-.6 1.7-1.2.2-.6.2-1 .1-1.2 0-.1-.2-.2-.4-.3Z"/></svg>
    </a>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var targets = document.querySelectorAll('.reveal');
            if (!('IntersectionObserver' in window) || targets.length === 0) {
                targets.forEach(function (el) { el.classList.add('reveal-visible'); });
                return;
            }
            var observer = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('reveal-visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.15 });
            targets.forEach(function (el) { observer.observe(el); });
        });
    </script>
</body>
</html>
