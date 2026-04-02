@extends('layouts.app')

@section('title', 'Share Cards')

@section('content')
<div class="container py-5">
    <h1 class="h2 fw-bold mb-4">Share Cards</h1>
    <p class="text-muted mb-4">Celebrate your child's achievements! Share these cards with family and friends.</p>

    @if($cards->isEmpty())
    <div class="text-center py-5">
        <div class="display-1 mb-3">🎉</div>
        <h3 class="fw-bold">No share cards yet!</h3>
        <p class="text-muted">Complete activities with your child to earn share cards.</p>
        <a href="{{ route('parent.dashboard') }}" class="btn btn-primary">Go to Dashboard</a>
    </div>
    @else
    <div class="row g-4">
        @foreach($cards as $card)
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm overflow-hidden">
                @if($card->image_url)
                <img src="{{ $card->image_url }}" alt="Share card" class="card-img-top" style="aspect-ratio:1.91/1;object-fit:cover;">
                @endif
                <div class="card-body">
                    <p class="card-text text-muted small">
                        {{ $card->child->name ?? '' }} · {{ $card->created_at->diffForHumans() }}
                    </p>
                    <div class="d-flex gap-2">
                        <a href="{{ route('share.card', $card) }}" class="btn btn-sm btn-outline-primary" target="_blank">View Card</a>
                        <a href="https://wa.me/?text={{ urlencode(route('share.card', $card)) }}" class="btn btn-sm btn-success" target="_blank">WhatsApp</a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div class="mt-4">{{ $cards->links() }}</div>
    @endif
</div>
@endsection
