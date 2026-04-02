@extends('layouts.app')

@section('title', 'Scholarships')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex align-items-center mb-4">
        <h1 class="h3 fw-bold mb-0">Scholarships</h1>
        <a href="{{ route('admin.scholarships.create') }}" class="btn btn-primary ms-auto fw-bold">+ New Scholarship</a>
    </div>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white fw-bold">Active Scholarships</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Recipient</th>
                            <th>Type</th>
                            <th>Discount</th>
                            <th>Valid Until</th>
                            <th>Granted</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($scholarships as $scholarship)
                        <tr>
                            <td>{{ $scholarship->user->name ?? '–' }}</td>
                            <td>{{ ucfirst($scholarship->type ?? 'full') }}</td>
                            <td>{{ $scholarship->discount_percent ?? 100 }}%</td>
                            <td>{{ $scholarship->expires_at ? $scholarship->expires_at->format('d M Y') : 'Never' }}</td>
                            <td>{{ $scholarship->created_at->format('d M Y') }}</td>
                            <td>
                                <span class="badge {{ $scholarship->active ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $scholarship->active ? 'Active' : 'Revoked' }}
                                </span>
                            </td>
                            <td>
                                @if($scholarship->active)
                                <form method="POST" action="{{ route('admin.scholarships.revoke', $scholarship) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Revoke this scholarship?')">Revoke</button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">No scholarships granted yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="mt-3">{{ $scholarships->links() }}</div>
</div>
@endsection
