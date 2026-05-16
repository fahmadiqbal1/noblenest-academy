@extends('layouts.practitioner')

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
    <a href="{{ route('practitioner.reviews.index') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed border-2 border-gray-300 text-gray-700 hover:bg-gray-100 px-3 py-1.5 text-sm">
        <x-ui.icon name="arrow-left" class="me-1" />Back to Queue
    </a>
</div>

{{-- Content details --}}
<div class="review-content-panel p-4 mb-4">
    <div class="flex items-start justify-between mb-3">
        <div>
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-50 text-gray-900 border mb-2">{{ ucfirst(str_replace('_', ' ', $maternalContent->content_type)) }}</span>
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-50 text-gray-900 border mb-2">{{ $maternalContent->category }}</span>
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-50 text-gray-900 border mb-2">{{ $maternalContent->stage }}</span>
            <h3 class="font-bold mb-1">{{ $maternalContent->title }}</h3>
        </div>
        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $maternalContent->moderation_status === 'approved' ? 'bg-success' : ($maternalContent->moderation_status 'rejected' 'bg-danger' 'bg-warning text-dark') }}">
            {{ ucfirst($maternalContent->moderation_status) }}
        </span>
    </div>

    <div class="mb-3">
        <h6 class="font-bold text-[var(--color-text-muted)] uppercase text-sm">Description</h6>
        <p>{{ $maternalContent->description }}</p>
    </div>

    @if($maternalContent->benefit_explanation)
    <div class="mb-3">
        <h6 class="font-bold text-[var(--color-text-muted)] uppercase text-sm">Benefits</h6>
        <p>{{ $maternalContent->benefit_explanation }}</p>
    </div>
    @endif

    @if($maternalContent->cultural_origin)
    <div class="mb-3">
        <h6 class="font-bold text-[var(--color-text-muted)] uppercase text-sm">Cultural Origin</h6>
        <p>{{ ucfirst($maternalContent->cultural_origin) }}</p>
    </div>
    @endif

    {{-- Steps --}}
    @if($maternalContent->steps->isNotEmpty())
    <h6 class="font-bold text-[var(--color-text-muted)] uppercase text-sm mt-4 mb-3">Steps ({{ $maternalContent->steps->count() }})</h6>
    <div class="flex flex-col gap-3">
        @foreach($maternalContent->steps->sortBy('step_number') as $step)
        <div class="step-card">
            <div class="flex items-center gap-2 mb-2">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium" style="background: var(--nn-primary);">Step {{ $step->step_number }}</span>
                <strong>{{ $step->title }}</strong>
                @if($step->duration_seconds)
                    <span class="text-[var(--color-text-muted)] text-sm ms-auto"><x-ui.icon name="clock" /> {{ $step->duration_seconds }}s</span>
                @endif
            </div>
            <p class="mb-1">{{ $step->instruction }}</p>
            @if($step->tip)
                <div class="text-[var(--color-text-muted)] text-sm"><x-ui.icon name="lightbulb" /> {{ $step->tip }}</div>
            @endif
        </div>
        @endforeach
    </div>
    @endif
</div>

{{-- Review form or existing review --}}
@if($existingReview)
    <div class="glass-panel p-4">
        <h5 class="font-bold mb-3"><x-ui.icon name="check-circle" class="me-2 text-emerald-600" />Your Review</h5>
        <div class="flex flex-wrap gap-3">
            <div class="md:w-4/12">
                <strong>Decision:</strong>
                @if($existingReview->decision === 'approved')
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-600">Approved</span>
                @elseif($existingReview->decision === 'rejected')
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-600">Rejected</span>
                @else
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-600 text-gray-900">Flagged</span>
                @endif
            </div>
            <div class="md:w-4/12">
                <strong>Credential Used:</strong> {{ ucfirst(str_replace('_', ' ', $existingReview->credential_used)) }}
            </div>
            <div class="md:w-4/12">
                <strong>Reviewed:</strong> {{ $existingReview->reviewed_at?->format('M j, Y') }}
            </div>
        </div>
        @if($existingReview->side_notes)
            <div class="mt-3 p-3 rounded-lg" style="background: #FEF3C7; border: 2px solid #F59E0B;">
                <strong><x-ui.icon name="alert-triangle" class="me-1" />Side Notes (visible to parents):</strong>
                <p class="mb-0 mt-1">{{ $existingReview->side_notes }}</p>
            </div>
        @endif
        @if($existingReview->internal_notes)
            <div class="mt-3 p-3 bg-gray-50 rounded-lg">
                <strong>Internal Notes (admin only):</strong>
                <p class="mb-0 mt-1">{{ $existingReview->internal_notes }}</p>
            </div>
        @endif
    </div>
@else
    <div class="review-form-panel">
        <h5 class="font-bold mb-3"><x-ui.icon name="edit" class="me-2" />Submit Your Review</h5>
        <p class="text-[var(--color-text-muted)] mb-4">
            Your review will be recorded with your credential: <strong>{{ $profile->formattedLicenseType() }}</strong>.
            Side notes will be shown as warnings to parents viewing this content.
        </p>

        <form method="POST" action="{{ route('practitioner.reviews.store', $maternalContent) }}">
            @csrf

            @if($errors->any())
                <div class="flex items-start gap-3 p-4 rounded-lg border bg-red-50 border-red-200 text-red-800">{{ $errors->first() }}</div>
            @endif

            <div class="mb-3">
                <label for="decision" class="block text-sm font-medium text-gray-700 mb-1 font-semibold">Decision <span class="text-red-600">*</span></label>
                <div class="flex gap-3">
                    <div class="flex items-center gap-2">
                        <input class="w-4 h-4 rounded border-gray-300 text-violet-600 focus:ring-violet-500" type="radio" name="decision" id="decision_approved" value="approved" {{ old('decision') === 'approved' ? 'checked' : '' }} required>
                        <label class="text-sm text-emerald-600 font-bold" for="decision_approved"><x-ui.icon name="check-circle" class="me-1" />Approve</label>
                    </div>
                    <div class="flex items-center gap-2">
                        <input class="w-4 h-4 rounded border-gray-300 text-violet-600 focus:ring-violet-500" type="radio" name="decision" id="decision_flagged" value="flagged" {{ old('decision') === 'flagged' ? 'checked' : '' }}>
                        <label class="text-sm text-amber-600 font-bold" for="decision_flagged"><x-ui.icon name="flag" class="me-1" />Flag for Review</label>
                    </div>
                    <div class="flex items-center gap-2">
                        <input class="w-4 h-4 rounded border-gray-300 text-violet-600 focus:ring-violet-500" type="radio" name="decision" id="decision_rejected" value="rejected" {{ old('decision') === 'rejected' ? 'checked' : '' }}>
                        <label class="text-sm text-red-600 font-bold" for="decision_rejected"><x-ui.icon name="x-circle" class="me-1" />Reject</label>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="side_notes" class="block text-sm font-medium text-gray-700 mb-1 font-semibold">
                    <x-ui.icon name="alert-triangle" class="text-amber-600 me-1" />Side Notes (visible to parents)
                </label>
                <textarea class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500" id="side_notes" name="side_notes" rows="3" maxlength="5000" placeholder="e.g. This technique should not be used during the first trimester or by women with pre-eclampsia...">{{ old('side_notes') }}</textarea>
                <div class="mt-1 text-sm text-[var(--color-text-muted)]">These notes will appear as yellow warning boxes when parents view this content.</div>
            </div>

            <div class="mb-4">
                <label for="internal_notes" class="block text-sm font-medium text-gray-700 mb-1 font-semibold">Internal Notes (admin only)</label>
                <textarea class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500" id="internal_notes" name="internal_notes" rows="2" maxlength="5000" placeholder="Notes for the admin team only...">{{ old('internal_notes') }}</textarea>
            </div>

            <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed bg-violet-600 text-white hover:bg-violet-700 px-5 py-3 text-lg">
                <x-ui.icon name="send" class="me-2" />Submit Review
            </button>
        </form>
    </div>
@endif
@endsection
