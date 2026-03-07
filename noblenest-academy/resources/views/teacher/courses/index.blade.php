@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0 fw-bold"><i class="bi bi-book text-primary"></i> My Courses</h1>
        <a href="{{ route('teacher.courses.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> New Course
        </a>
    </div>

    @if(session('status'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('status') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    @forelse($courses as $course)
    <div class="card shadow-sm mb-3">
        <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="mb-1 fw-semibold">{{ $course->title }}</h5>
                <div class="text-muted small">
                    <span class="badge bg-{{ $course->status === 'published' ? 'success' : 'secondary' }}">{{ $course->status }}</span>
                    @if($course->subject) <span class="ms-1">· {{ $course->subject }}</span> @endif
                    <span class="ms-2"><i class="bi bi-people"></i> {{ $course->enrollments_count }} enrolled</span>
                    <span class="ms-2"><i class="bi bi-camera-video"></i> {{ $course->class_sessions_count }} sessions</span>
                    @if($course->price > 0)
                        <span class="ms-2 text-success fw-semibold">${{ $course->price }}</span>
                    @else
                        <span class="ms-2 badge bg-info text-dark">Free</span>
                    @endif
                </div>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <form method="POST" action="{{ route('teacher.courses.publish', $course) }}">
                    @csrf
                    <button class="btn btn-sm btn-{{ $course->status === 'published' ? 'warning' : 'success' }}">
                        {{ $course->status === 'published' ? 'Unpublish' : 'Publish' }}
                    </button>
                </form>
                <a href="{{ route('teacher.courses.show', $course) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i> Manage</a>
                <a href="{{ route('teacher.courses.edit', $course) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i> Edit</a>
                <form method="POST" action="{{ route('teacher.courses.destroy', $course) }}" onsubmit="return confirm('Delete this course?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="text-center py-5">
        <i class="bi bi-book-half display-3 text-muted d-block mb-3"></i>
        <p class="text-muted">You haven't created any courses yet.</p>
        <a href="{{ route('teacher.courses.create') }}" class="btn btn-primary">Create Your First Course</a>
    </div>
    @endforelse

    @if($courses->hasPages())
        <div class="mt-3">{{ $courses->links() }}</div>
    @endif
</div>
@endsection
