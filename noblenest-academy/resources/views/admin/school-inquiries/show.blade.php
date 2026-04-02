@extends('layouts.app')

@section('title', 'School Inquiry')

@section('content')
<div class="container py-5">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('admin.school-inquiries.index') }}" class="btn btn-outline-secondary me-3">← Back</a>
        <h1 class="h3 fw-bold mb-0">{{ $schoolInquiry->school_name ?? 'School Inquiry' }}</h1>
        <span class="badge {{ $schoolInquiry->status === 'closed' ? 'bg-success' : 'bg-primary' }} ms-3">{{ ucfirst($schoolInquiry->status ?? 'open') }}</span>
    </div>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-bold">Inquiry Details</div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">School</dt>
                        <dd class="col-sm-8">{{ $schoolInquiry->school_name ?? '–' }}</dd>
                        <dt class="col-sm-4">Contact Name</dt>
                        <dd class="col-sm-8">{{ $schoolInquiry->contact_name ?? '–' }}</dd>
                        <dt class="col-sm-4">Contact Email</dt>
                        <dd class="col-sm-8">{{ $schoolInquiry->contact_email ?? '–' }}</dd>
                        <dt class="col-sm-4">Country</dt>
                        <dd class="col-sm-8">{{ $schoolInquiry->country ?? '–' }}</dd>
                        <dt class="col-sm-4">Students</dt>
                        <dd class="col-sm-8">{{ $schoolInquiry->student_count ?? '–' }}</dd>
                        <dt class="col-sm-4">Message</dt>
                        <dd class="col-sm-8">{{ $schoolInquiry->message ?? '–' }}</dd>
                        <dt class="col-sm-4">Assigned To</dt>
                        <dd class="col-sm-8">{{ $schoolInquiry->assignedAdmin->name ?? 'Unassigned' }}</dd>
                        <dt class="col-sm-4">Received</dt>
                        <dd class="col-sm-8">{{ $schoolInquiry->created_at->format('d M Y H:i') }}</dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white fw-bold">Assign</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.school-inquiries.assign', $schoolInquiry) }}">
                        @csrf @method('PATCH')
                        <div class="mb-3">
                            <label class="form-label">Assign to Admin</label>
                            <select name="admin_id" class="form-select">
                                @foreach($admins ?? [] as $admin)
                                <option value="{{ $admin->id }}" @selected($schoolInquiry->assigned_to == $admin->id)>{{ $admin->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Assign</button>
                    </form>
                </div>
            </div>

            @if($schoolInquiry->status !== 'closed')
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-bold">Close Inquiry</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.school-inquiries.close', $schoolInquiry) }}">
                        @csrf @method('PATCH')
                        <div class="mb-3">
                            <textarea name="resolution_notes" class="form-control" rows="3" placeholder="Resolution notes..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Mark as Closed</button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
