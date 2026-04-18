@extends('layouts.marketing')

@section('title', '403 — Access Denied')

@section('content')
<x-ui.empty-state
    title="Access Denied"
    description="You don't have permission to view this page. If you believe this is a mistake, please contact support."
>
    <x-slot:illustration>
        <div class="text-7xl select-none" aria-hidden="true">🔒</div>
    </x-slot:illustration>

    <x-slot:actions>
        <x-ui.button as="a" href="{{ url('/') }}" variant="primary" icon="home">Go Home</x-ui.button>
        <x-ui.button as="a" href="javascript:history.back()" variant="secondary" icon="arrow-left">Go Back</x-ui.button>
    </x-slot:actions>
</x-ui.empty-state>
@endsection
