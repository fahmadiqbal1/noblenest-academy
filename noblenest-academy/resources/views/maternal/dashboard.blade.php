@extends('layouts.app')

@section('title', 'Maternal Wellness — Noble Nest Academy')
@section('meta_description', 'Your personalized maternal wellness journey with ancient techniques, nutrition plans, and guided exercises.')

@section('content')
<div class="container py-4">
    <div class="row">
        {{-- Sidebar --}}
        <div class="col-lg-3 d-none d-lg-block">
            @include('maternal.partials.sidebar')
        </div>

        {{-- Main content --}}
        <div class="col-lg-9">
            {{-- Stage Banner --}}
            <div class="card border-0 mb-4" style="background:linear-gradient(135deg, #EC4899, #F472B6); border-radius:1.25rem; color:#fff;">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2 class="h4 mb-1" style="font-family:'Baloo 2',sans-serif;">Welcome back 💐</h2>
                            <p class="mb-0 opacity-90">
                                Week <strong>{{ $profile->current_week }}</strong> · Trimester {{ $profile->trimester }}
                                @if($profile->due_date)
                                    · Due {{ \Carbon\Carbon::parse($profile->due_date)->format('M j, Y') }}
                                @endif
                            </p>
                            <p class="mb-0 mt-1 opacity-75">
                                <i class="bi bi-check-circle me-1"></i> {{ $completedCount }} activities completed
                            </p>
                        </div>
                        <div class="col-md-4 text-md-end mt-3 mt-md-0">
                            <a href="{{ route('maternal.journey') }}" class="btn btn-light btn-sm rounded-pill fw-semibold">
                                <i class="bi bi-calendar-week me-1"></i> View Journey
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Emergency Signs Alert (if applicable) --}}
            @if($emergencySigns->isNotEmpty())
            <div class="alert border-0 mb-4" style="background:#FEF2F2; border-radius:1rem; border-left:4px solid #EF4444 !important;">
                <h6 class="text-danger mb-2"><i class="bi bi-exclamation-triangle-fill me-1"></i> Know Your Emergency Signs</h6>
                <ul class="mb-1 small">
                    @foreach($emergencySigns->take(3) as $sign)
                        <li>{{ $sign->symptom }} — <strong>{{ $sign->action_text }}</strong></li>
                    @endforeach
                </ul>
                <a href="{{ route('maternal.emergency-signs') }}" class="small text-danger fw-semibold">View all emergency signs →</a>
            </div>
            @endif

            {{-- Recommended Content --}}
            <h5 class="mb-3" style="font-family:'Baloo 2',sans-serif; color:var(--nn-text);">
                <i class="bi bi-stars me-1" style="color:#F59E0B;"></i> Recommended for You
            </h5>
            <div class="row g-3 mb-4">
                @forelse($recommended as $item)
                <div class="col-md-6 col-xl-4">
                    <div class="card h-100 border-0" style="background:rgba(255,255,255,0.82); border-radius:1rem; box-shadow:4px 4px 12px rgba(124,58,237,0.06);">
                        @if($item->thumbnail_url)
                            <img src="{{ $item->thumbnail_url }}" class="card-img-top" alt="{{ $item->title }}" style="border-radius:1rem 1rem 0 0; height:140px; object-fit:cover;">
                        @endif
                        <div class="card-body p-3">
                            <div class="d-flex gap-2 mb-2">
                                <span class="badge rounded-pill" style="background:var(--nn-primary-soft); color:var(--nn-primary); font-size:0.7rem;">{{ ucfirst($item->content_type) }}</span>
                                @if($item->cultural_origin)
                                    <span class="badge rounded-pill" style="background:#FEF3C7; color:#92400E; font-size:0.7rem;">{{ ucfirst($item->cultural_origin) }}</span>
                                @endif
                            </div>
                            <h6 class="mb-1" style="font-family:'Baloo 2',sans-serif;">{{ $item->title }}</h6>
                            <p class="small text-muted mb-2">{{ Str::limit($item->benefit_explanation, 80) }}</p>
                            <a href="{{ route('maternal.content.show', $item) }}" class="btn btn-sm rounded-pill fw-semibold" style="background:var(--nn-primary-soft); color:var(--nn-primary);">
                                View <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <p class="text-muted">Content coming soon for your current stage. Check back shortly!</p>
                </div>
                @endforelse
            </div>

            {{-- Recent Journal Entries --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0" style="font-family:'Baloo 2',sans-serif; color:var(--nn-text);">
                    <i class="bi bi-journal-richtext me-1" style="color:#7C3AED;"></i> Recent Journal
                </h5>
                <a href="{{ route('maternal.journal.create') }}" class="btn btn-sm rounded-pill fw-semibold" style="background:linear-gradient(135deg, #EC4899, #F472B6); color:#fff;">
                    <i class="bi bi-plus me-1"></i> New Entry
                </a>
            </div>
            @forelse($recentJournals as $entry)
            <div class="card border-0 mb-2" style="background:rgba(255,255,255,0.82); border-radius:1rem;">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <span class="fw-semibold">{{ \Carbon\Carbon::parse($entry->entry_date)->format('M j') }}</span>
                        <span class="mx-2 text-muted">·</span>
                        <span>Mood: {{ ucfirst($entry->mood) }}</span>
                        <span class="mx-2 text-muted">·</span>
                        <span>Energy: {{ $entry->energy_level }}/5</span>
                        @if($entry->baby_kicks)
                            <span class="mx-2 text-muted">·</span>
                            <span>{{ $entry->baby_kicks }} kicks</span>
                        @endif
                    </div>
                    <a href="{{ route('maternal.journal.show', $entry) }}" class="text-primary"><i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
            @empty
            <p class="text-muted small">No journal entries yet. Start tracking your wellness today!</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
