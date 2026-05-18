@extends('layouts.marketing')

@section('title', __('billing.pricing_meta_title'))
@section('meta_title', __('billing.pricing_meta_title'))
@section('meta_description', __('billing.pricing_meta_description'))

@section('content')

{{-- Page header --}}
<div class="text-center mb-12">
  <x-ui.badge tone="brand" class="mb-4">{{ __('billing.pricing_badge') }}</x-ui.badge>
  <h1 class="text-4xl font-bold text-[var(--color-text)] font-[var(--font-display)] mb-4">
    {{ __('billing.pricing_title') }}
  </h1>
  <p class="text-lg text-[var(--color-text-muted)] max-w-xl mx-auto">
    {{ __('billing.pricing_subtitle') }}
  </p>
</div>

{{-- Pricing tiers --}}
<div class="grid md:grid-cols-3 gap-6 mb-16 items-start">

  {{-- Free Tier --}}
  <x-ui.card variant="clay" padding="md">
    <p class="text-xs font-extrabold uppercase tracking-widest text-[var(--color-text-muted)] mb-1">{{ __('billing.plan_free') }}</p>
    <div class="flex items-end gap-1 mb-1">
      <span class="text-5xl font-bold text-[var(--color-text)] font-[var(--font-display)]">$0</span>
    </div>
    <p class="text-sm text-[var(--color-text-muted)] mb-6">{{ __('billing.plan_free_price') }}</p>
    <ul class="space-y-2 mb-8 text-sm" aria-label="{{ __('billing.plan_free') }}">
      <li class="flex items-center gap-2">
        <x-ui.icon name="check-circle" class="w-4 h-4 text-emerald-500 shrink-0" />
        {{ __('billing.plan_free_feat_activities') }}
      </li>
      <li class="flex items-center gap-2">
        <x-ui.icon name="check-circle" class="w-4 h-4 text-emerald-500 shrink-0" />
        {{ __('billing.plan_free_feat_profile') }}
      </li>
      <li class="flex items-center gap-2">
        <x-ui.icon name="check-circle" class="w-4 h-4 text-emerald-500 shrink-0" />
        {{ __('billing.plan_free_feat_progress') }}
      </li>
      <li class="flex items-center gap-2">
        <x-ui.icon name="check-circle" class="w-4 h-4 text-emerald-500 shrink-0" />
        {{ __('billing.plan_free_feat_share') }}
      </li>
      <li class="flex items-center gap-2 text-[var(--color-text-muted)]">
        <x-ui.icon name="x" class="w-4 h-4 shrink-0" />
        {{ __('billing.plan_free_feat_packs') }}
      </li>
      <li class="flex items-center gap-2 text-[var(--color-text-muted)]">
        <x-ui.icon name="x" class="w-4 h-4 shrink-0" />
        {{ __('billing.plan_free_feat_modules') }}
      </li>
    </ul>
    @auth
      <x-ui.button variant="secondary" class="w-full" href="{{ route('parent.dashboard') }}">{{ __('billing.plan_current') }}</x-ui.button>
    @else
      <x-ui.button variant="secondary" class="w-full" href="{{ route('register') }}">{{ __('billing.plan_get_started_free') }}</x-ui.button>
    @endauth
  </x-ui.card>

  {{-- Monthly (Featured) --}}
  <div class="relative">
    <div class="absolute -top-4 inset-x-0 flex justify-center">
      <x-ui.badge tone="brand" variant="solid" class="px-4 py-1">{{ __('billing.plan_most_popular') }}</x-ui.badge>
    </div>
    <x-ui.card variant="clay" padding="md" class="bg-gradient-to-br from-[var(--color-brand-600)] to-[var(--color-brand-400)] border-[var(--color-brand-500)] text-white mt-4">
      <p class="text-xs font-extrabold uppercase tracking-widest text-white/80 mb-1">{{ __('billing.plan_monthly') }}</p>
      <div class="flex items-end gap-1 mb-1">
        <span class="text-5xl font-bold font-[var(--font-display)]">${{ $tier['price_monthly'] }}</span>
        <span class="text-white/80 mb-1">{{ __('billing.plan_per_month_short') }}</span>
      </div>
      <p class="text-sm text-white/85 mb-1">{{ __('billing.plan_monthly_tagline') }}</p>
      <p class="text-xs text-white/70 mb-6">{{ __('billing.plan_region_pricing', ['region' => $tier['region_label'], 'currency' => $tier['currency_code']]) }}</p>
      <ul class="space-y-2 mb-8 text-sm text-white" aria-label="{{ __('billing.plan_monthly') }}">
        <li class="flex items-center gap-2">
          <x-ui.icon name="check-circle" class="w-4 h-4 shrink-0 text-white/80" />
          {{ __('billing.plan_monthly_feat_packs') }}
        </li>
        <li class="flex items-center gap-2">
          <x-ui.icon name="check-circle" class="w-4 h-4 shrink-0 text-white/80" />
          {{ __('billing.plan_monthly_feat_profiles') }}
        </li>
        <li class="flex items-center gap-2">
          <x-ui.icon name="check-circle" class="w-4 h-4 shrink-0 text-white/80" />
          {{ __('billing.plan_monthly_feat_modules') }}
        </li>
        <li class="flex items-center gap-2">
          <x-ui.icon name="check-circle" class="w-4 h-4 shrink-0 text-white/80" />
          {{ __('billing.plan_monthly_feat_adaptive') }}
        </li>
        <li class="flex items-center gap-2">
          <x-ui.icon name="check-circle" class="w-4 h-4 shrink-0 text-white/80" />
          {{ __('billing.plan_monthly_feat_milestones') }}
        </li>
        <li class="flex items-center gap-2">
          <x-ui.icon name="check-circle" class="w-4 h-4 shrink-0 text-white/80" />
          {{ __('billing.plan_monthly_feat_support') }}
        </li>
      </ul>
      <a href="{{ route('checkout') }}?plan=monthly"
         class="flex items-center justify-center w-full rounded-[var(--radius-sm)] border-[3px] border-white/60 bg-white text-[var(--color-brand-600)] font-bold py-2.5 px-5 transition-transform duration-[var(--duration-base)] hover:-translate-y-0.5 focus-visible:outline-2 focus-visible:outline-white focus-visible:outline-offset-2">
        {{ __('billing.plan_subscribe_monthly') }}
      </a>
    </x-ui.card>
  </div>

  {{-- Annual --}}
  <x-ui.card variant="clay" padding="md" class="border-emerald-200 relative">
    <div class="absolute top-4 end-4">
      <x-ui.badge tone="success" variant="solid">{{ __('billing.plan_save_20') }}</x-ui.badge>
    </div>
    <p class="text-xs font-extrabold uppercase tracking-widest text-emerald-700 mb-1">{{ __('billing.plan_annual') }}</p>
    <div class="flex items-end gap-1 mb-1">
      <span class="text-5xl font-bold text-[var(--color-text)] font-[var(--font-display)]">${{ $tier['price_yearly'] }}</span>
      <span class="text-[var(--color-text-muted)] mb-1">{{ __('billing.plan_per_year_short') }}</span>
    </div>
    <p class="text-sm text-[var(--color-text-muted)] mb-1">{{ __('billing.plan_annual_permonth', ['amount' => '$'.number_format($tier['price_yearly'] / 12, 2)]) }}</p>
    <p class="text-xs text-[var(--color-text-muted)] mb-6">{{ __('billing.plan_region_pricing', ['region' => $tier['region_label'], 'currency' => $tier['currency_code']]) }}</p>
    <ul class="space-y-2 mb-8 text-sm" aria-label="{{ __('billing.plan_annual') }}">
      <li class="flex items-center gap-2">
        <x-ui.icon name="check-circle" class="w-4 h-4 text-emerald-500 shrink-0" />
        {{ __('billing.plan_annual_feat_everything') }}
      </li>
      <li class="flex items-center gap-2">
        <x-ui.icon name="check-circle" class="w-4 h-4 text-emerald-500 shrink-0" />
        {{ __('billing.plan_annual_feat_savings') }}
      </li>
      <li class="flex items-center gap-2">
        <x-ui.icon name="check-circle" class="w-4 h-4 text-emerald-500 shrink-0" />
        {{ __('billing.plan_annual_feat_profiles') }}
      </li>
      <li class="flex items-center gap-2">
        <x-ui.icon name="check-circle" class="w-4 h-4 text-emerald-500 shrink-0" />
        {{ __('billing.plan_annual_feat_modules') }}
      </li>
      <li class="flex items-center gap-2">
        <x-ui.icon name="check-circle" class="w-4 h-4 text-emerald-500 shrink-0" />
        {{ __('billing.plan_annual_feat_support') }}
      </li>
      <li class="flex items-center gap-2">
        <x-ui.icon name="check-circle" class="w-4 h-4 text-emerald-500 shrink-0" />
        {{ __('billing.plan_annual_feat_offline') }}
      </li>
    </ul>
    <x-ui.button variant="primary" class="w-full" href="{{ route('checkout') }}?plan=annual">
      {{ __('billing.plan_subscribe_annual') }}
    </x-ui.button>
  </x-ui.card>

</div>

{{-- Region note --}}
<p class="text-center text-sm text-[var(--color-text-muted)] mb-16">
  {{ __('billing.pricing_region_note', ['region' => $tier['region_label']]) }}
</p>

{{-- Feature comparison matrix --}}
<x-ui.section title="{{ __('billing.pricing_comparison_title') }}" class="mb-16">
  <x-ui.card variant="outlined" padding="none" class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead>
        <tr class="border-b-[2px] border-[var(--color-border)]">
          <th class="text-left p-4 font-bold text-[var(--color-text)]">{{ __('billing.pricing_col_feature') }}</th>
          <th class="p-4 font-bold text-[var(--color-text)] text-center">{{ __('billing.pricing_col_free') }}</th>
          <th class="p-4 font-bold text-[var(--color-brand-600)] text-center">{{ __('billing.pricing_col_monthly') }}</th>
          <th class="p-4 font-bold text-emerald-700 text-center">{{ __('billing.pricing_col_annual') }}</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-[var(--color-border)]">
        @php
          $unlimited = __('billing.pricing_unlimited');
          $upTo5 = __('billing.pricing_up_to_5');
        @endphp
        @foreach([
          [__('billing.pricing_row_activities'), '7',       $unlimited, $unlimited],
          [__('billing.pricing_row_profiles'),   '1',       $upTo5,     $upTo5],
          [__('billing.pricing_row_adaptive'),   'check',    'check',     'check'],
          [__('billing.pricing_row_progress'),   'check',    'check',     'check'],
          [__('billing.pricing_row_share'),      'check',    'check',     'check'],
          [__('billing.pricing_row_packs'),      'x',         'check',     'check'],
          [__('billing.pricing_row_modules'),    'x',         'check',     'check'],
          [__('billing.pricing_row_support'),    'x',         'check',     'check'],
          [__('billing.pricing_row_offline'),    'x',         'x',         'check'],
          [__('billing.pricing_row_savings'),    'x',         'x',         'check'],
        ] as $row)
        <tr class="hover:bg-[var(--color-surface-strong)] transition-colors">
          <td class="p-4 text-[var(--color-text)]">{{ $row[0] }}</td>
          @foreach([$row[1], $row[2], $row[3]] as $cell)
          <td class="p-4 text-center">
            @if($cell === 'check')
              <x-ui.icon name="check-circle" class="w-5 h-5 text-emerald-500 mx-auto" />
              <span class="sr-only">{{ __('billing.pricing_yes') }}</span>
            @elseif($cell === 'x')
              <x-ui.icon name="x" class="w-5 h-5 text-[var(--color-text-muted)] mx-auto" />
              <span class="sr-only">{{ __('billing.pricing_no') }}</span>
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
<x-ui.section title="{{ __('billing.pricing_faq_title') }}" class="mb-12">
  <div class="max-w-2xl mx-auto space-y-4" x-data="{ open: null }">
    @foreach([
      ['q' => __('billing.pricing_faq1_q'), 'a' => __('billing.pricing_faq1_a')],
      ['q' => __('billing.pricing_faq2_q'), 'a' => __('billing.pricing_faq2_a')],
      ['q' => __('billing.pricing_faq3_q'), 'a' => __('billing.pricing_faq3_a')],
      ['q' => __('billing.pricing_faq4_q'), 'a' => __('billing.pricing_faq4_a')],
      ['q' => __('billing.pricing_faq5_q'), 'a' => __('billing.pricing_faq5_a')],
    ] as $i => $faq)
    <div class="border-[2px] border-[var(--color-border)] rounded-[var(--radius-sm)] overflow-hidden">
      <button
        type="button"
        class="w-full flex items-center justify-between gap-4 p-4 text-left font-semibold text-[var(--color-text)] hover:bg-[var(--color-surface-strong)] transition-colors focus-visible:outline-2 focus-visible:outline-[var(--color-brand-600)] focus-visible:outline-offset-[-2px]"
        @click="open = open === {{ $i }} ? null : {{ $i }}"
        :aria-expanded="open === {{ $i }}"
        aria-controls="faq-{{ $i }}"
      >
        {{ $faq['q'] }}
        <x-ui.icon name="chevron-down" class="w-5 h-5 shrink-0 text-[var(--color-text-muted)] transition-transform duration-[var(--duration-base)]" x-bind:class="open === {{ $i }} ? 'rotate-180' : ''" />
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
    {{ __('billing.pricing_cta') }}
  </x-ui.button>
</div>

@endsection
