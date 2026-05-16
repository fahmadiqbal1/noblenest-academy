@extends('layouts.auth')

@section('title', 'Create Account | NobleNest Global Academy')
@section('meta_title', 'Create Your Account | NobleNest Global Academy')
@section('meta_description', 'Create a NobleNest Global Academy parent account and start your child on a personalised, age-appropriate learning journey.')
@section('meta_image', asset('og-register.png'))

@section('content')

{{--
  Auth layout wraps content in a centred card (max-w-md).
  For register we want a two-column split, so we break out of that
  by using a full-bleed negative-margin trick inside the card slot.
--}}

<div class="flex flex-col lg:flex-row -m-8 overflow-hidden rounded-[var(--radius-card)]">

  {{-- Brand panel --}}
  <aside class="lg:w-2/5 p-8 text-white bg-gradient-to-b from-[var(--color-brand-700)] via-[var(--color-brand-600)] to-[var(--color-brand-400)]">
    <img src="{{ asset('brand/noblenest-logo.svg') }}" alt="NobleNest Global Academy logo" class="w-20 h-20 rounded-[var(--radius-sm)] shadow-[var(--shadow-clay)] mb-6">
    <p class="text-xs font-extrabold uppercase tracking-widest text-white/80 mb-2">Get started free</p>
    <h1 class="text-2xl font-bold font-[var(--font-display)] mb-3">{{ I18n::get('register') }}</h1>
    <p class="text-white/82 text-sm leading-relaxed mb-8">
      Create your parent account and give your child a personalised, age-appropriate learning journey from day one.
    </p>
    <div class="space-y-3">
      <div class="rounded-[var(--radius-sm)] p-3 bg-white/12 border border-white/18">
        <div class="flex items-center gap-2 mb-1">
          <x-ui.icon name="users" class="w-4 h-4" />
          <span class="font-semibold text-sm">Parents</span>
        </div>
        <p class="text-xs text-white/78">Give your child a personalised learning journey.</p>
      </div>
    </div>
  </aside>

  {{-- Form panel --}}
  <div class="flex-1 p-8"
       x-data="{
         showPw: false,
         showPwC: false,
         loading: false,
         role: 'Parent'
       }">

    <h2 class="text-2xl font-bold text-[var(--color-text)] font-[var(--font-display)] mb-1">Create your account</h2>
    <p class="text-sm text-[var(--color-text-muted)] mb-6">Fill in the details below to get started.</p>

    <form method="POST" action="{{ url('/register') }}" @submit="loading = true" novalidate>
      @csrf

      {{-- Hidden role field bound to Alpine state --}}
      <input type="hidden" name="role" :value="role">

      @if($errors->any())
        <x-ui.alert tone="danger" class="mb-4">{{ $errors->first() }}</x-ui.alert>
      @endif

      {{-- Role picker --}}
      <fieldset class="mb-5">
        <legend class="block text-sm font-semibold text-[var(--color-text)] mb-2">{{ I18n::get('register_as') }}</legend>
        <div class="grid grid-cols-2 gap-2">

          @php
          $roles = [
            'Parent' => ['icon' => 'users', 'desc' => 'Monitor learning'],
          ];
          @endphp

          @foreach($roles as $roleKey => $roleData)
          <button
            type="button"
            role="radio"
            :aria-checked="role === '{{ $roleKey }}' ? 'true' : 'false'"
            @click="role = '{{ $roleKey }}'"
            @keydown.enter="role = '{{ $roleKey }}'"
            @keydown.space.prevent="role = '{{ $roleKey }}'"
            class="relative flex flex-col items-center gap-1 rounded-[var(--radius-sm)] border-2 p-3 text-center cursor-pointer select-none transition-all duration-[var(--duration-fast)] focus-visible:outline-2 focus-visible:outline-[var(--color-brand-600)] focus-visible:outline-offset-2"
            :class="role === '{{ $roleKey }}' ? 'border-[var(--color-brand-500)] bg-[var(--color-brand-50)] shadow-sm -translate-y-0.5' : 'border-[var(--color-border)] bg-[var(--color-surface-strong)] hover:border-[var(--color-brand-300)] hover:bg-[var(--color-brand-50)]/40'"
          >
            <x-ui.icon
              name="{{ $roleData['icon'] }}"
              class="w-6 h-6 transition-colors duration-[var(--duration-fast)]"
              ::class="role === '{{ $roleKey }}' ? 'text-[var(--color-brand-600)]' : 'text-[var(--color-text-muted)]'"
            />
            <span class="text-xs font-bold text-[var(--color-text)]">{{ $roleKey }}</span>
            <span class="text-[0.65rem] text-[var(--color-text-muted)]">{{ $roleData['desc'] }}</span>
          </button>
          @endforeach

        </div>
      </fieldset>

      <div class="space-y-4">

        <x-ui.field name="name" label="{{ I18n::get('name') }}" :error="$errors->first('name')" required>
          <x-ui.input
            type="text"
            name="name"
            :value="old('name')"
            placeholder="Your full name"
            autocomplete="name"
            :invalid="$errors->has('name')"
            size="lg"
          />
        </x-ui.field>

        <x-ui.field name="email" label="{{ I18n::get('email') }}" :error="$errors->first('email')" required>
          <x-ui.input
            type="email"
            name="email"
            :value="old('email')"
            placeholder="you@example.com"
            autocomplete="email"
            :invalid="$errors->has('email')"
            size="lg"
          />
        </x-ui.field>

        {{-- Password with toggle --}}
        <x-ui.field name="password" label="{{ I18n::get('password') }}" :error="$errors->first('password')" required>
          <div class="relative flex">
            <input
              :type="showPw ? 'text' : 'password'"
              id="password"
              name="password"
              required
              autocomplete="new-password"
              placeholder="Min. 8 characters"
              aria-describedby="{{ $errors->has('password') ? 'password_error' : '' }}"
              class="block w-full rounded-s-[var(--radius-sm)] rounded-e-none border-2 border-e-0 border-[var(--color-border)] bg-[var(--color-surface-strong)] text-[var(--color-text)] py-3 px-4 text-base focus:outline-none focus:border-[var(--color-brand-500)] focus-visible:outline-2 focus-visible:outline-[var(--color-brand-600)] focus-visible:outline-offset-2 {{ $errors->has('password') ? 'border-[var(--color-coral-500)]' : '' }}"
            >
            <button
              type="button"
              @click="showPw = !showPw"
              :aria-label="showPw ? 'Hide password' : 'Show password'"
              class="px-4 border-2 border-[var(--color-border)] border-s-0 rounded-e-[var(--radius-sm)] bg-[var(--color-surface-strong)] text-[var(--color-text-muted)] hover:text-[var(--color-primary)] hover:bg-[var(--color-brand-50)] transition-colors focus-visible:outline-2 focus-visible:outline-[var(--color-brand-600)] focus-visible:outline-offset-2"
            >
              <x-ui.icon name="eye" class="w-5 h-5" x-show="!showPw" />
              <x-ui.icon name="eye-off" class="w-5 h-5" x-show="showPw" x-cloak />
            </button>
          </div>
        </x-ui.field>

        {{-- Confirm password with toggle --}}
        <x-ui.field name="password_confirmation" label="{{ I18n::get('confirm_password') }}" :error="$errors->first('password_confirmation')" required>
          <div class="relative flex">
            <input
              :type="showPwC ? 'text' : 'password'"
              id="password_confirmation"
              name="password_confirmation"
              required
              autocomplete="new-password"
              class="block w-full rounded-s-[var(--radius-sm)] rounded-e-none border-2 border-e-0 border-[var(--color-border)] bg-[var(--color-surface-strong)] text-[var(--color-text)] py-3 px-4 text-base focus:outline-none focus:border-[var(--color-brand-500)] focus-visible:outline-2 focus-visible:outline-[var(--color-brand-600)] focus-visible:outline-offset-2"
            >
            <button
              type="button"
              @click="showPwC = !showPwC"
              :aria-label="showPwC ? 'Hide password' : 'Show password'"
              class="px-4 border-2 border-[var(--color-border)] border-s-0 rounded-e-[var(--radius-sm)] bg-[var(--color-surface-strong)] text-[var(--color-text-muted)] hover:text-[var(--color-primary)] hover:bg-[var(--color-brand-50)] transition-colors focus-visible:outline-2 focus-visible:outline-[var(--color-brand-600)] focus-visible:outline-offset-2"
            >
              <x-ui.icon name="eye" class="w-5 h-5" x-show="!showPwC" />
              <x-ui.icon name="eye-off" class="w-5 h-5" x-show="showPwC" x-cloak />
            </button>
          </div>
        </x-ui.field>

        {{-- Terms checkbox --}}
        <div class="flex items-start gap-3">
          <input
            type="checkbox"
            id="terms"
            name="terms"
            required
            class="mt-0.5 w-4 h-4 rounded border-2 border-[var(--color-border)] text-[var(--color-primary)] focus-visible:outline-2 focus-visible:outline-[var(--color-brand-600)] focus-visible:outline-offset-2 cursor-pointer"
          >
          <label for="terms" class="text-sm text-[var(--color-text-muted)] cursor-pointer select-none leading-snug">
            I agree to the
            <a href="{{ url('/terms') }}" target="_blank" class="font-semibold text-[var(--color-primary)] hover:underline focus-visible:outline-2 focus-visible:outline-[var(--color-brand-600)] focus-visible:outline-offset-2 rounded">Terms of Service</a>
            and
            <a href="{{ url('/privacy') }}" target="_blank" class="font-semibold text-[var(--color-primary)] hover:underline focus-visible:outline-2 focus-visible:outline-[var(--color-brand-600)] focus-visible:outline-offset-2 rounded">Privacy Policy</a>.
          </label>
        </div>

        <x-ui.button
          variant="primary"
          size="lg"
          type="submit"
          class="w-full"
          x-bind:disabled="loading"
          x-bind:aria-busy="loading"
        >
          <span x-show="!loading">{{ I18n::get('register') }}</span>
          <span x-show="loading" x-cloak>Creating account&hellip;</span>
        </x-ui.button>

      </div>
    </form>

    <hr class="my-6 border-[var(--color-border)]">

    <p class="text-center text-sm text-[var(--color-text-muted)]">
      Already have an account?
      <a href="{{ route('login') }}" class="font-bold text-[var(--color-primary)] hover:underline focus-visible:outline-2 focus-visible:outline-[var(--color-brand-600)] focus-visible:outline-offset-2 rounded ms-1">
        Sign in
      </a>
    </p>

  </div>

</div>

@endsection
