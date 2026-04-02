@extends('layouts.app')

@section('title', '{{ $child->name }}\'s Milestones')

@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('parent.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('parent.child', $child) }}">{{ $child->name }}</a></li>
            <li class="breadcrumb-item active">Milestones</li>
        </ol>
    </nav>

    <h2 class="mb-1">🌱 Developmental Milestones</h2>
    <p class="text-muted mb-4">Track {{ $child->name }}'s growth and celebrate every achievement</p>

    @forelse($milestones->groupBy('domain') as $domain => $items)
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white fw-semibold text-capitalize">
            {{ $domain }}
        </div>
        <ul class="list-group list-group-flush">
            @foreach($items as $milestone)
            <li class="list-group-item d-flex align-items-start gap-3 py-3">
                <form action="{{ route('parent.milestone.toggle', [$child, $milestone]) }}" method="POST" class="mt-1">
                    @csrf
                    <button type="submit" class="btn btn-sm {{ $milestone->completed ? 'btn-success' : 'btn-outline-secondary' }}" title="{{ $milestone->completed ? 'Mark incomplete' : 'Mark complete' }}">
                        {{ $milestone->completed ? '✓' : '○' }}
                    </button>
                </form>
                <div>
                    <div class="fw-semibold">{{ $milestone->title }}</div>
                    @if($milestone->description)
                    <small class="text-muted">{{ $milestone->description }}</small>
                    @endif
                    <div class="mt-1">
                        <span class="badge bg-light text-dark border">{{ $milestone->age_months_min }}–{{ $milestone->age_months_max }} months</span>
                    </div>
                </div>
            </li>
            @endforeach
        </ul>
    </div>
    @empty
    <div class="text-center py-5 text-muted">
        <p class="fs-4">No milestones available for this age yet.</p>
        <p>We're always adding more!</p>
    </div>
    @endforelse
</div>
@endsection
