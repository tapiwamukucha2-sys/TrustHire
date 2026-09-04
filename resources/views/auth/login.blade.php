@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-sm" x-data="{ tab: '{{ old('_tab', session('tab', 'phone')) }}', emailMode: '{{ session('emailMode', 'login') }}' }">
    <h1 class="mb-6 text-2xl font-semibold text-primary">Log in to TrustHire</h1>

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
