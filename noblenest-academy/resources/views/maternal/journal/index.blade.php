@extends('layouts.maternal')

@section('title', 'Journal — Maternal Wellness')

@section('content')
<div class="container py-4">
    <div class="flex flex-wrap">
        <div class="lg:w-3/12 hidden lg:block">
            @include('maternal.partials.sidebar')
        </div>
        <div class="lg:w-9/12">
            <div class="flex justify-between items-center mb-4">
                <h3 class="mb-0" style="font-family:'Baloo 2',sans-serif; color:var(--nn-text);">
                    <x-ui.icon name="notebook-pen" class="me-2" style="color:#7C3AED;" /> Wellness Journal
                </h3>
                <a href="{{ route('maternal.journal.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed rounded-full" style="background:linear-gradient(135deg, #EC4899, #F472B6); color:#fff;">
                    <x-ui.icon name="plus" class="me-1" /> New Entry
                </a>
            </div>

            @forelse($entries as $entry)
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm border-0 mb-3" style="background:rgba(255,255,255,0.82); border-radius:1rem; box-shadow:4px 4px 12px rgba(124,58,237,0.06);">
                <div class="p-5 p-3">
                    <div class="flex justify-between items-start">
                        <div>
                            <h6 class="mb-1" style="font-family:'Baloo 2',sans-serif;">
                                {{ \Carbon\Carbon::parse($entry->entry_date)->format('l, M j, Y') }}
                            </h6>
                            <div class="flex gap-3 text-sm text-[var(--color-text-muted)]">
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
                                <p class="text-sm text-[var(--color-text-muted)] mt-2 mb-0">{{ Str::limit($entry->notes, 120) }}</p>
                            @endif
                        </div>
                        <a href="{{ route('maternal.journal.show', $entry) }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-3 py-1.5 text-sm rounded-full" style="background:var(--nn-primary-soft); color:var(--nn-primary);">
                            <x-ui.icon name="eye" />
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-5">
                <div class="mb-3" style="font-size:3rem;">📔</div>
                <h5>Start Your Wellness Journal</h5>
                <p class="text-[var(--color-text-muted)]">Track your mood, symptoms, baby kicks, and more to stay connected with your pregnancy journey.</p>
                <a href="{{ route('maternal.journal.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed rounded-full" style="background:linear-gradient(135deg, #EC4899, #F472B6); color:#fff;">
                    Create First Entry
                </a>
            </div>
            @endforelse

            <div class="mt-3">{{ $entries->links() }}</div>
        </div>
    </div>
</div>
@endsection
