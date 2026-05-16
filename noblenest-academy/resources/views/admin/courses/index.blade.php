@extends('layouts.admin')

@section('title', __('Courses'))

@section('content')
<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">

    <x-ui.page-header
        :title="__('Courses')"
        :subtitle="$courses->total() . ' ' . __('course(s) across all age groups')"
        :breadcrumbs="[
            ['label' => __('Admin'), 'url' => route('admin.analytics.index')],
            ['label' => __('Courses')],
        ]"
    >
        <x-slot:actions>
            <x-ui.button variant="primary" icon="plus" href="{{ route('admin.courses.create') }}">
                {{ __('New Course') }}
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    {{-- Flash messages --}}
    @if(session('status'))
        <x-ui.alert tone="success" dismissible class="mb-6">
            {{ session('status') }}
        </x-ui.alert>
    @endif

    {{-- Course grid --}}
    @if($courses->isNotEmpty())
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @foreach($courses as $course)
                @php
                    $color       = $course->color ?: '#64B5F6';
                    $emoji       = $course->emoji ?: '📘';
                    $ageLabel    = ($course->age_min !== null && $course->age_max !== null)
                        ? ($course->age_min === $course->age_max
                            ? __('Age :n', ['n' => $course->age_min])
                            : __('Ages :min–:max', ['min' => $course->age_min, 'max' => $course->age_max]))
                        : null;
                    $moduleCount   = $course->modules()->count();
                    $activityCount = $course->modules()->withCount('activities')->get()->sum('activities_count');
                @endphp
                <x-ui.card variant="outlined" padding="none" class="flex flex-col overflow-hidden">
                    {{-- Colour accent bar --}}
                    <div class="h-1.5 w-full" style="background:{{ $color }}" aria-hidden="true"></div>

                    <div class="p-4 flex flex-col flex-1">
                        {{-- Emoji + title --}}
                        <div class="flex items-start gap-3 mb-3">
                            <div
                                class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl shrink-0"
                                style="background:{{ $color }}22"
                                aria-hidden="true"
                            >{{ $emoji }}</div>
                            <div class="min-w-0 flex-1">
                                <h3 class="font-bold text-[var(--color-text)] leading-snug">
                                    <a
                                        href="{{ route('admin.courses.show', $course) }}"
                                        class="hover:text-[var(--color-primary)] focus-visible:outline-2 focus-visible:outline-[var(--color-brand-600)] focus-visible:outline-offset-2 rounded"
                                    >{{ $course->title }}</a>
                                </h3>
                                <x-ui.badge tone="neutral" size="sm" class="mt-1">{{ $course->slug }}</x-ui.badge>
                            </div>
                        </div>

                        @if($ageLabel)
                            <x-ui.badge tone="brand" size="sm" class="mb-2 self-start">🎂 {{ $ageLabel }}</x-ui.badge>
                        @endif

                        @if($course->description)
                            <p class="text-sm text-[var(--color-text-muted)] mb-3 flex-1 line-clamp-3 leading-relaxed">
                                {{ $course->description }}
                            </p>
                        @else
                            <div class="flex-1"></div>
                        @endif

                        <div class="flex gap-3 text-xs text-[var(--color-text-muted)] mb-3">
                            <span class="flex items-center gap-1">
                                <x-ui.icon name="book" class="w-3.5 h-3.5" />
                                {{ $moduleCount }} {{ __('module(s)') }}
                            </span>
                            <span class="flex items-center gap-1">
                                <x-ui.icon name="star" class="w-3.5 h-3.5" />
                                {{ $activityCount }} {{ __('activity(s)') }}
                            </span>
                        </div>

                        {{-- Actions --}}
                        <div class="flex gap-2">
                            <x-ui.button
                                variant="secondary"
                                size="sm"
                                icon="eye"
                                href="{{ route('admin.courses.show', $course) }}"
                                class="flex-1"
                            >
                                {{ __('View') }}
                            </x-ui.button>
                            <x-ui.button
                                variant="ghost"
                                size="sm"
                                icon="edit"
                                href="{{ route('admin.courses.edit', $course) }}"
                                :title="__('Edit')"
                            ></x-ui.button>
                            <form
                                method="POST"
                                action="{{ route('admin.courses.destroy', $course) }}"
                                onsubmit="return confirm('{{ __('Delete this course and all its modules?') }}')"
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
                </x-ui.card>
            @endforeach
        </div>
    @else
        <x-ui.empty-state
            icon="book"
            :title="__('No courses yet')"
            :description="__('Create the first course to get started.')"
        >
            <x-slot:actions>
                <x-ui.button variant="primary" icon="plus" href="{{ route('admin.courses.create') }}">
                    {{ __('Create Course') }}
                </x-ui.button>
            </x-slot:actions>
        </x-ui.empty-state>
    @endif

    {{-- Pagination --}}
    @if($courses->hasPages())
        <div class="mt-6 flex justify-end">
            {{ $courses->links() }}
        </div>
    @endif

</div>
@endsection
