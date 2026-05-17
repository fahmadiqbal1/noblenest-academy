@extends('layouts.parent')

@section('title', __('onboarding.step1_welcome') . ' — Noble Nest Academy')

@section('content')
<div class="flex items-center justify-center min-h-[80vh] py-8">
  <div class="w-full max-w-md">
    @include('onboarding._progress', ['step' => 1])

    <x-ui.card variant="clay" padding="lg">
      <div class="text-center mb-6">
        <div class="text-4xl mb-3" aria-hidden="true">🌟</div>
        <h1 class="text-2xl font-bold text-[var(--color-text)] font-[var(--font-display)] mb-1">
          {{ __('onboarding.step1_welcome') }}
        </h1>
        <p class="text-sm text-[var(--color-text-muted)]">{{ __('onboarding.step1_question') }}</p>
      </div>

      <form action="{{ route('onboarding.step1.store') }}" method="POST" novalidate>
        @csrf
        <fieldset class="mb-5">
          <legend class="sr-only">{{ __('onboarding.step1_legend') }}</legend>
          <div class="grid grid-cols-2 gap-2">
            @foreach($locales as $code => $label)
            <div>
              <input
                type="radio"
                class="sr-only peer"
                name="preferred_language"
                id="lang_{{ $code }}"
                value="{{ $code }}"
                {{ old('preferred_language', auth()->user()->preferred_language ?? 'en') === $code ? 'checked' : '' }}
              >
              <label
                for="lang_{{ $code }}"
                class="flex items-center gap-2 w-full rounded-[var(--radius-sm)] border-2 border-[var(--color-border)] bg-[var(--color-surface-strong)] px-3 py-3 cursor-pointer select-none transition hover:border-[var(--color-brand-300)] hover:bg-[var(--color-brand-50)]/40 peer-checked:border-[var(--color-brand-500)] peer-checked:bg-[var(--color-brand-50)] peer-checked:shadow-sm"
              >
                <span class="font-semibold text-sm">{{ $label }}</span>
              </label>
            </div>
            @endforeach
          </div>
        </fieldset>

        @error('preferred_language')
          <x-ui.alert tone="danger" class="mb-4">{{ $message }}</x-ui.alert>
        @enderror

        <x-ui.button variant="primary" size="lg" type="submit" class="w-full">
          {{ __('onboarding.continue') }}
        </x-ui.button>
      </form>
    </x-ui.card>
  </div>
</div>
@endsection
