@extends('layouts.app')

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

<div class="container-fluid py-4">

    {{-- Header --}}
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('admin.courses.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
             style="width:56px;height:56px;background:{{ $color }}22;font-size:1.8rem">
            {{ $emoji }}
        </div>
        <div>
            <h2 class="fw-bold mb-0">{{ $course->title }}</h2>
            <div class="d-flex gap-2 align-items-center mt-1">
                <span class="badge bg-secondary-subtle text-secondary-emphasis border small">{{ $course->slug }}</span>
                @if($ageLabel)
                    <span class="badge rounded-pill" style="background:{{ $color }}22;color:{{ $color }}">🎂 {{ $ageLabel }}</span>
                @endif
                <span class="text-muted small"><i class="bi bi-collection"></i> {{ $course->modules->count() }} modules</span>
                <span class="text-muted small"><i class="bi bi-lightning"></i> {{ $totalActivities }} activities</span>
            </div>
        </div>
        <div class="ms-auto d-flex gap-2">
            <a href="{{ route('admin.courses.edit', $course) }}" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-pencil me-1"></i>Edit Course
            </a>
        </div>
    </div>

    @if($course->description)
        <p class="text-muted mb-4">{{ $course->description }}</p>
    @endif

    {{-- Modules accordion --}}
    <div class="accordion" id="modulesAccordion">
        @forelse($course->modules as $mi => $module)
            @php $collapseId = "module-{$module->id}"; @endphp
            <div class="accordion-item border-0 shadow-sm mb-3 rounded-3 overflow-hidden">
                <h2 class="accordion-header">
                    <button class="accordion-button {{ $mi > 0 ? 'collapsed' : '' }} fw-semibold"
                            type="button" data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}">
                        <span class="badge bg-primary-subtle text-primary-emphasis me-2">{{ $mi + 1 }}</span>
                        {{ $module->title }}
                        <span class="badge bg-light text-dark ms-auto me-3">
                            {{ $module->activities->count() }} activities &middot; {{ $module->lessons->count() }} lessons
                        </span>
                    </button>
                </h2>
                <div id="{{ $collapseId }}" class="accordion-collapse collapse {{ $mi === 0 ? 'show' : '' }}"
                     data-bs-parent="#modulesAccordion">
                    <div class="accordion-body p-0">
                        {{-- Activities table --}}
                        @if($module->activities->count())
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
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
                                                <td class="text-muted">{{ $ai + 1 }}</td>
                                                <td>
                                                    <div class="fw-medium">{{ $activity->title }}</div>
                                                    @if($activity->description)
                                                        <small class="text-muted" style="display:-webkit-box;-webkit-line-clamp:1;-webkit-box-orient:vertical;overflow:hidden">
                                                            {{ $activity->description }}
                                                        </small>
                                                    @endif
                                                </td>
                                                <td><span class="badge bg-info-subtle text-info-emphasis">{{ $activity->activity_type }}</span></td>
                                                <td>
                                                    @php
                                                        $diffBadge = match($activity->difficulty) {
                                                            'easy' => 'success', 'medium' => 'warning', 'hard' => 'danger', default => 'secondary'
                                                        };
                                                    @endphp
                                                    <span class="badge bg-{{ $diffBadge }}-subtle text-{{ $diffBadge }}-emphasis">{{ ucfirst($activity->difficulty ?? '-') }}</span>
                                                </td>
                                                <td class="text-muted">{{ $activity->duration_minutes ? $activity->duration_minutes . 'min' : '-' }}</td>
                                                <td>{!! $activity->is_free ? '<i class="bi bi-check-circle-fill text-success"></i>' : '<i class="bi bi-lock-fill text-muted"></i>' !!}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-3 d-block mb-1"></i>
                                No activities in this module yet.
                            </div>
                        @endif

                        {{-- Lessons --}}
                        @if($module->lessons->count())
                            <div class="border-top px-3 py-3">
                                <h6 class="fw-semibold mb-2"><i class="bi bi-book me-1"></i>Lessons</h6>
                                <div class="list-group list-group-flush">
                                    @foreach($module->lessons as $lesson)
                                        <div class="list-group-item px-0 border-0">
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="badge bg-secondary-subtle text-secondary-emphasis">{{ $lesson->order }}</span>
                                                <span class="fw-medium">{{ $lesson->title }}</span>
                                                @if($lesson->duration)
                                                    <span class="text-muted small ms-auto">{{ $lesson->duration }}min</span>
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
            <div class="text-center text-muted py-5">
                <i class="bi bi-folder2-open fs-1 d-block mb-2"></i>
                No modules in this course yet.
            </div>
        @endforelse
    </div>
</div>
@endsection
