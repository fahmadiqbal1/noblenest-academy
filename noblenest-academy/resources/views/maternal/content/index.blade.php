@extends('layouts.maternal')

@section('title', 'Wellness Content — Maternal Wellness')

@section('content')
<div class="container py-4">
    <div class="flex flex-wrap">
        <div class="lg:w-3/12 hidden lg:block">
            @include('maternal.partials.sidebar')
        </div>
        <div class="lg:w-9/12">
            <div class="flex justify-between items-center mb-4">
                <h3 class="mb-0" style="font-family:'Baloo 2',sans-serif; color:var(--nn-text);">
                    <x-ui.icon name="circle-play" class="me-2" style="color:#7C3AED;" /> Wellness Content
                </h3>
            </div>

            {{-- Filters --}}
            <form method="GET" class="bg-white rounded-xl border border-gray-200 shadow-sm border-0 mb-4 p-3" style="background:rgba(255,255,255,0.82); border-radius:1rem;">
                <div class="flex flex-wrap gap-2 items-end">
                    <div class="md:w-4/12">
                        <label class="block text-sm font-medium text-gray-700 mb-1 font-semibold">Category</label>
                        <select name="category" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500 px-2 py-1 text-sm rounded-lg">
                            <option value="">All Categories</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $cat)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:w-3/12">
                        <label class="block text-sm font-medium text-gray-700 mb-1 font-semibold">Culture</label>
                        <select name="culture" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500 px-2 py-1 text-sm rounded-lg">
                            <option value="">All</option>
                            <option value="chinese" {{ request('culture') === 'chinese' ? 'selected' : '' }}>Chinese</option>
                            <option value="japanese" {{ request('culture') === 'japanese' ? 'selected' : '' }}>Japanese</option>
                            <option value="ayurvedic" {{ request('culture') === 'ayurvedic' ? 'selected' : '' }}>Ayurvedic</option>
                            <option value="general" {{ request('culture') === 'general' ? 'selected' : '' }}>General</option>
                        </select>
                    </div>
                    <div class="md:w-3/12">
                        <label class="block text-sm font-medium text-gray-700 mb-1 font-semibold">Search</label>
                        <input type="text" name="q" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500 px-2 py-1 text-sm rounded-lg" value="{{ request('q') }}" placeholder="Search...">
                    </div>
                    <div class="md:w-2/12">
                        <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-3 py-1.5 text-sm w-full rounded-full" style="background:var(--nn-primary); color:#fff;">Filter</button>
                    </div>
                </div>
            </form>

            {{-- Content grid --}}
            <div class="flex flex-wrap gap-3">
                @forelse($contents as $item)
                <div class="md:w-6/12 xl:w-4/12">
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm h-full border-0" style="background:rgba(255,255,255,0.82); border-radius:1rem; box-shadow:4px 4px 12px rgba(124,58,237,0.06);">
                        @if($item->thumbnail_url)
                            <img src="{{ $item->thumbnail_url }}" class="w-full rounded-t-xl" alt="{{ $item->title }}" style="border-radius:1rem 1rem 0 0; height:140px; object-fit:cover;">
                        @endif
                        <div class="p-5 p-3">
                            <div class="flex gap-2 mb-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium" style="background:var(--nn-primary-soft); color:var(--nn-primary); font-size:0.7rem;">{{ ucfirst($item->content_type) }}</span>
                                @if($item->cultural_origin)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium" style="background:#FEF3C7; color:#92400E; font-size:0.7rem;">{{ ucfirst($item->cultural_origin) }}</span>
                                @endif
                            </div>
                            <h6 class="mb-1" style="font-family:'Baloo 2',sans-serif;">{{ $item->title }}</h6>
                            <p class="text-sm text-[var(--color-text-muted)] mb-2">{{ Str::limit($item->benefit_explanation, 80) }}</p>
                            <a href="{{ route('maternal.content.show', $item) }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-3 py-1.5 text-sm rounded-full" style="background:var(--nn-primary-soft); color:var(--nn-primary);">
                                View <x-ui.icon name="arrow-right" class="ms-1" />
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="w-full">
                    <p class="text-[var(--color-text-muted)] text-center py-4">No content found matching your filters.</p>
                </div>
                @endforelse
            </div>

            <div class="mt-4">{{ $contents->withQueryString()->links() }}</div>
        </div>
    </div>
</div>
@endsection
