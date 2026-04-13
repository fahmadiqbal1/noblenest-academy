@extends('layouts.app')

@section('title', 'Parent Dashboard — Noble Nest Academy')

@section('content')
<div class="container py-4">
    @php
        $hour = (int) now()->format('H');
        $greeting = match(true) {
            $hour < 12 => 'Good morning',
            $hour < 17 => 'Good afternoon',
            default     => 'Good evening',
        };
        $greetEmoji = match(true) {
            $hour < 12 => '☀️',
            $hour < 17 => '🌤️',
            default     => '🌙',
        };
    @endphp
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0" style="font-family:'Baloo 2',sans-serif; color:var(--nn-text, #1E1B4B);">{{ $greeting }}, {{ Auth::user()->name }} {{ $greetEmoji }}</h1>
            <p class="text-muted mb-0">Here's what your children have been up to</p>
        </div>
        @unless($hasSubscription)
        <a href="{{ route('pricing') }}" class="btn btn-warning fw-bold rounded-pill px-4">
            <i class="bi bi-star-fill me-1"></i>Upgrade to Premium
        </a>
        @endunless
    </div>

    {{-- Children Cards --}}
    <div class="row g-4 mb-5">
        @forelse($children as $child)
        <div class="col-md-6 col-lg-4">
            @php
                $tierKey = match($child->age_bracket ?? 'learner') {
                    'baby'       => 'baby',
                    'toddler'    => 'toddler',
                    'preschool'  => 'preschool',
                    'school-age' => 'school',
                    default      => 'default',
                };
            @endphp
            <div class="nn-child-card h-100">
                {{-- Tier-coloured header strip --}}
                <div class="nn-child-card__header nn-tier-{{ $tierKey }}">
                    <div class="d-flex align-items-center gap-3">
                        <div class="nn-child-card__avatar d-flex align-items-center justify-content-center fw-bold"
                             style="width:52px;height:52px;font-size:1.3rem;background:rgba(255,255,255,0.30);">
                            {{ mb_strtoupper(mb_substr($child->name, 0, 1)) }}
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold" style="font-family:'Baloo 2',sans-serif;">{{ $child->name }}</h5>
                            <small style="opacity:0.8;">{{ $child->age_display ?? 'Learner' }} &middot; {{ ucfirst($child->age_bracket ?? 'learner') }}</small>
                        </div>
                    </div>
                </div>

                <div class="p-3">
                    {{-- Stats row --}}
                    <div class="row text-center g-2 mb-3">
                        <div class="col-4">
                            <div class="nn-child-card__stat">
                                <span class="nn-child-card__stat-value text-primary">{{ $child->activity_progress_count }}</span>
                                <span class="nn-child-card__stat-label">Activities</span>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="nn-child-card__stat">
                                <span class="nn-child-card__stat-value" style="color:var(--nn-accent, #F59E0B);">{{ $child->streak_days ?? 0 }}</span>
                                <span class="nn-child-card__stat-label">Day streak</span>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="nn-child-card__stat">
                                <span class="nn-child-card__stat-value text-success">{{ $child->age_months ? floor($child->age_months / 12) : '?' }}</span>
                                <span class="nn-child-card__stat-label">Age (yrs)</span>
                            </div>
                        </div>
                    </div>

                    @if($hasSubscription && isset($subscription))
                    <div class="mb-3 p-2 rounded-3" style="background:var(--nn-primary-soft);">
                        <div class="small fw-semibold mb-1"><i class="bi bi-calendar-week text-primary"></i> Week {{ $subscription->currentWeek() }} of 4</div>
                        <div class="progress rounded-pill" style="height:6px;">
                            <div class="progress-bar bg-primary" style="width:{{ ($subscription->currentWeek() / 4) * 100 }}%"></div>
                        </div>
                    </div>
                    @endif

                    <div class="nn-child-card__actions">
                        <a href="{{ route('child.dashboard', $child) }}" class="btn btn-sm btn-warning flex-fill fw-bold rounded-pill">
                            <i class="bi bi-controller me-1"></i>Dashboard
                        </a>
                        <a href="{{ route('child.activities', $child) }}" class="btn btn-sm btn-primary flex-fill fw-bold rounded-pill">
                            <i class="bi bi-rocket-takeoff me-1"></i>Activities
                        </a>
                        <a href="{{ route('parent.child', $child) }}" class="btn btn-sm btn-outline-secondary flex-fill rounded-pill">
                            <i class="bi bi-graph-up me-1"></i>Progress
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="text-center py-5 glass-panel">
                <i class="bi bi-person-plus-fill display-4 mb-3 d-block" style="color:var(--nn-primary);opacity:0.5;"></i>
                <h5 style="font-family:'Baloo 2',sans-serif; color:var(--nn-text);">Add your first child</h5>
                <p class="text-muted">Create a child profile to start their learning journey.</p>
                <a href="{{ route('children.create') }}" class="btn btn-primary rounded-pill px-4">
                    <i class="bi bi-plus-circle me-1"></i>Add Child
                </a>
            </div>
        </div>
        @endforelse
    </div>

    {{-- Recent Activity --}}
    @if($recentActivity->isNotEmpty())
    <h4 class="mb-3" style="font-family:'Baloo 2',sans-serif; color:var(--nn-text, #1E1B4B);">
        <i class="bi bi-clock-history me-2 text-primary"></i>Recent Activity
    </h4>
    <div class="list-group rounded-3 overflow-hidden" style="box-shadow:0 4px 16px rgba(124,58,237,0.07);">
        @foreach($recentActivity as $item)
        <div class="list-group-item border-0 d-flex align-items-center gap-3 py-3">
            <div class="d-flex align-items-center justify-content-center rounded-3"
                 style="width:44px;height:44px;min-width:44px;background:var(--nn-primary-soft);color:var(--nn-primary);">
                <i class="bi bi-book-open-fill"></i>
            </div>
            <div class="flex-fill">
                <div class="fw-semibold" style="color:var(--nn-text);">{{ $item->activity->title ?? 'Activity' }}</div>
                <small class="text-muted">{{ $item->childProfile->name }} &middot; {{ $item->completed_at?->diffForHumans() }}</small>
            </div>
            @if($item->childProfile->share_card_url)
            <a href="{{ $item->childProfile->share_card_url }}" target="_blank" rel="noopener"
               class="btn btn-sm btn-outline-primary rounded-pill">
                <i class="bi bi-share me-1"></i>Share
            </a>
            @endif
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection
