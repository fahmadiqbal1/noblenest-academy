@extends('layouts.app')
@section('meta_title', 'Milestone Wall – NobleNest Community')
@section('meta_description', 'Celebrate children's milestones together. A community wall of learning achievements from families worldwide.')

@section('content')
<div class="container py-5" style="max-width:900px">
    <div class="text-center mb-5">
        <div style="font-size:3rem;line-height:1">🏆</div>
        <h1 class="fw-bold mt-2 mb-1">Milestone Wall</h1>
        <p class="text-muted">Celebrating learning achievements from families around the world.</p>
    </div>

    {{-- Filter bar --}}
    <div class="d-flex flex-wrap gap-2 justify-content-center mb-5">
        <a href="{{ route('milestones.wall') }}"
           class="badge rounded-pill px-3 py-2 text-decoration-none fs-6 @if(!request('domain')) bg-dark text-white @else bg-light text-dark @endif">
            All
        </a>
        @foreach(['literacy','numeracy','creativity','social','motor','stem'] as $domain)
            <a href="{{ route('milestones.wall', ['domain' => $domain]) }}"
               class="badge rounded-pill px-3 py-2 text-decoration-none fs-6 @if(request('domain') === $domain) bg-dark text-white @else bg-light text-dark @endif">
                {{ ucfirst($domain) }}
            </a>
        @endforeach
    </div>

    {{-- Wall grid --}}
    @if($achievements->isEmpty())
        <div class="text-center py-5">
            <p class="text-muted">No milestones yet. Be the first to reach one!</p>
        </div>
    @else
        <div class="row g-3">
            @foreach($achievements as $achievement)
                @php
                    $milestone = $achievement->achievable;
                    $child = $achievement->child;
                @endphp
                @if(!$milestone || !$child) @continue @endif
                <div class="col-sm-6 col-md-4">
                    <div class="milestone-card h-100">
                        <div class="milestone-card__confetti">🎉</div>
                        <div class="milestone-card__child">{{ $child->name }}</div>
                        <div class="milestone-card__age">{{ $child->age_display }}</div>
                        <div class="milestone-card__milestone">{{ $milestone->name }}</div>
                        @if($milestone->domain)
                            <span class="badge bg-info-subtle text-info rounded-pill mt-1" style="font-size:0.65rem">
                                {{ ucfirst($milestone->domain) }}
                            </span>
                        @endif
                        <div class="milestone-card__date">{{ $achievement->achieved_at?->diffForHumans() }}</div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-4">{{ $achievements->links() }}</div>
    @endif

    {{-- Share CTA --}}
    @auth
        <div class="mt-5 p-4 rounded-4 text-center bg-light border">
            <p class="fw-bold mb-2">Has your child reached a milestone? Share it!</p>
            <a href="{{ route('parent.milestones') }}" class="btn btn-dark rounded-pill px-4">
                Track Milestones
            </a>
        </div>
    @endauth
</div>

<style>
.milestone-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 1.25rem; padding: 1.5rem; text-align: center; transition: transform 0.15s, box-shadow 0.15s; }
.milestone-card:hover { transform: translateY(-3px); box-shadow: 0 8px 32px rgba(0,0,0,0.09); }
.milestone-card__confetti { font-size: 2rem; }
.milestone-card__child { font-size: 1rem; font-weight: 700; color: #111827; margin-top: 0.5rem; }
.milestone-card__age { font-size: 0.72rem; color: #9ca3af; }
.milestone-card__milestone { font-size: 0.9rem; font-weight: 600; color: #374151; margin: 0.5rem 0; }
.milestone-card__date { font-size: 0.7rem; color: #9ca3af; margin-top: 0.5rem; }
</style>
@endsection
