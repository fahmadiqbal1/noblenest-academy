@extends('layouts.app')

@section('title', 'Teacher Vetting')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex align-items-center mb-4">
        <h1 class="h3 fw-bold mb-0">Teacher Vetting</h1>
        <span class="badge bg-warning text-dark ms-3 fs-6">{{ $pending->total() }} Pending</span>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white fw-bold">Pending Applications</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Teacher</th>
                            <th>Email</th>
                            <th>Subjects</th>
                            <th>Country</th>
                            <th>Applied</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pending as $profile)
                        <tr>
                            <td class="fw-semibold">{{ $profile->user->name ?? '–' }}</td>
                            <td>{{ $profile->user->email ?? '–' }}</td>
                            <td>{{ $profile->subjects ?? '–' }}</td>
                            <td>{{ $profile->country ?? '–' }}</td>
                            <td>{{ $profile->created_at->format('d M Y') }}</td>
                            <td>
                                <a href="{{ route('admin.teacher-vetting.show', $profile) }}" class="btn btn-sm btn-outline-primary">Review</a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">No pending applications. 🎉</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div>{{ $pending->links() }}</div>
</div>
@endsection
