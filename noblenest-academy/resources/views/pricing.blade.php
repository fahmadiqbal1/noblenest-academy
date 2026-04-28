@extends('layouts.marketing')

@section('title', 'Pricing — Noble Nest Academy')
@section('meta_title', 'Pricing — Noble Nest Academy')
@section('meta_description', 'Simple, fair pricing for families worldwide. Automatically adjusted for your region. Start free, upgrade anytime.')

@section('content')

{{-- Page header --}}
<div class="text-center mb-12">
  <x-ui.badge tone="brand" class="mb-4">Simple, Fair Pricing</x-ui.badge>
  <h1 class="text-4xl font-bold text-[var(--color-text)] font-[var(--font-display)] mb-4">
    Learning for Every Family
  </h1>
  <p class="text-lg text-[var(--color-text-muted)] max-w-xl mx-auto">
    Automatically adjusted for your region. Learning should be accessible everywhere.
  </p>
</div>

{{-- Pricing tiers --}}
<div class="grid md:grid-cols-3 gap-6 mb-16 items-start">

  {{-- Free Tier --}}
  <x-ui.card variant="clay" padding="md">
    <p class="text-xs font-extrabold uppercase tracking-widest text-[var(--color-text-muted)] mb-1">Free</p>
    <div class="flex items-end gap-1 mb-1">
      <span class="text-5xl font-bold text-[var(--color-text)] font-[var(--font-display)]">$0</span>
    </div>
    <p class="text-sm text-[var(--color-text-muted)] mb-6">Always free, forever</p>
    <ul class="space-y-2 mb-8 text-sm" aria-label="Free plan features">
      <li class="flex items-center gap-2">
        <x-ui.icon name="check-circle" class="w-4 h-4 text-emerald-500 shrink-0" />
        7 activities per module
      </li>
      <li class="flex items-center gap-2">
        <x-ui.icon name="check-circle" class="w-4 h-4 text-emerald-500 shrink-0" />
        1 child profile
      </li>
      <li class="flex items-center gap-2">
        <x-ui.icon name="check-circle" class="w-4 h-4 text-emerald-500 shrink-0" />
        Progress tracking
      </li>
      <li class="flex items-center gap-2">
        <x-ui.icon name="check-circle" class="w-4 h-4 text-emerald-500 shrink-0" />
        Share cards
      </li>
      <li class="flex items-center gap-2 text-[var(--color-text-muted)]">
        <x-ui.icon name="x" class="w-4 h-4 shrink-0" />
        Weekly content packs
      </li>
      <li class="flex items-center gap-2 text-[var(--color-text-muted)]">
        <x-ui.icon name="x" class="w-4 h-4 shrink-0" />
        Full module access
      </li>
    </ul>
    @auth
      <x-ui.button variant="secondary" class="w-full" href="{{ route('parent.dashboard') }}">Current Plan</x-ui.button>
    @else
      <x-ui.button variant="secondary" class="w-full" href="{{ route('register') }}">Get Started Free</x-ui.button>
    @endauth
  </x-ui.card>

  {{-- Monthly (Featured) --}}
  <div class="relative">
    <div class="absolute -top-4 inset-x-0 flex justify-center">
      <x-ui.badge tone="brand" variant="solid" class="px-4 py-1">Most Popular</x-ui.badge>
    </div>
    <x-ui.card variant="clay" padding="md" class="bg-gradient-to-br from-[var(--color-brand-600)] to-[var(--color-brand-400)] border-[var(--color-brand-500)] text-white mt-4">
      <p class="text-xs font-extrabold uppercase tracking-widest text-white/80 mb-1">Monthly</p>
      <div class="flex items-end gap-1 mb-1">
        <span class="text-5xl font-bold font-[var(--font-display)]">${{ $tier['price_monthly'] }}</span>
        <span class="text-white/80 mb-1">/mo</span>
      </div>
      <p class="text-sm text-white/85 mb-1">New activities unlocked every week</p>
      <p class="text-xs text-white/70 mb-6">{{ $tier['region_label'] }} pricing · {{ $tier['currency_code'] }}</p>
      <ul class="space-y-2 mb-8 text-sm text-white" aria-label="Monthly plan features">
        <li class="flex items-center gap-2">
          <x-ui.icon name="check-circle" class="w-4 h-4 shrink-0 text-white/80" />
          Weekly content packs (5 activities/week)
        </li>
        <li class="flex items-center gap-2">
          <x-ui.icon name="check-circle" class="w-4 h-4 shrink-0 text-white/80" />
          Up to 5 child profiles
        </li>
        <li class="flex items-center gap-2">
          <x-ui.icon name="check-circle" class="w-4 h-4 shrink-0 text-white/80" />
          All modules &amp; subjects
        </li>
        <li class="flex items-center gap-2">
          <x-ui.icon name="check-circle" class="w-4 h-4 shrink-0 text-white/80" />
          Age-adaptive content
        </li>
        <li class="flex items-center gap-2">
          <x-ui.icon name="check-circle" class="w-4 h-4 shrink-0 text-white/80" />
          Progress milestones
        </li>
        <li class="flex items-center gap-2">
          <x-ui.icon name="check-circle" class="w-4 h-4 shrink-0 text-white/80" />
          Priority support
        </li>
      </ul>
      <a href="{{ route('checkout') }}?plan=monthly"
         class="flex items-center justify-center w-full rounded-[var(--radius-sm)] border-[3px] border-white/60 bg-white text-[var(--color-brand-600)] font-bold py-2.5 px-5 transition-transform duration-[var(--duration-base)] hover:-translate-y-0.5 focus-visible:outline-2 focus-visible:outline-white focus-visible:outline-offset-2">
        Subscribe Monthly
      </a>
    </x-ui.card>
  </div>

  {{-- Annual --}}
  <x-ui.card variant="clay" padding="md" class="border-emerald-200 relative">
    <div class="absolute top-4 end-4">
      <x-ui.badge tone="success" variant="solid">Save 20%</x-ui.badge>
    </div>
    <p class="text-xs font-extrabold uppercase tracking-widest text-emerald-600 mb-1">Annual</p>
    <div class="flex items-end gap-1 mb-1">
      <span class="text-5xl font-bold text-[var(--color-text)] font-[var(--font-display)]">${{ $tier['price_yearly'] }}</span>
      <span class="text-[var(--color-text-muted)] mb-1">/yr</span>
    </div>
    <p class="text-sm text-[var(--color-text-muted)] mb-1">That's ${{ number_format($tier['price_yearly'] / 12, 2) }}/month</p>
    <p class="text-xs text-[var(--color-text-muted)] mb-6">{{ $tier['region_label'] }} pricing · {{ $tier['currency_code'] }}</p>
    <ul class="space-y-2 mb-8 text-sm" aria-label="Annual plan features">
      <li class="flex items-center gap-2">
        <x-ui.icon name="check-circle" class="w-4 h-4 text-emerald-500 shrink-0" />
        Everything in Monthly
      </li>
      <li class="flex items-center gap-2">
        <x-ui.icon name="check-circle" class="w-4 h-4 text-emerald-500 shrink-0" />
        20% savings
      </li>
      <li class="flex items-center gap-2">
        <x-ui.icon name="check-circle" class="w-4 h-4 text-emerald-500 shrink-0" />
        Up to 5 child profiles
      </li>
      <li class="flex items-center gap-2">
        <x-ui.icon name="check-circle" class="w-4 h-4 text-emerald-500 shrink-0" />
        All modules &amp; subjects
      </li>
      <li class="flex items-center gap-2">
        <x-ui.icon name="check-circle" class="w-4 h-4 text-emerald-500 shrink-0" />
        Priority support
      </li>
      <li class="flex items-center gap-2">
        <x-ui.icon name="check-circle" class="w-4 h-4 text-emerald-500 shrink-0" />
        Offline downloads
      </li>
    </ul>
    <x-ui.button variant="primary" class="w-full" href="{{ route('checkout') }}?plan=annual">
      Subscribe Annually
    </x-ui.button>
  </x-ui.card>

</div>

{{-- Region note --}}
<p class="text-center text-sm text-[var(--color-text-muted)] mb-16">
  Prices shown are for your region ({{ $tier['region_label'] }}).
  All plans include a 7-day money-back guarantee.
</p>

{{-- Feature comparison matrix --}}
<x-ui.section title="Full Feature Comparison" class="mb-16">
  <x-ui.card variant="outlined" padding="none" class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead>
        <tr class="border-b-[2px] border-[var(--color-border)]">
          <th class="text-start p-4 font-bold text-[var(--color-text)]">Feature</th>
          <th class="p-4 font-bold text-[var(--color-text)] text-center">Free</th>
          <th class="p-4 font-bold text-[var(--color-brand-600)] text-center">Monthly</th>
          <th class="p-4 font-bold text-emerald-600 text-center">Annual</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-[var(--color-border)]">
        @foreach([
          ['Activities per day',       '7',         'Unlimited', 'Unlimited'],
          ['Child profiles',           '1',         'Up to 5',   'Up to 5'],
          ['Age-adaptive content',     'check',     'check',     'check'],
          ['Progress tracking',        'check',     'check',     'check'],
          ['Share cards',              'check',     'check',     'check'],
          ['Weekly content packs',     'x',         'check',     'check'],
          ['Full module access',       'x',         'check',     'check'],
          ['Priority support',         'x',         'check',     'check'],
          ['Offline downloads',        'x',         'x',         'check'],
          ['20% savings vs monthly',   'x',         'x',         'check'],
        ] as $row)
        <tr class="hover:bg-[var(--color-surface-strong)] transition-colors">
          <td class="p-4 text-[var(--color-text)]">{{ $row[0] }}</td>
          @foreach([$row[1], $row[2], $row[3]] as $cell)
          <td class="p-4 text-center">
            @if($cell === 'check')
              <x-ui.icon name="check-circle" class="w-5 h-5 text-emerald-500 mx-auto" />
              <span class="sr-only">Yes</span>
            @elseif($cell === 'x')
              <x-ui.icon name="x" class="w-5 h-5 text-[var(--color-text-muted)] mx-auto" />
              <span class="sr-only">No</span>
            @else
              <span class="font-semibold text-[var(--color-text)]">{{ $cell }}</span>
            @endif
          </td>
          @endforeach
        </tr>
        @endforeach
      </tbody>
    </table>
  </x-ui.card>
</x-ui.section>

{{-- FAQ --}}
<x-ui.section title="Frequently Asked Questions" class="mb-12">
  <div class="max-w-2xl mx-auto space-y-4" x-data="{ open: null }">
    @foreach([
      ['q' => 'Can I switch plans later?',                   'a' => 'Yes — you can upgrade, downgrade, or cancel at any time from your account settings. Prorated credits apply when upgrading.'],
      ['q' => 'Is there a free trial for paid plans?',       'a' => 'We offer a 7-day money-back guarantee on all paid plans. If you\'re not satisfied, contact support for a full refund.'],
      ['q' => 'How does regional pricing work?',             'a' => 'We automatically detect your country and apply a fair regional price. You\'ll always see your local price before subscribing.'],
      ['q' => 'How many children can use one account?',      'a' => 'Free accounts support 1 child profile. Monthly and Annual plans support up to 5 child profiles under one subscription.'],
      ['q' => 'What payment methods do you accept?',         'a' => 'We accept all major credit/debit cards, and regional payment methods where available.'],
    ] as $i => $faq)
    <div class="border-[2px] border-[var(--color-border)] rounded-[var(--radius-sm)] overflow-hidden">
      <button
        type="button"
        class="w-full flex items-center justify-between gap-4 p-4 text-start font-semibold text-[var(--color-text)] hover:bg-[var(--color-surface-strong)] transition-colors focus-visible:outline-2 focus-visible:outline-[var(--color-brand-600)] focus-visible:outline-offset-[-2px]"
        @click="open = open === {{ $i }} ? null : {{ $i }}"
        :aria-expanded="open === {{ $i }}"
        aria-controls="faq-{{ $i }}"
      >
        {{ $faq['q'] }}
        <x-ui.icon name="chevron-down" class="w-5 h-5 shrink-0 text-[var(--color-text-muted)] transition-transform duration-[var(--duration-base)]" :class="open === {{ $i }} ? 'rotate-180' : ''" />
      </button>
      <div id="faq-{{ $i }}" x-show="open === {{ $i }}" x-collapse class="px-4 pb-4 text-sm text-[var(--color-text-muted)]">
        {{ $faq['a'] }}
      </div>
    </div>
    @endforeach
  </div>
</x-ui.section>

{{-- Final CTA --}}
<div class="text-center">
  <x-ui.button variant="primary" size="lg" href="{{ route('register') }}" iconRight="arrow-right">
    Start Free Today
  </x-ui.button>
</div>

@endsection
