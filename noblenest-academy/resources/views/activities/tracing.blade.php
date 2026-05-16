@extends('layouts.child')

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
    <div class="mb-3 flex flex-col items-center">
        <div class="relative" style="border-radius:24px;overflow:hidden;box-shadow:0 4px 24px rgba(255,105,180,0.08);border:4px solid {{ $isPlayful ? '#ffb6f9' : '#dee2e6' }};background:{{ $isPlayful ? '#fff0fa' : '#fff' }};">
            <canvas id="tracing-canvas" width="400" height="300" style="display:block;{{ $isRTL ? 'transform:scaleX(-1);' : '' }}"></canvas>
            @if($activity->sample_image)
                <img src="{{ $activity->sample_image }}" alt="Sample" style="position:absolute;top:0;left:0;width:400px;height:300px;opacity:0.18;pointer-events:none;z-index:1;{{ $isRTL ? 'transform:scaleX(-1);' : '' }}">
            @endif
            @if($lastTrace)
                <img src="{{ $lastTrace }}" alt="Last Trace" style="position:absolute;top:0;left:0;width:400px;height:300px;opacity:0.35;pointer-events:none;z-index:2;">
            @endif
        </div>
    </div>
    {{-- Color Picker --}}
    <div class="flex gap-2 justify-center mb-2 flex-wrap">
        @foreach(['#ff69b4', '#7ed6a5', '#85c1e9', '#f8c471', '#bb8fce', '#f1948a'] as $color)
        <button class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-3 py-1.5 text-sm rounded-full trace-color-btn" data-color="{{ $color }}" style="width:36px;height:36px;background:{{ $color }};border:3px solid transparent;transition:border 0.2s;" title="{{ $color }}"></button>
        @endforeach
    </div>

    {{-- Difficulty --}}
    <div class="flex gap-2 justify-center mb-2">
        <span class="text-sm text-[var(--color-text-muted)]">Difficulty:</span>
        <button class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-3 py-1.5 text-sm border-2 border-emerald-600 text-emerald-600 hover:bg-emerald-600 hover:text-white trace-difficulty active" data-opacity="0.25">Easy</button>
        <button class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-3 py-1.5 text-sm border-2 border-amber-500 text-amber-600 hover:bg-amber-500 hover:text-gray-900 trace-difficulty" data-opacity="0.12">Medium</button>
        <button class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-3 py-1.5 text-sm border-2 border-red-600 text-red-600 hover:bg-red-600 hover:text-white trace-difficulty" data-opacity="0.04">Hard</button>
    </div>

    <div class="flex gap-3 justify-center mb-3">
        <button id="undo-canvas" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-5 py-3 text-lg border-2 border-amber-500 text-amber-600 hover:bg-amber-500 hover:text-gray-900"><x-ui.icon name="rotate-ccw" /> Undo</button>
        <button id="clear-canvas" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-5 py-3 text-lg {{ $isPlayful ? 'bg-pink-500 text-white hover:bg-pink-600' : 'bg-gray-500 text-white hover:bg-gray-600' }}"><x-ui.icon name="eraser" /> {{ I18n::get('clear') }}</button>
        <button id="save-canvas" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-5 py-3 text-lg {{ $isPlayful ? 'bg-emerald-600 text-white hover:bg-emerald-700' : 'bg-violet-600 text-white hover:bg-violet-700' }}"><x-ui.icon name="check-circle" /> {{ I18n::get('save') }}</button>
    </div>
    <div id="save-status" class="mt-2 text-center"></div>
    <div class="mt-4 flex flex-col items-center">
        <div class="h-2 w-full bg-gray-200 rounded-full overflow-hidden w-1/2 mb-2" style="height:1.2rem;">
            <div class="h-full bg-violet-600 transition-all {{ $isPlayful ? 'bg-pink' : 'bg-primary' }}" role="progressbar" style="width:{{ $progress ?? 0 }}%">{{ $progress ?? 0 }}%</div>
        </div>
        <a href="/activities" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed border-2 border-sky-600 text-sky-600 hover:bg-sky-600 hover:text-white mt-2"><x-ui.icon name="circle-arrow-right" /> {{ I18n::get('next_activity') }}</a>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
<script>
const canvas = document.getElementById('tracing-canvas');
const signaturePad = new SignaturePad(canvas, { minWidth: 2, maxWidth: 4, penColor: '{{ $isPlayful ? '#ff69b4' : 'rgb(44,62,80)' }}' });

// Undo support — track stroke history
let undoHistory = [];
signaturePad.addEventListener('endStroke', () => { undoHistory.push(signaturePad.toData().length); });
document.getElementById('undo-canvas').onclick = () => {
    const data = signaturePad.toData();
    if (data.length) { data.pop(); signaturePad.fromData(data); undoHistory.pop(); }
};

// Color picker
document.querySelectorAll('.trace-color-btn').forEach(btn => {
    btn.onclick = () => {
        document.querySelectorAll('.trace-color-btn').forEach(b => b.style.border = '3px solid transparent');
        btn.style.border = '3px solid #333';
        signaturePad.penColor = btn.dataset.color;
    };
});

// Difficulty — adjusts overlay opacity
document.querySelectorAll('.trace-difficulty').forEach(btn => {
    btn.onclick = () => {
        document.querySelectorAll('.trace-difficulty').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        const overlay = canvas.parentElement.querySelector('img[alt="Sample"]');
        if (overlay) overlay.style.opacity = btn.dataset.opacity;
    };
});

document.getElementById('clear-canvas').onclick = () => { signaturePad.clear(); undoHistory = []; };
document.getElementById('save-canvas').onclick = function() {
    if (signaturePad.isEmpty()) {
        document.getElementById('save-status').innerHTML = '<span class="text-red-600">{{ I18n::get('please_trace_something') }}</span>';
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
        document.getElementById('save-status').innerHTML = '<span class="text-emerald-600">' + (data.message || 'Saved!') + '</span>';
        setTimeout(() => location.reload(), 1200);
    })
    .catch(() => {
        document.getElementById('save-status').innerHTML = '<span class="text-red-600">{{ I18n::get('save_failed') ?? 'Save failed. Try again.' }}</span>';
    });
};
</script>
@endsection
