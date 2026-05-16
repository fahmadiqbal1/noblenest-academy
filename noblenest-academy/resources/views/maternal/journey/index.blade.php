@extends('layouts.maternal')

@section('title', 'My Journey — Maternal Wellness')

@section('content')
<div class="container py-4">
    <div class="flex flex-wrap">
        <div class="lg:w-3/12 hidden lg:block">
            @include('maternal.partials.sidebar')
        </div>
        <div class="lg:w-9/12">
            <h3 class="mb-4" style="font-family:'Baloo 2',sans-serif; color:var(--nn-text);">
                <x-ui.icon name="calendar" class="me-2" style="color:#EC4899;" /> Your Pregnancy Journey
            </h3>

            {{-- Trimester tabs --}}
            <div class="flex gap-2 mb-4 flex-wrap">
                @foreach([1 => 'Trimester 1 (Weeks 1-12)', 2 => 'Trimester 2 (Weeks 13-27)', 3 => 'Trimester 3 (Weeks 28-40)', 4 => 'Postnatal'] as $tri => $label)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium px-3 py-2 {{ $profile->trimester == $tri ? 'text-white' : '' }}"
                          style="{{ $profile->trimester == $tri ? 'background:linear-gradient(135deg, #EC4899, #F472B6);' : 'background:var(--nn-primary-soft); color:var(--nn-primary);' }}">
                        {{ $label }}
                    </span>
                @endforeach
            </div>

            {{-- Week grid --}}
            <div class="flex flex-wrap gap-2">
                @foreach($weeks as $w)
                    @php
                        $bgStyle = $w['is_current']
                            ? 'background:linear-gradient(135deg, #EC4899, #F472B6); color:#fff; border:none;'
                            : ($w['is_past']
                                ? 'background:#E0E7FF; color:#4338CA; border:none;'
                                : 'background:rgba(255,255,255,0.7); color:var(--nn-text-muted); border:1px solid var(--nn-border);');
                    @endphp
                    <div class="w-3/12 sm:w-2/12 md:w-auto" style="min-width:60px;">
                        <a href="{{ route('maternal.journey.week', $w['week']) }}"
                           class="block text-center no-underline p-2 rounded-lg {{ $w['is_current'] ? 'shadow-sm' : '' }}"
                           style="{{ $bgStyle }}">
                            <div class="text-sm font-semibold">W{{ $w['week'] }}</div>
                        </a>
                    </div>
                @endforeach
            </div>

            <div class="text-center mt-4">
                <a href="{{ route('maternal.journey.week', $currentWeek) }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed rounded-full" style="background:linear-gradient(135deg, #EC4899, #F472B6); color:#fff;">
                    Go to Current Week ({{ $currentWeek }}) <x-ui.icon name="arrow-right" class="ms-1" />
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
