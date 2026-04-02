@extends('layouts.app')

@section('title', 'Review Teacher Application')

@section('content')
<div class="container py-5">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('admin.teacher-vetting') }}" class="btn btn-outline-secondary me-3">← Back</a>
        <h1 class="h3 fw-bold mb-0">Review: {{ $teacherProfile->user->name ?? 'Teacher' }}</h1>
    </div>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white fw-bold">Profile Details</div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Name</dt>
                        <dd class="col-sm-8">{{ $teacherProfile->user->name ?? '–' }}</dd>
                        <dt class="col-sm-4">Email</dt>
                        <dd class="col-sm-8">{{ $teacherProfile->user->email ?? '–' }}</dd>
                        <dt class="col-sm-4">Country</dt>
                        <dd class="col-sm-8">{{ $teacherProfile->country ?? '–' }}</dd>
                        <dt class="col-sm-4">Subjects</dt>
                        <dd class="col-sm-8">{{ $teacherProfile->subjects ?? '–' }}</dd>
                        <dt class="col-sm-4">Languages</dt>
                        <dd class="col-sm-8">{{ $teacherProfile->languages ?? '–' }}</dd>
                        <dt class="col-sm-4">Qualifications</dt>
                        <dd class="col-sm-8">{{ $teacherProfile->qualifications ?? '–' }}</dd>
                        <dt class="col-sm-4">Bio</dt>
                        <dd class="col-sm-8">{{ $teacherProfile->bio ?? '–' }}</dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-bold">Decision</div>
                <div class="card-body">
                    <p class="text-muted small">Current status: <span class="badge {{ $teacherProfile->vetting_status === 'approved' ? 'bg-success' : ($teacherProfile->vetting_status === 'rejected' ? 'bg-danger' : 'bg-warning text-dark') }}">{{ ucfirst($teacherProfile->vetting_status ?? 'pending') }}</span></p>

                    <form method="POST" action="{{ route('admin.teacher-vetting.approve', $teacherProfile) }}" class="mb-2">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn btn-success w-100 fw-bold">✓ Approve</button>
                    </form>
                    <form method="POST" action="{{ route('admin.teacher-vetting.reject', $teacherProfile) }}">
                        @csrf @method('PATCH')
                        <div class="mb-2">
                            <textarea name="rejection_reason" class="form-control" rows="2" placeholder="Reason for rejection (optional)"></textarea>
                        </div>
                        <button type="submit" class="btn btn-danger w-100">✗ Reject</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
