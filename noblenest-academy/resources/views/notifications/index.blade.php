@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="container py-4" style="max-width: 680px;">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h2 class="h4 mb-0">Notifications</h2>
        @if($notifications->isNotEmpty())
        <form action="{{ route('notifications.read-all') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-secondary">Mark all read</button>
        </form>
        @endif
    </div>

    @forelse($notifications as $n)
    <div class="card border-0 mb-2 shadow-sm {{ $n->is_read ? 'opacity-75' : '' }}">
        <div class="card-body d-flex align-items-start gap-3 py-3">
            <span class="fs-4 mt-1">
                @switch($n->type)
                    @case('badge_earned') 🏅 @break
                    @case('streak_milestone') 🔥 @break
                    @case('activity_complete') ✅ @break
                    @case('teacher_approved') 🎓 @break
                    @case('payout_processed') 💳 @break
                    @default 🔔
                @endswitch
            </span>
            <div class="flex-fill">
                @if(!empty($n->data['title']))
                <div class="fw-semibold">{{ $n->data['title'] }}</div>
                @endif
                @if(!empty($n->data['body']))
                <div class="text-muted small">{{ $n->data['body'] }}</div>
                @endif
                <div class="text-muted small mt-1">{{ $n->created_at->diffForHumans() }}</div>
            </div>
            @unless($n->is_read)
            <form action="{{ route('notifications.read', $n->id) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-sm btn-link text-muted p-0" title="Mark as read">✓</button>
            </form>
            @endunless
        </div>
    </div>
    @empty
    <div class="text-center py-5 text-muted">
        <p class="fs-2 mb-2">🔔</p>
        <p>You're all caught up!</p>
    </div>
    @endforelse

    <div class="mt-3">
        {{ $notifications->links() }}
    </div>
</div>
@endsection
