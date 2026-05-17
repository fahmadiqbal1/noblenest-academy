@extends('layouts.parent')

@section('title', __('onboarding.step3_consent_heading') . ' — Noble Nest Academy')

@section('content')
<div class="flex items-center justify-center min-h-[80vh] py-8">
  <div class="w-full max-w-2xl">
    @include('onboarding._progress', ['step' => 3])

    <x-ui.card variant="clay" padding="lg">
      <div class="text-center mb-4">
        <div class="text-4xl mb-3" aria-hidden="true">🛡️</div>
        <h1 class="text-2xl font-bold mb-1">{{ __('onboarding.step3_consent_heading') }}</h1>
        <p class="text-sm text-[var(--color-text-muted)]">{{ __('onboarding.step3_consent_sub') }}</p>
      </div>

      <div class="border border-[var(--color-border)] rounded-[var(--radius-sm)] p-4 bg-[var(--color-surface-strong)] max-h-72 overflow-y-auto text-sm leading-relaxed mb-4">
        <p class="mb-2"><strong>{{ __('legal.coppa_consent_title') }}</strong></p>
        <p class="mb-2">{{ __('legal.coppa_consent_body_1') }}</p>
        <p class="mb-2">{{ __('legal.coppa_consent_body_2') }}</p>
        <p class="mb-2">{{ __('legal.coppa_consent_body_3') }}</p>
        <p class="text-xs text-[var(--color-text-muted)]">{{ __('legal.coppa_doc_version', ['v' => $document_version]) }}</p>
      </div>

      <form action="{{ route('onboarding.step3.store') }}" method="POST" novalidate>
        @csrf

        <label class="flex items-start gap-3 p-3 rounded-[var(--radius-sm)] border-2 border-[var(--color-border)] cursor-pointer hover:bg-[var(--color-brand-50)]/40">
          <input type="checkbox" name="agree" value="1" required class="mt-1">
          <span class="text-sm">{{ __('legal.coppa_checkbox_label') }}</span>
        </label>
        @error('agree')<p class="text-xs text-[var(--color-danger)] mt-1">{{ $message }}</p>@enderror

        <div class="mt-6">
          <x-ui.button variant="primary" size="lg" type="submit" class="w-full">{{ __('onboarding.consent_continue') }}</x-ui.button>
        </div>
      </form>
    </x-ui.card>
  </div>
</div>
@endsection
