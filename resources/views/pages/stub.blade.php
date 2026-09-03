@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-lg">
    <h1 class="text-2xl font-semibold text-primary">{{ $title }}</h1>
    <p class="mt-4 text-gray-600">{{ $body }}</p>
</div>
@endsection
