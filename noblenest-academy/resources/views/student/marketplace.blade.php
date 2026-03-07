@extends('layouts.app')

@section('content')
<div class="container py-4">
    {{-- Header --}}
    <div class="mb-4">
        <h1 class="fw-bold"><i class="bi bi-shop text-primary"></i> Find a Teacher</h1>
        <p class="text-muted">Browse courses taught by expert teachers. Enrol and learn online.</p>
    </div>

    @if(session('status'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('status') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    {{-- Filters --}}
    <form method="GET" class="card shadow-sm mb-4 p-3">
        <div class="row g-2 align-items-end">
            <div class="col-md-4">
                <input type="text" name="q" class="form-control" placeholder="Search courses, subjects..." value="{{ request('q') }}">
            </div>
            <div class="col-md-2">
                <select name="subject" class="form-select">
                    <option value="">All Subjects</option>
                    @foreach($subjects as $subj)
                        <option value="{{ $subj }}" {{ request('subject') === $subj ? 'selected' : '' }}>{{ $subj }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="level" class="form-select">
                    <option value="">All Levels</option>
                    @foreach(['beginner','intermediate','advanced'] as $lvl)
                        <option value="{{ $lvl }}" {{ request('level') === $lvl ? 'selected' : '' }}>{{ ucfirst($lvl) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <input type="number" name="age" class="form-control" placeholder="Child age" value="{{ request('age') }}" min="0" max="18">
            </div>
            <div class="col-md-1">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="free" value="1" id="freeOnly" {{ request('free') ? 'checked' : '' }}>
                    <label class="form-check-label" for="freeOnly">Free</label>
                </div>
            </div>
            <div class="col-md-1">
                <button class="btn btn-primary w-100"><i class="bi bi-search"></i></button>
            </div>
        </div>
    </form>

    {{-- Results --}}
    @if($courses->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-search display-3 text-muted d-block mb-3"></i>
            <p class="text-muted">No courses found. Try a different search.</p>
        </div>
    @else
    <div class="row g-4">
        @foreach($courses as $course)
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm border-0 hover-shadow">
                @if($course->thumbnail)
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($course->thumbnail) }}" class="card-img-top" style="height:160px;object-fit:cover;" alt="{{ $course->title }}">
                @else
                    <div class="card-img-top bg-gradient d-flex align-items-center justify-content-center" style="height:160px;background:linear-gradient(135deg,#6f42c1,#0d6efd)">
                        <i class="bi bi-book-half text-white" style="font-size:3rem"></i>
                    </div>
                @endif
                <div class="card-body d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start mb-1">
                        @if($course->subject)
                            <span class="badge bg-light text-dark border">{{ $course->subject }}</span>
                        @endif
                        @if($course->price > 0)
                            <span class="fw-bold text-success">${{ $course->price }}</span>
                        @else
                            <span class="badge bg-success">Free</span>
                        @endif
                    </div>
                    <h5 class="card-title fw-semibold">{{ $course->title }}</h5>
                    <p class="card-text text-muted small flex-grow-1">{{ Str::limit($course->description, 100) }}</p>
                    <div class="text-muted small mb-3">
                        <i class="bi bi-person-circle"></i> {{ $course->teacher->name ?? 'Teacher' }}
                        @if($course->age_min || $course->age_max)
                            · Ages {{ $course->age_min ?? '?' }}–{{ $course->age_max ?? '?' }}
                        @endif
                        <br>
                        <i class="bi bi-people"></i> {{ $course->active_enrollments_count ?? 0 }} enrolled ·
                        <span class="badge bg-light text-dark border-0 p-0">{{ ucfirst($course->level) }}</span>
                    </div>
                    <a href="{{ route('marketplace.show', $course->slug) }}" class="btn btn-primary stretched-link">
                        View Course <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-4">{{ $courses->links() }}</div>
    @endif

    {{-- CTA for teachers --}}
    <div class="card mt-5 border-0 bg-gradient" style="background: linear-gradient(135deg, #6f42c1, #0d6efd) !important">
        <div class="card-body text-white text-center py-5">
            <h3 class="fw-bold">Are you a teacher?</h3>
            <p>Create your own courses and reach students worldwide. Set your own schedule and prices.</p>
            <a href="{{ route('register') }}?role=Teacher" class="btn btn-light btn-lg">
                <i class="bi bi-person-video3"></i> Register as Teacher
            </a>
        </div>
    </div>
</div>
@endsection
