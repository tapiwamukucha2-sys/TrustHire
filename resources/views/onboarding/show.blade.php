@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-md" x-data="{ preview: null }">
    <h1 class="mb-2 text-2xl font-semibold text-primary">Complete your profile</h1>
    <p class="mb-6 text-sm text-gray-600">A few details before you start hiring or lending on TrustHire.</p>

    <form method="POST" action="{{ route('onboarding.update') }}" class="space-y-5">
        @csrf
        <input type="hidden" name="callback" value="{{ $callback }}">

        <div class="flex flex-col items-center">
            <label class="flex h-24 w-24 cursor-pointer items-center justify-center rounded-full border-2 border-dashed border-gray-300 bg-gray-50 text-gray-400 hover:border-accent">
                <template x-if="preview"><img :src="preview" alt="" class="h-full w-full rounded-full object-cover"></template>
                <template x-if="!preview"><span class="text-2xl">+</span></template>
                <input type="file" accept="image/*" class="hidden" @change="
                    const file = $event.target.files[0];
                    if (!file) return;
                    const reader = new FileReader();
                    reader.onload = () => preview = reader.result;
                    reader.readAsDataURL(file);
                ">
            </label>
            <input type="hidden" name="photo_data_url" :value="preview">
            <p class="mt-2 text-xs text-gray-500">Profile photo (optional)</p>
        </div>

        <label class="block text-sm text-gray-600">
            Full name
            <input type="text" name="name" required value="{{ old('name', $user->name) }}" class="mt-1 w-full rounded border border-gray-300 px-3 py-2 focus:border-accent focus:outline-none">
        </label>

        <fieldset>
            <legend class="block text-sm text-gray-600">Account type</legend>
            <div class="mt-2 grid grid-cols-2 gap-3">
                <label class="flex cursor-pointer items-center gap-2 rounded border border-gray-300 px-3 py-2 text-sm has-[:checked]:border-accent has-[:checked]:bg-accent/5">
                    <input type="radio" name="account_type" value="INDIVIDUAL" checked>
                    Individual
                </label>
                <label class="flex cursor-pointer items-center gap-2 rounded border border-gray-300 px-3 py-2 text-sm has-[:checked]:border-accent has-[:checked]:bg-accent/5">
                    <input type="radio" name="account_type" value="BUSINESS">
                    Business
                </label>
            </div>
        </fieldset>

        <label class="block text-sm text-gray-600">
            Location
            <input type="text" name="location" placeholder="City or area" class="mt-1 w-full rounded border border-gray-300 px-3 py-2 focus:border-accent focus:outline-none">
        </label>

        <div class="space-y-2 border-t pt-4 text-sm text-gray-600">
            <label class="flex items-start gap-2">
                <input type="checkbox" name="age_confirmed" required class="mt-1">
                I confirm that I am 18 years or older.
            </label>
            <label class="flex items-start gap-2">
                <input type="checkbox" name="terms_accepted" required class="mt-1">
                I agree to the <a href="{{ route('terms') }}" class="text-accent hover:underline">Terms and Conditions</a> and <a href="{{ route('privacy') }}" class="text-accent hover:underline">Privacy Policy</a>.
            </label>
            <label class="flex items-start gap-2">
                <input type="checkbox" name="marketing_opt_in" class="mt-1">
                I'd like to receive updates and community news from TrustHire.
            </label>
        </div>

        <button type="submit" class="w-full rounded-full bg-accent px-3 py-2 font-medium text-white hover:opacity-90">Continue</button>
    </form>
</div>
@endsection
