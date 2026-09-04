@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-sm">
    <h1 class="mb-6 text-2xl font-semibold text-primary">Reset your password</h1>

    @if (session('devCode'))
        <p class="mb-4 rounded border border-accent/30 bg-accent/5 p-3 text-sm text-gray-600">
            Dev mode: no email provider is wired up yet, so your reset code is <strong class="text-accent">{{ session('devCode') }}</strong>.
        </p>
    @endif

    <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
        @csrf
        <label class="block text-sm text-gray-600">
            Email
            <input type="email" name="email" required value="{{ old('email', session('email', $email)) }}" class="mt-1 w-full rounded border border-gray-300 px-3 py-2 focus:border-accent focus:outline-none">
        </label>
        <label class="block text-sm text-gray-600">
            6-digit code
            <input type="text" name="code" required maxlength="6" class="mt-1 w-full rounded border border-gray-300 px-3 py-2 focus:border-accent focus:outline-none">
        </label>
        @error('code')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
        <label class="block text-sm text-gray-600">
            New password
            <x-password-input minlength="8" class="mt-1" />
        </label>
        <label class="block text-sm text-gray-600">
            Confirm new password
            <x-password-input name="password_confirmation" class="mt-1" />
        </label>
        <p class="text-xs text-gray-500">Must include at least 8 characters, uppercase, lowercase, a number, and a special character.</p>
        @error('password')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
        <button type="submit" class="w-full rounded-full bg-accent px-3 py-2 font-medium text-white hover:opacity-90">Reset password</button>
    </form>
</div>
@endsection
