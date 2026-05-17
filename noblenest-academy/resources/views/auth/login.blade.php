@extends('layouts.auth')

@section('title', 'Login | NobleNest Global Academy')
@section('meta_title', 'Login | NobleNest Global Academy')
@section('meta_description', 'Log in to NobleNest Global Academy to continue learning, manage courses, monitor children, or access admin workflows from one secure account.')
@section('meta_image', asset('og-login.png'))

@section('content')

{{--
  Auth layout wraps content in a centred card (max-w-md).
  For login we want a two-column split, so we break out of that
  by using a full-bleed negative-margin trick inside the card slot.
--}}

<div class="flex flex-col lg:flex-row -m-8 overflow-hidden rounded-[var(--radius-card)]">

  {{-- Brand panel --}}
  <aside class="lg:w-2/5 p-8 text-white bg-gradient-to-b from-[var(--color-brand-700)] via-[var(--color-brand-600)] to-[var(--color-brand-400)]">
    <img src="{{ asset('brand/noblenest-logo.svg') }}" alt="NobleNest Global Academy logo" class="w-20 h-20 rounded-[var(--radius-sm)] shadow-[var(--shadow-clay)] mb-6">
    <p class="text-xs font-extrabold uppercase tracking-widest text-white/80 mb-2">{{ __('auth.login_welcome_back') }}</p>
    <h1 class="text-2xl font-bold font-[var(--font-display)] mb-3">{{ __('auth.login') }}</h1>
    <p class="text-white/82 text-sm leading-relaxed mb-8">
      {{ __('auth.login_resume') }}
    </p>
    <div class="space-y-3">
      <div class="rounded-[var(--radius-sm)] p-3 bg-white/12 border border-white/18">
        <div class="flex items-center gap-2 mb-1">
          <x-ui.icon name="book-open" class="w-4 h-4" />
          <span class="font-semibold text-sm">{{ __('auth.login_students') }}</span>
        </div>
        <p class="text-xs text-white/78">{{ __('auth.login_students_desc') }}</p>
      </div>
      <div class="rounded-[var(--radius-sm)] p-3 bg-white/12 border border-white/18">
        <div class="flex items-center gap-2 mb-1">
          <x-ui.icon name="users" class="w-4 h-4" />
          <span class="font-semibold text-sm">{{ __('auth.login_parents_admins') }}</span>
        </div>
        <p class="text-xs text-white/78">{{ __('auth.login_parents_admins_desc') }}</p>
      </div>
    </div>
  </aside>

  {{-- Form panel --}}
  <div class="flex-1 p-8" x-data="{ showPw: false, loading: false }">
    <h2 class="text-2xl font-bold text-[var(--color-text)] font-[var(--font-display)] mb-1">{{ __('auth.login_heading') }}</h2>
    <p class="text-sm text-[var(--color-text-muted)] mb-6">{{ __('auth.login_subheading') }}</p>

    <form method="POST" action="{{ url('/login') }}" @submit="loading = true" novalidate>
      @csrf

      @if($errors->any())
        <x-ui.alert tone="danger" class="mb-4">{{ $errors->first() }}</x-ui.alert>
      @endif

      <div class="space-y-4">
        <x-ui.field name="email" label="{{ __('auth.email') }}" :error="$errors->first('email')" required>
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

        <x-ui.field name="password" :error="$errors->first('password')" required>
          <x-slot name="label">
            <div class="flex items-center justify-between">
              <label for="password" class="block text-sm font-semibold text-[var(--color-text)]">
                {{ __('auth.password') }}
                <span class="text-[var(--color-coral-500)] ms-0.5" aria-hidden="true">*</span>
              </label>
              <a href="{{ route('password.request') }}"
                 class="text-xs font-semibold text-[var(--color-primary)] hover:underline focus-visible:outline-2 focus-visible:outline-[var(--color-brand-600)] focus-visible:outline-offset-2 rounded">
                {{ __('auth.forgot_password_link') }}
              </a>
            </div>
          </x-slot>
          <div class="relative flex">
            <input
              :type="showPw ? 'text' : 'password'"
              id="password"
              name="password"
              required
              autocomplete="current-password"
              aria-describedby="{{ $errors->has('password') ? 'password_error' : '' }}"
              :aria-invalid="'{{ $errors->has('password') ? 'true' : 'false' }}'"
              class="block w-full rounded-s-[var(--radius-sm)] rounded-e-none border-[2px] border-e-0 border-[var(--color-border)] bg-[var(--color-surface-strong)] text-[var(--color-text)] py-3 px-4 text-base focus:outline-none focus:border-[var(--color-brand-500)] focus-visible:outline-2 focus-visible:outline-[var(--color-brand-600)] focus-visible:outline-offset-2 {{ $errors->has('password') ? 'border-[var(--color-coral-500)]' : '' }}"
            >
            <button
              type="button"
              @click="showPw = !showPw"
              :aria-label="showPw ? '{{ __('auth.hide_password') }}' : '{{ __('auth.show_password') }}'"
              class="px-4 border-[2px] border-[var(--color-border)] border-s-0 rounded-e-[var(--radius-sm)] bg-[var(--color-surface-strong)] text-[var(--color-text-muted)] hover:text-[var(--color-primary)] hover:bg-[var(--color-brand-50)] transition-colors focus-visible:outline-2 focus-visible:outline-[var(--color-brand-600)] focus-visible:outline-offset-2"
            >
              <x-ui.icon name="eye" class="w-5 h-5" x-show="!showPw" />
              <x-ui.icon name="eye-off" class="w-5 h-5" x-show="showPw" x-cloak />
            </button>
          </div>
        </x-ui.field>

        <div class="flex items-center gap-2">
          <input
            type="checkbox"
            id="remember"
            name="remember"
            class="w-4 h-4 rounded border-[2px] border-[var(--color-border)] text-[var(--color-primary)] focus-visible:outline-2 focus-visible:outline-[var(--color-brand-600)] focus-visible:outline-offset-2 cursor-pointer"
          >
          <label for="remember" class="text-sm text-[var(--color-text)] cursor-pointer select-none">{{ __('auth.remember_me') }}</label>
        </div>

        <x-ui.button
          variant="primary"
          size="lg"
          type="submit"
          class="w-full"
          x-bind:disabled="loading"
          x-bind:aria-busy="loading"
        >
          <span x-show="!loading">{{ __('auth.login') }}</span>
          <span x-show="loading" x-cloak>{{ __('auth.signing_in') }}</span>
        </x-ui.button>
      </div>
    </form>

    <hr class="my-6 border-[var(--color-border)]">

    <p class="text-center text-sm text-[var(--color-text-muted)]">
      {{ __('auth.no_account') }}
      <a href="{{ route('register') }}" class="font-bold text-[var(--color-primary)] hover:underline focus-visible:outline-2 focus-visible:outline-[var(--color-brand-600)] focus-visible:outline-offset-2 rounded ms-1">
        {{ __('auth.create_one_free') }}
      </a>
    </p>
  </div>

</div>

@endsection
