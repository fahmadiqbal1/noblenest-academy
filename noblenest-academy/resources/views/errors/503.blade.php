@extends('layouts.marketing')

@section('title', '503 — Service Unavailable')

@section('content')
<x-ui.empty-state
    title="Down for Maintenance"
    description="NobleNest is briefly offline for scheduled maintenance. We'll be back shortly — thank you for your patience!"
>
    <x-slot:illustration>
        <div class="text-7xl select-none" aria-hidden="true">🌙</div>
    </x-slot:illustration>

    <x-slot:actions>
        <x-ui.button as="a" href="javascript:location.reload()" variant="primary" icon="arrow-right">Refresh Page</x-ui.button>
        <x-ui.button as="a" href="mailto:support@noblenest.com" variant="secondary" icon="mail">Contact Support</x-ui.button>
    </x-slot:actions>
</x-ui.empty-state>
@endsection
