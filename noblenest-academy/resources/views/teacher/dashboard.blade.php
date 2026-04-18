@extends('layouts.teacher')

@section('title', __('Teacher Dashboard'))

@section('content')
<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">

    <x-ui.page-header
        :title="__('Teacher Dashboard')"
        :subtitle="__('Welcome back, :name', ['name' => $teacher->name])"
        :breadcrumbs="[['label' => __('Dashboard')]]"
    >
        <x-slot:actions>
            <x-ui.button variant="primary" icon="plus" href="{{ route('teacher.courses.create') }}">
                {{ __('New Course') }}
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    {{-- Flash message --}}
    @if(session('status'))
        <x-ui.alert tone="success" dismissible class="mb-6">
            {{ session('status') }}
        </x-ui.alert>
    @endif

    {{-- Stat cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <x-ui.stat
            :label="__('Courses')"
            :value="$courses->count()"
            icon="book"
        />
        <x-ui.stat
            :label="__('Students')"
            :value="$totalStudents"
            icon="users"
        />
        <x-ui.stat
            :label="__('Upcoming Sessions')"
            :value="$upcomingSessions->count()"
            icon="calendar"
        />
        <x-ui.stat
            :label="__('Total Earned')"
            :value="'$' . number_format($totalEarnings, 0)"
            icon="dollar-sign"
        />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- My Courses --}}
        <div class="lg:col-span-2">
            <x-ui.section>
                <x-slot:title>{{ __('My Courses') }}</x-slot:title>
                <x-slot:actions>
                    <x-ui.button variant="ghost" size="sm" href="{{ route('teacher.courses.index') }}">
                        {{ __('View all') }}
                        <x-ui.icon name="arrow-right" class="w-4 h-4" />
                    </x-ui.button>
                </x-slot:actions>

                @forelse($courses->take(5) as $course)
                    <x-ui.card variant="flat" padding="none" class="mb-2 last:mb-0">
                        <div class="flex items-center justify-between gap-4 px-4 py-3">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="font-semibold text-[var(--color-text)] truncate">{{ $course->title }}</span>
                                    <x-ui.badge :tone="$course->status === 'published' ? 'success' : 'neutral'" size="sm">
                                        {{ $course->status }}
                                    </x-ui.badge>
                                </div>
                                <p class="text-sm text-[var(--color-text-muted)] mt-0.5">
                                    <x-ui.icon name="users" class="w-3.5 h-3.5 inline -mt-0.5" />
                                    {{ $course->active_enrollments_count ?? 0 }} {{ __('students') }}
                                    &middot;
                                    <x-ui.icon name="calendar" class="w-3.5 h-3.5 inline -mt-0.5" />
                                    {{ $course->class_sessions_count ?? 0 }} {{ __('sessions') }}
                                    @if($course->price > 0)
                                        &middot; ${{ $course->price }}
                                    @else
                                        &middot; <span class="text-emerald-600">{{ __('Free') }}</span>
                                    @endif
                                </p>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <x-ui.button variant="ghost" size="sm" icon="eye" href="{{ route('teacher.courses.show', $course) }}" :title="__('View')">
                                </x-ui.button>
                                <x-ui.button variant="ghost" size="sm" icon="edit" href="{{ route('teacher.courses.edit', $course) }}" :title="__('Edit')">
                                </x-ui.button>
                            </div>
                        </div>
                    </x-ui.card>
                @empty
                    <x-ui.empty-state
                        icon="book"
                        :title="__('No courses yet')"
                        :description="__('Create your first course to start teaching.')"
                    >
                        <x-slot:actions>
                            <x-ui.button variant="primary" icon="plus" href="{{ route('teacher.courses.create') }}">
                                {{ __('Create Course') }}
                            </x-ui.button>
                        </x-slot:actions>
                    </x-ui.empty-state>
                @endforelse
            </x-ui.section>
        </div>

        {{-- Sidebar: Upcoming Sessions + Quick Actions --}}
        <div class="space-y-6">

            {{-- Upcoming Sessions --}}
            <x-ui.section>
                <x-slot:title>{{ __('Upcoming Sessions') }}</x-slot:title>

                @forelse($upcomingSessions as $session)
                    <x-ui.card variant="flat" padding="sm" class="mb-2 last:mb-0">
                        <p class="font-semibold text-sm text-[var(--color-text)]">{{ $session->title }}</p>
                        <p class="text-xs text-[var(--color-text-muted)] mt-0.5 flex items-center gap-1">
                            <x-ui.icon name="clock" class="w-3.5 h-3.5 shrink-0" />
                            {{ $session->starts_at->format('D, M j · g:i A') }}
                            &middot; {{ $session->duration_minutes }}{{ __('min') }}
                        </p>
                        <p class="text-xs text-[var(--color-text-muted)]">{{ $session->course->title }}</p>
                        <form method="POST" action="{{ route('teacher.sessions.start', $session) }}" class="mt-2">
                            @csrf
                            <x-ui.button type="submit" variant="primary" size="sm" icon="play">
                                {{ __('Start Class') }}
                            </x-ui.button>
                        </form>
                    </x-ui.card>
                @empty
                    <p class="text-sm text-[var(--color-text-muted)] text-center py-4">{{ __('No upcoming sessions.') }}</p>
                @endforelse
            </x-ui.section>

            {{-- Quick Actions --}}
            <x-ui.section>
                <x-slot:title>{{ __('Quick Actions') }}</x-slot:title>
                <div class="flex flex-col gap-2">
                    <x-ui.button variant="primary" icon="plus" href="{{ route('teacher.courses.create') }}" class="w-full justify-start">
                        {{ __('Create Course') }}
                    </x-ui.button>
                    <x-ui.button variant="secondary" icon="dollar-sign" href="#" class="w-full justify-start">
                        {{ __('Request Payout') }}
                    </x-ui.button>
                </div>
            </x-ui.section>

        </div>
    </div>

</div>
@endsection
