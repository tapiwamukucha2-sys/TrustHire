@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-sm" x-data="{ tab: '{{ old('_tab', session('tab', 'phone')) }}', emailMode: '{{ session('emailMode', 'login') }}' }">
    <h1 class="mb-6 text-2xl font-semibold text-primary">Log in to TrustHire</h1>

    <a href="{{ route('auth.google.redirect', ['callback' => $callbackUrl]) }}" class="mb-6 flex w-full items-center justify-center gap-2 rounded-full border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 transition hover:border-accent hover:text-accent">
        <svg viewBox="0 0 24 24" class="h-4 w-4"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1Z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.99.66-2.25 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.85A11 11 0 0 0 12 23Z"/><path fill="#FBBC05" d="M5.84 14.1A6.6 6.6 0 0 1 5.5 12c0-.73.13-1.44.34-2.1V7.05H2.18A11 11 0 0 0 1 12c0 1.77.43 3.45 1.18 4.95l3.66-2.85Z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1a11 11 0 0 0-9.82 6.05l3.66 2.85C6.71 7.31 9.14 5.38 12 5.38Z"/></svg>
        Continue with Google
    </a>

    <div class="mb-6 flex items-center gap-3 text-xs text-gray-400">
        <div class="h-px flex-1 bg-gray-200"></div>
        or
        <div class="h-px flex-1 bg-gray-200"></div>
    </div>

    <div class="mb-6 flex gap-6 border-b text-sm">
        <button type="button" @click="tab = 'phone'" :class="tab === 'phone' ? 'border-b-2 border-accent font-semibold text-accent' : 'text-gray-500'" class="pb-2">Phone</button>
        <button type="button" @click="tab = 'email'" :class="tab === 'email' ? 'border-b-2 border-accent font-semibold text-accent' : 'text-gray-500'" class="pb-2">Email</button>
    </div>

    <div x-show="tab === 'phone'">
        @if (session('step') === 'code')
            <form method="POST" action="{{ route('otp.verify') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="phone" value="{{ session('phone') }}">
                <input type="hidden" name="callback" value="{{ $callbackUrl }}">
                @if (session('devCode'))
                    <p class="rounded border border-accent/30 bg-accent/5 p-3 text-sm text-gray-600">
                        Dev mode: no SMS provider is wired up yet, so your code is <strong class="text-accent">{{ session('devCode') }}</strong>.
                    </p>
                @endif
                <label class="block text-sm text-gray-600">
                    6-digit code
                    <input type="text" name="code" required maxlength="6" class="mt-1 w-full rounded border border-gray-300 px-3 py-2 focus:border-accent focus:outline-none">
                </label>
                @error('code')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
                <button type="submit" class="w-full rounded-full bg-accent px-3 py-2 font-medium text-white hover:opacity-90">Verify & log in</button>
            </form>
        @else
            <form method="POST" action="{{ route('otp.send') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="callback" value="{{ $callbackUrl }}">
                <label class="block text-sm text-gray-600">
                    Phone number
                    <input type="tel" name="phone" required placeholder="+263 77 123 4567" class="mt-1 w-full rounded border border-gray-300 px-3 py-2 focus:border-accent focus:outline-none">
                </label>
                @error('phone')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
                <button type="submit" class="w-full rounded-full bg-accent px-3 py-2 font-medium text-white hover:opacity-90">Send code</button>
            </form>
        @endif
    </div>

    <div x-show="tab === 'email'" x-cloak>
        <div class="mb-4 flex justify-center gap-4 text-xs text-gray-500">
            <button type="button" @click="emailMode = 'login'" :class="emailMode === 'login' ? 'font-semibold text-accent' : ''">Log in</button>
            <span>&middot;</span>
            <button type="button" @click="emailMode = 'signup'" :class="emailMode === 'signup' ? 'font-semibold text-accent' : ''">Sign up</button>
        </div>

        <div x-show="emailMode === 'login'">
            <form method="POST" action="{{ route('login.email') }}" class="space-y-4">
                @csrf
                <label class="block text-sm text-gray-600">
                    Email
                    <input type="email" name="email" required class="mt-1 w-full rounded border border-gray-300 px-3 py-2 focus:border-accent focus:outline-none">
                </label>
                <label class="block text-sm text-gray-600">
                    Password
                    <x-password-input class="mt-1" />
                </label>
                <a href="{{ route('password.forgot') }}" class="block text-right text-xs text-accent hover:underline">Forgot password?</a>
                @error('email')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
                <button type="submit" class="w-full rounded-full bg-accent px-3 py-2 font-medium text-white hover:opacity-90">Log in</button>
            </form>
        </div>

        <div x-show="emailMode === 'signup'" x-cloak>
            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf
                <label class="block text-sm text-gray-600">
                    Email
                    <input type="email" name="email" required class="mt-1 w-full rounded border border-gray-300 px-3 py-2 focus:border-accent focus:outline-none">
                </label>
                <label class="block text-sm text-gray-600">
                    Password
                    <x-password-input minlength="8" class="mt-1" />
                </label>
                <label class="block text-sm text-gray-600">
                    Confirm password
                    <x-password-input name="password_confirmation" class="mt-1" />
                </label>
                <p class="text-xs text-gray-500">Must include at least 8 characters, uppercase, lowercase, a number, and a special character.</p>
                @error('password')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
                @error('email')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
                <button type="submit" class="w-full rounded-full bg-accent px-3 py-2 font-medium text-white hover:opacity-90">Create account</button>
            </form>
        </div>
    </div>
</div>
@endsection
