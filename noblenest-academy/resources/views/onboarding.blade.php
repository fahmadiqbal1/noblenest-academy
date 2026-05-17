@extends('layouts.parent')

@section('content')
<div class="container py-5">
    <div class="flex flex-wrap justify-center">
        <div class="md:w-7/12">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm shadow border-0">
                <div class="p-5">
                    <div class="text-center mb-4">
                        <span class="text-4xl font-bold">🌟</span>
                        <h2 class="font-bold mt-2">{{ __('onboarding.welcome_title') }}</h2>
                        <p class="text-[var(--color-text-muted)]">{{ __('onboarding.welcome_subtitle') }}</p>
                    </div>

                    @if($errors->any())
                        <div class="flex items-start gap-3 p-4 rounded-lg border bg-red-50 border-red-200 text-red-800">
                            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('onboarding.store') }}">
                        @csrf

                        {{-- Step 1: Language --}}
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1 font-semibold">{{ __('onboarding.preferred_language') }}</label>
                            <div class="flex flex-wrap gap-2">
                                @foreach($locales as $code => $name)
                                    <div>
                                        <input type="radio" class="sr-only" name="preferred_language"
                                            id="lang_{{ $code }}" value="{{ $code }}"
                                            {{ old('preferred_language', 'en') === $code ? 'checked' : '' }}>
                                        <label class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed border-2 border-violet-600 text-violet-600 hover:bg-violet-600 hover:text-white" for="lang_{{ $code }}">{{ $name }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Step 2: Child info --}}
                        <div class="flex flex-wrap gap-3 mb-4">
                            <div class="md:w-6/12">
                                <label class="block text-sm font-medium text-gray-700 mb-1 font-semibold">{{ __('onboarding.child_name') }} <span class="text-[var(--color-text-muted)] text-sm">{{ __('onboarding.optional') }}</span></label>
                                <input type="text" name="child_name" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500"
                                    placeholder="{{ __('onboarding.child_name_placeholder') }}" value="{{ old('child_name') }}">
                            </div>
                            <div class="md:w-6/12">
                                <label class="block text-sm font-medium text-gray-700 mb-1 font-semibold">{{ __('onboarding.child_age') }}</label>
                                <select name="child_age" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500">
                                    <option value="">{{ __('onboarding.select_age') }}</option>
                                    @for($i = 0; $i <= 12; $i++)
                                        <option value="{{ $i }}" {{ old('child_age') == $i ? 'selected' : '' }}>{{ __('onboarding.years', ['count' => $i]) }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        {{-- Step 3: Daily time --}}
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1 font-semibold">
                                {{ __('onboarding.daily_learning_time') }} <span id="minutesLabel">30</span> {{ __('onboarding.minutes') }}
                            </label>
                            <input type="range" name="daily_minutes" class="w-full accent-violet-600"
                                min="5" max="120" step="5" value="{{ old('daily_minutes', 30) }}"
                                oninput="document.getElementById('minutesLabel').textContent = this.value">
                        </div>

                        {{-- Step 4: Goals --}}
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1 font-semibold">{{ __('onboarding.learning_goals') }}</label>
                            <div class="flex flex-wrap gap-2">
                                @php
                                    $goals = [
                                        'language' => __('onboarding.goal_language'),
                                        'numeracy' => __('onboarding.goal_numeracy'),
                                        'creativity' => __('onboarding.goal_creativity'),
                                        'stem' => __('onboarding.goal_stem'),
                                        'social' => __('onboarding.goal_social'),
                                        'culture' => __('onboarding.goal_culture'),
                                    ];
                                @endphp
                                @foreach($goals as $val => $label)
                                    <div class="w-6/12 md:w-4/12">
                                        <div class="flex items-center gap-2">
                                            <input class="w-4 h-4 rounded border-gray-300 text-violet-600 focus:ring-violet-500" type="checkbox"
                                                name="goals[]" value="{{ $val }}" id="goal_{{ $val }}"
                                                {{ in_array($val, old('goals', [])) ? 'checked' : '' }}>
                                            <label class="text-sm" for="goal_{{ $val }}">{{ $label }}</label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="grid gap-2">
                            <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed bg-violet-600 text-white hover:bg-violet-700 px-5 py-3 text-lg">
                                <x-ui.icon name="rocket" /> {{ __('onboarding.start_learning') }}
                            </button>
                            <a href="{{ route('home') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed bg-transparent text-violet-600 hover:underline shadow-none text-[var(--color-text-muted)]">{{ __('onboarding.skip_for_now') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
