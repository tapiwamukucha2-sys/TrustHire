<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Admin' }} — TrustHire</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-gray-50 text-ink antialiased">
    <div class="flex min-h-screen">
        <aside class="hidden w-56 shrink-0 bg-[#0B3A1F] text-white/80 md:block">
            <div class="flex items-center gap-2 px-5 py-5 font-serif text-lg font-semibold text-white">
                <x-trust-shield class="h-7 w-7" />
                TrustHire Admin
            </div>
            <nav class="space-y-1 px-3 py-2 text-sm">
                @php
                    $links = [
                        ['admin.dashboard', 'Dashboard'],
                        ['admin.listings.index', 'Listings'],
                        ['admin.categories.index', 'Categories'],
                        ['admin.users.index', 'Users'],
                        ['admin.verifications.index', 'Verifications'],
                        ['admin.reports.index', 'Reports'],
                        ['admin.messages.index', 'Messages'],
                        ['admin.content.show', 'Site Content & Hero'],
                    ];
                @endphp
                @foreach ($links as [$route, $label])
                    <a href="{{ route($route) }}" class="block rounded px-3 py-2 transition {{ request()->routeIs($route) ? 'bg-accent text-white' : 'hover:bg-white/10 hover:text-white' }}">{{ $label }}</a>
                @endforeach
            </nav>
        </aside>

        <div class="flex-1">
            <header class="flex items-center justify-between border-b border-gray-200 bg-white px-6 py-3">
                <a href="{{ route('home') }}" class="text-sm text-gray-500 hover:text-accent">&larr; Visit Site</a>
                <div class="flex items-center gap-4 text-sm">
                    <span class="text-gray-600">{{ auth()->user()->name ?? auth()->user()->username ?? 'Admin' }}</span>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="text-gray-500 hover:text-accent">Log out</button>
                    </form>
                </div>
            </header>

            <main class="p-6">
                @if (session('status'))
                    <div class="mb-6 rounded border border-accent/30 bg-accent/5 p-3 text-sm text-primary">{{ session('status') }}</div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
