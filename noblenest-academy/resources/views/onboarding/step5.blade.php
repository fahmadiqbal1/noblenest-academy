@extends('layouts.parent')

@section('title', __('onboarding.step5_walkthrough_heading') . ' — Noble Nest Academy')

@section('content')
<div class="flex items-center justify-center min-h-[80vh] py-8">
  <div class="w-full max-w-2xl">
    @include('onboarding._progress', ['step' => 5])

    <x-ui.card variant="clay" padding="lg">
      <div class="text-center mb-6">
        <div class="text-4xl mb-3" aria-hidden="true">🎉</div>
        <h1 class="text-2xl font-bold mb-1">{{ __('onboarding.step5_walkthrough_heading') }}</h1>
        <p class="text-sm text-[var(--color-text-muted)]">
          {{ __('onboarding.step5_walkthrough_sub', ['name' => $child->name, 'tier' => $tier]) }}
        </p>
      </div>

      @if($samples->isNotEmpty())
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-6">
          @foreach($samples as $a)
            <div class="rounded-[var(--radius-sm)] border border-[var(--color-border)] p-4 bg-[var(--color-surface-strong)]">
              <h3 class="font-bold text-sm mb-1">{{ $a->title }}</h3>
              <p class="text-xs text-[var(--color-text-muted)] line-clamp-3">{{ \Illuminate\Support\Str::limit((string) $a->description, 100) }}</p>
            </div>
          @endforeach
        </div>
      @else
        <p class="text-center text-sm text-[var(--color-text-muted)] mb-6">
          {{ __('onboarding.no_samples_yet') }}
        </p>
      @endif

      <form action="{{ route('onboarding.step5.complete', $child) }}" method="POST">
        @csrf
        <x-ui.button variant="primary" size="lg" type="submit" class="w-full">
          {{ __('onboarding.lets_start') }}
        </x-ui.button>
      </form>
    </x-ui.card>
  </div>
</div>
@endsection
