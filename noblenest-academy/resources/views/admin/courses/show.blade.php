@extends('layouts.admin')

@section('title', $course->title)

@section('content')
@php
    $color       = $course->color ?: '#64B5F6';
    $emoji       = $course->emoji ?: '📘';
    $ageLabel    = ($course->age_min !== null && $course->age_max !== null)
        ? ($course->age_min === $course->age_max ? "Age {$course->age_min}" : "Ages {$course->age_min}–{$course->age_max}")
        : null;
    $totalActivities = $course->modules->sum(fn($m) => $m->activities->count());
@endphp

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    {{-- Header --}}
    <div class="flex items-start gap-4">
        <a href="{{ route('admin.courses.index') }}"
           class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-semibold border-2 border-gray-300 text-gray-700 hover:bg-gray-100 transition shrink-0">
            <x-ui.icon name="arrow-left" />
        </a>
        <div class="flex items-center gap-3 flex-1 min-w-0">
            <div class="w-14 h-14 rounded-xl flex items-center justify-center text-3xl shrink-0"
                 style="background:{{ $color }}22">{{ $emoji }}</div>
            <div class="min-w-0">
                <h1 class="text-xl font-bold text-gray-900 truncate">{{ $course->title }}</h1>
                <div class="flex flex-wrap items-center gap-2 mt-1">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700 border">{{ $course->slug }}</span>
                    @if($ageLabel)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                              style="background:{{ $color }}22;color:{{ $color }}">🎂 {{ $ageLabel }}</span>
                    @endif
                    <span class="text-sm text-gray-500">
                        <x-ui.icon name="folder" /> {{ $course->modules->count() }} {{ __('admin.courses.modules_count') }}
                    </span>
                    <span class="text-sm text-gray-500">
                        <x-ui.icon name="zap" /> {{ $totalActivities }} {{ __('admin.courses.activities_count') }}
                    </span>
                </div>
            </div>
        </div>
        <a href="{{ route('admin.courses.edit', $course) }}"
           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold border-2 border-violet-600 text-violet-600 hover:bg-violet-600 hover:text-white transition shrink-0">
            <x-ui.icon name="pencil" /> {{ __('admin.courses.edit_course') }}
        </a>
    </div>

    @if($course->description)
        <p class="text-gray-500">{{ $course->description }}</p>
    @endif

    {{-- Modules accordion (native <details>) --}}
    <div class="space-y-3">
        @forelse($course->modules as $mi => $module)
            <details class="group bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden" {{ $mi === 0 ? 'open' : '' }}>
                <summary class="flex items-center gap-3 px-5 py-4 cursor-pointer select-none hover:bg-gray-50 list-none [&::-webkit-details-marker]:hidden">
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-violet-100 text-violet-700 text-xs font-bold shrink-0">
                        {{ $mi + 1 }}
                    </span>
                    <span class="font-semibold text-gray-900 flex-1">{{ $module->title }}</span>
                    <span class="text-xs text-gray-500 mr-2">
                        {{ __('admin.courses.activities_lessons', ['activities' => $module->activities->count(), 'lessons' => $module->lessons->count()]) }}
                    </span>
                    <svg class="w-4 h-4 text-gray-400 transition-transform group-open:rotate-180 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </summary>

                <div class="border-t border-gray-100">
                    {{-- Activities table --}}
                    @if($module->activities->count())
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50 text-left">
                                    <tr>
                                        <th class="px-5 py-3 font-semibold text-gray-500 text-xs w-10">#</th>
                                        <th class="px-5 py-3 font-semibold text-gray-500 text-xs">{{ __('admin.courses.col_activity') }}</th>
                                        <th class="px-5 py-3 font-semibold text-gray-500 text-xs w-28">{{ __('admin.courses.col_type') }}</th>
                                        <th class="px-5 py-3 font-semibold text-gray-500 text-xs w-24">{{ __('admin.courses.col_difficulty') }}</th>
                                        <th class="px-5 py-3 font-semibold text-gray-500 text-xs w-20">{{ __('admin.courses.col_duration') }}</th>
                                        <th class="px-5 py-3 font-semibold text-gray-500 text-xs w-16">{{ __('admin.courses.col_free') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($module->activities as $ai => $activity)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-5 py-3 text-gray-400">{{ $ai + 1 }}</td>
                                            <td class="px-5 py-3">
                                                <div class="font-medium text-gray-900">{{ $activity->title }}</div>
                                                @if($activity->description)
                                                    <div class="text-xs text-gray-500 line-clamp-1">{{ $activity->description }}</div>
                                                @endif
                                            </td>
                                            <td class="px-5 py-3">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-sky-100 text-sky-800">
                                                    {{ $activity->activity_type }}
                                                </span>
                                            </td>
                                            <td class="px-5 py-3">
                                                @php
                                                    $diffBadge = match($activity->difficulty) {
                                                        'easy'   => 'bg-emerald-100 text-emerald-800',
                                                        'medium' => 'bg-amber-100 text-amber-800',
                                                        'hard'   => 'bg-red-100 text-red-800',
                                                        default  => 'bg-gray-100 text-gray-700',
                                                    };
                                                @endphp
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $diffBadge }}">
                                                    {{ ucfirst($activity->difficulty ?? '—') }}
                                                </span>
                                            </td>
                                            <td class="px-5 py-3 text-gray-500">
                                                {{ $activity->duration_minutes ? $activity->duration_minutes . 'min' : '—' }}
                                            </td>
                                            <td class="px-5 py-3">
                                                @if($activity->is_free)
                                                    <x-ui.icon name="check-circle" class="text-emerald-600" />
                                                @else
                                                    <x-ui.icon name="lock" class="text-gray-400" />
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-gray-400 py-8">
                            <x-ui.icon name="inbox" class="text-3xl block mb-1 mx-auto" />
                            <p class="text-sm">{{ __('admin.courses.no_activities') }}</p>
                        </div>
                    @endif

                    {{-- Lessons --}}
                    @if($module->lessons->count())
                        <div class="border-t border-gray-100 px-5 py-4">
                            <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-1.5">
                                <x-ui.icon name="book" /> {{ __('admin.courses.lessons') }}
                            </h3>
                            <div class="divide-y divide-gray-100 rounded-lg border border-gray-200 overflow-hidden">
                                @foreach($module->lessons as $lesson)
                                    <div class="flex items-center gap-3 px-4 py-3 bg-white">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700 shrink-0">
                                            {{ $lesson->order }}
                                        </span>
                                        <span class="font-medium text-sm text-gray-900 flex-1">{{ $lesson->title }}</span>
                                        @if($lesson->duration)
                                            <span class="text-xs text-gray-500">{{ $lesson->duration }}min</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </details>
        @empty
            <div class="text-center text-gray-400 py-12">
                <x-ui.icon name="folder-open" class="text-5xl block mb-2 mx-auto" />
                <p class="text-sm">{{ __('admin.courses.no_modules') }}</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
