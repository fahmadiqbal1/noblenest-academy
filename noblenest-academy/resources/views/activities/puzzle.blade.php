@extends('layouts.app')

@section('content')
@php
    $isPlayful = session('theme', (auth()->user()->role ?? null) === 'Student' ? 'playful' : 'professional') === 'playful';
@endphp
<div class="container py-4">
    <h2 class="mb-2 {{ $isPlayful ? 'playful-font text-pink' : 'professional-font text-primary' }}">
        <i class="bi {{ $isPlayful ? 'bi-puzzle' : 'bi-grid-3x3-gap' }}"></i> {{ $activity->title }}
    </h2>
    <p class="mb-4 {{ $isPlayful ? 'playful-font' : 'professional-font' }}">{{ $activity->description }}</p>
    <div class="mb-3">
        <div class="d-flex flex-wrap gap-3 justify-content-center">
            <div class="puzzle-piece" draggable="true" id="piece1" style="width:80px;height:80px;background:#f8c471;border-radius:18px;display:flex;align-items:center;justify-content:center;font-size:2.2rem;cursor:grab;box-shadow:0 2px 8px #f8c47155;transition:transform 0.2s;">A</div>
            <div class="puzzle-piece" draggable="true" id="piece2" style="width:80px;height:80px;background:#7ed6a5;border-radius:18px;display:flex;align-items:center;justify-content:center;font-size:2.2rem;cursor:grab;box-shadow:0 2px 8px #7ed6a555;transition:transform 0.2s;">B</div>
            <div class="puzzle-piece" draggable="true" id="piece3" style="width:80px;height:80px;background:#85c1e9;border-radius:18px;display:flex;align-items:center;justify-content:center;font-size:2.2rem;cursor:grab;box-shadow:0 2px 8px #85c1e955;transition:transform 0.2s;">C</div>
            <div class="puzzle-piece" draggable="true" id="piece4" style="width:80px;height:80px;background:#f1948a;border-radius:18px;display:flex;align-items:center;justify-content:center;font-size:2.2rem;cursor:grab;box-shadow:0 2px 8px #f1948a55;transition:transform 0.2s;">D</div>
        </div>
        <div class="d-flex flex-wrap gap-3 justify-content-center mt-4">
            <div class="puzzle-drop" id="drop1" style="width:80px;height:80px;border:3px dashed #bbb;border-radius:18px;display:flex;align-items:center;justify-content:center;font-size:2.2rem;background:{{ $isPlayful ? '#fff0fa' : '#f8f9fa' }};transition:background 0.2s;"><span>?</span></div>
            <div class="puzzle-drop" id="drop2" style="width:80px;height:80px;border:3px dashed #bbb;border-radius:18px;display:flex;align-items:center;justify-content:center;font-size:2.2rem;background:{{ $isPlayful ? '#fff0fa' : '#f8f9fa' }};transition:background 0.2s;"><span>?</span></div>
            <div class="puzzle-drop" id="drop3" style="width:80px;height:80px;border:3px dashed #bbb;border-radius:18px;display:flex;align-items:center;justify-content:center;font-size:2.2rem;background:{{ $isPlayful ? '#fff0fa' : '#f8f9fa' }};transition:background 0.2s;"><span>?</span></div>
            <div class="puzzle-drop" id="drop4" style="width:80px;height:80px;border:3px dashed #bbb;border-radius:18px;display:flex;align-items:center;justify-content:center;font-size:2.2rem;background:{{ $isPlayful ? '#fff0fa' : '#f8f9fa' }};transition:background 0.2s;"><span>?</span></div>
        </div>
    </div>
    <div id="puzzle-status" class="mt-3 text-center"></div>
    <div class="d-flex gap-3 justify-content-center mt-2">
        <button onclick="resetPuzzle()" class="btn btn-lg {{ $isPlayful ? 'btn-pink shadow' : 'btn-outline-primary' }}">
            <i class="bi bi-arrow-repeat"></i> {{ I18n::get('reset_puzzle') }}
        </button>
        <a href="/activities" class="btn btn-lg {{ $isPlayful ? 'btn-info shadow' : 'btn-outline-info' }}">
            <i class="bi bi-arrow-right-circle"></i> {{ I18n::get('next_activity') }}
        </a>
    </div>
    <div class="mt-3 text-center">
        <strong><i class="bi bi-lightbulb"></i> {{ I18n::get('hint') }}:</strong> {{ I18n::get('drag_letters_in_order') }} (A, B, C, D)
    </div>
    @if($isPlayful)
    <div class="mt-4 text-center">
        <img src="https://cdn.jsdelivr.net/gh/twitter/twemoji@14.0.2/assets/72x72/1f389.png" alt="party" style="width:48px;vertical-align:middle;"> <span class="playful-font text-pink">{{ I18n::get('have_fun') ?? 'Have fun solving the puzzle!' }}</span>
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
let dragged = null;
[...document.querySelectorAll('.puzzle-piece')].forEach(piece => {
    piece.ondragstart = e => {
        dragged = piece;
        setTimeout(() => piece.style.opacity = '0.5', 0);
        piece.style.transform = 'scale(1.1)';
    };
    piece.ondragend = e => {
        dragged = null;
        piece.style.opacity = '1';
        piece.style.transform = '';
    };
});
[...document.querySelectorAll('.puzzle-drop')].forEach(drop => {
    drop.ondragover = e => e.preventDefault();
    drop.ondrop = e => {
        e.preventDefault();
        if (dragged && drop.innerText === '?') {
            drop.innerText = dragged.innerText;
            dragged.style.visibility = 'hidden';
            drop.style.background = '#d4efdf';
            checkPuzzle();
        }
    };
});
function resetPuzzle() {
    document.querySelectorAll('.puzzle-piece').forEach(p => { p.style.visibility = 'visible'; });
    document.querySelectorAll('.puzzle-drop').forEach(d => { d.innerHTML = '<span>?</span>'; d.style.background = '{{ $isPlayful ? '#fff0fa' : '#f8f9fa' }}'; });
    document.getElementById('puzzle-status').innerHTML = '';
}
function checkPuzzle() {
    const drops = Array.from(document.querySelectorAll('.puzzle-drop'));
    const answer = ['A','B','C','D'];
    if (drops.every((d,i) => d.innerText === answer[i])) {
        document.getElementById('puzzle-status').innerHTML = '<span class="text-success fw-bold">🎉 {{ I18n::get('puzzle_completed') }}</span>';
        if({{ $isPlayful ? 'true' : 'false' }}) {
            import('https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js').then(({default:confetti})=>{
                confetti({particleCount:100,spread:90,origin:{y:0.7}});
            });
        }
    }
}
</script>
@endsection
