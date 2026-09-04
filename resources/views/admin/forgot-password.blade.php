<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Password Recovery — TrustHire</title>
    @vite(['resources/css/app.css'])
</head>
<body class="flex min-h-screen items-center justify-center bg-[#0B3A1F] px-4">
    <div class="w-full max-w-sm rounded-lg bg-white p-8 shadow-xl">
        <div class="mb-6 flex items-center gap-2 font-serif text-xl font-semibold text-primary">
            <x-trust-shield class="h-7 w-7" color="#14532D" />
            TrustHire Admin
        </div>

        <h1 class="mb-2 text-lg font-semibold text-primary">Forgot your password?</h1>
        <p class="mb-6 text-sm text-gray-600">Enter your admin username and we'll send you a reset code.</p>

        @if ($errors->any())
            <p class="mb-4 rounded border border-red-200 bg-red-50 p-3 text-sm text-red-700">{{ $errors->first() }}</p>
        @endif

        <form method="POST" action="{{ route('admin.password.send') }}" class="space-y-4">
            @csrf
            <label class="block text-sm text-gray-600">
                Username
                <input type="text" name="username" required autofocus value="{{ old('username') }}" class="mt-1 w-full rounded border border-gray-300 px-3 py-2 focus:border-accent focus:outline-none">
            </label>
            <button type="submit" class="w-full rounded-full bg-accent px-3 py-2 font-medium text-white hover:opacity-90">Send reset code</button>
        </form>

        <a href="{{ route('admin.login') }}" class="mt-6 block text-center text-xs text-gray-400 hover:text-gray-600">&larr; Back to log in</a>
    </div>
</body>
</html>
