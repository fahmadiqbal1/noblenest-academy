@extends('layouts.app')

@section('content')
@php
    $isPlayful = session('theme', (auth()->user()->role ?? null) === 'Student' ? 'playful' : 'professional') === 'playful';
    $currentLang = session('locale', 'en');
    $isRTL = in_array($currentLang, ['ar', 'ur']);
    $lastTrace = $lastTrace ?? null; // Pass from controller if available
@endphp
<div class="container py-4" @if($isRTL) dir="rtl" class="rtl" @endif>
    <h2 class="mb-2 {{ $isPlayful ? 'playful-font text-pink' : 'professional-font text-primary' }}">
        <i class="bi {{ $isPlayful ? 'bi-pencil' : 'bi-vector-pen' }}"></i> {{ $activity->title }}
    </h2>
    <p class="mb-4 {{ $isPlayful ? 'playful-font' : 'professional-font' }}">{{ $activity->description }}</p>
    <div class="mb-3 d-flex flex-column align-items-center">
        <div class="position-relative" style="border-radius:24px;overflow:hidden;box-shadow:0 4px 24px rgba(255,105,180,0.08);border:4px solid {{ $isPlayful ? '#ffb6f9' : '#dee2e6' }};background:{{ $isPlayful ? '#fff0fa' : '#fff' }};">
            <canvas id="tracing-canvas" width="400" height="300" style="display:block;{{ $isRTL ? 'transform:scaleX(-1);' : '' }}"></canvas>
            @if($activity->sample_image)
                <img src="{{ $activity->sample_image }}" alt="Sample" style="position:absolute;top:0;left:0;width:400px;height:300px;opacity:0.18;pointer-events:none;z-index:1;{{ $isRTL ? 'transform:scaleX(-1);' : '' }}">
            @endif
            @if($lastTrace)
                <img src="{{ $lastTrace }}" alt="Last Trace" style="position:absolute;top:0;left:0;width:400px;height:300px;opacity:0.35;pointer-events:none;z-index:2;">
            @endif
        </div>
    </div>
    <div class="d-flex gap-3 justify-content-center mb-3">
        <button id="clear-canvas" class="btn btn-lg {{ $isPlayful ? 'btn-pink' : 'btn-secondary' }}"><i class="bi bi-eraser"></i> {{ I18n::get('clear') }}</button>
        <button id="save-canvas" class="btn btn-lg {{ $isPlayful ? 'btn-success' : 'btn-primary' }}"><i class="bi bi-check2-circle"></i> {{ I18n::get('save') }}</button>
    </div>
    <div id="save-status" class="mt-2 text-center"></div>
    <div class="mt-4 d-flex flex-column align-items-center">
        <div class="progress w-50 mb-2" style="height:1.2rem;">
            <div class="progress-bar {{ $isPlayful ? 'bg-pink' : 'bg-primary' }}" role="progressbar" style="width:{{ $progress ?? 0 }}%">{{ $progress ?? 0 }}%</div>
        </div>
        <a href="/activities" class="btn btn-outline-info mt-2"><i class="bi bi-arrow-right-circle"></i> {{ I18n::get('next_activity') }}</a>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
<script>
const canvas = document.getElementById('tracing-canvas');
const signaturePad = new SignaturePad(canvas, { minWidth: 2, maxWidth: 4, penColor: '{{ $isPlayful ? '#ff69b4' : 'rgb(44,62,80)' }}' });
document.getElementById('clear-canvas').onclick = () => signaturePad.clear();
document.getElementById('save-canvas').onclick = function() {
    if (signaturePad.isEmpty()) {
        document.getElementById('save-status').innerHTML = '<span class="text-danger">{{ I18n::get('please_trace_something') }}</span>';
        return;
    }
    const dataUrl = signaturePad.toDataURL();
    fetch('/activities/{{ $activity->id }}/trace', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ image: dataUrl })
    })
    .then(res => res.json())
    .then(data => {
        document.getElementById('save-status').innerHTML = '<span class="text-success">' + (data.message || 'Saved!') + '</span>';
        setTimeout(() => location.reload(), 1200);
    })
    .catch(() => {
        document.getElementById('save-status').innerHTML = '<span class="text-danger">{{ I18n::get('save_failed') ?? 'Save failed. Try again.' }}</span>';
    });
};
</script>
@endsection
