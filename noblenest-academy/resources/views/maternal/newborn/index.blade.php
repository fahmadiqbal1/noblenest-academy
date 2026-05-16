@extends('layouts.maternal')

@section('title', 'Newborn Care — Maternal Wellness')

@section('content')
<div class="container py-4">
    <div class="flex flex-wrap">
        <div class="lg:w-3/12 hidden lg:block">
            @include('maternal.partials.sidebar')
        </div>
        <div class="lg:w-9/12">
            <h3 class="mb-2" style="font-family:'Baloo 2',sans-serif; color:var(--nn-text);">
                <x-ui.icon name="smile" class="me-2" style="color:#F59E0B;" /> Newborn Care & Training
            </h3>
            <p class="text-[var(--color-text-muted)] mb-4">Everything you need to know about caring for your newborn — from bathing and swaddling to grooming and sleep training.</p>

            <div class="flex flex-wrap gap-3">
                @forelse($content as $item)
                <div class="md:w-6/12 xl:w-4/12">
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm h-full border-0" style="background:rgba(255,255,255,0.82); border-radius:1rem; box-shadow:4px 4px 12px rgba(124,58,237,0.06);">
                        @if($item->thumbnail_url)
                            <img src="{{ $item->thumbnail_url }}" class="w-full rounded-t-xl" alt="{{ $item->title }}" style="border-radius:1rem 1rem 0 0; height:140px; object-fit:cover;">
                        @endif
                        <div class="p-5 p-3">
                            <h6 class="mb-1" style="font-family:'Baloo 2',sans-serif;">{{ $item->title }}</h6>
                            <p class="text-sm text-[var(--color-text-muted)] mb-2">{{ Str::limit($item->benefit_explanation, 100) }}</p>
                            @if($item->skills_improved)
                                <div class="flex flex-wrap gap-1 mb-2">
                                    @foreach(array_slice($item->skills_improved, 0, 2) as $skill)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium" style="background:#ECFDF5; color:#065F46; font-size:0.65rem;">{{ $skill }}</span>
                                    @endforeach
                                </div>
                            @endif
                            <a href="{{ route('maternal.content.show', $item) }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-3 py-1.5 text-sm rounded-full" style="background:var(--nn-primary-soft); color:var(--nn-primary);">
                                Learn More <x-ui.icon name="arrow-right" class="ms-1" />
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="w-full">
                    <p class="text-[var(--color-text-muted)] text-center py-4">Newborn care content coming soon.</p>
                </div>
                @endforelse
            </div>

            <div class="mt-4">{{ $content->links() }}</div>
        </div>
    </div>
</div>
@endsection
