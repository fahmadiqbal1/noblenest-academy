@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-bold"><i class="bi bi-book-open text-primary"></i> My Courses</h1>
        <a href="{{ route('marketplace.index') }}" class="btn btn-outline-primary">
            <i class="bi bi-shop"></i> Find More Courses
        </a>
    </div>

    @if(session('status'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('status') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    @forelse($enrollments as $enrollment)
    @php $course = $enrollment->course; @endphp
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-start">
                <div class="col-auto">
                    @if($course->thumbnail)
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($course->thumbnail) }}" style="width:80px;height:80px;object-fit:cover;border-radius:8px">
                    @else
                        <div style="width:80px;height:80px;border-radius:8px;background:linear-gradient(135deg,#6f42c1,#0d6efd)" class="d-flex align-items-center justify-content-center">
                            <i class="bi bi-book-half text-white fs-4"></i>
                        </div>
                    @endif
                </div>
                <div class="col flex-grow-1">
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
                        <div class="d-flex align-items-center gap-2 mt-1">
                            <i class="bi bi-calendar-event text-primary"></i>
                            <span class="small">{{ $session->title }} — {{ $session->starts_at->format('M j, g:i A') }}</span>
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
