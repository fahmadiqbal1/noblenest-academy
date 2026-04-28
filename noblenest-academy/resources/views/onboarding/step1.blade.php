@extends('layouts.parent')

@section('title', 'Choose Your Language — Noble Nest Academy')
@section('meta_title', 'Choose Your Language — Noble Nest Academy')

@section('content')

<div class="flex items-center justify-center min-h-[80vh] py-8">
  <div class="w-full max-w-md">

    {{-- Step indicator --}}
    <div class="mb-8">
      {{-- Visual step orbs --}}
      <div class="flex items-center justify-center gap-3 mb-4" role="list" aria-label="Onboarding progress">
        {{-- Step 1 — current --}}
        <div role="listitem" class="flex flex-col items-center gap-1">
          <div class="w-9 h-9 rounded-full flex items-center justify-center bg-[var(--color-brand-600)] text-white font-bold text-sm shadow-sm" aria-current="step" aria-label="Step 1 of 3, current">
            1
          </div>
          <span class="text-[0.65rem] font-semibold text-[var(--color-brand-600)]">Language</span>
        </div>

        {{-- Connector --}}
        <div class="flex-1 max-w-[3rem] h-0.5 bg-[var(--color-border)] rounded-full" aria-hidden="true"></div>

        {{-- Step 2 — upcoming --}}
        <div role="listitem" class="flex flex-col items-center gap-1">
          <div class="w-9 h-9 rounded-full flex items-center justify-center bg-[var(--color-brand-50)] text-[var(--color-brand-600)] font-bold text-sm border-2 border-[var(--color-border)]" aria-label="Step 2 of 3">
            2
          </div>
          <span class="text-[0.65rem] font-semibold text-[var(--color-text-muted)]">Child</span>
        </div>

        {{-- Connector --}}
        <div class="flex-1 max-w-[3rem] h-0.5 bg-[var(--color-border)] rounded-full" aria-hidden="true"></div>

        {{-- Step 3 — upcoming --}}
        <div role="listitem" class="flex flex-col items-center gap-1">
          <div class="w-9 h-9 rounded-full flex items-center justify-center bg-[var(--color-brand-50)] text-[var(--color-brand-600)] font-bold text-sm border-2 border-[var(--color-border)]" aria-label="Step 3 of 3">
            3
          </div>
          <span class="text-[0.65rem] font-semibold text-[var(--color-text-muted)]">Goals</span>
        </div>
      </div>

      <x-ui.progress value="33" size="sm" />
    </div>

    <x-ui.card variant="clay" padding="lg">

      <div class="text-center mb-6">
        <div class="text-4xl mb-3" aria-hidden="true">🌟</div>
        <h1 class="text-2xl font-bold text-[var(--color-text)] font-[var(--font-display)] mb-1">Welcome to Noble Nest!</h1>
        <p class="text-sm text-[var(--color-text-muted)]">What language do you prefer?</p>
      </div>

      <form action="{{ route('onboarding.step1.store') }}" method="POST" novalidate>
        @csrf

        @if(request()->filled('ref'))
          <input type="hidden" name="ref" value="{{ e(request('ref')) }}">
        @endif

        @php
        $locales = [
          'en' => ['label' => 'English',   'flag' => '🇬🇧'],
          'ar' => ['label' => 'العربية',    'flag' => '🇸🇦'],
          'fr' => ['label' => 'Français',   'flag' => '🇫🇷'],
          'ur' => ['label' => 'اردو',        'flag' => '🇵🇰'],
          'ru' => ['label' => 'Русский',    'flag' => '🇷🇺'],
          'zh' => ['label' => '中文',        'flag' => '🇨🇳'],
          'es' => ['label' => 'Español',    'flag' => '🇪🇸'],
          'ko' => ['label' => '한국어',      'flag' => '🇰🇷'],
        ];
        @endphp

        <fieldset class="mb-5">
          <legend class="sr-only">Preferred language</legend>
          <div class="grid grid-cols-2 gap-2">
            @foreach($locales as $code => $info)
            <div>
              <input
                type="radio"
                class="sr-only peer"
                name="preferred_language"
                id="lang_{{ $code }}"
                value="{{ $code }}"
                {{ old('preferred_language', 'en') === $code ? 'checked' : '' }}
              >
              <label
                for="lang_{{ $code }}"
                class="flex items-center gap-2 w-full rounded-[var(--radius-sm)] border-2 border-[var(--color-border)] bg-[var(--color-surface-strong)] px-3 py-3 cursor-pointer select-none transition-all duration-[var(--duration-fast)]
                       hover:border-[var(--color-brand-300)] hover:bg-[var(--color-brand-50)]/40
                       peer-checked:border-[var(--color-brand-500)] peer-checked:bg-[var(--color-brand-50)] peer-checked:shadow-sm
                       peer-focus-visible:outline-2 peer-focus-visible:outline-[var(--color-brand-600)] peer-focus-visible:outline-offset-2"
              >
                <span class="text-xl" aria-hidden="true">{{ $info['flag'] }}</span>
                <span class="font-semibold text-sm text-[var(--color-text)]">{{ $info['label'] }}</span>
              </label>
            </div>
            @endforeach
          </div>
        </fieldset>

        @error('preferred_language')
          <x-ui.alert tone="danger" class="mb-4">{{ $message }}</x-ui.alert>
        @enderror

        <x-ui.button variant="primary" size="lg" type="submit" class="w-full">
          Continue
          <x-ui.icon name="arrow-right" class="w-4 h-4 ms-1" />
        </x-ui.button>

      </form>

    </x-ui.card>

  </div>
</div>

@endsection
