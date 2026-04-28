@extends('layouts.auth')

@section('title', 'Forgot Password | NobleNest Global Academy')
@section('meta_title', 'Reset Your Password | NobleNest Global Academy')
@section('meta_description', 'Enter your email address and we will send you a secure link to reset your NobleNest Global Academy password.')

@section('content')

<div class="text-center mb-6">
  <div class="inline-flex items-center justify-center w-14 h-14 rounded-[var(--radius-sm)] bg-[var(--color-brand-100)] mb-4">
    <x-ui.icon name="mail" class="w-7 h-7 text-[var(--color-brand-600)]" />
  </div>
  <h1 class="text-2xl font-bold text-[var(--color-text)] font-[var(--font-display)] mb-2">Forgot your password?</h1>
  <p class="text-sm text-[var(--color-text-muted)] max-w-xs mx-auto">
    No worries. Enter the email linked to your account and we&rsquo;ll send you a reset link right away.
  </p>
</div>

@if(session('status'))
  <x-ui.alert tone="success" class="mb-4">{{ session('status') }}</x-ui.alert>
@endif

<form method="POST" action="{{ route('password.email') }}" novalidate>
  @csrf

  @if($errors->any())
    <x-ui.alert tone="danger" class="mb-4">{{ $errors->first() }}</x-ui.alert>
  @endif

  <div class="space-y-4">

    <x-ui.field name="email" label="Email address" :error="$errors->first('email')" required>
      <x-ui.input
        type="email"
        name="email"
        :value="old('email')"
        placeholder="you@example.com"
        autocomplete="email"
        autofocus
        :invalid="$errors->has('email')"
        size="lg"
      />
    </x-ui.field>

    <x-ui.button
      variant="primary"
      size="lg"
      type="submit"
      class="w-full"
    >
      Send reset link
    </x-ui.button>

  </div>
</form>

<hr class="my-6 border-[var(--color-border)]">

<p class="text-center text-sm text-[var(--color-text-muted)]">
  Remember your password?
  <a href="{{ route('login') }}" class="font-bold text-[var(--color-primary)] hover:underline focus-visible:outline-2 focus-visible:outline-[var(--color-brand-600)] focus-visible:outline-offset-2 rounded ms-1">
    Back to sign in
  </a>
</p>

@endsection
