@extends('layouts.parent')

@section('title', __('Parent PIN') . ' — Noble Nest Academy')

@section('content')
<div class="flex items-center justify-center min-h-[70vh] py-8">
  <div class="w-full max-w-sm">
    <x-ui.card variant="clay" padding="lg">
      <div class="text-center mb-6">
        <div class="text-4xl mb-3" aria-hidden="true">🔒</div>
        <h1 class="text-2xl font-bold mb-1">{{ __('Enter your parent PIN') }}</h1>
        <p class="text-sm text-[var(--color-text-muted)]">{{ __('Confirm it\'s really you before continuing.') }}</p>
      </div>

      @if(! $has_pin)
        <x-ui.alert tone="warning" class="mb-4">{{ __('Set a 4-digit parent PIN to continue. You will use it to confirm sensitive actions.') }}</x-ui.alert>
      @endif

      <form action="{{ route('parent.pin.verify') }}" method="POST" novalidate>
        @csrf
        <input type="password" name="pin" inputmode="numeric" pattern="\d{4}" maxlength="4" required
          autofocus autocomplete="one-time-code"
          class="w-full rounded-[var(--radius-sm)] border-2 border-[var(--color-border)] px-3 py-3 bg-[var(--color-surface-strong)] text-center tracking-[0.5em] text-2xl">

        @error('pin')<p class="text-xs text-[var(--color-danger)] mt-2">{{ $message }}</p>@enderror

        <div class="mt-4">
          <x-ui.button variant="primary" size="lg" type="submit" class="w-full">{{ __('Verify') }}</x-ui.button>
        </div>
      </form>
    </x-ui.card>
  </div>
</div>
@endsection
