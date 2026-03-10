@extends('layouts.app')

@section('content')
<style>
    .teacher-hero,
    .teacher-card,
    .teacher-stat {
        background: rgba(255,255,255,0.84);
        border: 1px solid rgba(24,34,47,0.08);
        box-shadow: 0 24px 48px rgba(24,34,47,0.10);
        border-radius: 1.5rem;
    }
    .teacher-hero {
        padding: 1.7rem;
        margin-bottom: 1.5rem;
        background:
            radial-gradient(circle at 12% 18%, rgba(242,165,65,0.16), transparent 18%),
            radial-gradient(circle at 82% 16%, rgba(13,92,99,0.16), transparent 22%),
            linear-gradient(145deg, rgba(255,255,255,0.96), rgba(238,244,246,0.94));
    }
    .teacher-stat { padding: 1rem; height: 100%; }
    .teacher-card { overflow: hidden; }
</style>

<div class="container py-4">
    <div class="teacher-hero d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <div class="text-uppercase fw-bold small text-primary mb-2" style="letter-spacing:0.14em;">Teaching workspace</div>
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
            <div class="teacher-stat text-center text-white" style="background:linear-gradient(135deg,#0d5c63,#1f7a8c);">
                <div class="py-3">
                    <div class="fs-2 fw-bold">{{ $courses->count() }}</div>
                    <div class="small">Courses</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="teacher-stat text-center text-white" style="background:linear-gradient(135deg,#16866b,#3aa17c);">
                <div class="py-3">
                    <div class="fs-2 fw-bold">{{ $totalStudents }}</div>
                    <div class="small">Students</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="teacher-stat text-center text-white" style="background:linear-gradient(135deg,#1f7a8c,#4aa3b5);">
                <div class="py-3">
                    <div class="fs-2 fw-bold">{{ $upcomingSessions->count() }}</div>
                    <div class="small">Upcoming Sessions</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="teacher-stat text-center text-dark" style="background:linear-gradient(135deg,#f2a541,#f7bf65);">
                <div class="py-3">
                    <div class="fs-2 fw-bold">${{ number_format($totalEarnings, 0) }}</div>
                    <div class="small">Total Earned</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- My Courses --}}
        <div class="col-lg-8">
            <div class="teacher-card">
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
            <div class="teacher-card">
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
