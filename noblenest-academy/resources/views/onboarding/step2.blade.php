@extends('layouts.parent')

@section('title', 'Tell Us About Your Child — Noble Nest Academy')
@section('meta_title', 'Tell Us About Your Child — Noble Nest Academy')

@section('content')

<div class="flex items-center justify-center min-h-[80vh] py-8">
  <div class="w-full max-w-md">

    {{-- Step indicator --}}
    <div class="mb-8">
      <div class="flex items-center justify-center gap-3 mb-4" role="list" aria-label="Onboarding progress">

        {{-- Step 1 — complete --}}
        <div role="listitem" class="flex flex-col items-center gap-1">
          <div class="w-9 h-9 rounded-full flex items-center justify-center bg-[var(--color-success)] text-white font-bold text-sm shadow-sm" aria-label="Step 1 of 3, complete">
            <x-ui.icon name="check" class="w-4 h-4" />
          </div>
          <span class="text-[0.65rem] font-semibold text-[var(--color-success)]">Language</span>
        </div>

        <div class="flex-1 max-w-[3rem] h-0.5 bg-[var(--color-success)] rounded-full" aria-hidden="true"></div>

        {{-- Step 2 — current --}}
        <div role="listitem" class="flex flex-col items-center gap-1">
          <div class="w-9 h-9 rounded-full flex items-center justify-center bg-[var(--color-brand-600)] text-white font-bold text-sm shadow-sm" aria-current="step" aria-label="Step 2 of 3, current">
            2
          </div>
          <span class="text-[0.65rem] font-semibold text-[var(--color-brand-600)]">Child</span>
        </div>

        <div class="flex-1 max-w-[3rem] h-0.5 bg-[var(--color-border)] rounded-full" aria-hidden="true"></div>

        {{-- Step 3 — upcoming --}}
        <div role="listitem" class="flex flex-col items-center gap-1">
          <div class="w-9 h-9 rounded-full flex items-center justify-center bg-[var(--color-brand-50)] text-[var(--color-brand-600)] font-bold text-sm border-2 border-[var(--color-border)]" aria-label="Step 3 of 3">
            3
          </div>
          <span class="text-[0.65rem] font-semibold text-[var(--color-text-muted)]">Goals</span>
        </div>

      </div>
      <x-ui.progress value="66" size="sm" />
    </div>

    <x-ui.card variant="clay" padding="lg">

      <div class="text-center mb-6">
        <div class="text-4xl mb-3" aria-hidden="true">👶</div>
        <h1 class="text-2xl font-bold text-[var(--color-text)] font-[var(--font-display)] mb-1">Tell us about your child</h1>
        <p class="text-sm text-[var(--color-text-muted)]">We use this to personalise the learning journey.</p>
      </div>

      <form action="{{ route('onboarding.step2.store') }}" method="POST" novalidate>
        @csrf

        <div class="space-y-4">

          <x-ui.field name="child_name" label="Child's first name" :error="$errors->first('child_name')" required>
            <x-ui.input
              type="text"
              name="child_name"
              :value="old('child_name')"
              placeholder="e.g. Aisha"
              maxlength="100"
              :invalid="$errors->has('child_name')"
              size="lg"
            />
          </x-ui.field>

          <x-ui.field
            name="date_of_birth"
            label="Date of birth"
            :error="$errors->first('date_of_birth')"
            help="We calculate the age automatically — no year-based tracking."
            required
          >
            <x-ui.input
              type="date"
              name="date_of_birth"
              :value="old('date_of_birth')"
              :max="now()->subDay()->format('Y-m-d')"
              :min="now()->subYears(11)->format('Y-m-d')"
              :invalid="$errors->has('date_of_birth')"
              size="lg"
            />
          </x-ui.field>

          {{-- Faith-based curriculum (optional) --}}
          <fieldset>
            <legend class="block text-sm font-semibold text-[var(--color-text)] mb-1">
              Islamic studies? <span class="font-normal text-[var(--color-text-muted)]">(optional)</span>
            </legend>
            <p class="text-xs text-[var(--color-text-muted)] mb-3">
              If yes, we'll include Quran memorisation, Arabic alphabet, duas, and Islamic character activities. Non-Muslim families still have full access to all other activities.
            </p>
            <div class="grid grid-cols-3 gap-2">
              @php
              $faithOptions = [
                'yes'  => ['label' => 'Yes, Muslim household', 'icon' => '☪️'],
                'no'   => ['label' => 'Non-Muslim',            'icon' => '🌍'],
                'skip' => ['label' => 'Skip for now',          'icon' => '→'],
              ];
              @endphp
              @foreach($faithOptions as $val => $opt)
              <div>
                <input
                  type="radio"
                  class="sr-only peer"
                  name="is_muslim"
                  id="faith_{{ $val }}"
                  value="{{ $val }}"
                  {{ old('is_muslim', 'skip') === $val ? 'checked' : '' }}
                >
                <label
                  for="faith_{{ $val }}"
                  class="flex flex-col items-center gap-1 w-full rounded-[var(--radius-sm)] border-2 border-[var(--color-border)] bg-[var(--color-surface-strong)] px-2 py-3 cursor-pointer select-none text-center transition-all duration-[var(--duration-fast)] text-xs font-semibold text-[var(--color-text)] hover:border-[var(--color-brand-300)] hover:bg-[var(--color-brand-50)]/40 peer-checked:border-[var(--color-brand-500)] peer-checked:bg-[var(--color-brand-50)] peer-checked:shadow-sm peer-focus-visible:outline-2 peer-focus-visible:outline-[var(--color-brand-600)] peer-focus-visible:outline-offset-2"
                >
                  <span class="text-xl" aria-hidden="true">{{ $opt['icon'] }}</span>
                  {{ $opt['label'] }}
                </label>
              </div>
              @endforeach
            </div>
            <p class="text-xs text-[var(--color-text-muted)] mt-2">You can change this anytime in your child's profile settings.</p>
          </fieldset>

        </div>

        <div class="mt-6 space-y-3">
          <x-ui.button variant="primary" size="lg" type="submit" class="w-full">
            Next
            <x-ui.icon name="arrow-right" class="w-4 h-4 ms-1" />
          </x-ui.button>

          <div class="text-center">
            <a href="{{ route('onboarding.step3') }}"
               class="text-sm text-[var(--color-text-muted)] hover:text-[var(--color-text)] focus-visible:outline-2 focus-visible:outline-[var(--color-brand-600)] focus-visible:outline-offset-2 rounded">
              Skip for now
            </a>
          </div>
        </div>

      </form>

    </x-ui.card>

  </div>
</div>

@endsection
