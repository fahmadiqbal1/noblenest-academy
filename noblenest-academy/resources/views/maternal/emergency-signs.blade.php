@extends('layouts.app')

@section('title', 'Emergency Signs — Maternal Wellness')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-lg-3 d-none d-lg-block">
            @include('maternal.partials.sidebar')
        </div>
        <div class="col-lg-9">
            <h3 class="mb-2" style="font-family:'Baloo 2',sans-serif; color:var(--nn-text);">
                <i class="bi bi-exclamation-triangle me-2 text-danger"></i> Emergency Signs
            </h3>
            <p class="text-muted mb-4">Know the warning signs for your current stage ({{ ucfirst(str_replace('_', ' ', $profile->stage)) }}). If you experience any emergency symptoms, contact your healthcare provider or go to the nearest hospital immediately.</p>

            {{-- Emergency --}}
            @if(isset($emergencySigns['emergency']) && $emergencySigns['emergency']->isNotEmpty())
            <div class="card border-0 mb-4" style="background:#FEF2F2; border-radius:1.25rem; border-left:5px solid #EF4444 !important;">
                <div class="card-body p-4">
                    <h5 class="text-danger mb-3"><i class="bi bi-exclamation-octagon-fill me-2"></i> EMERGENCY — Go to Hospital Immediately</h5>
                    @foreach($emergencySigns['emergency'] as $sign)
                    <div class="d-flex gap-3 mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                        <div class="flex-shrink-0">
                            <div class="d-flex align-items-center justify-content-center" style="width:36px;height:36px;border-radius:50%;background:#EF4444;color:#fff;">
                                <i class="bi bi-exclamation-lg"></i>
                            </div>
                        </div>
                        <div>
                            <h6 class="mb-1 text-danger">{{ $sign->symptom }}</h6>
                            <p class="mb-0 small">{{ $sign->action_text }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Warning --}}
            @if(isset($emergencySigns['warning']) && $emergencySigns['warning']->isNotEmpty())
            <div class="card border-0 mb-4" style="background:#FFFBEB; border-radius:1.25rem; border-left:5px solid #F59E0B !important;">
                <div class="card-body p-4">
                    <h5 style="color:#B45309;" class="mb-3"><i class="bi bi-exclamation-triangle-fill me-2"></i> WARNING — Contact Your Doctor</h5>
                    @foreach($emergencySigns['warning'] as $sign)
                    <div class="d-flex gap-3 mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                        <div class="flex-shrink-0">
                            <div class="d-flex align-items-center justify-content-center" style="width:36px;height:36px;border-radius:50%;background:#F59E0B;color:#fff;">
                                <i class="bi bi-exclamation"></i>
                            </div>
                        </div>
                        <div>
                            <h6 class="mb-1" style="color:#B45309;">{{ $sign->symptom }}</h6>
                            <p class="mb-0 small">{{ $sign->action_text }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Info --}}
            @if(isset($emergencySigns['info']) && $emergencySigns['info']->isNotEmpty())
            <div class="card border-0 mb-4" style="background:#EFF6FF; border-radius:1.25rem; border-left:5px solid #3B82F6 !important;">
                <div class="card-body p-4">
                    <h5 style="color:#1D4ED8;" class="mb-3"><i class="bi bi-info-circle-fill me-2"></i> Good to Know</h5>
                    @foreach($emergencySigns['info'] as $sign)
                    <div class="d-flex gap-3 mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                        <div class="flex-shrink-0">
                            <div class="d-flex align-items-center justify-content-center" style="width:36px;height:36px;border-radius:50%;background:#3B82F6;color:#fff;">
                                <i class="bi bi-info-lg"></i>
                            </div>
                        </div>
                        <div>
                            <h6 class="mb-1" style="color:#1D4ED8;">{{ $sign->symptom }}</h6>
                            <p class="mb-0 small">{{ $sign->action_text }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="alert border-0" style="background:#F3E8FF; border-radius:1rem;">
                <p class="mb-0 small"><strong>Important Disclaimer:</strong> This information is for educational purposes only and does not replace professional medical advice. Always consult your healthcare provider for medical concerns.</p>
            </div>
        </div>
    </div>
</div>
@endsection
