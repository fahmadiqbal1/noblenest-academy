@extends('layouts.student')

@section('title', 'My Courses — Noble Nest Academy')

@section('content')

{{-- ── Page header ── --}}
<x-ui.card variant="clay" padding="md" class="mb-6 bg-gradient-to-br from-[var(--color-brand-50)] to-[var(--color-accent-50)]">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <div class="text-xs font-black uppercase tracking-widest text-[var(--color-primary)] mb-1">Student dashboard</div>
            <h1 class="font-display font-black text-2xl text-[var(--color-text)] flex items-center gap-2">
                <x-ui.icon name="book-open" class="w-7 h-7 text-[var(--color-primary)]" aria-hidden="true" />My Courses
            </h1>
            <p class="text-[var(--color-text-muted)] mt-1">Track your learning path and jump back into sessions.</p>
        </div>
        <x-ui.button variant="secondary" href="{{ route('marketplace.index') }}" icon="search">
            Find More Courses
        </x-ui.button>
    </div>
</x-ui.card>

{{-- ── Flash ── --}}
@if(session('status'))
    <x-ui.alert tone="success" dismissible class="mb-4">{{ session('status') }}</x-ui.alert>
@endif

{{-- ── Enrolled courses ── --}}
@forelse($enrollments as $enrollment)
@php $course = $enrollment->course; @endphp
<x-ui.card variant="clay" padding="md" class="mb-4">
    <div class="flex gap-4 items-start">
        {{-- Thumbnail --}}
        <div class="shrink-0">
            @if($course->thumbnail)
                <img src="{{ \Illuminate\Support\Facades\Storage::url($course->thumbnail) }}"
                     class="w-20 h-20 rounded-[var(--radius-sm)] object-cover"
                     alt="{{ $course->title }}" loading="lazy" decoding="async">
            @else
                <div class="w-20 h-20 rounded-[var(--radius-sm)] flex items-center justify-center bg-gradient-to-br from-[var(--color-brand-600)] to-[var(--color-accent)]">
                    <x-ui.icon name="book" class="w-8 h-8 text-white/70" aria-hidden="true" />
                </div>
            @endif
        </div>

        {{-- Course info --}}
        <div class="flex-1 min-w-0">
            <h2 class="font-bold text-[var(--color-text)] leading-snug mb-0.5">{{ $course->title }}</h2>
            <div class="text-sm text-[var(--color-text-muted)] mb-2 flex flex-wrap gap-x-3 gap-y-0.5">
                <span class="flex items-center gap-1">
                    <x-ui.icon name="user" class="w-3.5 h-3.5" aria-hidden="true" />{{ $course->teacher->name ?? 'Teacher' }}
                </span>
                @if($course->subject)
                    <span>{{ $course->subject }}</span>
                @endif
                <span class="flex items-center gap-1">
                    <x-ui.icon name="calendar" class="w-3.5 h-3.5" aria-hidden="true" />
                    Enrolled {{ $enrollment->enrolled_at?->diffForHumans() ?? 'recently' }}
                </span>
            </div>

            {{-- Upcoming sessions --}}
            @php $upcomingSessions = $course->classSessions->where('status', 'scheduled'); @endphp
            @if($upcomingSessions->isNotEmpty())
            <div class="space-y-2 mt-2">
                <div class="text-xs font-semibold text-[var(--color-text-muted)]">Next sessions:</div>
                @foreach($upcomingSessions->take(2) as $session)
                <div class="flex items-center justify-between gap-3 flex-wrap rounded-[var(--radius-sm)] border-[2px] border-[var(--color-border)] bg-[var(--color-surface)] px-3 py-2">
                    <div class="text-sm text-[var(--color-text)] flex items-center gap-1.5">
                        <x-ui.icon name="calendar" class="w-3.5 h-3.5 text-[var(--color-primary)]" aria-hidden="true" />
                        {{ $session->title }} — {{ $session->starts_at->format('M j, g:i A') }}
                    </div>
                    @if($session->status === 'live')
                        <x-ui.button variant="danger" href="{{ route('classroom.room', $session->room_id) }}" icon="play" size="sm">
                            Join Live
                        </x-ui.button>
                    @endif
                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Actions --}}
        <div class="flex flex-col gap-2 shrink-0">
            <x-ui.button variant="secondary" href="{{ route('marketplace.show', $course->slug) }}" icon="info" size="sm">
                Details
            </x-ui.button>
            @if($course->syllabus_file)
                <x-ui.button variant="ghost" href="{{ \Illuminate\Support\Facades\Storage::url($course->syllabus_file) }}" target="_blank" icon="download" size="sm">
                    Syllabus
                </x-ui.button>
            @endif
        </div>
    </div>
</x-ui.card>

@empty
<x-ui.empty-state
    icon="book-open"
    title="No courses yet"
    description="You haven't enrolled in any courses. Browse the marketplace to get started."
>
    <x-slot name="actions">
        <x-ui.button variant="primary" href="{{ route('marketplace.index') }}" icon="search">
            Browse Courses
        </x-ui.button>
    </x-slot>
</x-ui.empty-state>
@endforelse

@endsection
