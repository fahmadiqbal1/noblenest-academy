@extends('layouts.marketing')

@section('title', '404 — Page Not Found')

@section('content')
<x-ui.empty-state
    title="Page Not Found"
    description="Oops! The page you're looking for doesn't exist or has been moved. Let's get you back on track."
>
    <x-slot:illustration>
        <div class="text-7xl select-none" aria-hidden="true">🔍</div>
    </x-slot:illustration>

    <x-slot:actions>
        <x-ui.button as="a" href="{{ url('/') }}" variant="primary" icon="home">Go Home</x-ui.button>
        <x-ui.button as="a" href="mailto:support@noblenest.com" variant="secondary" icon="mail">Contact Support</x-ui.button>
    </x-slot:actions>
</x-ui.empty-state>
@endsection
