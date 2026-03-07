@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h1 class="mb-0 fw-bold"><i class="bi bi-person-video3 text-primary"></i> Teacher Dashboard</h1>
            <p class="text-muted mb-0">Welcome back, <strong>{{ $teacher->name }}</strong>!</p>
        </div>
        <a href="{{ route('teacher.courses.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> New Course
        </a>
    </div>

    @if(session('status'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('status') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    {{-- Stats --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card text-center shadow-sm border-0 bg-primary text-white">
                <div class="card-body py-3">
                    <div class="fs-2 fw-bold">{{ $courses->count() }}</div>
                    <div class="small">Courses</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center shadow-sm border-0 bg-success text-white">
                <div class="card-body py-3">
                    <div class="fs-2 fw-bold">{{ $totalStudents }}</div>
                    <div class="small">Students</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center shadow-sm border-0 bg-info text-white">
                <div class="card-body py-3">
                    <div class="fs-2 fw-bold">{{ $upcomingSessions->count() }}</div>
                    <div class="small">Upcoming Sessions</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center shadow-sm border-0 bg-warning text-dark">
                <div class="card-body py-3">
                    <div class="fs-2 fw-bold">${{ number_format($totalEarnings, 0) }}</div>
                    <div class="small">Total Earned</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- My Courses --}}
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header fw-bold d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-book"></i> My Courses</span>
                    <a href="{{ route('teacher.courses.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    @forelse($courses->take(5) as $course)
                    <div class="d-flex align-items-center justify-content-between px-3 py-3 border-bottom">
                        <div>
                            <span class="fw-semibold">{{ $course->title }}</span>
                            <span class="badge bg-{{ $course->status === 'published' ? 'success' : 'secondary' }} ms-2">{{ $course->status }}</span>
                            <div class="text-muted small">
                                <i class="bi bi-people"></i> {{ $course->active_enrollments_count ?? 0 }} students ·
                                <i class="bi bi-camera-video"></i> {{ $course->class_sessions_count ?? 0 }} sessions
                                @if($course->price > 0)
                                    · ${{ $course->price }}
                                @else
                                    · <span class="text-success">Free</span>
                                @endif
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('teacher.courses.show', $course) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('teacher.courses.edit', $course) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-book-half display-4 d-block mb-2"></i>
                        No courses yet. <a href="{{ route('teacher.courses.create') }}">Create your first course!</a>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Upcoming Sessions --}}
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header fw-bold"><i class="bi bi-calendar-event"></i> Upcoming Sessions</div>
                <div class="card-body p-0">
                    @forelse($upcomingSessions as $session)
                    <div class="px-3 py-3 border-bottom">
                        <div class="fw-semibold">{{ $session->title }}</div>
                        <div class="text-muted small">
                            <i class="bi bi-clock"></i> {{ $session->starts_at->format('D, M j · g:i A') }}
                            <span class="ms-2">{{ $session->duration_minutes }}min</span>
                        </div>
                        <div class="small text-muted">{{ $session->course->title }}</div>
                        <form method="POST" action="{{ route('teacher.sessions.start', $session) }}" class="mt-2">
                            @csrf
                            <button class="btn btn-sm btn-success"><i class="bi bi-camera-video"></i> Start Class</button>
                        </form>
                    </div>
                    @empty
                    <div class="text-center text-muted py-3 small">No upcoming sessions.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
