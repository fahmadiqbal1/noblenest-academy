@extends('layouts.app')

@section('title', 'Admin: Maternal Content')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 style="font-family:'Baloo 2',sans-serif;">Maternal Content Management</h3>
        <a href="{{ route('admin.maternal.content.create') }}" class="btn rounded-pill fw-semibold" style="background:var(--nn-primary); color:#fff;">
            <i class="bi bi-plus me-1"></i> Add Content
        </a>
    </div>

    {{-- Filters --}}
    <form method="GET" class="d-flex gap-2 mb-4">
        <select name="status" class="form-select form-select-sm rounded-3" style="width:auto;">
            <option value="">All Status</option>
            @foreach(['pending', 'approved', 'rejected'] as $s)
                <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
        <input type="text" name="q" class="form-control form-control-sm rounded-3" value="{{ request('q') }}" placeholder="Search..." style="width:200px;">
        <button type="submit" class="btn btn-sm btn-outline-primary rounded-pill">Filter</button>
    </form>

    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Type</th>
                    <th>Stage</th>
                    <th>Culture</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($contents as $content)
                <tr>
                    <td class="fw-semibold">{{ $content->title }}</td>
                    <td><span class="badge rounded-pill" style="background:var(--nn-primary-soft); color:var(--nn-primary);">{{ ucfirst($content->content_type) }}</span></td>
                    <td>{{ ucfirst(str_replace('_', ' ', $content->stage)) }}</td>
                    <td>{{ ucfirst($content->cultural_origin ?? '—') }}</td>
                    <td>
                        @if($content->moderation_status === 'approved')
                            <span class="badge bg-success rounded-pill">Approved</span>
                        @elseif($content->moderation_status === 'rejected')
                            <span class="badge bg-danger rounded-pill">Rejected</span>
                        @else
                            <span class="badge bg-warning text-dark rounded-pill">Pending</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('admin.maternal.content.edit', $content) }}" class="btn btn-sm btn-outline-primary rounded-pill">Edit</a>
                            @if($content->moderation_status === 'pending')
                                <form method="POST" action="{{ route('admin.maternal.content.approve', $content) }}" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="medical_reviewer_name" value="{{ auth()->user()->name }}">
                                    <button type="submit" class="btn btn-sm btn-success rounded-pill">Approve</button>
                                </form>
                                <form method="POST" action="{{ route('admin.maternal.content.reject', $content) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill">Reject</button>
                                </form>
                            @endif
                            @if($content->steps_count ?? $content->steps()->count())
                                <form method="POST" action="{{ route('admin.maternal.content.generateAnimations', $content) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-info rounded-pill" title="Generate step illustrations & narration">
                                        <i class="bi bi-film"></i> Animate
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-4">No content yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $contents->withQueryString()->links() }}
</div>
@endsection
