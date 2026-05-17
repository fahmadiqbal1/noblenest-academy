@extends('layouts.marketing')

@section('title', __('marketing.about_meta_title'))
@section('meta_description', __('marketing.about_meta_description'))

@section('content')
<div class="max-w-3xl mx-auto py-12 space-y-10">
    <header class="text-center space-y-3">
        <p class="text-xs font-semibold tracking-widest uppercase text-[var(--color-primary)]">{{ __('marketing.about_eyebrow') }}</p>
        <h1 class="text-4xl font-bold text-[var(--color-text)] font-[var(--font-display)]">{{ __('marketing.about_title') }}</h1>
        <p class="text-[var(--color-text-muted)] max-w-xl mx-auto">
            {{ __('marketing.about_intro') }}
        </p>
    </header>

    <section class="space-y-3">
        <h2 class="text-2xl font-bold">{{ __('marketing.about_guides_title') }}</h2>
        <ul class="space-y-2 text-[var(--color-text)]">
            <li>• <strong>{{ __('marketing.about_guide_play_label') }}</strong> {{ __('marketing.about_guide_play_body') }}</li>
            <li>• <strong>{{ __('marketing.about_guide_multilingual_label') }}</strong> {{ __('marketing.about_guide_multilingual_body') }}</li>
            <li>• <strong>{{ __('marketing.about_guide_privacy_label') }}</strong> {{ __('marketing.about_guide_privacy_body') }}</li>
            <li>• <strong>{{ __('marketing.about_guide_accessibility_label') }}</strong> {!! __('marketing.about_guide_accessibility_body') !!}</li>
            <li>• <strong>{{ __('marketing.about_guide_pedagogy_label') }}</strong> {{ __('marketing.about_guide_pedagogy_body') }}</li>
        </ul>
    </section>

    <section class="space-y-3">
        <h2 class="text-2xl font-bold">{{ __('marketing.about_serve_title') }}</h2>
        <p class="text-[var(--color-text)]">
            {{ __('marketing.about_serve_body') }}
        </p>
    </section>

    <section class="space-y-3">
        <h2 class="text-2xl font-bold">{{ __('marketing.about_contact_title') }}</h2>
        <p class="text-[var(--color-text)]">
            {{ __('marketing.about_contact_body_before') }}
            <a href="{{ url('/contact') }}" class="text-violet-600 hover:underline">{{ __('marketing.about_contact_link') }}</a>.
        </p>
    </section>
</div>
@endsection
