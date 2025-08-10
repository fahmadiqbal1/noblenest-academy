@extends('layouts.app')

@section('content')
@php
    $isPlayful = session('theme', (auth()->user()->role ?? null) === 'Student' ? 'playful' : 'professional') === 'playful';
@endphp
<div class="container py-4">
    <h2 class="mb-2 {{ $isPlayful ? 'playful-font text-pink' : 'professional-font text-primary' }}">
        <i class="bi {{ $isPlayful ? 'bi-brush' : 'bi-palette' }}"></i> {{ $activity->title }}
    </h2>
    <p class="mb-4 {{ $isPlayful ? 'playful-font' : 'professional-font' }}">{{ $activity->description }}</p>
    <div class="mb-3 d-flex flex-column align-items-center">
        <div class="position-relative" style="border-radius:24px;overflow:hidden;box-shadow:0 4px 24px rgba(255,105,180,0.08);border:4px solid {{ $isPlayful ? '#b2ebf2' : '#dee2e6' }};background:{{ $isPlayful ? '#e0f7fa' : '#fff' }};">
            <canvas id="drawing-canvas" width="400" height="300" style="display:block;"></canvas>
        </div>
    </div>
    <div class="d-flex gap-3 justify-content-center mb-3">
        <button id="clear-drawing" class="btn btn-lg {{ $isPlayful ? 'btn-info' : 'btn-secondary' }}"><i class="bi bi-eraser"></i> {{ I18n::get('clear') }}</button>
        <button id="save-drawing" class="btn btn-lg {{ $isPlayful ? 'btn-success' : 'btn-primary' }}"><i class="bi bi-check2-circle"></i> {{ I18n::get('save') }}</button>
    </div>
    <div id="drawing-save-status" class="mt-2 text-center"></div>
    <div class="mt-4 d-flex flex-column align-items-center">
        <a href="/activities" class="btn btn-outline-info mt-2"><i class="bi bi-arrow-right-circle"></i> {{ I18n::get('next_activity') }}</a>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
<script>
const drawingCanvas = document.getElementById('drawing-canvas');
const drawingPad = new SignaturePad(drawingCanvas, { minWidth: 2, maxWidth: 4, penColor: '{{ $isPlayful ? '#00bcd4' : 'rgb(0,0,0)' }}' });
document.getElementById('clear-drawing').onclick = () => drawingPad.clear();
document.getElementById('save-drawing').onclick = function() {
    if (drawingPad.isEmpty()) {
        document.getElementById('drawing-save-status').innerHTML = '<span class="text-danger">{{ I18n::get('please_draw_something') }}</span>';
        return;
    }
    const dataUrl = drawingPad.toDataURL();
    fetch('/activities/{{ $activity->id }}/drawing', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').content
        },
        body: JSON.stringify({ image: dataUrl })
    })
    .then(r => r.json())
    .then(data => {
        document.getElementById('drawing-save-status').innerHTML = '<span class="text-success">' + (data.message || 'Drawing saved!') + '</span>';
        if({{ $isPlayful ? 'true' : 'false' }}) {
            import('https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js').then(({default:confetti})=>{
                confetti({particleCount:80,spread:70,origin:{y:0.7}});
            });
        }
    })
    .catch(() => {
        document.getElementById('drawing-save-status').innerHTML = '<span class="text-danger">{{ I18n::get('save_failed') }}</span>';
    });
};
</script>
@endsection
