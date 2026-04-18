@extends('layouts.marketing')

@section('title', '500 — Server Error')

@section('content')
<x-ui.empty-state
    title="Something Went Wrong"
    description="We're sorry — our servers hit a bump. Our team has been notified. Please try again in a moment."
>
    <x-slot:illustration>
        <div class="text-7xl select-none" aria-hidden="true">🛠️</div>
    </x-slot:illustration>

    <x-slot:actions>
        <x-ui.button as="a" href="{{ url('/') }}" variant="primary" icon="home">Go Home</x-ui.button>
        <x-ui.button as="a" href="javascript:location.reload()" variant="secondary" icon="arrow-right">Try Again</x-ui.button>
    </x-slot:actions>
</x-ui.empty-state>
@endsection
