@extends('layouts.parent')

@section('title', 'Learning Goals — Noble Nest Academy')
@section('meta_title', 'Learning Goals — Noble Nest Academy')

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

        {{-- Step 2 — complete --}}
        <div role="listitem" class="flex flex-col items-center gap-1">
          <div class="w-9 h-9 rounded-full flex items-center justify-center bg-[var(--color-success)] text-white font-bold text-sm shadow-sm" aria-label="Step 2 of 3, complete">
            <x-ui.icon name="check" class="w-4 h-4" />
          </div>
          <span class="text-[0.65rem] font-semibold text-[var(--color-success)]">Child</span>
        </div>

        <div class="flex-1 max-w-[3rem] h-0.5 bg-[var(--color-success)] rounded-full" aria-hidden="true"></div>

        {{-- Step 3 — current --}}
        <div role="listitem" class="flex flex-col items-center gap-1">
          <div class="w-9 h-9 rounded-full flex items-center justify-center bg-[var(--color-brand-600)] text-white font-bold text-sm shadow-sm" aria-current="step" aria-label="Step 3 of 3, current">
            3
          </div>
          <span class="text-[0.65rem] font-semibold text-[var(--color-brand-600)]">Goals</span>
        </div>

      </div>
      <x-ui.progress value="100" size="sm" />
    </div>

    <x-ui.card variant="clay" padding="lg">

      <div class="text-center mb-6">
        <div class="text-4xl mb-3" aria-hidden="true">🎯</div>
        <h1 class="text-2xl font-bold text-[var(--color-text)] font-[var(--font-display)] mb-1">What are your goals?</h1>
        <p class="text-sm text-[var(--color-text-muted)]">Optional — helps us recommend the best activities.</p>
      </div>

      <form action="{{ route('onboarding.step3.store') }}" method="POST" novalidate x-data="{ minutes: 15 }">
        @csrf

        <div class="space-y-5">

          {{-- Goal checkboxes --}}
          <fieldset>
            <legend class="block text-sm font-semibold text-[var(--color-text)] mb-2">
              Learning goals <span class="font-normal text-[var(--color-text-muted)]">(select all that apply)</span>
            </legend>

            @php
            $goals = [
              'language'   => ['label' => 'Language skills',   'emoji' => '🗣️'],
              'motor'      => ['label' => 'Motor development',  'emoji' => '🖐️'],
              'creativity' => ['label' => 'Creativity',         'emoji' => '🎨'],
              'stem'       => ['label' => 'STEM exploration',   'emoji' => '🔬'],
              'social'     => ['label' => 'Social skills',      'emoji' => '🤝'],
              'cultural'   => ['label' => 'Cultural awareness', 'emoji' => '🌍'],
            ];
            @endphp

            <div class="grid grid-cols-2 gap-2">
              @foreach($goals as $key => $goal)
              <div>
                <input
                  type="checkbox"
                  class="sr-only peer"
                  name="goals[]"
                  id="goal_{{ $key }}"
                  value="{{ $key }}"
                  {{ in_array($key, (array) old('goals', [])) ? 'checked' : '' }}
                >
                <label
                  for="goal_{{ $key }}"
                  class="flex items-center gap-2 w-full rounded-[var(--radius-sm)] border-2 border-[var(--color-border)] bg-[var(--color-surface-strong)] px-3 py-3 cursor-pointer select-none transition-all duration-[var(--duration-fast)]
                         hover:border-[var(--color-brand-300)] hover:bg-[var(--color-brand-50)]/40
                         peer-checked:border-[var(--color-brand-500)] peer-checked:bg-[var(--color-brand-50)] peer-checked:shadow-sm
                         peer-focus-visible:outline-2 peer-focus-visible:outline-[var(--color-brand-600)] peer-focus-visible:outline-offset-2"
                >
                  <span class="text-lg" aria-hidden="true">{{ $goal['emoji'] }}</span>
                  <span class="text-xs font-semibold text-[var(--color-text)]">{{ $goal['label'] }}</span>
                </label>
              </div>
              @endforeach
            </div>
          </fieldset>

          {{-- Daily minutes slider --}}
          <div>
            <label for="daily_minutes" class="block text-sm font-semibold text-[var(--color-text)] mb-1">
              Daily learning time
              <span class="font-normal text-[var(--color-text-muted)] ms-1" x-text="'(' + minutes + ' min)'">(15 min)</span>
            </label>
            <input
              type="range"
              id="daily_minutes"
              name="daily_minutes"
              min="5"
              max="60"
              step="5"
              value="{{ old('daily_minutes', 15) }}"
              x-model="minutes"
              class="w-full h-2 rounded-full bg-[var(--color-border)] appearance-none cursor-pointer accent-[var(--color-brand-600)] focus-visible:outline-2 focus-visible:outline-[var(--color-brand-600)] focus-visible:outline-offset-2"
            >
            <div class="flex justify-between text-xs text-[var(--color-text-muted)] mt-1">
              <span>5 min</span>
              <span>30 min</span>
              <span>60 min</span>
            </div>
          </div>

        </div>

        <div class="mt-6 space-y-3">
          <x-ui.button variant="primary" size="lg" type="submit" class="w-full">
            Start learning!
            <x-ui.icon name="rocket" class="w-4 h-4 ms-1" />
          </x-ui.button>

          <div class="text-center">
            <a href="{{ route('home') }}"
               class="text-sm text-[var(--color-text-muted)] hover:text-[var(--color-text)] focus-visible:outline-2 focus-visible:outline-[var(--color-brand-600)] focus-visible:outline-offset-2 rounded">
              Skip and go to home
            </a>
          </div>
        </div>

      </form>

    </x-ui.card>

  </div>
</div>

@endsection
