@extends('layouts.student')

@section('title', 'Find a Teacher — Noble Nest Academy')
@section('meta_title', 'Find a Teacher | NobleNest Global Academy')
@section('meta_description', 'Browse live online courses from expert teachers on NobleNest Global Academy. Compare subjects, age fit, pricing, and enrolment options in one marketplace.')
@section('meta_image', asset('og-marketplace.png'))

@section('content')

{{-- ── Hero banner ── --}}
<x-ui.card variant="clay" padding="none" class="mb-6 overflow-hidden">
    <div class="px-6 py-8 bg-gradient-to-br from-[var(--color-brand-50)] to-[var(--color-accent-50)]">
        <div class="grid lg:grid-cols-[1fr_auto] gap-6 items-center">
            <div>
                <div class="text-xs font-black uppercase tracking-widest text-[var(--color-primary)] mb-2">Live learning marketplace</div>
                <h1 class="font-display font-black text-3xl text-[var(--color-text)] mb-2 flex items-center gap-2">
                    <x-ui.icon name="graduation-cap" class="w-8 h-8 text-[var(--color-primary)]" aria-hidden="true" />Find a Teacher
                </h1>
                <p class="text-[var(--color-text-muted)] text-base max-w-lg">Browse expert-led courses, compare age fit and price instantly, and move from discovery to enrolment without friction.</p>
            </div>
            <div class="grid grid-cols-2 gap-3 min-w-[240px]">
                <x-ui.stat label="Published" :value="$courses->total()" icon="book" />
                <x-ui.stat label="Subjects" :value="$subjects->count()" icon="layers" />
            </div>
        </div>
    </div>
</x-ui.card>

{{-- ── Flash messages ── --}}
@if(session('status'))
    <x-ui.alert tone="success" dismissible class="mb-4">{{ session('status') }}</x-ui.alert>
@endif
@if(session('error'))
    <x-ui.alert tone="danger" dismissible class="mb-4">{{ session('error') }}</x-ui.alert>
@endif

{{-- ── Filter bar ── --}}
<x-ui.card variant="clay" padding="md" class="mb-6">
    <form method="GET" novalidate>
        <div class="grid sm:grid-cols-2 lg:grid-cols-[1fr_auto_auto_auto_auto_auto] gap-3 items-end">
            <x-ui.field label="Search" name="q">
                <x-ui.input
                    type="text"
                    name="q"
                    :value="request('q')"
                    placeholder="Courses, subjects..."
                    icon="search"
                />
            </x-ui.field>

            <x-ui.field label="Subject" name="subject">
                <x-ui.select
                    name="subject"
                    :value="request('subject')"
                    placeholder="All Subjects"
                    :options="$subjects->mapWithKeys(fn($s) => [$s => $s])->toArray()"
                />
            </x-ui.field>

            <x-ui.field label="Level" name="level">
                <x-ui.select
                    name="level"
                    :value="request('level')"
                    placeholder="All Levels"
                    :options="['beginner' => 'Beginner', 'intermediate' => 'Intermediate', 'advanced' => 'Advanced']"
                />
            </x-ui.field>

            <x-ui.field label="Child age" name="age">
                <x-ui.input
                    type="number"
                    name="age"
                    :value="request('age')"
                    placeholder="Any age"
                    min="0"
                    max="18"
                />
            </x-ui.field>

            <x-ui.field label="Free only" name="free">
                <div class="flex items-center min-h-[2.5rem] gap-2">
                    <x-ui.checkbox name="free" value="1" :checked="(bool) request('free')" />
                    <label for="free" class="text-sm font-medium text-[var(--color-text)] cursor-pointer">Free</label>
                </div>
            </x-ui.field>

            <div class="flex items-end">
                <x-ui.button type="submit" variant="primary" icon="search" class="w-full min-h-[2.5rem]">
                    Search
                </x-ui.button>
            </div>
        </div>
    </form>
</x-ui.card>

{{-- ── Results grid ── --}}
@if($courses->isEmpty())
    <x-ui.empty-state
        icon="search"
        title="No courses found"
        description="Try a different search term, subject, or level."
    />
@else
<div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3 mb-6">
    @foreach($courses as $course)
    <x-ui.card variant="clay" padding="none" class="flex flex-col overflow-hidden">
        {{-- Thumbnail --}}
        @if($course->thumbnail)
            <img src="{{ \Illuminate\Support\Facades\Storage::url($course->thumbnail) }}"
                 class="w-full h-48 object-cover" alt="{{ $course->title }}"
                 loading="lazy" decoding="async">
        @else
            <div class="w-full h-48 flex items-center justify-center bg-gradient-to-br from-[var(--color-brand-600)] to-[var(--color-accent)]">
                <x-ui.icon name="book" class="w-12 h-12 text-white/70" aria-hidden="true" />
            </div>
        @endif

        <div class="flex flex-col flex-1 p-4">
            {{-- Subject + price --}}
            <div class="flex items-start justify-between gap-2 mb-2">
                @if($course->subject)
                    <x-ui.badge tone="brand" size="sm">{{ $course->subject }}</x-ui.badge>
                @endif
                @if($course->price > 0)
                    <span class="font-black text-emerald-600 text-sm shrink-0">${{ $course->price }}</span>
                @else
                    <x-ui.badge tone="success" size="sm">Free</x-ui.badge>
                @endif
            </div>

            <h2 class="font-semibold text-[var(--color-text)] leading-snug mb-1">{{ $course->title }}</h2>
            <p class="text-sm text-[var(--color-text-muted)] mb-3 flex-1 line-clamp-2">{{ Str::limit($course->description, 110) }}</p>

            {{-- Meta badges --}}
            <div class="flex flex-wrap gap-1.5 mb-3">
                @if($course->age_min || $course->age_max)
                    <x-ui.badge tone="neutral" size="sm">Ages {{ $course->age_min ?? '?' }}–{{ $course->age_max ?? '?' }}</x-ui.badge>
                @endif
                <x-ui.badge tone="neutral" size="sm">{{ ucfirst($course->level) }}</x-ui.badge>
                <x-ui.badge tone="neutral" size="sm">{{ $course->active_enrollments_count ?? 0 }} enrolled</x-ui.badge>
            </div>

            {{-- Teacher --}}
            <div class="flex items-center gap-2 mb-4 p-2 rounded-[var(--radius-sm)] bg-[var(--color-border)] border border-[var(--color-border)]">
                <img src="https://api.dicebear.com/7.x/bottts/svg?seed={{ $course->teacher_id }}"
                     class="w-8 h-8 rounded-full shrink-0" alt="Teacher avatar" loading="lazy">
                <div class="min-w-0">
                    <div class="text-sm font-semibold text-[var(--color-text)] truncate">{{ $course->teacher->name ?? 'Teacher' }}</div>
                    <div class="text-xs text-[var(--color-text-muted)]">Instructor</div>
                </div>
            </div>

            <x-ui.button variant="primary" href="{{ route('marketplace.show', $course->slug) }}" icon-right="arrow-right" class="w-full justify-center">
                View Course
            </x-ui.button>
        </div>
    </x-ui.card>
    @endforeach
</div>

<div class="flex justify-center mb-6">
    {{ $courses->links() }}
</div>
@endif

{{-- ── Teacher CTA ── --}}
<x-ui.card variant="clay" padding="lg" class="text-center bg-gradient-to-br from-[var(--color-brand-600)] to-[var(--color-brand-400)] border-[var(--color-brand-600)]">
    <h2 class="font-display font-black text-2xl text-white mb-2">Are you a teacher?</h2>
    <p class="text-white/85 mb-5 max-w-md mx-auto">Create your own courses and reach students worldwide. Set your own schedule and prices.</p>
    <x-ui.button variant="secondary" href="{{ route('register') }}?role=Teacher" icon="graduation-cap" size="lg">
        Register as Teacher
    </x-ui.button>
</x-ui.card>

@endsection
