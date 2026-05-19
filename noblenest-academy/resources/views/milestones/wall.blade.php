@extends('layouts.marketing')
@section('meta_title', 'Milestone Wall – NobleNest Community')
@section('meta_description', "Celebrate children's milestones together. A community wall of learning achievements from families worldwide.")

@section('content')
<div class="container py-5" style="max-width:900px">
    <div class="text-center mb-5">
        <div style="font-size:3rem;line-height:1">🏆</div>
        <h1 class="font-bold mt-2 mb-1">{{ __('activities.milestone_wall') }}</h1>
        <p class="text-[var(--color-text-muted)]">{{ __('activities.milestone_wall_intro') }}</p>
    </div>

    {{-- Filter bar --}}
    <div class="flex flex-wrap gap-2 justify-center mb-5">
        <a href="{{ route('milestones.wall') }}"
           class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium px-3 py-2 no-underline text-base @if(!request('domain')) bg-gray-900 text-white @else bg-gray-50 text-gray-900 @endif">
            {{ __('activities.milestone_filter_all') }}
        </a>
        @foreach(['literacy','numeracy','creativity','social','motor','stem'] as $domain)
            <a href="{{ route('milestones.wall', ['domain' => $domain]) }}"
               class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium px-3 py-2 no-underline text-base @if(request('domain') === $domain) bg-gray-900 text-white @else bg-gray-50 text-gray-900 @endif">
                {{ ucfirst($domain) }}
            </a>
        @endforeach
    </div>

    {{-- Wall grid --}}
    @if($achievements->isEmpty())
        <div class="text-center py-5">
            <p class="text-[var(--color-text-muted)]">{{ __('activities.no_milestones') }}</p>
        </div>
    @else
        <div class="flex flex-wrap gap-3">
            @foreach($achievements as $achievement)
                @php
                    $milestone = $achievement->achievable;
                    $child = $achievement->child;
                @endphp
                @if(!$milestone || !$child) @continue @endif
                <div class="sm:w-6/12 md:w-4/12">
                    <div class="milestone-card h-full">
                        <div class="milestone-card__confetti">🎉</div>
                        <div class="milestone-card__child">{{ $child->name }}</div>
                        <div class="milestone-card__age">{{ $child->age_display }}</div>
                        <div class="milestone-card__milestone">{{ $milestone->name }}</div>
                        @if($milestone->domain)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-sky-50 text-sky-600 mt-1" style="font-size:0.65rem">
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
        <div class="mt-5 p-4 rounded-xl text-center bg-gray-50 border">
            <p class="font-bold mb-2">{{ __('activities.milestone_share_prompt') }}</p>
            <a href="{{ route('parent.milestones') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed bg-gray-900 text-white hover:bg-gray-800 rounded-full">
                {{ __('activities.track_milestones') }}
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
