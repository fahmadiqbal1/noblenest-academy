@extends('layouts.app')

@section('content')
<style>
    .learning-hero,
    .learning-card {
        background: rgba(255,255,255,0.84);
        border: 1px solid rgba(24,34,47,0.08);
        box-shadow: 0 24px 48px rgba(24,34,47,0.10);
        border-radius: 1.5rem;
    }
    .learning-hero {
        padding: 1.6rem;
        margin-bottom: 1.5rem;
        background:
            radial-gradient(circle at 12% 18%, rgba(242,165,65,0.18), transparent 18%),
            radial-gradient(circle at 85% 15%, rgba(13,92,99,0.18), transparent 22%),
            linear-gradient(145deg, rgba(255,255,255,0.96), rgba(238,244,246,0.94));
    }
    .learning-card { padding: 1.2rem; }
    .learning-thumb {
        width: 88px;
        height: 88px;
        border-radius: 1rem;
        object-fit: cover;
    }
    .learning-session {
        padding: 0.7rem 0.8rem;
        border-radius: 0.95rem;
        background: rgba(241,247,248,0.86);
        border: 1px solid rgba(24,34,47,0.06);
    }
</style>

<div class="container py-4">
    <div class="learning-hero d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <div class="text-uppercase fw-bold small text-primary mb-2" style="letter-spacing:0.14em;">Student dashboard</div>
            <h1 class="fw-bold mb-1"><i class="bi bi-book-open text-primary"></i> My Courses</h1>
            <p class="text-muted mb-0">Track your current learning path, jump back into sessions, and explore what to take next.</p>
        </div>
        <a href="{{ route('marketplace.index') }}" class="btn btn-outline-primary">
            <i class="bi bi-shop"></i> Find More Courses
        </a>
    </div>

    @if(session('status'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('status') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    @forelse($enrollments as $enrollment)
    @php $course = $enrollment->course; @endphp
    <div class="learning-card mb-4">
        <div>
            <div class="row g-3 align-items-start">
                <div class="col-auto">
                    @if($course->thumbnail)
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($course->thumbnail) }}" class="learning-thumb">
                    @else
                        <div class="learning-thumb d-flex align-items-center justify-content-center" style="background:linear-gradient(135deg,#0d5c63,#1f7a8c 58%, #f2a541)">
                            <i class="bi bi-book-half text-white fs-4"></i>
                        </div>
                    @endif
                </div>
                <div class="col grow">
                    <h5 class="fw-semibold mb-1">{{ $course->title }}</h5>
                    <div class="text-muted small mb-2">
                        <i class="bi bi-person-circle me-1"></i> {{ $course->teacher->name ?? 'Teacher' }}
                        @if($course->subject) · {{ $course->subject }}@endif
                        · Enrolled {{ $enrollment->enrolled_at?->diffForHumans() ?? 'recently' }}
                    </div>

                    {{-- Upcoming sessions for this course --}}
                    @php $upcomingSessions = $course->classSessions->where('status', 'scheduled'); @endphp
                    @if($upcomingSessions->isNotEmpty())
                    <div class="mb-2">
                        <span class="text-muted small fw-semibold">Next sessions:</span>
                        @foreach($upcomingSessions->take(2) as $session)
                        <div class="d-flex align-items-center justify-content-between gap-2 mt-2 learning-session flex-wrap">
                            <div>
                            <i class="bi bi-calendar-event text-primary"></i>
                            <span class="small">{{ $session->title }} — {{ $session->starts_at->format('M j, g:i A') }}</span>
                            </div>
                            @if($session->status === 'live')
                                <a href="{{ route('classroom.room', $session->room_id) }}" class="btn btn-sm btn-danger py-0 px-2 ms-1">
                                    <i class="bi bi-camera-video-fill"></i> Join Live
                                </a>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
                <div class="col-auto d-flex flex-column gap-2">
                    <a href="{{ route('marketplace.show', $course->slug) }}" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-info-circle"></i> Course Details
                    </a>
                    @if($course->syllabus_file)
                    <a href="{{ \Illuminate\Support\Facades\Storage::url($course->syllabus_file) }}" target="_blank" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-file-earmark-pdf"></i> Syllabus
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="text-center py-5">
        <i class="bi bi-book-open display-3 text-muted d-block mb-3"></i>
        <p class="text-muted">You haven't enrolled in any courses yet.</p>
        <a href="{{ route('marketplace.index') }}" class="btn btn-primary">Browse Courses</a>
    </div>
    @endforelse
</div>
@endsection
