@extends('layouts.app')

@section('title', 'School Inquiries')

@section('content')
<div class="container-fluid py-4">
    <h1 class="h3 fw-bold mb-4">School Inquiries</h1>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white fw-bold">All Inquiries</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>School</th>
                            <th>Contact</th>
                            <th>Country</th>
                            <th>Students</th>
                            <th>Status</th>
                            <th>Received</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($inquiries as $inquiry)
                        <tr>
                            <td class="fw-semibold">{{ $inquiry->school_name ?? '–' }}</td>
                            <td>{{ $inquiry->contact_email ?? '–' }}</td>
                            <td>{{ $inquiry->country ?? '–' }}</td>
                            <td>{{ $inquiry->student_count ?? '–' }}</td>
                            <td>
                                <span class="badge {{ $inquiry->status === 'closed' ? 'bg-success' : ($inquiry->status === 'open' ? 'bg-primary' : 'bg-warning text-dark') }}">
                                    {{ ucfirst($inquiry->status ?? 'open') }}
                                </span>
                            </td>
                            <td>{{ $inquiry->created_at->format('d M Y') }}</td>
                            <td>
                                <a href="{{ route('admin.school-inquiries.show', $inquiry) }}" class="btn btn-sm btn-outline-primary">View</a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">No school inquiries yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="mt-3">{{ $inquiries->links() }}</div>
</div>
@endsection
