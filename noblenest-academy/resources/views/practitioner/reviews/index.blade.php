@extends('layouts.app')

@section('meta_title', 'Review Queue | NobleNest Global Academy')

@section('content')
<h2 class="fw-bold mb-4"><i class="bi bi-clipboard-check me-2"></i>Content Review Queue</h2>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

{{-- Pending content --}}
<div class="glass-panel p-4 mb-4">
    <h5 class="fw-bold mb-3"><i class="bi bi-hourglass-split me-2 text-warning"></i>Pending Review ({{ $pending->total() }})</h5>

    @if($pending->isEmpty())
        <p class="text-muted mb-0">No content needs your review at this time.</p>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Stage</th>
                        <th>Type</th>
                        <th>Created</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pending as $content)
                    <tr>
                        <td class="fw-semibold">{{ $content->title }}</td>
                        <td><span class="badge bg-light text-dark border">{{ $content->category }}</span></td>
                        <td>{{ $content->stage }}</td>
                        <td>{{ ucfirst(str_replace('_', ' ', $content->content_type)) }}</td>
                        <td>{{ $content->created_at->diffForHumans() }}</td>
                        <td>
                            <a href="{{ route('practitioner.reviews.show', $content) }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-eye me-1"></i>Review
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $pending->links() }}</div>
    @endif
</div>

{{-- My past reviews --}}
<div class="glass-panel p-4">
    <h5 class="fw-bold mb-3"><i class="bi bi-journal-check me-2 text-success"></i>My Reviews ({{ $myReviews->total() }})</h5>

    @if($myReviews->isEmpty())
        <p class="text-muted mb-0">You haven't reviewed any content yet.</p>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Content</th>
                        <th>Decision</th>
                        <th>Side Notes</th>
                        <th>Reviewed</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($myReviews as $review)
                    <tr>
                        <td>
                            @if($review->content)
                                <a href="{{ route('practitioner.reviews.show', $review->content) }}">{{ $review->content->title }}</a>
                            @else
                                <span class="text-muted">[deleted]</span>
                            @endif
                        </td>
                        <td>
                            @if($review->decision === 'approved')
                                <span class="badge bg-success">Approved</span>
                            @elseif($review->decision === 'rejected')
                                <span class="badge bg-danger">Rejected</span>
                            @else
                                <span class="badge bg-warning text-dark">Flagged</span>
                            @endif
                        </td>
                        <td>{{ $review->side_notes ? Str::limit($review->side_notes, 60) : '—' }}</td>
                        <td>{{ $review->reviewed_at?->diffForHumans() ?? $review->created_at->diffForHumans() }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $myReviews->links() }}</div>
    @endif
</div>
@endsection
