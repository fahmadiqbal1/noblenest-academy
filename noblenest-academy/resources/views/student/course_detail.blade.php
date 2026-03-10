@extends('layouts.app')

@section('content')
<style>
    .course-shell,
    .course-panel,
    .course-sidebar {
        background: rgba(255,255,255,0.84);
        border: 1px solid rgba(24,34,47,0.08);
        box-shadow: 0 24px 48px rgba(24,34,47,0.10);
        border-radius: 1.5rem;
    }
    .course-shell {
        padding: 1.4rem;
        margin-bottom: 1.25rem;
        background:
            radial-gradient(circle at 12% 18%, rgba(242,165,65,0.16), transparent 18%),
            radial-gradient(circle at 82% 16%, rgba(13,92,99,0.16), transparent 22%),
            linear-gradient(145deg, rgba(255,255,255,0.96), rgba(238,244,246,0.94));
    }
    .course-cover {
        width: 100%;
        max-height: 360px;
        object-fit: cover;
        border-radius: 1.4rem;
    }
    .course-panel,
    .course-sidebar { overflow: hidden; }
    .course-badge-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }
    .course-teacher-card,
    .course-feature-list li,
    .course-session-row {
        border-radius: 1rem;
        background: rgba(241,247,248,0.86);
        border: 1px solid rgba(24,34,47,0.06);
    }
    .course-teacher-card { padding: 0.85rem; }
    .course-feature-list { list-style: none; padding-left: 0; }
    .course-feature-list li { padding: 0.7rem 0.8rem; margin-bottom: 0.6rem; }
    .course-session-row { padding: 1rem; margin-bottom: 0.75rem; }
</style>

<div class="container py-4">
    <a href="{{ route('marketplace.index') }}" class="btn btn-sm btn-outline-secondary mb-3">
        <i class="bi bi-arrow-left"></i> Back to Marketplace
    </a>

    @if(session('status'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('status') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    <div class="row g-4">
        {{-- Left: Course info --}}
        <div class="col-lg-8">
            {{-- Hero --}}
            <div class="course-shell">
            @if($course->thumbnail)
                <img src="{{ \Illuminate\Support\Facades\Storage::url($course->thumbnail) }}" class="course-cover mb-4">
            @else
                <div class="rounded mb-4 d-flex align-items-center justify-content-center" style="height:260px;background:linear-gradient(135deg,#0d5c63,#1f7a8c 58%, #f2a541)">
                    <i class="bi bi-book-half text-white" style="font-size:5rem"></i>
                </div>
            @endif

            <h1 class="fw-bold">{{ $course->title }}</h1>
            <div class="course-badge-grid">
                @if($course->subject)<span class="badge bg-light text-dark border">{{ $course->subject }}</span>@endif
                <span class="badge bg-light text-dark border">{{ ucfirst($course->level) }}</span>
                @if($course->age_min || $course->age_max)
                    <span class="badge bg-light text-dark border">Ages {{ $course->age_min ?? '?' }}–{{ $course->age_max ?? '?' }}</span>
                @endif
                <span class="badge bg-light text-dark border">{{ strtoupper($course->language) }}</span>
            </div>

            <p class="lead">{{ $course->description }}</p>
            </div>

            @if($course->what_you_learn)
            <div class="course-panel mb-4">
                <div class="card-header fw-bold"><i class="bi bi-check-circle text-success"></i> What You'll Learn</div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        @foreach(explode("\n", $course->what_you_learn) as $line)
                            @if(trim($line))
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>{{ trim($line) }}</li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif

            {{-- Curriculum --}}
            @if($course->sections->isNotEmpty())
            <div class="course-panel mb-4">
                <div class="card-header fw-bold"><i class="bi bi-list-ol"></i> Curriculum</div>
                <div class="accordion" id="curriculumAccordion">
                    @foreach($course->sections as $sec)
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sec{{ $sec->id }}">
                                <span class="fw-semibold me-2">{{ $loop->iteration }}.</span> {{ $sec->title }}
                            </button>
                        </h2>
                        @if($sec->description)
                        <div id="sec{{ $sec->id }}" class="accordion-collapse collapse">
                            <div class="accordion-body text-muted">{{ $sec->description }}</div>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Upcoming sessions --}}
            @if($course->classSessions->isNotEmpty())
            <div class="course-panel mb-4">
                <div class="card-header fw-bold"><i class="bi bi-calendar-event"></i> Upcoming Live Sessions</div>
                <div class="card-body">
                    @foreach($course->classSessions as $session)
                    <div class="course-session-row d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div>
                            <div class="fw-semibold">{{ $session->title }}</div>
                            <div class="text-muted small"><i class="bi bi-clock"></i> {{ $session->starts_at->format('D, M j, Y · g:i A') }} · {{ $session->duration_minutes }} min</div>
                        </div>
                        @if($enrollment && $enrollment->isActive())
                            @if($session->status === 'live')
                                <a href="{{ route('classroom.room', $session->room_id) }}" class="btn btn-sm btn-danger"><i class="bi bi-camera-video-fill"></i> Join Now</a>
                            @else
                                <span class="badge bg-primary">Scheduled</span>
                            @endif
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- Right: Enrol card --}}
        <div class="col-lg-4">
            <div class="course-sidebar sticky-top" style="top:80px">
                <div class="card-body p-4">
                    {{-- Price --}}
                    <div class="text-center mb-3">
                        @if($course->price > 0)
                            <div class="display-6 fw-bold text-success">${{ $course->price }} <span class="fs-6 text-muted">/ once</span></div>
                        @else
                            <div class="display-6 fw-bold text-success">Free</div>
                        @endif
                    </div>

                    {{-- Teacher info --}}
                    <div class="course-teacher-card d-flex align-items-center gap-3 mb-3">
                        <img src="https://api.dicebear.com/7.x/bottts/svg?seed={{ $course->teacher_id }}" style="width:48px;height:48px;border-radius:50%">
                        <div>
                            <div class="fw-semibold">{{ $course->teacher->name ?? 'Teacher' }}</div>
                            <div class="text-muted small">Course Instructor</div>
                        </div>
                    </div>

                    @if($course->syllabus_file)
                    <a href="{{ \Illuminate\Support\Facades\Storage::url($course->syllabus_file) }}" target="_blank" class="btn btn-outline-secondary w-100 mb-3">
                        <i class="bi bi-file-earmark-pdf"></i> Download Syllabus
                    </a>
                    @endif

                    @if($enrollment && $enrollment->isActive())
                        <div class="alert alert-success text-center mb-3">
                            <i class="bi bi-check-circle-fill"></i> You are enrolled!
                        </div>
                        @if(auth()->user()->role === 'Student')
                            <a href="{{ route('student.my-courses') }}" class="btn btn-primary w-100">
                                <i class="bi bi-book-open"></i> Go to My Courses
                            </a>
                        @endif
                    @elseif(auth()->check())
                        @if(auth()->user()->role === 'Student')
                            <a href="{{ route('student.enroll.checkout', $course->slug) }}" class="btn btn-primary w-100 btn-lg">
                                <i class="bi bi-person-plus"></i>
                                {{ $course->price > 0 ? 'Enrol & Pay' : 'Enrol for Free' }}
                            </a>
                        @else
                            <p class="text-muted text-center small">Log in as a student to enrol.</p>
                        @endif
                    @else
                        <a href="{{ route('register') }}" class="btn btn-primary w-100 btn-lg mb-2">
                            <i class="bi bi-person-plus"></i> Register to Enrol
                        </a>
                        <a href="{{ route('login') }}" class="btn btn-outline-secondary w-100">
                            Already have an account? Log in
                        </a>
                    @endif

                    <ul class="course-feature-list mt-3 text-muted small">
                        <li><i class="bi bi-camera-video text-primary me-2"></i> Live online classes</li>
                        <li><i class="bi bi-people text-primary me-2"></i> Small group sessions</li>
                        @if($course->syllabus_file)
                        <li><i class="bi bi-file-earmark-pdf text-primary me-2"></i> Curriculum included</li>
                        @endif
                        @if($course->max_students)
                        <li><i class="bi bi-people-fill text-warning me-2"></i> Max {{ $course->max_students }} students</li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
