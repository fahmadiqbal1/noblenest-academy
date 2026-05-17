@extends('layouts.marketing')

@section('title', __('marketing.contact_meta_title'))
@section('meta_description', __('marketing.contact_meta_description'))

@section('content')
<div class="max-w-2xl mx-auto py-12 space-y-8">
    <header class="text-center space-y-2">
        <p class="text-xs font-semibold tracking-widest uppercase text-[var(--color-primary)]">{{ __('marketing.contact_eyebrow') }}</p>
        <h1 class="text-4xl font-bold text-[var(--color-text)] font-[var(--font-display)]">{{ __('marketing.contact_title') }}</h1>
        <p class="text-[var(--color-text-muted)] max-w-md mx-auto">
            {{ __('marketing.contact_intro') }}
        </p>
    </header>

    <div class="grid sm:grid-cols-2 gap-4">
        <x-ui.card padding="md" class="space-y-2">
            <h2 class="font-bold flex items-center gap-2"><x-ui.icon name="mail" /> {{ __('marketing.contact_email') }}</h2>
            <p class="text-sm"><a class="text-violet-600 hover:underline" href="mailto:hello@noblenest.example">hello@noblenest.example</a></p>
            <p class="text-xs text-[var(--color-text-muted)]">{{ __('marketing.contact_email_desc') }}</p>
        </x-ui.card>
        <x-ui.card padding="md" class="space-y-2">
            <h2 class="font-bold flex items-center gap-2"><x-ui.icon name="shield-check" /> {{ __('marketing.contact_privacy') }}</h2>
            <p class="text-sm"><a class="text-violet-600 hover:underline" href="mailto:privacy@noblenest.example">privacy@noblenest.example</a></p>
            <p class="text-xs text-[var(--color-text-muted)]">{{ __('marketing.contact_privacy_desc') }}</p>
        </x-ui.card>
        <x-ui.card padding="md" class="space-y-2">
            <h2 class="font-bold flex items-center gap-2"><x-ui.icon name="award" /> {{ __('marketing.contact_schools') }}</h2>
            <p class="text-sm"><a class="text-violet-600 hover:underline" href="mailto:schools@noblenest.example">schools@noblenest.example</a></p>
            <p class="text-xs text-[var(--color-text-muted)]">{{ __('marketing.contact_schools_desc') }}</p>
        </x-ui.card>
        <x-ui.card padding="md" class="space-y-2">
            <h2 class="font-bold flex items-center gap-2"><x-ui.icon name="file-text" /> {{ __('marketing.contact_accreditation') }}</h2>
            <p class="text-sm"><a class="text-violet-600 hover:underline" href="mailto:accreditation@noblenest.example">accreditation@noblenest.example</a></p>
            <p class="text-xs text-[var(--color-text-muted)]">{{ __('marketing.contact_accreditation_desc') }}</p>
        </x-ui.card>
    </div>

    <p class="text-center text-xs text-[var(--color-text-muted)]">
        {!! __('marketing.contact_response_note', ['email' => '<strong>privacy@noblenest.example</strong>']) !!}
    </p>
</div>
@endsection
