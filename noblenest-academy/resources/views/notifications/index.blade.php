@extends('layouts.parent')

@section('title', 'Notifications')

@section('content')
<div class="container py-4" style="max-width: 680px;">
    <div class="flex items-center justify-between mb-4">
        <h2 class="h4 mb-0">Notifications</h2>
        @if($notifications->isNotEmpty())
        <form action="{{ route('notifications.read-all') }}" method="POST">
            @csrf
            <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-3 py-1.5 text-sm border-2 border-gray-300 text-gray-700 hover:bg-gray-100">Mark all read</button>
        </form>
        @endif
    </div>

    @forelse($notifications as $n)
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm border-0 mb-2 {{ $n->is_read ? 'opacity-75' : '' }}">
        <div class="p-5 flex items-start gap-3 py-3">
            <span class="text-2xl mt-1">
                @switch($n->type)
                    @case('badge_earned') 🏅 @break
                    @case('streak_milestone') 🔥 @break
                    @case('activity_complete') ✅ @break
                    @case('teacher_approved') 🎓 @break
                    @case('payout_processed') 💳 @break
                    @default 🔔
                @endswitch
            </span>
            <div class="flex-1">
                @if(!empty($n->data['title']))
                <div class="font-semibold">{{ $n->data['title'] }}</div>
                @endif
                @if(!empty($n->data['body']))
                <div class="text-[var(--color-text-muted)] text-sm">{{ $n->data['body'] }}</div>
                @endif
                <div class="text-[var(--color-text-muted)] text-sm mt-1">{{ $n->created_at->diffForHumans() }}</div>
            </div>
            @unless($n->is_read)
            <form action="{{ route('notifications.read', $n->id) }}" method="POST">
                @csrf
                <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-3 py-1.5 text-sm bg-transparent text-violet-600 hover:underline shadow-none text-[var(--color-text-muted)] p-0" title="Mark as read">✓</button>
            </form>
            @endunless
        </div>
    </div>
    @empty
    <div class="text-center py-5 text-[var(--color-text-muted)]">
        <p class="text-4xl mb-2">🔔</p>
        <p>You're all caught up!</p>
    </div>
    @endforelse

    <div class="mt-3">
        {{ $notifications->links() }}
    </div>
</div>
@endsection
