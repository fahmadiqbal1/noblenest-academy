@extends('layouts.student')

@section('title', $course->title . ' — Noble Nest Academy')

@section('content')

{{-- ── Back link ── --}}
<x-ui.button variant="ghost" href="{{ route('marketplace.index') }}" icon="arrow-left" size="sm" class="mb-5">
    Back to Marketplace
</x-ui.button>

{{-- ── Flash ── --}}
@if(session('status'))
    <x-ui.alert tone="success" dismissible class="mb-4">{{ session('status') }}</x-ui.alert>
@endif
@if(session('error'))
    <x-ui.alert tone="danger" dismissible class="mb-4">{{ session('error') }}</x-ui.alert>
@endif

<div class="grid lg:grid-cols-[1fr_340px] gap-6 items-start">

    {{-- ─── Left: course info ─── --}}
    <div class="space-y-5">

        {{-- Hero --}}
        <x-ui.card variant="clay" padding="md">
            @if($course->thumbnail)
                <img src="{{ \Illuminate\Support\Facades\Storage::url($course->thumbnail) }}"
                     class="w-full max-h-80 object-cover rounded-[var(--radius-sm)] mb-4"
                     alt="{{ $course->title }}" loading="lazy" decoding="async">
            @else
                <div class="w-full h-52 rounded-[var(--radius-sm)] mb-4 flex items-center justify-center bg-gradient-to-br from-[var(--color-brand-600)] to-[var(--color-accent)]">
                    <x-ui.icon name="book" class="w-16 h-16 text-white/70" aria-hidden="true" />
                </div>
            @endif

            <h1 class="font-display font-black text-2xl text-[var(--color-text)] mb-3">{{ $course->title }}</h1>

            <div class="flex flex-wrap gap-2 mb-4">
                @if($course->subject)
                    <x-ui.badge tone="brand">{{ $course->subject }}</x-ui.badge>
                @endif
                <x-ui.badge tone="neutral">{{ ucfirst($course->level) }}</x-ui.badge>
                @if($course->age_min || $course->age_max)
                    <x-ui.badge tone="neutral">Ages {{ $course->age_min ?? '?' }}–{{ $course->age_max ?? '?' }}</x-ui.badge>
                @endif
                <x-ui.badge tone="neutral">{{ strtoupper($course->language) }}</x-ui.badge>
            </div>

            <p class="text-[var(--color-text-muted)] leading-relaxed">{{ $course->description }}</p>
        </x-ui.card>

        {{-- What you'll learn --}}
        @if($course->what_you_learn)
        <x-ui.card variant="clay" padding="md">
            <h2 class="font-bold text-[var(--color-text)] flex items-center gap-2 mb-4">
                <x-ui.icon name="check-circle" class="w-5 h-5 text-emerald-500" aria-hidden="true" />What You'll Learn
            </h2>
            <ul class="space-y-2">
                @foreach(explode("\n", $course->what_you_learn) as $line)
                    @if(trim($line))
                    <li class="flex items-start gap-2 text-[var(--color-text)]">
                        <x-ui.icon name="check" class="w-4 h-4 text-emerald-500 mt-0.5 shrink-0" aria-hidden="true" />
                        {{ trim($line) }}
                    </li>
                    @endif
                @endforeach
            </ul>
        </x-ui.card>
        @endif

        {{-- Curriculum accordion --}}
        @if($course->sections->isNotEmpty())
        <x-ui.card variant="clay" padding="none" class="overflow-hidden">
            <div class="px-4 py-3 border-b border-[var(--color-border)]">
                <h2 class="font-bold text-[var(--color-text)] flex items-center gap-2">
                    <x-ui.icon name="list" class="w-5 h-5 text-[var(--color-primary)]" aria-hidden="true" />Curriculum
                </h2>
            </div>
            <div x-data="{ open: null }">
                @foreach($course->sections as $sec)
                <div class="border-b border-[var(--color-border)] last:border-0">
                    <button
                        type="button"
                        class="w-full flex items-center justify-between gap-4 px-4 py-3 text-start font-semibold text-[var(--color-text)] hover:bg-[var(--color-border)] transition-colors focus-visible:outline-2 focus-visible:outline-[var(--color-brand-600)] focus-visible:outline-offset-[-2px]"
                        @click="open = open === {{ $loop->index }} ? null : {{ $loop->index }}"
                        :aria-expanded="open === {{ $loop->index }}"
                    >
                        <span><span class="font-black text-[var(--color-primary)] me-2">{{ $loop->iteration }}.</span>{{ $sec->title }}</span>
                        <x-ui.icon name="chevron-down" class="w-4 h-4 shrink-0 transition-transform" x-bind:class="open === {{ $loop->index }} ? 'rotate-180' : ''" aria-hidden="true" />
                    </button>
                    @if($sec->description)
                    <div x-show="open === {{ $loop->index }}" x-collapse class="px-5 pb-3 text-sm text-[var(--color-text-muted)]">
                        {{ $sec->description }}
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        </x-ui.card>
        @endif

        {{-- Upcoming sessions --}}
        @if($course->classSessions->isNotEmpty())
        <x-ui.card variant="clay" padding="md">
            <h2 class="font-bold text-[var(--color-text)] flex items-center gap-2 mb-4">
                <x-ui.icon name="calendar" class="w-5 h-5 text-[var(--color-primary)]" aria-hidden="true" />Upcoming Live Sessions
            </h2>
            <div class="space-y-3">
                @foreach($course->classSessions as $session)
                <div class="flex items-center justify-between gap-3 flex-wrap rounded-[var(--radius-sm)] border-[2px] border-[var(--color-border)] bg-[var(--color-surface)] p-3">
                    <div>
                        <div class="font-semibold text-[var(--color-text)]">{{ $session->title }}</div>
                        <div class="text-sm text-[var(--color-text-muted)] flex items-center gap-1 mt-0.5">
                            <x-ui.icon name="clock" class="w-3.5 h-3.5" aria-hidden="true" />
                            {{ $session->starts_at->format('D, M j, Y · g:i A') }} &middot; {{ $session->duration_minutes }} min
                        </div>
                    </div>
                    @if($enrollment && $enrollment->isActive())
                        @if($session->status === 'live')
                            <x-ui.button variant="danger" href="{{ route('classroom.room', $session->room_id) }}" icon="play" size="sm">
                                Join Now
                            </x-ui.button>
                        @else
                            <x-ui.badge tone="brand">Scheduled</x-ui.badge>
                        @endif
                    @endif
                </div>
                @endforeach
            </div>
        </x-ui.card>
        @endif

    </div>{{-- /left --}}

    {{-- ─── Right: enrol card (sticky) ─── --}}
    <aside class="lg:sticky lg:top-20">
        <x-ui.card variant="clay" padding="md">

            {{-- Price --}}
            <div class="text-center mb-4">
                @if($course->price > 0)
                    <div class="font-black text-4xl text-emerald-600">${{ $course->price }}</div>
                    <div class="text-sm text-[var(--color-text-muted)]">one-time payment</div>
                @else
                    <div class="font-black text-4xl text-emerald-600">Free</div>
                @endif
            </div>

            {{-- Teacher info --}}
            <div class="flex items-center gap-3 rounded-[var(--radius-sm)] border-[2px] border-[var(--color-border)] p-3 mb-4">
                <img src="https://api.dicebear.com/7.x/bottts/svg?seed={{ $course->teacher_id }}"
                     class="w-12 h-12 rounded-full shrink-0" alt="Teacher avatar" loading="lazy">
                <div class="min-w-0">
                    <div class="font-semibold text-[var(--color-text)] truncate">{{ $course->teacher->name ?? 'Teacher' }}</div>
                    <div class="text-xs text-[var(--color-text-muted)]">Course Instructor</div>
                </div>
            </div>

            @if($course->syllabus_file)
                <x-ui.button variant="secondary" href="{{ \Illuminate\Support\Facades\Storage::url($course->syllabus_file) }}" target="_blank" icon="download" class="w-full justify-center mb-3">
                    Download Syllabus
                </x-ui.button>
            @endif

            {{-- Enrolment state --}}
            @if($enrollment && $enrollment->isActive())
                <x-ui.alert tone="success" class="mb-3">
                    <x-ui.icon name="check-circle" class="w-4 h-4" aria-hidden="true" />You are enrolled!
                </x-ui.alert>
                @if(auth()->user()->role === 'Student')
                    <x-ui.button variant="primary" href="{{ route('student.my-courses') }}" icon="book-open" class="w-full justify-center">
                        Go to My Courses
                    </x-ui.button>
                @endif
            @elseif(auth()->check())
                @if(auth()->user()->role === 'Student')
                    <x-ui.button variant="primary" href="{{ route('student.enroll.checkout', $course->slug) }}" icon="person-add" size="lg" class="w-full justify-center">
                        {{ $course->price > 0 ? 'Enrol &amp; Pay' : 'Enrol for Free' }}
                    </x-ui.button>
                @else
                    <p class="text-sm text-[var(--color-text-muted)] text-center">Log in as a student to enrol.</p>
                @endif
            @else
                <x-ui.button variant="primary" href="{{ route('register') }}" icon="person-add" size="lg" class="w-full justify-center mb-2">
                    Register to Enrol
                </x-ui.button>
                <x-ui.button variant="secondary" href="{{ route('login') }}" class="w-full justify-center">
                    Already have an account? Log in
                </x-ui.button>
            @endif

            {{-- Feature list --}}
            <ul class="mt-4 space-y-2 text-sm text-[var(--color-text-muted)]">
                <li class="flex items-center gap-2">
                    <x-ui.icon name="play" class="w-4 h-4 text-[var(--color-primary)]" aria-hidden="true" />Live online classes
                </li>
                <li class="flex items-center gap-2">
                    <x-ui.icon name="users" class="w-4 h-4 text-[var(--color-primary)]" aria-hidden="true" />Small group sessions
                </li>
                @if($course->syllabus_file)
                <li class="flex items-center gap-2">
                    <x-ui.icon name="download" class="w-4 h-4 text-[var(--color-primary)]" aria-hidden="true" />Curriculum included
                </li>
                @endif
                @if($course->max_students)
                <li class="flex items-center gap-2">
                    <x-ui.icon name="users" class="w-4 h-4 text-amber-500" aria-hidden="true" />Max {{ $course->max_students }} students
                </li>
                @endif
            </ul>

        </x-ui.card>
    </aside>

</div>
@endsection
