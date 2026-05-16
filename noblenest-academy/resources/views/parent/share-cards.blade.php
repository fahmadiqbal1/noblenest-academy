@extends('layouts.parent')

@section('title', 'Share Cards')

@section('content')
<div class="container py-5">
    <h1 class="h2 font-bold mb-4">Share Cards</h1>
    <p class="text-[var(--color-text-muted)] mb-4">Celebrate your child's achievements! Share these cards with family and friends.</p>

    @if($cards->isEmpty())
    <div class="text-center py-5">
        <div class="text-7xl font-bold mb-3">🎉</div>
        <h3 class="font-bold">No share cards yet!</h3>
        <p class="text-[var(--color-text-muted)]">Complete activities with your child to earn share cards.</p>
        <a href="{{ route('parent.dashboard') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed bg-violet-600 text-white hover:bg-violet-700">Go to Dashboard</a>
    </div>
    @else
    <div class="flex flex-wrap gap-4">
        @foreach($cards as $card)
        <div class="md:w-6/12 lg:w-4/12">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm border-0 overflow-hidden">
                @if($card->image_url)
                <img src="{{ $card->image_url }}" alt="Share card" class="w-full rounded-t-xl" style="aspect-ratio:1.91/1;object-fit:cover;">
                @endif
                <div class="p-5">
                    <p class="text-[var(--color-text-muted)] text-sm">
                        {{ $card->child->name ?? '' }} · {{ $card->created_at->diffForHumans() }}
                    </p>
                    <div class="flex gap-2">
                        <a href="{{ route('share.card', $card) }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-3 py-1.5 text-sm border-2 border-violet-600 text-violet-600 hover:bg-violet-600 hover:text-white" target="_blank">View Card</a>
                        <a href="https://wa.me/?text={{ urlencode(route('share.card', $card)) }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-3 py-1.5 text-sm bg-emerald-600 text-white hover:bg-emerald-700" target="_blank">WhatsApp</a>
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
