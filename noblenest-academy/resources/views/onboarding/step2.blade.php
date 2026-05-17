@extends('layouts.parent')

@section('title', __('onboarding.step2_heading') . ' — Noble Nest Academy')

@section('content')
<div class="flex items-center justify-center min-h-[80vh] py-8">
  <div class="w-full max-w-md">
    @include('onboarding._progress', ['step' => 2])

    <x-ui.card variant="clay" padding="lg">
      <div class="text-center mb-6">
        <div class="text-4xl mb-3" aria-hidden="true">👤</div>
        <h1 class="text-2xl font-bold mb-1">{{ __('onboarding.step2_profile_heading') }}</h1>
        <p class="text-sm text-[var(--color-text-muted)]">{{ __('onboarding.step2_profile_sub') }}</p>
      </div>

      <form action="{{ route('onboarding.step2.store') }}" method="POST" novalidate>
        @csrf

        <div class="space-y-4">
          <div>
            <label for="name" class="block text-sm font-semibold mb-1">{{ __('onboarding.your_name') }}</label>
            <input type="text" id="name" name="name" required maxlength="120"
              value="{{ old('name', auth()->user()->name ?? '') }}"
              class="w-full rounded-[var(--radius-sm)] border-2 border-[var(--color-border)] px-3 py-2 bg-[var(--color-surface-strong)]">
            @error('name')<p class="text-xs text-[var(--color-danger)] mt-1">{{ $message }}</p>@enderror
          </div>

          <div>
            <label for="country_code" class="block text-sm font-semibold mb-1">
              {{ __('onboarding.country_code') }} <span class="font-normal text-[var(--color-text-muted)]">{{ __('onboarding.optional') }}</span>
            </label>
            <input type="text" id="country_code" name="country_code" maxlength="2" minlength="2"
              value="{{ old('country_code', auth()->user()->country_code ?? '') }}" placeholder="US"
              class="w-full rounded-[var(--radius-sm)] border-2 border-[var(--color-border)] px-3 py-2 bg-[var(--color-surface-strong)] uppercase">
          </div>

          <div>
            <label for="parent_pin" class="block text-sm font-semibold mb-1">{{ __('onboarding.parent_pin') }}</label>
            <p class="text-xs text-[var(--color-text-muted)] mb-2">{{ __('onboarding.parent_pin_help') }}</p>
            <input type="password" id="parent_pin" name="parent_pin" required inputmode="numeric"
              pattern="\d{4}" maxlength="4" autocomplete="new-password"
              class="w-full rounded-[var(--radius-sm)] border-2 border-[var(--color-border)] px-3 py-2 bg-[var(--color-surface-strong)] text-center tracking-[0.5em] text-xl">
            @error('parent_pin')<p class="text-xs text-[var(--color-danger)] mt-1">{{ $message }}</p>@enderror
          </div>
        </div>

        <div class="mt-6">
          <x-ui.button variant="primary" size="lg" type="submit" class="w-full">{{ __('onboarding.next') }}</x-ui.button>
        </div>
      </form>
    </x-ui.card>
  </div>
</div>
@endsection
