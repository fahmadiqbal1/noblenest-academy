@extends('layouts.auth')

@section('title', 'Reset Password | NobleNest Global Academy')
@section('meta_title', 'Choose a New Password | NobleNest Global Academy')
@section('meta_description', 'Set a new password for your NobleNest Global Academy account.')

@section('content')

<div class="text-center mb-6">
  <div class="inline-flex items-center justify-center w-14 h-14 rounded-[var(--radius-sm)] bg-[var(--color-brand-100)] mb-4">
    <x-ui.icon name="lock" class="w-7 h-7 text-[var(--color-brand-600)]" />
  </div>
  <h1 class="text-2xl font-bold text-[var(--color-text)] font-[var(--font-display)] mb-2">Choose a new password</h1>
  <p class="text-sm text-[var(--color-text-muted)]">Must be at least 8 characters.</p>
</div>

<form method="POST" action="{{ route('password.update') }}" x-data="{ showPw: false, showPwC: false }" novalidate>
  @csrf

  {{-- Preserve token and pre-filled email --}}
  <input type="hidden" name="token" value="{{ $token ?? request()->route('token') }}">

  @if($errors->any())
    <x-ui.alert tone="danger" class="mb-4">{{ $errors->first() }}</x-ui.alert>
  @endif

  <div class="space-y-4">

    <x-ui.field name="email" label="Email address" :error="$errors->first('email')" required>
      <x-ui.input
        type="email"
        name="email"
        :value="old('email', request()->email)"
        placeholder="you@example.com"
        autocomplete="email"
        :invalid="$errors->has('email')"
        size="lg"
      />
    </x-ui.field>

    {{-- New password with toggle --}}
    <x-ui.field name="password" label="New password" :error="$errors->first('password')" required>
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
    <x-ui.field name="password_confirmation" label="Confirm new password" :error="$errors->first('password_confirmation')" required>
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

    <x-ui.button
      variant="primary"
      size="lg"
      type="submit"
      class="w-full"
    >
      Reset password
    </x-ui.button>

  </div>
</form>

<hr class="my-6 border-[var(--color-border)]">

<p class="text-center text-sm text-[var(--color-text-muted)]">
  Remember it now?
  <a href="{{ route('login') }}" class="font-bold text-[var(--color-primary)] hover:underline focus-visible:outline-2 focus-visible:outline-[var(--color-brand-600)] focus-visible:outline-offset-2 rounded ms-1">
    Back to sign in
  </a>
</p>

@endsection
