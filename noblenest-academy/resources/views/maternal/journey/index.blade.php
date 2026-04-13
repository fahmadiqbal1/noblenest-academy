@extends('layouts.app')

@section('title', 'My Journey — Maternal Wellness')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-lg-3 d-none d-lg-block">
            @include('maternal.partials.sidebar')
        </div>
        <div class="col-lg-9">
            <h3 class="mb-4" style="font-family:'Baloo 2',sans-serif; color:var(--nn-text);">
                <i class="bi bi-calendar-week me-2" style="color:#EC4899;"></i> Your Pregnancy Journey
            </h3>

            {{-- Trimester tabs --}}
            <div class="d-flex gap-2 mb-4 flex-wrap">
                @foreach([1 => 'Trimester 1 (Weeks 1-12)', 2 => 'Trimester 2 (Weeks 13-27)', 3 => 'Trimester 3 (Weeks 28-40)', 4 => 'Postnatal'] as $tri => $label)
                    <span class="badge rounded-pill px-3 py-2 {{ $profile->trimester == $tri ? 'text-white' : '' }}"
                          style="{{ $profile->trimester == $tri ? 'background:linear-gradient(135deg, #EC4899, #F472B6);' : 'background:var(--nn-primary-soft); color:var(--nn-primary);' }}">
                        {{ $label }}
                    </span>
                @endforeach
            </div>

            {{-- Week grid --}}
            <div class="row g-2">
                @foreach($weeks as $w)
                    @php
                        $bgStyle = $w['is_current']
                            ? 'background:linear-gradient(135deg, #EC4899, #F472B6); color:#fff; border:none;'
                            : ($w['is_past']
                                ? 'background:#E0E7FF; color:#4338CA; border:none;'
                                : 'background:rgba(255,255,255,0.7); color:var(--nn-text-muted); border:1px solid var(--nn-border);');
                    @endphp
                    <div class="col-3 col-sm-2 col-md-auto" style="min-width:60px;">
                        <a href="{{ route('maternal.journey.week', $w['week']) }}"
                           class="d-block text-center text-decoration-none p-2 rounded-3 {{ $w['is_current'] ? 'shadow-sm' : '' }}"
                           style="{{ $bgStyle }}">
                            <div class="small fw-semibold">W{{ $w['week'] }}</div>
                        </a>
                    </div>
                @endforeach
            </div>

            <div class="text-center mt-4">
                <a href="{{ route('maternal.journey.week', $currentWeek) }}" class="btn rounded-pill fw-semibold px-4" style="background:linear-gradient(135deg, #EC4899, #F472B6); color:#fff;">
                    Go to Current Week ({{ $currentWeek }}) <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
