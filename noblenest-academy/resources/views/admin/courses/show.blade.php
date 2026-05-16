@extends('layouts.admin')

@section('title', $course->title)

@section('content')
@php
    $color = $course->color ?: '#64B5F6';
    $emoji = $course->emoji ?: '📘';
    $ageLabel = ($course->age_min !== null && $course->age_max !== null)
        ? ($course->age_min === $course->age_max ? "Age {$course->age_min}" : "Ages {$course->age_min}–{$course->age_max}")
        : null;
    $totalActivities = $course->modules->sum(fn($m) => $m->activities->count());
@endphp

<div class="w-full px-4 py-4">

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-4">
        <a href="{{ route('admin.courses.index') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed border-2 border-gray-300 text-gray-700 hover:bg-gray-100 px-3 py-1.5 text-sm">
            <x-ui.icon name="arrow-left" />
        </a>
        <div class="rounded-lg flex items-center justify-center flex-shrink-0"
             style="width:56px;height:56px;background:{{ $color }}22;font-size:1.8rem">
            {{ $emoji }}
        </div>
        <div>
            <h2 class="font-bold mb-0">{{ $course->title }}</h2>
            <div class="flex gap-2 items-center mt-1">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700 border text-sm">{{ $course->slug }}</span>
                @if($ageLabel)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium" style="background:{{ $color }}22;color:{{ $color }}">🎂 {{ $ageLabel }}</span>
                @endif
                <span class="text-[var(--color-text-muted)] text-sm"><x-ui.icon name="folder" /> {{ $course->modules->count() }} modules</span>
                <span class="text-[var(--color-text-muted)] text-sm"><x-ui.icon name="zap" /> {{ $totalActivities }} activities</span>
            </div>
        </div>
        <div class="ms-auto flex gap-2">
            <a href="{{ route('admin.courses.edit', $course) }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed border-2 border-violet-600 text-violet-600 hover:bg-violet-600 hover:text-white px-3 py-1.5 text-sm">
                <x-ui.icon name="pencil" class="me-1" />Edit Course
            </a>
        </div>
    </div>

    @if($course->description)
        <p class="text-[var(--color-text-muted)] mb-4">{{ $course->description }}</p>
    @endif

    {{-- Modules accordion --}}
    <div class="flex flex-col gap-2" id="modulesAccordion">
        @forelse($course->modules as $mi => $module)
            @php $collapseId = "module-{$module->id}"; @endphp
            <div class="border border-gray-200 rounded-lg overflow-hidden bg-white border-0 shadow-sm mb-3">
                <h2 class="">
                    <button class="w-full text-left px-4 py-3 flex items-center justify-between font-medium hover:bg-gray-50 {{ $mi > 0 ? 'collapsed' : '' }} font-semibold"
                            type="button">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-violet-50 text-violet-700 me-2">{{ $mi + 1 }}</span>
                        {{ $module->title }}
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-50 text-gray-900 ms-auto me-3">
                            {{ $module->activities->count() }} activities &middot; {{ $module->lessons->count() }} lessons
                        </span>
                    </button>
                </h2>
                <div id="{{ $collapseId }}" class="{{ $mi === 0 ? 'show' : '' }}"
>
                    <div class="px-4 py-3 p-0">
                        {{-- Activities table --}}
                        @if($module->activities->count())
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm border-collapse table-hover-tw align-middle mb-0">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th style="width:40px">#</th>
                                            <th>Activity</th>
                                            <th style="width:100px">Type</th>
                                            <th style="width:80px">Difficulty</th>
                                            <th style="width:80px">Duration</th>
                                            <th style="width:60px">Free?</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($module->activities as $ai => $activity)
                                            <tr>
                                                <td class="text-[var(--color-text-muted)]">{{ $ai + 1 }}</td>
                                                <td>
                                                    <div class="font-medium">{{ $activity->title }}</div>
                                                    @if($activity->description)
                                                        <small class="text-[var(--color-text-muted)]" style="display:-webkit-box;-webkit-line-clamp:1;-webkit-box-orient:vertical;overflow:hidden">
                                                            {{ $activity->description }}
                                                        </small>
                                                    @endif
                                                </td>
                                                <td><span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-sky-50 text-sky-800">{{ $activity->activity_type }}</span></td>
                                                <td>
                                                    @php
                                                        $diffBadge = match($activity->difficulty) {
                                                            'easy'   => 'bg-emerald-50 text-emerald-800',
                                                            'medium' => 'bg-amber-50 text-amber-800',
                                                            'hard'   => 'bg-red-50 text-red-800',
                                                            default  => 'bg-gray-50 text-gray-800',
                                                        };
                                                    @endphp
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $diffBadge }}">{{ ucfirst($activity->difficulty ?? '-') }}</span>
                                                </td>
                                                <td class="text-[var(--color-text-muted)]">{{ $activity->duration_minutes ? $activity->duration_minutes . 'min' : '-' }}</td>
                                                <td>{!! $activity->is_free ? '<x-ui.icon name="check-circle" class="text-emerald-600" />' : '<x-ui.icon name="lock" class="text-[var(--color-text-muted)]" />' !!}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center text-[var(--color-text-muted)] py-4">
                                <x-ui.icon name="inbox" class="text-3xl block mb-1" />
                                No activities in this module yet.
                            </div>
                        @endif

                        {{-- Lessons --}}
                        @if($module->lessons->count())
                            <div class="border-t px-3 py-3">
                                <h6 class="font-semibold mb-2"><x-ui.icon name="book" class="me-1" />Lessons</h6>
                                <div class="divide-y divide-gray-200 border border-gray-200 rounded-lg bg-white rounded-none border-0">
                                    @foreach($module->lessons as $lesson)
                                        <div class="px-4 py-3 px-0 border-0">
                                            <div class="flex items-center gap-2">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">{{ $lesson->order }}</span>
                                                <span class="font-medium">{{ $lesson->title }}</span>
                                                @if($lesson->duration)
                                                    <span class="text-[var(--color-text-muted)] text-sm ms-auto">{{ $lesson->duration }}min</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center text-[var(--color-text-muted)] py-5">
                <x-ui.icon name="folder-open" class="text-5xl block mb-2" />
                No modules in this course yet.
            </div>
        @endforelse
    </div>
</div>
@endsection
