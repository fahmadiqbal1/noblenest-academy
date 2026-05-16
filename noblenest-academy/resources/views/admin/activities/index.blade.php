@extends('layouts.admin')

@section('title', __('Activities'))

@section('content')

@php
$subjectColors = ['sensory'=>'#f59e0b','motor'=>'#10b981','language'=>'#3b82f6','literacy'=>'#6366f1',
    'numeracy'=>'#ec4899','science'=>'#06b6d4','art'=>'#f97316','music'=>'#8b5cf6',
    'social'=>'#14b8a6','character'=>'#22c55e','etiquette'=>'#a855f7','quran'=>'#059669',
    'islamic'=>'#065f46','arabic'=>'#0891b2','coding'=>'#1d4ed8','robotics'=>'#7c3aed',
    'stem'=>'#0369a1','cultural'=>'#b45309'];
$subjects = ['sensory'=>'🌈 Sensory','motor'=>'🏃 Motor','language'=>'💬 Language',
    'literacy'=>'📖 Literacy','numeracy'=>'🔢 Numeracy','science'=>'🔬 Science',
    'art'=>'🎨 Art','music'=>'🎵 Music','social'=>'🤝 Social','character'=>'💛 Character',
    'etiquette'=>'🎩 Etiquette','quran'=>'📿 Quran','islamic'=>'☪️ Islamic',
    'arabic'=>'ع Arabic','coding'=>'💻 Coding','robotics'=>'🤖 Robotics',
    'stem'=>'🧪 STEM','cultural'=>'🌍 Cultural'];
$actTypeIcons = ['video'=>'📹','tracing'=>'✏️','drawing'=>'🎨','puzzle'=>'🧩','quiz'=>'🧠',
    'story'=>'📖','music'=>'🎵','outdoor'=>'🌿','experiment'=>'🔬','coding'=>'💻'];
$diffTones  = ['easy'=>'success','medium'=>'warning','hard'=>'danger'];
$langFlags  = ['en'=>'🇬🇧','fr'=>'🇫🇷','ru'=>'🇷🇺','zh'=>'🇨🇳','es'=>'🇪🇸','ko'=>'🇰🇷','ur'=>'🇵🇰','ar'=>'🇸🇦','multi'=>'🌐'];
@endphp

<div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 py-8">

    <x-ui.page-header
        :title="__('Activities')"
        :breadcrumbs="[
            ['label' => __('Admin'), 'url' => route('admin.analytics.index')],
            ['label' => __('Activities')],
        ]"
    >
        <x-slot:actions>
            <x-ui.button
                variant="secondary"
                icon="plus"
                x-data
                @click="$dispatch('open-modal', 'quick-add-activity')"
                type="button"
                id="addActivityBtn"
            >
                {{ __('Quick Add') }}
            </x-ui.button>
            <x-ui.button variant="primary" icon="plus" href="{{ route('admin.activities.create') }}">
                {{ __('New Activity') }}
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    {{-- Alerts --}}
    @if(session('success'))
        <x-ui.alert tone="success" dismissible class="mb-4">
            {{ session('success') }}
        </x-ui.alert>
    @endif
    @if($errors->any())
        <x-ui.alert tone="danger" dismissible class="mb-4" :title="__('Please fix the following errors')">
            <ul class="mt-1 list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </x-ui.alert>
    @endif

    {{-- Filter bar --}}
    <form method="GET" class="flex flex-wrap gap-2 mb-4" role="search" aria-label="{{ __('Filter activities') }}">
        <x-ui.input
            type="text"
            name="q"
            :placeholder="__('Search title…')"
            :value="request('q')"
            icon="search"
            class="max-w-xs"
        />
        <div class="relative">
            <select
                name="subject"
                class="block rounded-[var(--radius-sm)] border-[2px] border-[var(--color-border)] bg-[var(--color-surface-strong)] text-[var(--color-text)] text-sm py-2.5 ps-4 pe-10 focus:outline-none focus:border-[var(--color-brand-500)] appearance-none cursor-pointer min-h-[2.5rem]"
                aria-label="{{ __('Filter by subject') }}"
            >
                <option value="">{{ __('All Subjects') }}</option>
                @foreach($subjects as $k => $label)
                    <option value="{{ $k }}" @selected(request('subject') === $k)>{{ $label }}</option>
                @endforeach
            </select>
            <span class="pointer-events-none absolute inset-y-0 end-0 flex items-center pe-3 text-[var(--color-text-muted)]">
                <x-ui.icon name="chevron-down" class="w-4 h-4" />
            </span>
        </div>
        <div class="relative">
            <select
                name="type"
                class="block rounded-[var(--radius-sm)] border-[2px] border-[var(--color-border)] bg-[var(--color-surface-strong)] text-[var(--color-text)] text-sm py-2.5 ps-4 pe-10 focus:outline-none focus:border-[var(--color-brand-500)] appearance-none cursor-pointer min-h-[2.5rem]"
                aria-label="{{ __('Filter by type') }}"
            >
                <option value="">{{ __('All Types') }}</option>
                @foreach($actTypeIcons as $k => $icon)
                    <option value="{{ $k }}" @selected(request('type') === $k)>{{ $icon }} {{ ucfirst($k) }}</option>
                @endforeach
            </select>
            <span class="pointer-events-none absolute inset-y-0 end-0 flex items-center pe-3 text-[var(--color-text-muted)]">
                <x-ui.icon name="chevron-down" class="w-4 h-4" />
            </span>
        </div>
        <x-ui.button type="submit" variant="secondary" size="md" icon="search">
            {{ __('Filter') }}
        </x-ui.button>
        @if(request()->hasAny(['q', 'subject', 'type']))
            <x-ui.button variant="ghost" size="md" href="{{ route('admin.activities.index') }}">
                {{ __('Clear') }}
            </x-ui.button>
        @endif
    </form>

    {{-- Activity rows --}}
    <div class="flex flex-col gap-2">
        @forelse($activities as $activity)
            @php
                $color  = $subjectColors[$activity->subject ?? ''] ?? '#9ca3af';
                $typeIc = $actTypeIcons[$activity->activity_type ?? ''] ?? '📌';
                $langFl = $langFlags[$activity->language ?? ''] ?? '';
            @endphp
            <div
                class="group relative bg-[var(--color-surface-strong)] rounded-[var(--radius-card)] border-[2px] border-[var(--color-border)] border-s-4 p-4 hover:shadow-[var(--shadow-soft)] transition-shadow"
                style="border-left-color: {{ $color }}"
            >
                <div class="flex items-start gap-3 pe-24">
                    {{-- Emoji / type icon --}}
                    <div class="text-3xl leading-none shrink-0 mt-0.5" aria-hidden="true">
                        {{ $activity->emoji ?: $typeIc }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="font-bold text-[var(--color-text)] text-sm leading-snug">{{ $activity->title }}</p>
                        <p class="text-xs text-[var(--color-text-muted)] mt-0.5 mb-2 truncate max-w-xl">
                            {{ $activity->description }}
                        </p>
                        <div class="flex flex-wrap gap-1.5 items-center">
                            @if($activity->subject)
                                <span
                                    class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-bold"
                                    style="background:{{ $color }}22;color:{{ $color }}"
                                >{{ $subjects[$activity->subject] ?? $activity->subject }}</span>
                            @endif
                            @if($activity->activity_type)
                                <x-ui.badge tone="neutral" size="sm">{{ $typeIc }} {{ ucfirst($activity->activity_type) }}</x-ui.badge>
                            @endif
                            @if($activity->age_min !== null && $activity->age_max !== null)
                                <x-ui.badge tone="neutral" size="sm">👶 {{ $activity->age_min }}–{{ $activity->age_max }}y</x-ui.badge>
                            @endif
                            @if($activity->difficulty)
                                <x-ui.badge :tone="$diffTones[$activity->difficulty] ?? 'neutral'" size="sm">
                                    {{ ucfirst($activity->difficulty) }}
                                </x-ui.badge>
                            @endif
                            @if($activity->duration_minutes)
                                <x-ui.badge tone="neutral" size="sm">⏱ {{ $activity->duration_minutes }}min</x-ui.badge>
                            @endif
                            @if($langFl)
                                <x-ui.badge tone="neutral" size="sm">{{ $langFl }}</x-ui.badge>
                            @endif
                            @if($activity->is_muslim_only)
                                <x-ui.badge tone="success" size="sm">☪️</x-ui.badge>
                            @endif
                            @if($activity->is_free)
                                <x-ui.badge tone="info" size="sm">{{ __('Free') }}</x-ui.badge>
                            @else
                                <x-ui.badge tone="warning" size="sm">💎 {{ __('Premium') }}</x-ui.badge>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Row actions — visible on hover/focus --}}
                <div class="absolute top-3 end-3 flex items-center gap-1.5 opacity-0 group-hover:opacity-100 group-focus-within:opacity-100 transition-opacity">
                    <x-ui.button
                        variant="ghost"
                        size="sm"
                        icon="edit"
                        href="{{ route('admin.activities.edit', $activity) }}"
                        :title="__('Edit')"
                    ></x-ui.button>
                    <form
                        method="POST"
                        action="{{ route('admin.activities.destroy', $activity) }}"
                        onsubmit="return confirm('{{ __('Delete this activity?') }}')"
                    >
                        @csrf
                        @method('DELETE')
                        <x-ui.button
                            type="submit"
                            variant="ghost"
                            size="sm"
                            icon="trash"
                            :title="__('Delete')"
                            class="text-[var(--color-coral-500)] hover:bg-[var(--color-coral-50)]"
                        ></x-ui.button>
                    </form>
                </div>
            </div>
        @empty
            <x-ui.empty-state
                icon="star"
                :title="__('No activities found')"
                :description="__('Try adjusting your filters or create a new activity.')"
            >
                <x-slot:actions>
                    <x-ui.button variant="primary" icon="plus" href="{{ route('admin.activities.create') }}">
                        {{ __('Add Activity') }}
                    </x-ui.button>
                </x-slot:actions>
            </x-ui.empty-state>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $activities->withQueryString()->links() }}
    </div>

    {{-- Bulk Upload --}}
    <div class="mt-6">
        <x-ui.card variant="outlined" padding="md">
            <form
                method="POST"
                action="{{ route('admin.activities.bulkUpload') }}"
                enctype="multipart/form-data"
                class="flex items-center flex-wrap gap-3"
            >
                @csrf
                <label class="text-sm font-semibold text-[var(--color-text-muted)]">{{ __('Bulk Upload (CSV):') }}</label>
                <x-ui.input
                    type="file"
                    name="file"
                    accept=".csv,.txt"
                    class="max-w-xs"
                    required
                />
                <x-ui.button type="submit" variant="secondary" size="sm" icon="upload">
                    {{ __('Upload CSV') }}
                </x-ui.button>
            </form>
        </x-ui.card>
    </div>

</div>

{{-- Quick Add Modal (Alpine-driven) --}}
<div
    x-data="{ open: false }"
    x-on:open-modal.window="if ($event.detail === 'quick-add-activity') open = true"
    x-on:keydown.escape.window="open = false"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
    role="dialog"
    aria-modal="true"
    aria-labelledby="quick-add-title"
>
    {{-- Overlay --}}
    <div
        class="absolute inset-0 bg-black/40"
        @click="open = false"
        aria-hidden="true"
    ></div>

    {{-- Panel --}}
    <div
        class="relative w-full max-w-2xl max-h-[90vh] overflow-y-auto bg-[var(--color-surface-strong)] rounded-[var(--radius-card)] border-[2px] border-[var(--color-border)] shadow-[var(--shadow-clay)] flex flex-col"
        x-transition
    >
        <form method="POST" action="{{ route('admin.activities.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="flex items-center justify-between gap-4 px-6 py-4 border-b border-[var(--color-border)]">
                <h2 id="quick-add-title" class="text-lg font-bold text-[var(--color-text)]">
                    {{ __('Quick Add Activity') }}
                </h2>
                <button
                    type="button"
                    @click="open = false"
                    class="rounded p-1.5 hover:bg-[var(--color-primary-soft)] focus-visible:outline-2 focus-visible:outline-[var(--color-brand-600)] focus-visible:outline-offset-2 transition-colors"
                    aria-label="{{ __('Close modal') }}"
                >
                    <x-ui.icon name="x" class="w-5 h-5 text-[var(--color-text-muted)]" />
                </button>
            </div>
            <div class="px-6 py-4 overflow-y-auto">
                @include('admin.activities.partials.form', ['activity' => null])
            </div>
            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-[var(--color-border)]">
                <x-ui.button type="button" variant="ghost" @click="open = false">
                    {{ __('Cancel') }}
                </x-ui.button>
                <x-ui.button type="submit" variant="primary" icon="plus">
                    {{ __('Add Activity') }}
                </x-ui.button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const params = new URLSearchParams(window.location.search);
    if (params.get('openAdd') === '1') {
        window.dispatchEvent(new CustomEvent('open-modal', { detail: 'quick-add-activity' }));
        const sub = params.get('subject');
        if (sub) {
            // Small delay to let Alpine render the modal first
            setTimeout(() => {
                const sel = document.querySelector('[x-data] select[name="subject"]');
                if (sel) { sel.value = sub; sel.dispatchEvent(new Event('change')); }
            }, 50);
        }
    }
});
</script>
@endpush

@endsection
