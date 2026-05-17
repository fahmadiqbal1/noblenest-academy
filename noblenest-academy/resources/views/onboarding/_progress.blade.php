@php
    /** @var int $step  current step number 1..5 */
    $step = $step ?? 1;
    $total = 5;
    $percent = (int) round($step / $total * 100);
    $labels = [
        1 => __('onboarding.step_language'),
        2 => __('onboarding.step_profile'),
        3 => __('onboarding.step_consent'),
        4 => __('onboarding.step_child'),
        5 => __('onboarding.step_walkthrough'),
    ];
@endphp
<div class="mb-6">
  <div class="flex items-center justify-between mb-2 text-xs text-[var(--color-text-muted)] font-semibold">
    <span>{{ __('onboarding.step_of', ['n' => $step, 'total' => $total]) }} — {{ $labels[$step] ?? '' }}</span>
    <span>{{ $percent }}%</span>
  </div>
  <div class="w-full h-2 rounded-full bg-[var(--color-border)] overflow-hidden" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $percent }}">
    <div class="h-full bg-[var(--color-brand-600)] transition-all duration-300" style="width: {{ $percent }}%"></div>
  </div>
</div>
