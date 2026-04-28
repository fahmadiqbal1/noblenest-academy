{{--
 * Activity Card — child grid tile
 * Props: $activity, $child, $activityUrl, $isLocked, $isDone
--}}
@php
    $subjectTones = [
        'quran' => 'subject-quran', 'arabic' => 'subject-arabic', 'islamic_studies' => 'subject-islamic',
        'math' => 'subject-math', 'literacy' => 'subject-reading', 'science' => 'subject-science',
        'art' => 'subject-art', 'motor' => 'subject-motor', 'sensory' => 'subject-sensory',
        'social' => 'subject-social', 'language' => 'subject-language',
        'stem' => 'subject-science', 'coding' => 'subject-coding',
    ];
    $subjectColors = [
        'quran' => '#059669', 'arabic' => '#7C3AED', 'islamic_studies' => '#059669',
        'math' => '#EF4444', 'literacy' => '#F59E0B', 'science' => '#10B981',
        'art' => '#EC4899', 'motor' => '#22C55E', 'sensory' => '#A855F7',
        'social' => '#06B6D4', 'language' => '#3B82F6', 'stem' => '#10B981',
        'coding' => '#6366F1', 'stories' => '#F59E0B', 'islamic' => '#059669',
    ];
    $cardColor = $subjectColors[$activity->subject ?? ''] ?? '#7C3AED';
    $isParentLed = ($activity->age_tier ?? '') === 'baby';
    $isIslamic   = in_array($activity->subject ?? '', ['quran', 'islamic_studies', 'arabic', 'islamic']);
@endphp

<div class="relative flex flex-col rounded-[var(--radius-card)] border-[3px] border-[var(--color-border)] shadow-[var(--shadow-clay)] bg-[var(--color-surface-strong)] overflow-hidden h-full transition-all hover:-translate-y-1 hover:shadow-[var(--shadow-clay-hover)]
    {{ $isLocked ? 'opacity-85' : '' }}
    {{ $isIslamic ? 'bg-gradient-to-b from-emerald-50 to-white' : '' }}">

    {{-- Colour top stripe --}}
    <div class="h-1.5 w-full shrink-0" style="background: {{ $cardColor }};"></div>

    {{-- Lock overlay --}}
    @if($isLocked)
    <div class="absolute inset-0 z-10 flex items-center justify-center rounded-[var(--radius-card)] bg-[rgba(30,27,75,0.45)] backdrop-blur-[3px]">
        <div class="text-center text-white">
            <div class="text-3xl mb-1" aria-hidden="true">🔒</div>
            <div class="text-xs font-bold">Premium</div>
        </div>
    </div>
    @endif

    <div class="flex flex-col flex-1 p-3 gap-1.5">

        {{-- Top: emoji + badges --}}
        <div class="flex items-start justify-between gap-2 mb-1">
            <span class="text-3xl leading-none shrink-0" aria-hidden="true">{{ $activity->emoji ?? '📚' }}</span>
            <div class="flex flex-col items-end gap-1 shrink-0">
                {{-- Subject badge --}}
                <span class="inline-flex items-center gap-0.5 rounded-full px-2 py-0.5 text-xs font-bold text-white whitespace-nowrap"
                      style="background: {{ $cardColor }};">
                    {{ ucfirst(str_replace('_', ' ', $activity->subject ?? 'activity')) }}
                </span>
                @if($isDone)
                    <x-ui.badge tone="success" size="sm">
                        <x-ui.icon name="check" class="w-2.5 h-2.5" aria-hidden="true" />Done
                    </x-ui.badge>
                @elseif($isLocked && $hasSubscription)
                    <x-ui.badge tone="warning" size="sm">
                        Wk {{ $activity->unlock_week ?? '?' }}
                    </x-ui.badge>
                @elseif(!$isLocked && $activity->is_free)
                    <x-ui.badge tone="success" size="sm">Free</x-ui.badge>
                @endif
                @if($isParentLed)
                    <x-ui.badge tone="info" size="sm">Guide</x-ui.badge>
                @endif
            </div>
        </div>

        {{-- Title --}}
        <h3 class="text-sm font-bold text-[var(--color-text)] leading-tight line-clamp-2">{{ $activity->title }}</h3>

        {{-- Description --}}
        @if($activity->description)
            <p class="text-xs text-[var(--color-text-muted)] leading-snug line-clamp-2 flex-1">{{ Str::limit($activity->description, 75) }}</p>
        @else
            <div class="flex-1"></div>
        @endif

        {{-- Meta chips --}}
        <div class="flex flex-wrap gap-1 mb-1">
            @if($activity->duration_minutes)
                <span class="inline-flex items-center gap-0.5 rounded-full bg-[var(--color-border)] text-[var(--color-text-muted)] text-xs px-2 py-0.5 font-medium">
                    <x-ui.icon name="clock" class="w-2.5 h-2.5" aria-hidden="true" />{{ $activity->duration_minutes }}min
                </span>
            @elseif($activity->duration)
                <span class="inline-flex items-center gap-0.5 rounded-full bg-[var(--color-border)] text-[var(--color-text-muted)] text-xs px-2 py-0.5 font-medium">
                    <x-ui.icon name="clock" class="w-2.5 h-2.5" aria-hidden="true" />{{ $activity->duration }}min
                </span>
            @endif
            @if($activity->activity_type)
                <span class="inline-flex items-center rounded-full bg-[var(--color-border)] text-[var(--color-text-muted)] text-xs px-2 py-0.5 font-medium">
                    {{ ucfirst($activity->activity_type) }}
                </span>
            @endif
        </div>

        {{-- CTA button --}}
        @if($isLocked)
            @if($hasSubscription)
                <div class="w-full px-3 py-2 min-h-[2.75rem] rounded-[var(--radius-sm)] bg-[var(--color-border)] text-[var(--color-text-muted)] text-sm font-bold text-center flex items-center justify-center gap-1.5"
                     aria-label="Unlocks in week {{ $activity->unlock_week ?? '?' }}">
                    <x-ui.icon name="lock" class="w-3.5 h-3.5" aria-hidden="true" />Unlocks Week {{ $activity->unlock_week ?? '?' }}
                </div>
            @elseif(in_array(auth()->user()->role ?? '', ['Parent', 'Student']))
                <a href="{{ route('pricing') }}"
                   class="block w-full px-3 py-2 min-h-[2.75rem] rounded-[var(--radius-sm)] bg-gradient-to-br from-[var(--color-brand-600)] to-[var(--color-brand-400)] border-[2px] border-[var(--color-brand-600)] text-white text-sm font-bold text-center flex items-center justify-center gap-1.5 hover:-translate-y-[1px] transition-all focus-visible:outline-2 focus-visible:outline-[var(--color-brand-600)] focus-visible:outline-offset-2">
                    <x-ui.icon name="sparkles" class="w-3.5 h-3.5" aria-hidden="true" />Unlock Premium
                </a>
            @else
                <div class="w-full px-3 py-2 min-h-[2.75rem] rounded-[var(--radius-sm)] bg-[var(--color-border)] text-[var(--color-text-muted)] text-sm font-bold text-center flex items-center justify-center gap-1.5">
                    <x-ui.icon name="lock" class="w-3.5 h-3.5" aria-hidden="true" />Locked
                </div>
            @endif
        @elseif($isDone)
            <a href="{{ $activityUrl }}"
               class="block w-full px-3 py-2 min-h-[2.75rem] rounded-[var(--radius-sm)] border-[2px] border-emerald-500 bg-emerald-50 text-emerald-700 text-sm font-bold text-center flex items-center justify-center gap-1.5 hover:bg-emerald-100 transition-colors focus-visible:outline-2 focus-visible:outline-emerald-600 focus-visible:outline-offset-2">
                <x-ui.icon name="play" class="w-3.5 h-3.5" aria-hidden="true" />Play Again
            </a>
        @else
            <a href="{{ $activityUrl }}"
               class="block w-full px-3 py-2 min-h-[2.75rem] rounded-[var(--radius-sm)] border-[2px] text-white text-sm font-bold text-center flex items-center justify-center gap-1.5 hover:-translate-y-[1px] transition-all focus-visible:outline-2 focus-visible:outline-offset-2"
               style="background: {{ $cardColor }}; border-color: {{ $cardColor }};"
               :aria-label="'Play ' + activity.title">
                <x-ui.icon name="play" class="w-3.5 h-3.5" aria-hidden="true" />
                {{ $isParentLed ? '🌱 Open Guide' : 'Play Now' }}
            </a>
        @endif

    </div>
</div>
