@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <a href="{{ route('teacher.courses.index') }}" class="btn btn-sm btn-outline-secondary mb-2">
                <i class="bi bi-arrow-left"></i> My Courses
            </a>
            <h1 class="fw-bold mb-0">{{ $course->title }}</h1>
            <p class="text-muted mb-0">
                <span class="badge bg-{{ $course->status === 'published' ? 'success' : 'secondary' }}">{{ $course->status }}</span>
                @if($course->subject) <span class="ms-2">{{ $course->subject }}</span> @endif
                @if($course->price > 0) <span class="ms-2 text-success fw-semibold">${{ $course->price }} {{ $course->currency }}</span>
                @else <span class="badge bg-info text-dark ms-2">Free</span> @endif
            </p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <form method="POST" action="{{ route('teacher.courses.publish', $course) }}">
                @csrf
                <button class="btn btn-{{ $course->status === 'published' ? 'warning' : 'success' }}">
                    <i class="bi bi-{{ $course->status === 'published' ? 'eye-slash' : 'globe' }}"></i>
                    {{ $course->status === 'published' ? 'Unpublish' : 'Publish' }}
                </button>
            </form>
            <a href="{{ route('teacher.courses.edit', $course) }}" class="btn btn-outline-secondary">
                <i class="bi bi-pencil"></i> Edit
            </a>
            @if($course->isPublished())
                <a href="{{ route('marketplace.show', $course->slug) }}" target="_blank" class="btn btn-outline-info">
                    <i class="bi bi-box-arrow-up-right"></i> Public View
                </a>
            @endif
        </div>
    </div>

    @if(session('status'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('status') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    <div class="row g-4">
        {{-- Left: Sections + Students --}}
        <div class="col-lg-8">
            {{-- Curriculum Sections --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header fw-bold d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-list-ol"></i> Curriculum</span>
                    <a href="{{ route('teacher.courses.edit', $course) }}" class="btn btn-sm btn-outline-primary">Edit Sections</a>
                </div>
                <div class="card-body p-0">
                    @forelse($course->sections as $sec)
                    <div class="px-3 py-3 border-bottom d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-semibold">{{ $loop->iteration }}. {{ $sec->title }}</div>
                            @if($sec->description)<div class="text-muted small">{{ $sec->description }}</div>@endif
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-3 small">No sections yet. <a href="{{ route('teacher.courses.edit', $course) }}">Add sections</a> to outline your curriculum.</div>
                    @endforelse
                </div>
            </div>

            {{-- Enrolled Students --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header fw-bold"><i class="bi bi-people"></i> Enrolled Students ({{ $course->enrollments->count() }})</div>
                <div class="card-body p-0">
                    @forelse($course->enrollments->take(10) as $enrollment)
                    <div class="d-flex align-items-center gap-3 px-3 py-2 border-bottom">
                        <img src="https://api.dicebear.com/7.x/bottts/svg?seed={{ $enrollment->student_id }}" style="width:36px;height:36px;border-radius:50%">
                        <div class="flex-grow-1">
                            <div class="fw-semibold">{{ $enrollment->student->name ?? 'Student #'.$enrollment->student_id }}</div>
                            <div class="text-muted small">Enrolled {{ $enrollment->enrolled_at?->diffForHumans() ?? 'recently' }}</div>
                        </div>
                        <span class="badge bg-{{ $enrollment->payment_status === 'paid' ? 'success' : 'warning text-dark' }}">
                            {{ $enrollment->payment_status }}
                        </span>
                    </div>
                    @empty
                    <div class="text-center text-muted py-3 small">No students enrolled yet.</div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Right: Sessions + Invite Links --}}
        <div class="col-lg-4">
            {{-- Schedule New Session --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header fw-bold"><i class="bi bi-camera-video"></i> Schedule a Session</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('teacher.sessions.store', $course) }}">
                        @csrf
                        <div class="mb-2">
                            <label class="form-label small">Session Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control form-control-sm" required placeholder="e.g. Week 1 Introduction">
                        </div>
                        <div class="mb-2">
                            <label class="form-label small">Date &amp; Time <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="starts_at" class="form-control form-control-sm" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small">Duration (minutes)</label>
                            <input type="number" name="duration_minutes" class="form-control form-control-sm" value="60" min="15" max="480">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small">External Meeting URL (optional)</label>
                            <input type="url" name="meeting_url" class="form-control form-control-sm" placeholder="https://meet.google.com/...">
                            <div class="form-text">Leave blank to use the built-in classroom.</div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-calendar-plus"></i> Schedule</button>
                    </form>
                </div>
            </div>

            {{-- Upcoming Sessions --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header fw-bold"><i class="bi bi-calendar-event"></i> Sessions</div>
                <div class="card-body p-0" style="max-height:300px;overflow-y:auto;">
                    @forelse($course->classSessions as $session)
                    <div class="px-3 py-2 border-bottom">
                        <div class="d-flex justify-content-between align-items-start gap-1">
                            <div>
                                <div class="fw-semibold small">{{ $session->title }}</div>
                                <div class="text-muted" style="font-size:0.75rem">{{ $session->starts_at->format('D M j, g:i A') }} · {{ $session->duration_minutes }}min</div>
                            </div>
                            <span class="badge bg-{{ $session->status === 'live' ? 'danger' : ($session->status === 'scheduled' ? 'primary' : 'secondary') }}">
                                {{ $session->status }}
                            </span>
                        </div>
                        <div class="d-flex gap-1 mt-1">
                            @if($session->status === 'scheduled')
                                <form method="POST" action="{{ route('teacher.sessions.start', $session) }}">@csrf<button class="btn btn-xs btn-success btn-sm py-0 px-2"><i class="bi bi-play-fill"></i> Start</button></form>
                                <form method="POST" action="{{ route('teacher.sessions.cancel', $session) }}">@csrf<button class="btn btn-xs btn-outline-danger btn-sm py-0 px-2"><i class="bi bi-x"></i></button></form>
                            @elseif($session->status === 'live')
                                <a href="{{ route('classroom.room', $session->room_id) }}" class="btn btn-sm btn-danger py-0 px-2"><i class="bi bi-camera-video-fill"></i> Enter</a>
                                <form method="POST" action="{{ route('teacher.sessions.end', $session) }}">@csrf<button class="btn btn-sm btn-outline-secondary py-0 px-2">End</button></form>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-3 small">No sessions scheduled yet.</div>
                    @endforelse
                </div>
            </div>

            {{-- Invite Links --}}
            <div class="card shadow-sm">
                <div class="card-header fw-bold"><i class="bi bi-link-45deg"></i> Invite Links</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('teacher.invite-links.store', $course) }}" class="mb-3">
                        @csrf
                        <div class="mb-2">
                            <input type="text" name="label" class="form-control form-control-sm" placeholder="Label (optional)">
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col">
                                <input type="number" name="max_uses" class="form-control form-control-sm" placeholder="Max uses (optional)" min="1">
                            </div>
                            <div class="col">
                                <input type="datetime-local" name="expires_at" class="form-control form-control-sm" placeholder="Expires">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-outline-primary w-100 btn-sm"><i class="bi bi-plus"></i> Generate Link</button>
                    </form>

                    @forelse($course->inviteLinks as $link)
                    <div class="border rounded p-2 mb-2 bg-light small">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                @if($link->label)<div class="fw-semibold">{{ $link->label }}</div>@endif
                                <div class="text-break" style="font-size:0.75rem">
                                    <code>{{ route('invite.join', $link->token) }}</code>
                                </div>
                                <div class="text-muted mt-1" style="font-size:0.72rem">
                                    Uses: {{ $link->uses }}{{ $link->max_uses ? '/'.$link->max_uses : '' }}
                                    @if($link->expires_at) · Expires {{ $link->expires_at->format('M j') }}@endif
                                    @if($link->isExpired()) <span class="text-danger">Expired</span>
                                    @elseif($link->isExhausted()) <span class="text-warning">Exhausted</span>
                                    @else <span class="text-success">Active</span> @endif
                                </div>
                            </div>
                            <div class="d-flex gap-1">
                                <button class="btn btn-xs btn-outline-secondary btn-sm py-0 px-1"
                                    onclick="navigator.clipboard.writeText('{{ route('invite.join', $link->token) }}').then(()=>alert('Copied!'))"
                                    title="Copy"><i class="bi bi-clipboard"></i></button>
                                <form method="POST" action="{{ route('teacher.invite-links.destroy', $link) }}" onsubmit="return confirm('Delete?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-xs btn-outline-danger btn-sm py-0 px-1"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-muted small text-center">No invite links yet.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
