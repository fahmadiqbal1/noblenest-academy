@extends('layouts.practitioner')

@section('meta_title', 'Review Queue | NobleNest Global Academy')

@section('content')
<h2 class="font-bold mb-4"><x-ui.icon name="clipboard-check" class="me-2" />Content Review Queue</h2>

@if(session('success'))
    <div class="flex items-start gap-3 p-4 rounded-lg border bg-emerald-50 border-emerald-200 text-emerald-800">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="flex items-start gap-3 p-4 rounded-lg border bg-red-50 border-red-200 text-red-800">{{ session('error') }}</div>
@endif

{{-- Pending content --}}
<div class="glass-panel p-4 mb-4">
    <h5 class="font-bold mb-3"><x-ui.icon name="hourglass" class="me-2 text-amber-600" />Pending Review ({{ $pending->total() }})</h5>

    @if($pending->isEmpty())
        <p class="text-[var(--color-text-muted)] mb-0">No content needs your review at this time.</p>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm border-collapse table-hover-tw align-middle mb-0">
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
                        <td class="font-semibold">{{ $content->title }}</td>
                        <td><span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-50 text-gray-900 border">{{ $content->category }}</span></td>
                        <td>{{ $content->stage }}</td>
                        <td>{{ ucfirst(str_replace('_', ' ', $content->content_type)) }}</td>
                        <td>{{ $content->created_at->diffForHumans() }}</td>
                        <td>
                            <a href="{{ route('practitioner.reviews.show', $content) }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-3 py-1.5 text-sm bg-violet-600 text-white hover:bg-violet-700">
                                <x-ui.icon name="eye" class="me-1" />Review
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
    <h5 class="font-bold mb-3"><x-ui.icon name="book-marked" class="me-2 text-emerald-600" />My Reviews ({{ $myReviews->total() }})</h5>

    @if($myReviews->isEmpty())
        <p class="text-[var(--color-text-muted)] mb-0">You haven't reviewed any content yet.</p>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm border-collapse table-hover-tw align-middle mb-0">
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
                                <span class="text-[var(--color-text-muted)]">[deleted]</span>
                            @endif
                        </td>
                        <td>
                            @if($review->decision === 'approved')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-600">Approved</span>
                            @elseif($review->decision === 'rejected')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-600">Rejected</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-600 text-gray-900">Flagged</span>
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
