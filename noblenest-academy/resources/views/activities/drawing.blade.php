@extends('layouts.child')

@section('content')
@php
    $isPlayful = session('theme', (auth()->user()->role ?? null) === 'Student' ? 'playful' : 'professional') === 'playful';
@endphp
<div class="container py-4">
    <h2 class="mb-2 {{ $isPlayful ? 'playful-font text-pink' : 'professional-font text-primary' }}">
        <i class="bi {{ $isPlayful ? 'bi-brush' : 'bi-palette' }}"></i> {{ $activity->title }}
    </h2>
    <p class="mb-4 {{ $isPlayful ? 'playful-font' : 'professional-font' }}">{{ $activity->description }}</p>
    <div class="mb-3 flex flex-col items-center">
        <div class="relative" style="border-radius:24px;overflow:hidden;box-shadow:0 4px 24px rgba(255,105,180,0.08);border:4px solid {{ $isPlayful ? '#b2ebf2' : '#dee2e6' }};background:{{ $isPlayful ? '#e0f7fa' : '#fff' }};">
            <canvas id="drawing-canvas" width="400" height="300" style="display:block;"></canvas>
        </div>
    </div>
    {{-- Color Palette --}}
    <div class="flex gap-2 justify-center mb-2 flex-wrap">
        @foreach(['#000000', '#ff69b4', '#00bcd4', '#7ed6a5', '#f8c471', '#bb8fce', '#f1948a', '#7C3AED'] as $color)
        <button class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-3 py-1.5 text-sm rounded-full draw-color-btn" data-color="{{ $color }}" style="width:36px;height:36px;background:{{ $color }};border:3px solid {{ $loop->first ? '#333' : 'transparent' }};transition:border 0.2s;"></button>
        @endforeach
    </div>

    {{-- Brush Size + Eraser --}}
    <div class="flex gap-2 justify-center mb-2 items-center flex-wrap">
        <span class="text-sm text-[var(--color-text-muted)]">Brush:</span>
        <button class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-3 py-1.5 text-sm border-2 border-gray-300 text-gray-700 hover:bg-gray-100 draw-brush-btn active" data-min="1" data-max="2">S</button>
        <button class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-3 py-1.5 text-sm border-2 border-gray-300 text-gray-700 hover:bg-gray-100 draw-brush-btn" data-min="2" data-max="4">M</button>
        <button class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-3 py-1.5 text-sm border-2 border-gray-300 text-gray-700 hover:bg-gray-100 draw-brush-btn" data-min="4" data-max="8">L</button>
        <button class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-3 py-1.5 text-sm border-2 border-gray-300 text-gray-700 hover:bg-gray-100 draw-brush-btn" data-min="8" data-max="16">XL</button>
        <span class="mx-2">|</span>
        <button id="eraser-toggle" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-3 py-1.5 text-sm border-2 border-red-600 text-red-600 hover:bg-red-600 hover:text-white"><x-ui.icon name="eraser" /> Eraser</button>
    </div>

    <div class="flex gap-3 justify-center mb-3">
        <button id="undo-drawing" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-5 py-3 text-lg border-2 border-amber-500 text-amber-600 hover:bg-amber-500 hover:text-gray-900"><x-ui.icon name="rotate-ccw" /> Undo</button>
        <button id="clear-drawing" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-5 py-3 text-lg {{ $isPlayful ? 'bg-sky-500 text-white hover:bg-sky-600' : 'bg-gray-500 text-white hover:bg-gray-600' }}"><x-ui.icon name="eraser" /> {{ I18n::get('clear') }}</button>
        <button id="save-drawing" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-5 py-3 text-lg {{ $isPlayful ? 'bg-emerald-600 text-white hover:bg-emerald-700' : 'bg-violet-600 text-white hover:bg-violet-700' }}"><x-ui.icon name="check-circle" /> {{ I18n::get('save') }}</button>
    </div>
    <div id="drawing-save-status" class="mt-2 text-center"></div>
    <div class="mt-4 flex flex-col items-center">
        <a href="/activities" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed border-2 border-sky-600 text-sky-600 hover:bg-sky-600 hover:text-white mt-2"><x-ui.icon name="circle-arrow-right" /> {{ I18n::get('next_activity') }}</a>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
<script>
const drawingCanvas = document.getElementById('drawing-canvas');
const drawingPad = new SignaturePad(drawingCanvas, { minWidth: 2, maxWidth: 4, penColor: '{{ $isPlayful ? '#00bcd4' : 'rgb(0,0,0)' }}' });
let lastDrawColor = drawingPad.penColor;
let eraserOn = false;

// Undo support
document.getElementById('undo-drawing').onclick = () => {
    const data = drawingPad.toData();
    if (data.length) { data.pop(); drawingPad.fromData(data); }
};

// Color palette
document.querySelectorAll('.draw-color-btn').forEach(btn => {
    btn.onclick = () => {
        document.querySelectorAll('.draw-color-btn').forEach(b => b.style.border = '3px solid transparent');
        btn.style.border = '3px solid #333';
        lastDrawColor = btn.dataset.color;
        if (!eraserOn) drawingPad.penColor = lastDrawColor;
    };
});

// Brush size
document.querySelectorAll('.draw-brush-btn').forEach(btn => {
    btn.onclick = () => {
        document.querySelectorAll('.draw-brush-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        drawingPad.minWidth = parseInt(btn.dataset.min);
        drawingPad.maxWidth = parseInt(btn.dataset.max);
    };
});

// Eraser toggle
document.getElementById('eraser-toggle').onclick = function() {
    eraserOn = !eraserOn;
    this.classList.toggle('active', eraserOn);
    this.classList.toggle('btn-danger', eraserOn);
    this.classList.toggle('btn-outline-danger', !eraserOn);
    drawingPad.penColor = eraserOn ? '{{ $isPlayful ? '#e0f7fa' : '#ffffff' }}' : lastDrawColor;
};

document.getElementById('clear-drawing').onclick = () => drawingPad.clear();
document.getElementById('save-drawing').onclick = function() {
    if (drawingPad.isEmpty()) {
        document.getElementById('drawing-save-status').innerHTML = '<span class="text-red-600">{{ I18n::get('please_draw_something') }}</span>';
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
        document.getElementById('drawing-save-status').innerHTML = '<span class="text-emerald-600">' + (data.message || 'Drawing saved!') + '</span>';
        if({{ $isPlayful ? 'true' : 'false' }}) {
            import('https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js').then(({default:confetti})=>{
                confetti({particleCount:80,spread:70,origin:{y:0.7}});
            });
        }
    })
    .catch(() => {
        document.getElementById('drawing-save-status').innerHTML = '<span class="text-red-600">{{ I18n::get('save_failed') }}</span>';
    });
};
</script>
@endsection
