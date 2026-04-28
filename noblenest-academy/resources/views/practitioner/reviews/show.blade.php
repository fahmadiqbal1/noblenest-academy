@extends('layouts.app')

@section('meta_title', 'Review: ' . $maternalContent->title . ' | NobleNest Global Academy')

@section('content')
<style>
    .review-content-panel {
        background: var(--nn-surface);
        border: var(--nn-border-w) solid var(--nn-border);
        border-radius: var(--nn-radius);
        box-shadow: var(--nn-shadow);
    }
    .step-card {
        background: rgba(255,255,255,0.7);
        border: 2px solid var(--nn-border);
        border-radius: var(--nn-radius-sm);
        padding: 1rem;
    }
    .review-form-panel {
        background: linear-gradient(135deg, rgba(5,150,105,0.05), rgba(52,211,153,0.08));
        border: var(--nn-border-w) solid rgba(5,150,105,0.2);
        border-radius: var(--nn-radius);
        padding: 2rem;
    }
</style>

<div class="mb-3">
    <a href="{{ route('practitioner.reviews.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Back to Queue
    </a>
</div>

{{-- Content details --}}
<div class="review-content-panel p-4 mb-4">
    <div class="d-flex align-items-start justify-content-between mb-3">
        <div>
            <span class="badge bg-light text-dark border mb-2">{{ ucfirst(str_replace('_', ' ', $maternalContent->content_type)) }}</span>
            <span class="badge bg-light text-dark border mb-2">{{ $maternalContent->category }}</span>
            <span class="badge bg-light text-dark border mb-2">{{ $maternalContent->stage }}</span>
            <h3 class="fw-bold mb-1">{{ $maternalContent->title }}</h3>
        </div>
        <span class="badge {{ $maternalContent->moderation_status === 'approved' ? 'bg-success' : ($maternalContent->moderation_status === 'rejected' ? 'bg-danger' : 'bg-warning text-dark') }}">
            {{ ucfirst($maternalContent->moderation_status) }}
        </span>
    </div>

    <div class="mb-3">
        <h6 class="fw-bold text-muted text-uppercase small">Description</h6>
        <p>{{ $maternalContent->description }}</p>
    </div>

    @if($maternalContent->benefit_explanation)
    <div class="mb-3">
        <h6 class="fw-bold text-muted text-uppercase small">Benefits</h6>
        <p>{{ $maternalContent->benefit_explanation }}</p>
    </div>
    @endif

    @if($maternalContent->cultural_origin)
    <div class="mb-3">
        <h6 class="fw-bold text-muted text-uppercase small">Cultural Origin</h6>
        <p>{{ ucfirst($maternalContent->cultural_origin) }}</p>
    </div>
    @endif

    {{-- Steps --}}
    @if($maternalContent->steps->isNotEmpty())
    <h6 class="fw-bold text-muted text-uppercase small mt-4 mb-3">Steps ({{ $maternalContent->steps->count() }})</h6>
    <div class="d-flex flex-column gap-3">
        @foreach($maternalContent->steps->sortBy('step_number') as $step)
        <div class="step-card">
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="badge rounded-pill" style="background: var(--nn-primary);">Step {{ $step->step_number }}</span>
                <strong>{{ $step->title }}</strong>
                @if($step->duration_seconds)
                    <span class="text-muted small ms-auto"><i class="bi bi-clock"></i> {{ $step->duration_seconds }}s</span>
                @endif
            </div>
            <p class="mb-1">{{ $step->instruction }}</p>
            @if($step->tip)
                <div class="text-muted small"><i class="bi bi-lightbulb"></i> {{ $step->tip }}</div>
            @endif
        </div>
        @endforeach
    </div>
    @endif
</div>

{{-- Review form or existing review --}}
@if($existingReview)
    <div class="glass-panel p-4">
        <h5 class="fw-bold mb-3"><i class="bi bi-check-circle me-2 text-success"></i>Your Review</h5>
        <div class="row g-3">
            <div class="col-md-4">
                <strong>Decision:</strong>
                @if($existingReview->decision === 'approved')
                    <span class="badge bg-success">Approved</span>
                @elseif($existingReview->decision === 'rejected')
                    <span class="badge bg-danger">Rejected</span>
                @else
                    <span class="badge bg-warning text-dark">Flagged</span>
                @endif
            </div>
            <div class="col-md-4">
                <strong>Credential Used:</strong> {{ ucfirst(str_replace('_', ' ', $existingReview->credential_used)) }}
            </div>
            <div class="col-md-4">
                <strong>Reviewed:</strong> {{ $existingReview->reviewed_at?->format('M j, Y') }}
            </div>
        </div>
        @if($existingReview->side_notes)
            <div class="mt-3 p-3 rounded-3" style="background: #FEF3C7; border: 2px solid #F59E0B;">
                <strong><i class="bi bi-exclamation-triangle me-1"></i>Side Notes (visible to parents):</strong>
                <p class="mb-0 mt-1">{{ $existingReview->side_notes }}</p>
            </div>
        @endif
        @if($existingReview->internal_notes)
            <div class="mt-3 p-3 bg-light rounded-3">
                <strong>Internal Notes (admin only):</strong>
                <p class="mb-0 mt-1">{{ $existingReview->internal_notes }}</p>
            </div>
        @endif
    </div>
@else
    <div class="review-form-panel">
        <h5 class="fw-bold mb-3"><i class="bi bi-pencil-square me-2"></i>Submit Your Review</h5>
        <p class="text-muted mb-4">
            Your review will be recorded with your credential: <strong>{{ $profile->formattedLicenseType() }}</strong>.
            Side notes will be shown as warnings to parents viewing this content.
        </p>

        <form method="POST" action="{{ route('practitioner.reviews.store', $maternalContent) }}">
            @csrf

            @if($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif

            <div class="mb-3">
                <label for="decision" class="form-label fw-semibold">Decision <span class="text-danger">*</span></label>
                <div class="d-flex gap-3">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="decision" id="decision_approved" value="approved" {{ old('decision') === 'approved' ? 'checked' : '' }} required>
                        <label class="form-check-label text-success fw-bold" for="decision_approved"><i class="bi bi-check-circle me-1"></i>Approve</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="decision" id="decision_flagged" value="flagged" {{ old('decision') === 'flagged' ? 'checked' : '' }}>
                        <label class="form-check-label text-warning fw-bold" for="decision_flagged"><i class="bi bi-flag me-1"></i>Flag for Review</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="decision" id="decision_rejected" value="rejected" {{ old('decision') === 'rejected' ? 'checked' : '' }}>
                        <label class="form-check-label text-danger fw-bold" for="decision_rejected"><i class="bi bi-x-circle me-1"></i>Reject</label>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="side_notes" class="form-label fw-semibold">
                    <i class="bi bi-exclamation-triangle text-warning me-1"></i>Side Notes (visible to parents)
                </label>
                <textarea class="form-control" id="side_notes" name="side_notes" rows="3" maxlength="5000" placeholder="e.g. This technique should not be used during the first trimester or by women with pre-eclampsia...">{{ old('side_notes') }}</textarea>
                <div class="form-text">These notes will appear as yellow warning boxes when parents view this content.</div>
            </div>

            <div class="mb-4">
                <label for="internal_notes" class="form-label fw-semibold">Internal Notes (admin only)</label>
                <textarea class="form-control" id="internal_notes" name="internal_notes" rows="2" maxlength="5000" placeholder="Notes for the admin team only...">{{ old('internal_notes') }}</textarea>
            </div>

            <button type="submit" class="btn btn-primary btn-lg">
                <i class="bi bi-send me-2"></i>Submit Review
            </button>
        </form>
    </div>
@endif
@endsection
