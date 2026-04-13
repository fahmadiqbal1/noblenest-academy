@extends('layouts.app')

@section('title', 'Courses')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0">{{ I18n::get('courses') }}</h2>
            <small class="text-muted">{{ $courses->total() }} course(s) across all age groups</small>
        </div>
        <a class="btn btn-primary" href="{{ route('admin.courses.create') }}">
            <i class="bi bi-plus-lg me-1"></i> {{ I18n::get('new_course') }}
        </a>
    </div>

    @if(session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-3">
        @forelse($courses as $course)
            @php
                $color  = $course->color ?: '#64B5F6';
                $emoji  = $course->emoji ?: '📘';
                $ageLabel = ($course->age_min !== null && $course->age_max !== null)
                    ? ($course->age_min === $course->age_max
                        ? "Age {$course->age_min}"
                        : "Ages {$course->age_min}–{$course->age_max}")
                    : null;
                $moduleCount   = $course->modules()->count();
                $activityCount = $course->modules()->withCount('activities')->get()->sum('activities_count');
            @endphp
            <div class="col-sm-6 col-lg-4 col-xl-3">
                <div class="card h-100 border-0 shadow-sm overflow-hidden">
                    {{-- Coloured top bar --}}
                    <div style="height:6px;background:{{ $color }}"></div>
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex align-items-start gap-3 mb-3">
                            <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                                 style="width:52px;height:52px;background:{{ $color }}22;font-size:1.6rem">
                                {{ $emoji }}
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                <h6 class="fw-bold mb-0 lh-sm"><a href="{{ route('admin.courses.show', $course) }}" class="text-decoration-none text-body">{{ $course->title }}</a></h6>
                                <span class="badge bg-secondary-subtle text-secondary-emphasis border small mt-1">{{ $course->slug }}</span>
                            </div>
                        </div>

                        @if($ageLabel)
                            <span class="badge rounded-pill mb-2 align-self-start"
                                  style="background:{{ $color }}22;color:{{ $color }};font-size:.8rem">
                                🎂 {{ $ageLabel }}
                            </span>
                        @endif

                        @if($course->description)
                            <p class="text-muted small mb-3 flex-grow-1" style="display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden">
                                {{ $course->description }}
                            </p>
                        @else
                            <div class="flex-grow-1"></div>
                        @endif

                        <div class="d-flex gap-2 text-muted small mb-3">
                            <span><i class="bi bi-collection"></i> {{ $moduleCount }} module(s)</span>
                            <span><i class="bi bi-lightning"></i> {{ $activityCount }} activity(s)</span>
                        </div>

                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.courses.show', $course) }}"
                               class="btn btn-sm btn-outline-success flex-fill">
                                <i class="bi bi-eye"></i> View
                            </a>
                            <a href="{{ route('admin.courses.edit', $course) }}"
                               class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.courses.destroy', $course) }}" method="POST"
                                  onsubmit="return confirm('Delete this course and all its modules?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center text-muted py-5">
                <i class="bi bi-collection fs-1 d-block mb-2"></i>
                No courses yet. <a href="{{ route('admin.courses.create') }}">Create the first one.</a>
            </div>
        @endforelse
    </div>

    @if($courses->hasPages())
        <div class="d-flex justify-content-end mt-4">
            {{ $courses->links() }}
        </div>
    @endif

</div>
@endsection
