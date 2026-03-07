@extends('layouts.app')

@section('content')
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
            @if($course->thumbnail)
                <img src="{{ \Illuminate\Support\Facades\Storage::url($course->thumbnail) }}" class="img-fluid rounded mb-4" style="max-height:320px;width:100%;object-fit:cover;">
            @else
                <div class="rounded mb-4 d-flex align-items-center justify-content-center" style="height:220px;background:linear-gradient(135deg,#6f42c1,#0d6efd)">
                    <i class="bi bi-book-half text-white" style="font-size:5rem"></i>
                </div>
            @endif

            <h1 class="fw-bold">{{ $course->title }}</h1>
            <div class="d-flex flex-wrap gap-2 mb-3">
                @if($course->subject)<span class="badge bg-light text-dark border">{{ $course->subject }}</span>@endif
                <span class="badge bg-light text-dark border">{{ ucfirst($course->level) }}</span>
                @if($course->age_min || $course->age_max)
                    <span class="badge bg-light text-dark border">Ages {{ $course->age_min ?? '?' }}–{{ $course->age_max ?? '?' }}</span>
                @endif
                <span class="badge bg-light text-dark border">{{ strtoupper($course->language) }}</span>
            </div>

            <p class="lead">{{ $course->description }}</p>

            @if($course->what_you_learn)
            <div class="card shadow-sm mb-4">
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
            <div class="card shadow-sm mb-4">
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
            <div class="card shadow-sm mb-4">
                <div class="card-header fw-bold"><i class="bi bi-calendar-event"></i> Upcoming Live Sessions</div>
                <div class="card-body p-0">
                    @foreach($course->classSessions as $session)
                    <div class="d-flex align-items-center justify-content-between px-3 py-3 border-bottom">
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
            <div class="card shadow border-0 sticky-top" style="top:80px">
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
                    <div class="d-flex align-items-center gap-3 mb-3 p-3 bg-light rounded">
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

                    <ul class="list-unstyled mt-3 text-muted small">
                        <li class="mb-1"><i class="bi bi-camera-video text-primary me-2"></i> Live online classes</li>
                        <li class="mb-1"><i class="bi bi-people text-primary me-2"></i> Small group sessions</li>
                        @if($course->syllabus_file)
                        <li class="mb-1"><i class="bi bi-file-earmark-pdf text-primary me-2"></i> Curriculum included</li>
                        @endif
                        @if($course->max_students)
                        <li class="mb-1"><i class="bi bi-people-fill text-warning me-2"></i> Max {{ $course->max_students }} students</li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
