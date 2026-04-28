@extends('layouts.marketing')

@section('title', '419 — Session Expired')

@section('content')
<x-ui.empty-state
    title="Session Expired"
    description="Your session has expired for security reasons. Please go back and try your action again — it will only take a second."
>
    <x-slot:illustration>
        <div class="text-7xl select-none" aria-hidden="true">⏱️</div>
    </x-slot:illustration>

    <x-slot:actions>
        <x-ui.button as="a" href="javascript:history.back()" variant="primary" icon="arrow-left">Go Back</x-ui.button>
        <x-ui.button as="a" href="{{ url('/') }}" variant="secondary" icon="home">Go Home</x-ui.button>
    </x-slot:actions>
</x-ui.empty-state>
@endsection
