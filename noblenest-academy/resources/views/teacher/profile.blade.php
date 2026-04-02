@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white fw-bold" style="width:64px;height:64px;font-size:1.5rem;">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div>
                    <h1 class="h3 fw-bold mb-0">{{ auth()->user()->name }}</h1>
                    <p class="text-muted mb-0">{{ auth()->user()->email }}</p>
                </div>
                <a href="{{ route('teacher.profile.edit') }}" class="btn btn-outline-primary ms-auto">Edit Profile</a>
            </div>

            @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white fw-bold">Vetting Status</div>
                <div class="card-body">
                    @php $profile = $teacherProfile ?? null; @endphp
                    @if($profile)
                    <div class="d-flex align-items-center gap-3">
                        @if($profile->vetting_status === 'approved')
                        <span class="badge bg-success fs-6">✓ Approved</span>
                        <span class="text-muted small">You can accept students and create paid courses.</span>
                        @elseif($profile->vetting_status === 'pending')
                        <span class="badge bg-warning text-dark fs-6">⏳ Under Review</span>
                        <span class="text-muted small">We'll notify you when your application is reviewed.</span>
                        @elseif($profile->vetting_status === 'rejected')
                        <span class="badge bg-danger fs-6">✗ Rejected</span>
                        <form method="POST" action="{{ route('teacher.profile.update') }}" class="mt-2">
                            @csrf @method('PUT')
                            <button type="submit" class="btn btn-sm btn-outline-warning">Re-submit Application</button>
                        </form>
                        @else
                        <span class="badge bg-secondary fs-6">Not Submitted</span>
                        <form method="POST" action="{{ route('teacher.profile.update') }}" class="mt-2">
                            @csrf @method('PUT')
                            <button type="submit" class="btn btn-sm btn-primary">Submit for Vetting</button>
                        </form>
                        @endif
                    </div>
                    @else
                    <p class="text-muted">No teacher profile found. <a href="{{ route('teacher.profile.edit') }}">Set up your profile</a> to get started.</p>
                    @endif
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-bold">My Courses</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr><th>Course</th><th>Students</th><th>Status</th><th></th></tr>
                            </thead>
                            <tbody>
                                @forelse($courses ?? [] as $course)
                                <tr>
                                    <td>{{ $course->title }}</td>
                                    <td>{{ $course->enrollments_count ?? 0 }}</td>
                                    <td><span class="badge {{ $course->published ? 'bg-success' : 'bg-secondary' }}">{{ $course->published ? 'Published' : 'Draft' }}</span></td>
                                    <td><a href="{{ route('teacher.courses.show', $course) }}" class="btn btn-sm btn-outline-primary">Manage</a></td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="text-center text-muted py-3">No courses yet. <a href="{{ route('teacher.courses.create') }}">Create one →</a></td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
