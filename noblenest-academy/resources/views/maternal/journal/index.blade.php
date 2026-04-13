@extends('layouts.app')

@section('title', 'Journal — Maternal Wellness')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-lg-3 d-none d-lg-block">
            @include('maternal.partials.sidebar')
        </div>
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="mb-0" style="font-family:'Baloo 2',sans-serif; color:var(--nn-text);">
                    <i class="bi bi-journal-richtext me-2" style="color:#7C3AED;"></i> Wellness Journal
                </h3>
                <a href="{{ route('maternal.journal.create') }}" class="btn rounded-pill fw-semibold px-4" style="background:linear-gradient(135deg, #EC4899, #F472B6); color:#fff;">
                    <i class="bi bi-plus me-1"></i> New Entry
                </a>
            </div>

            @forelse($entries as $entry)
            <div class="card border-0 mb-3" style="background:rgba(255,255,255,0.82); border-radius:1rem; box-shadow:4px 4px 12px rgba(124,58,237,0.06);">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1" style="font-family:'Baloo 2',sans-serif;">
                                {{ \Carbon\Carbon::parse($entry->entry_date)->format('l, M j, Y') }}
                            </h6>
                            <div class="d-flex gap-3 small text-muted">
                                <span>Mood: <strong>{{ ucfirst($entry->mood) }}</strong></span>
                                <span>Energy: <strong>{{ $entry->energy_level }}/5</strong></span>
                                @if($entry->baby_kicks)
                                    <span>Kicks: <strong>{{ $entry->baby_kicks }}</strong></span>
                                @endif
                                @if($entry->weight_kg)
                                    <span>Weight: <strong>{{ $entry->weight_kg }}kg</strong></span>
                                @endif
                            </div>
                            @if($entry->notes)
                                <p class="small text-muted mt-2 mb-0">{{ Str::limit($entry->notes, 120) }}</p>
                            @endif
                        </div>
                        <a href="{{ route('maternal.journal.show', $entry) }}" class="btn btn-sm rounded-pill" style="background:var(--nn-primary-soft); color:var(--nn-primary);">
                            <i class="bi bi-eye"></i>
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-5">
                <div class="mb-3" style="font-size:3rem;">📔</div>
                <h5>Start Your Wellness Journal</h5>
                <p class="text-muted">Track your mood, symptoms, baby kicks, and more to stay connected with your pregnancy journey.</p>
                <a href="{{ route('maternal.journal.create') }}" class="btn rounded-pill fw-semibold px-4" style="background:linear-gradient(135deg, #EC4899, #F472B6); color:#fff;">
                    Create First Entry
                </a>
            </div>
            @endforelse

            <div class="mt-3">{{ $entries->links() }}</div>
        </div>
    </div>
</div>
@endsection
