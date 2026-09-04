@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-sm">
    <h1 class="mb-2 text-2xl font-semibold text-primary">Forgot your password?</h1>
    <p class="mb-6 text-sm text-gray-600">Enter the email on your account and we'll send you a reset code.</p>

    <form method="POST" action="{{ route('password.send') }}" class="space-y-4">
        @csrf
        <label class="block text-sm text-gray-600">
            Email
            <input type="email" name="email" required autofocus value="{{ old('email') }}" class="mt-1 w-full rounded border border-gray-300 px-3 py-2 focus:border-accent focus:outline-none">
        </label>
        @error('email')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
        <button type="submit" class="w-full rounded-full bg-accent px-3 py-2 font-medium text-white hover:opacity-90">Send reset code</button>
    </form>

    <a href="{{ route('login') }}" class="mt-6 block text-center text-xs text-gray-400 hover:text-gray-600">&larr; Back to log in</a>
</div>
@endsection
