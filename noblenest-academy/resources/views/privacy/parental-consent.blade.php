@extends('layouts.parent')

@section('title', __('legal.consent_title_meta'))

@section('content')
<div class="max-w-2xl mx-auto py-8 space-y-6">
    <header class="space-y-2 text-center">
        <p class="text-xs font-semibold tracking-widest uppercase text-[var(--color-primary)]">{{ __('legal.consent_eyebrow') }}</p>
        <h1 class="text-3xl font-bold text-[var(--color-text)] font-[var(--font-display)]">{{ __('legal.consent_heading', ['name' => $child->name]) }}</h1>
        <p class="text-[var(--color-text-muted)]">
            {{ __('legal.consent_intro') }}
        </p>
    </header>

    <x-ui.card padding="lg" class="space-y-4">
        <h2 class="text-xl font-bold">{{ __('legal.consent_collect_title') }}</h2>
        <ul class="list-disc list-inside space-y-1 text-sm text-[var(--color-text)]">
            <li>{{ __('legal.consent_collect_item1', ['name' => $child->name]) }}</li>
            <li>{{ __('legal.consent_collect_item2') }}</li>
            <li>{{ __('legal.consent_collect_item3') }}</li>
            <li>{{ __('legal.consent_collect_item4') }}</li>
        </ul>

        <h2 class="text-xl font-bold pt-2">{{ __('legal.consent_not_collect_title') }}</h2>
        <ul class="list-disc list-inside space-y-1 text-sm text-[var(--color-text)]">
            <li>{{ __('legal.consent_not_collect_item1') }}</li>
            <li>{{ __('legal.consent_not_collect_item2') }}</li>
            <li>{{ __('legal.consent_not_collect_item3') }}</li>
        </ul>

        <h2 class="text-xl font-bold pt-2">{{ __('legal.consent_rights_title') }}</h2>
        <p class="text-sm text-[var(--color-text-muted)]">
            {{ __('legal.consent_rights_body_before', ['name' => $child->name]) }}
            <a href="{{ route('privacy.dashboard') }}" class="text-violet-600 hover:underline">{{ __('legal.consent_rights_link') }}</a>.
        </p>
    </x-ui.card>

    <form method="POST" action="{{ route('privacy.parental-consent.store', ['child' => $child->id]) }}"
          class="space-y-4">
        @csrf
        <div class="space-y-3">
            <label class="flex items-start gap-3">
                <input type="checkbox" name="agree_terms" value="1" required
                       class="mt-1 w-5 h-5 rounded border-gray-300 text-violet-600 focus:ring-violet-500">
                <span class="text-sm">
                    {{ __('legal.consent_agree_terms_before') }}
                    <a href="{{ url('/terms') }}" target="_blank" class="text-violet-600 hover:underline">{{ __('legal.consent_terms_link') }}</a>.
                </span>
            </label>
            <label class="flex items-start gap-3">
                <input type="checkbox" name="agree_privacy" value="1" required
                       class="mt-1 w-5 h-5 rounded border-gray-300 text-violet-600 focus:ring-violet-500">
                <span class="text-sm">
                    {{ __('legal.consent_agree_privacy_before') }}
                    <a href="{{ url('/privacy') }}" target="_blank" class="text-violet-600 hover:underline">{{ __('legal.consent_privacy_link') }}</a>.
                </span>
            </label>
            <label class="flex items-start gap-3">
                <input type="checkbox" name="agree_coppa" value="1" required
                       class="mt-1 w-5 h-5 rounded border-gray-300 text-violet-600 focus:ring-violet-500">
                <span class="text-sm">
                    {{ __('legal.consent_agree_coppa', ['name' => $child->name]) }}
                </span>
            </label>
        </div>

        <div class="flex flex-col sm:flex-row gap-2 justify-between items-center pt-2">
            <a href="{{ route('privacy.dashboard') }}"
               class="text-sm text-[var(--color-text-muted)] hover:underline">
                {{ __('legal.consent_back') }}
            </a>
            <x-ui.button type="submit" variant="primary" size="lg" icon="badge-check">
                {{ __('legal.consent_record') }}
            </x-ui.button>
        </div>
    </form>

    <p class="text-xs text-center text-[var(--color-text-muted)]">
        {{ __('legal.consent_log_note') }}
    </p>
</div>
@endsection
