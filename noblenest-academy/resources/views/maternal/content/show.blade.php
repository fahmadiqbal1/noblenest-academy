@extends('layouts.maternal')

@section('title', $maternalContent->title . ' — Maternal Wellness')
@section('meta_description', Str::limit($maternalContent->benefit_explanation, 160))

@section('content')
<div class="container py-4">
    <div class="flex flex-wrap">
        <div class="lg:w-3/12 hidden lg:block">
            @include('maternal.partials.sidebar')
        </div>
        <div class="lg:w-9/12">
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="flex items-center gap-2 text-sm flex-wrap">
                    <li class=""><a href="{{ route('maternal.content.index') }}">Content</a></li>
                    <li class="active">{{ $maternalContent->title }}</li>
                </ol>
            </nav>

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm border-0 mb-4" style="background:rgba(255,255,255,0.88); border-radius:1.25rem; box-shadow:8px 8px 16px rgba(124,58,237,0.08);">
                {{-- Header image/video --}}
                @if($maternalContent->video_url)
                    <div class="ratio ratio-16x9" style="border-radius:1.25rem 1.25rem 0 0; overflow:hidden;">
                        <video controls preload="metadata" poster="{{ $maternalContent->thumbnail_url }}">
                            <source src="{{ $maternalContent->video_url }}" type="video/mp4">
                        </video>
                    </div>
                @elseif($maternalContent->thumbnail_url)
                    <img src="{{ $maternalContent->thumbnail_url }}" class="w-full rounded-t-xl" alt="{{ $maternalContent->title }}" style="border-radius:1.25rem 1.25rem 0 0; max-height:300px; object-fit:cover;">
                @endif

                <div class="p-5 p-4">
                    {{-- Badges --}}
                    <div class="flex flex-wrap gap-2 mb-3">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium" style="background:var(--nn-primary-soft); color:var(--nn-primary);">{{ ucfirst($maternalContent->content_type) }}</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium" style="background:#E0E7FF; color:#4338CA;">{{ ucfirst(str_replace('_', ' ', $maternalContent->stage)) }}</span>
                        @if($maternalContent->cultural_origin)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium" style="background:#FEF3C7; color:#92400E;">{{ ucfirst($maternalContent->cultural_origin) }}</span>
                        @endif
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium" style="background:#ECFDF5; color:#065F46;">{{ ucfirst(str_replace('_', ' ', $maternalContent->category)) }}</span>
                    </div>

                    <h2 style="font-family:'Baloo 2',sans-serif; color:var(--nn-text);">{{ $maternalContent->title }}</h2>

                    {{-- Benefit explanation (always visible) --}}
                    <div class="flex items-start gap-3 p-4 rounded-lg border border-0 mb-4" style="background:linear-gradient(135deg, #ECFDF5, #D1FAE5); border-radius:1rem;">
                        <h6 class="mb-1"><x-ui.icon name="lightbulb" class="me-1" style="color:#059669;" /> Why This Matters</h6>
                        <p class="mb-0">{{ $maternalContent->benefit_explanation }}</p>
                    </div>

                    {{-- Practitioner side notes (warnings visible to parents) --}}
                    @if($maternalContent->sideNotes->isNotEmpty())
                    <div class="mb-4">
                        @foreach($maternalContent->sideNotes as $review)
                        <div class="flex items-start gap-3 p-4 rounded-lg border border-0 mb-2" style="background:linear-gradient(135deg, #FEF3C7, #FDE68A); border-radius:1rem; border-left: 4px solid #F59E0B !important;">
                            <h6 class="mb-1 font-bold" style="color:#92400E;"><x-ui.icon name="alert-triangle" class="me-1" /> Practitioner Note</h6>
                            <p class="mb-1">{{ $review->side_notes }}</p>
                            <small class="text-[var(--color-text-muted)]">— {{ ucfirst(str_replace('_', ' ', $review->credential_used)) }}, reviewed {{ $review->reviewed_at?->format('M j, Y') }}</small>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    {{-- Skills improved --}}
                    @if($maternalContent->skills_improved)
                    <div class="mb-4">
                        <h6 class="font-semibold"><x-ui.icon name="trending-up" class="me-1" /> Skills & Benefits</h6>
                        <div class="flex flex-wrap gap-2">
                            @foreach($maternalContent->skills_improved as $skill)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium px-3 py-2" style="background:#F3E8FF; color:#7C3AED;">{{ $skill }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Audio player --}}
                    @if($maternalContent->audio_url)
                    <div class="mb-4">
                        <h6 class="font-semibold"><x-ui.icon name="headphones" class="me-1" /> Listen</h6>
                        <audio controls class="w-full" preload="metadata">
                            <source src="{{ $maternalContent->audio_url }}" type="audio/mpeg">
                        </audio>
                    </div>
                    @endif

                    {{-- Main description --}}
                    <div class="mb-4 content-body">
                        {!! nl2br(e($maternalContent->description)) !!}
                    </div>

                    {{-- Step Player (animated slideshow) --}}
                    @if($maternalContent->steps->isNotEmpty() && $maternalContent->steps->contains(fn($s) => $s->visual_url || $s->audio_url))
                    <div class="mb-4">
                        <h5 class="font-semibold mb-3"><x-ui.icon name="circle-play" class="me-1" /> Guided Walkthrough</h5>
                        <x-step-player :steps="$maternalContent->steps" />
                    </div>
                    @endif

                    {{-- Steps --}}
                    @if($maternalContent->steps->isNotEmpty())
                    <div class="mb-4">
                        <h5 class="font-semibold mb-3"><x-ui.icon name="list-ordered" class="me-1" /> Steps</h5>
                        @foreach($maternalContent->steps as $step)
                        <div class="bg-white rounded-xl border border-gray-200 shadow-sm border-0 mb-2" style="background:rgba(124,58,237,0.04); border-radius:1rem;">
                            <div class="p-5 p-3">
                                <div class="flex gap-3">
                                    <div class="flex-shrink-0 flex items-center justify-center" style="width:32px;height:32px;border-radius:50%;background:var(--nn-primary);color:#fff;font-weight:700;font-size:0.85rem;">
                                        {{ $step->step_number }}
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ $step->title }}</h6>
                                        <p class="mb-0 text-sm text-[var(--color-text-muted)]">{{ $step->instruction }}</p>
                                        @if($step->image_url)
                                            <img src="{{ $step->image_url }}" class="mt-2 rounded-lg" style="max-height:200px;" alt="Step {{ $step->step_number }}">
                                        @endif
                                        @if($step->video_url)
                                            <video controls class="mt-2 rounded-lg w-full" preload="metadata" style="max-height:200px;">
                                                <source src="{{ $step->video_url }}" type="video/mp4">
                                            </video>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    {{-- Progress actions --}}
                    <div class="border-t pt-3">
                        @if(!$progress)
                            <form method="POST" action="{{ route('maternal.content.start', $maternalContent) }}">
                                @csrf
                                <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed rounded-full" style="background:linear-gradient(135deg, #EC4899, #F472B6); color:#fff;">
                                    <x-ui.icon name="play" class="me-1" /> Start Activity
                                </button>
                            </form>
                        @elseif(!$progress->completed_at)
                            <form method="POST" action="{{ route('maternal.content.complete', $maternalContent) }}">
                                @csrf
                                <div class="flex gap-3 items-end">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1 font-semibold">Rate this content</label>
                                        <select name="rating" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500 px-2 py-1 text-sm rounded-lg" style="width:120px;">
                                            <option value="">Skip</option>
                                            @for($i = 5; $i >= 1; $i--)
                                                <option value="{{ $i }}">{{ str_repeat('★', $i) }}{{ str_repeat('☆', 5-$i) }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                    <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed rounded-full" style="background:linear-gradient(135deg, #10B981, #34D399); color:#fff;">
                                        <x-ui.icon name="check-circle" class="me-1" /> Mark Complete
                                    </button>
                                </div>
                            </form>
                        @else
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium px-3 py-2" style="background:#ECFDF5; color:#065F46; font-size:0.85rem;">
                                    <x-ui.icon name="check-circle" class="me-1" /> Completed {{ $progress->completed_at->diffForHumans() }}
                                </span>
                                @if($progress->rating)
                                    <span class="text-amber-600">{{ str_repeat('★', $progress->rating) }}{{ str_repeat('☆', 5-$progress->rating) }}</span>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Related content --}}
            @if($related->isNotEmpty())
            <h5 class="mb-3" style="font-family:'Baloo 2',sans-serif;">Related Content</h5>
            <div class="flex flex-wrap gap-3">
                @foreach($related as $item)
                <div class="md:w-6/12 xl:w-3/12">
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm h-full border-0" style="background:rgba(255,255,255,0.82); border-radius:1rem;">
                        <div class="p-5 p-3">
                            <h6 class="mb-1" style="font-family:'Baloo 2',sans-serif; font-size:0.9rem;">{{ $item->title }}</h6>
                            <p class="text-sm text-[var(--color-text-muted)] mb-2">{{ Str::limit($item->benefit_explanation, 60) }}</p>
                            <a href="{{ route('maternal.content.show', $item) }}" class="text-sm font-semibold" style="color:var(--nn-primary);">View →</a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
