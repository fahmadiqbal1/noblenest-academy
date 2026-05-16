@extends('layouts.maternal')

@section('title', 'Emergency Signs — Maternal Wellness')

@section('content')
<div class="container py-4">
    <div class="flex flex-wrap">
        <div class="lg:w-3/12 hidden lg:block">
            @include('maternal.partials.sidebar')
        </div>
        <div class="lg:w-9/12">
            <h3 class="mb-2" style="font-family:'Baloo 2',sans-serif; color:var(--nn-text);">
                <x-ui.icon name="alert-triangle" class="me-2 text-red-600" /> Emergency Signs
            </h3>
            <p class="text-[var(--color-text-muted)] mb-4">Know the warning signs for your current stage ({{ ucfirst(str_replace('_', ' ', $profile->stage)) }}). If you experience any emergency symptoms, contact your healthcare provider or go to the nearest hospital immediately.</p>

            {{-- Emergency --}}
            @if(isset($emergencySigns['emergency']) && $emergencySigns['emergency']->isNotEmpty())
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm border-0 mb-4" style="background:#FEF2F2; border-radius:1.25rem; border-left:5px solid #EF4444 !important;">
                <div class="p-5 p-4">
                    <h5 class="text-red-600 mb-3"><x-ui.icon name="octagon-alert" class="me-2" /> EMERGENCY — Go to Hospital Immediately</h5>
                    @foreach($emergencySigns['emergency'] as $sign)
                    <div class="flex gap-3 mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center" style="width:36px;height:36px;border-radius:50%;background:#EF4444;color:#fff;">
                                <x-ui.icon name="alert-circle" />
                            </div>
                        </div>
                        <div>
                            <h6 class="mb-1 text-red-600">{{ $sign->symptom }}</h6>
                            <p class="mb-0 text-sm">{{ $sign->action_text }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Warning --}}
            @if(isset($emergencySigns['warning']) && $emergencySigns['warning']->isNotEmpty())
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm border-0 mb-4" style="background:#FFFBEB; border-radius:1.25rem; border-left:5px solid #F59E0B !important;">
                <div class="p-5 p-4">
                    <h5 style="color:#B45309;" class="mb-3"><x-ui.icon name="alert-triangle" class="me-2" /> WARNING — Contact Your Doctor</h5>
                    @foreach($emergencySigns['warning'] as $sign)
                    <div class="flex gap-3 mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center" style="width:36px;height:36px;border-radius:50%;background:#F59E0B;color:#fff;">
                                <x-ui.icon name="alert-circle" />
                            </div>
                        </div>
                        <div>
                            <h6 class="mb-1" style="color:#B45309;">{{ $sign->symptom }}</h6>
                            <p class="mb-0 text-sm">{{ $sign->action_text }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Info --}}
            @if(isset($emergencySigns['info']) && $emergencySigns['info']->isNotEmpty())
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm border-0 mb-4" style="background:#EFF6FF; border-radius:1.25rem; border-left:5px solid #3B82F6 !important;">
                <div class="p-5 p-4">
                    <h5 style="color:#1D4ED8;" class="mb-3"><x-ui.icon name="info" class="me-2" /> Good to Know</h5>
                    @foreach($emergencySigns['info'] as $sign)
                    <div class="flex gap-3 mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center" style="width:36px;height:36px;border-radius:50%;background:#3B82F6;color:#fff;">
                                <x-ui.icon name="info" />
                            </div>
                        </div>
                        <div>
                            <h6 class="mb-1" style="color:#1D4ED8;">{{ $sign->symptom }}</h6>
                            <p class="mb-0 text-sm">{{ $sign->action_text }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="flex items-start gap-3 p-4 rounded-lg border border-0" style="background:#F3E8FF; border-radius:1rem;">
                <p class="mb-0 text-sm"><strong>Important Disclaimer:</strong> This information is for educational purposes only and does not replace professional medical advice. Always consult your healthcare provider for medical concerns.</p>
            </div>
        </div>
    </div>
</div>
@endsection
