@extends('layouts.parent')

@section('title', __('onboarding.step4_child_heading') . ' — Noble Nest Academy')

@section('content')
<div class="flex items-center justify-center min-h-[80vh] py-8">
  <div class="w-full max-w-md">
    @include('onboarding._progress', ['step' => 4])

    <x-ui.card variant="clay" padding="lg">
      <div class="text-center mb-6">
        <div class="text-4xl mb-3" aria-hidden="true">👶</div>
        <h1 class="text-2xl font-bold mb-1">{{ __('onboarding.step4_child_heading') }}</h1>
        <p class="text-sm text-[var(--color-text-muted)]">{{ __('onboarding.step4_child_sub') }}</p>
      </div>

      <form action="{{ route('onboarding.step4.store') }}" method="POST" novalidate>
        @csrf

        <div class="space-y-4">
          <div>
            <label for="child_name" class="block text-sm font-semibold mb-1">{{ __('onboarding.child_first_name') }}</label>
            <input type="text" id="child_name" name="child_name" required maxlength="100"
              value="{{ old('child_name') }}" placeholder="{{ __('onboarding.child_first_name_placeholder') }}"
              class="w-full rounded-[var(--radius-sm)] border-2 border-[var(--color-border)] px-3 py-2 bg-[var(--color-surface-strong)]">
            @error('child_name')<p class="text-xs text-[var(--color-danger)] mt-1">{{ $message }}</p>@enderror
          </div>

          <div>
            <label for="date_of_birth" class="block text-sm font-semibold mb-1">{{ __('onboarding.date_of_birth') }}</label>
            <input type="date" id="date_of_birth" name="date_of_birth" required
              max="{{ now()->subDay()->format('Y-m-d') }}"
              value="{{ old('date_of_birth') }}"
              class="w-full rounded-[var(--radius-sm)] border-2 border-[var(--color-border)] px-3 py-2 bg-[var(--color-surface-strong)]">
            @error('date_of_birth')<p class="text-xs text-[var(--color-danger)] mt-1">{{ $message }}</p>@enderror
          </div>

          <div>
            <label class="block text-sm font-semibold mb-1">
              {{ __('onboarding.gender') }} <span class="font-normal text-[var(--color-text-muted)]">{{ __('onboarding.optional') }}</span>
            </label>
            <div class="grid grid-cols-3 gap-2">
              @foreach(['male' => __('onboarding.gender_male'), 'female' => __('onboarding.gender_female'), 'other' => __('onboarding.gender_other')] as $v => $l)
              <label class="flex items-center justify-center gap-1 rounded-[var(--radius-sm)] border-2 border-[var(--color-border)] px-2 py-2 cursor-pointer text-sm font-semibold hover:bg-[var(--color-brand-50)]/40 has-[:checked]:border-[var(--color-brand-500)] has-[:checked]:bg-[var(--color-brand-50)]">
                <input type="radio" name="gender" value="{{ $v }}" {{ old('gender') === $v ? 'checked' : '' }} class="sr-only">
                {{ $l }}
              </label>
              @endforeach
            </div>
          </div>
        </div>

        <div class="mt-6">
          <x-ui.button variant="primary" size="lg" type="submit" class="w-full">{{ __('onboarding.add_child_continue') }}</x-ui.button>
        </div>
      </form>
    </x-ui.card>
  </div>
</div>
@endsection
