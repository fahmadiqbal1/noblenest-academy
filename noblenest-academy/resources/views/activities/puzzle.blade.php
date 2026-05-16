@extends('layouts.child')

@section('content')
@php
    $isPlayful = session('theme', (auth()->user()->role ?? null) === 'Student' ? 'playful' : 'professional') === 'playful';
@endphp
<div class="container py-4">
    <h2 class="mb-2 {{ $isPlayful ? 'playful-font text-pink' : 'professional-font text-primary' }}">
        <i class="bi {{ $isPlayful ? 'bi-puzzle' : 'bi-grid-3x3-gap' }}"></i> {{ $activity->title }}
    </h2>
    <p class="mb-4 {{ $isPlayful ? 'playful-font' : 'professional-font' }}">{{ $activity->description }}</p>
    {{-- Difficulty + Timer --}}
    <div class="flex gap-3 justify-center mb-3 items-center flex-wrap">
        <span class="text-sm text-[var(--color-text-muted)]">Difficulty:</span>
        <button class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-3 py-1.5 text-sm border-2 border-emerald-600 text-emerald-600 hover:bg-emerald-600 hover:text-white puzzle-diff active" data-count="4">Easy (4)</button>
        <button class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-3 py-1.5 text-sm border-2 border-amber-500 text-amber-600 hover:bg-amber-500 hover:text-gray-900 puzzle-diff" data-count="6">Medium (6)</button>
        <button class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-3 py-1.5 text-sm border-2 border-red-600 text-red-600 hover:bg-red-600 hover:text-white puzzle-diff" data-count="8">Hard (8)</button>
        <span class="mx-2">|</span>
        <span class="text-sm text-[var(--color-text-muted)]"><x-ui.icon name="timer" /> <span id="puzzleTimer">0:00</span></span>
    </div>

    @php
        $pieceData = $activity->instructions;
        if (!is_array($pieceData) || count($pieceData) < 2) {
            $pieceData = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
        }
        $colors = ['#f8c471', '#7ed6a5', '#85c1e9', '#f1948a', '#bb8fce', '#aed6f1', '#f9e79f', '#d5f5e3'];
    @endphp

    <div class="mb-3">
        <div class="flex flex-wrap gap-3 justify-center" id="puzzlePiecesContainer">
            @foreach(array_slice($pieceData, 0, 4) as $i => $label)
            <div class="puzzle-piece" draggable="true" id="piece{{ $i+1 }}" data-value="{{ is_string($label) ? $label : ($i+1) }}"
                style="width:80px;height:80px;background:{{ $colors[$i % count($colors)] }};border-radius:18px;display:flex;align-items:center;justify-content:center;font-size:2.2rem;cursor:grab;box-shadow:0 2px 8px {{ $colors[$i % count($colors)] }}55;transition:transform 0.2s;">
                {{ is_string($label) ? Str::limit($label, 3, '') : ($i+1) }}
            </div>
            @endforeach
        </div>
        <div class="flex flex-wrap gap-3 justify-center mt-4" id="puzzleDropsContainer">
            @foreach(array_slice($pieceData, 0, 4) as $i => $label)
            <div class="puzzle-drop" id="drop{{ $i+1 }}" data-expected="{{ is_string($label) ? $label : ($i+1) }}"
                style="width:80px;height:80px;border:3px dashed #bbb;border-radius:18px;display:flex;align-items:center;justify-content:center;font-size:2.2rem;background:{{ $isPlayful ? '#fff0fa' : '#f8f9fa' }};transition:background 0.2s;">
                <span>?</span>
            </div>
            @endforeach
        </div>
    </div>
    <div id="puzzle-status" class="mt-3 text-center"></div>
    <div class="flex gap-3 justify-center mt-2">
        <button onclick="resetPuzzle()" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-5 py-3 text-lg {{ $isPlayful ? 'bg-pink-500 text-white hover:bg-pink-600 shadow' : 'border-2 border-violet-600 text-violet-600 hover:bg-violet-600 hover:text-white' }}">
            <x-ui.icon name="rotate-cw" /> {{ I18n::get('reset_puzzle') }}
        </button>
        <a href="/activities" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-5 py-3 text-lg {{ $isPlayful ? 'bg-sky-500 text-white hover:bg-sky-600 shadow' : 'border-2 border-sky-600 text-sky-600 hover:bg-sky-600 hover:text-white' }}">
            <x-ui.icon name="circle-arrow-right" /> {{ I18n::get('next_activity') }}
        </a>
    </div>
    <div class="mt-3 text-center">
        <strong><x-ui.icon name="lightbulb" /> {{ I18n::get('hint') }}:</strong> {{ I18n::get('drag_letters_in_order') }} (A, B, C, D)
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
const allPieceData = @json($pieceData);
const COLORS = ['#f8c471','#7ed6a5','#85c1e9','#f1948a','#bb8fce','#aed6f1','#f9e79f','#d5f5e3'];
let currentCount = 4;
let dragged = null;
let timerStart = Date.now();
let timerInterval = null;

// Timer
function startTimer() {
    timerStart = Date.now();
    clearInterval(timerInterval);
    timerInterval = setInterval(() => {
        const s = Math.floor((Date.now() - timerStart) / 1000);
        document.getElementById('puzzleTimer').textContent = Math.floor(s/60) + ':' + String(s%60).padStart(2,'0');
    }, 1000);
}
startTimer();

// Difficulty switcher
document.querySelectorAll('.puzzle-diff').forEach(btn => {
    btn.onclick = () => {
        document.querySelectorAll('.puzzle-diff').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        currentCount = parseInt(btn.dataset.count);
        buildPuzzle(currentCount);
    };
});

function buildPuzzle(count) {
    const pieces = allPieceData.slice(0, count);
    // Shuffle pieces for display
    const shuffled = [...pieces].sort(() => Math.random() - 0.5);

    const pc = document.getElementById('puzzlePiecesContainer');
    const dc = document.getElementById('puzzleDropsContainer');
    pc.innerHTML = '';
    dc.innerHTML = '';

    shuffled.forEach((label, i) => {
        const val = typeof label === 'string' ? label : (i+1);
        const display = typeof label === 'string' ? label.substring(0,3) : (i+1);
        const el = document.createElement('div');
        el.className = 'puzzle-piece';
        el.draggable = true;
        el.id = 'piece' + (i+1);
        el.dataset.value = val;
        el.style.cssText = 'width:80px;height:80px;background:'+COLORS[i%COLORS.length]+';border-radius:18px;display:flex;align-items:center;justify-content:center;font-size:2.2rem;cursor:grab;box-shadow:0 2px 8px '+COLORS[i%COLORS.length]+'55;transition:transform 0.2s;';
        el.textContent = display;
        pc.appendChild(el);
    });

    pieces.forEach((label, i) => {
        const val = typeof label === 'string' ? label : (i+1);
        const el = document.createElement('div');
        el.className = 'puzzle-drop';
        el.id = 'drop' + (i+1);
        el.dataset.expected = val;
        el.style.cssText = 'width:80px;height:80px;border:3px dashed #bbb;border-radius:18px;display:flex;align-items:center;justify-content:center;font-size:2.2rem;background:{{ $isPlayful ? "#fff0fa" : "#f8f9fa" }};transition:background 0.2s;';
        el.innerHTML = '<span>?</span>';
        dc.appendChild(el);
    });

    bindDragDrop();
    startTimer();
    document.getElementById('puzzle-status').innerHTML = '';
}

function bindDragDrop() {
    document.querySelectorAll('.puzzle-piece').forEach(piece => {
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
    document.querySelectorAll('.puzzle-drop').forEach(drop => {
        drop.ondragover = e => e.preventDefault();
        drop.ondrop = e => {
            e.preventDefault();
            if (dragged && drop.innerText === '?') {
                const val = dragged.dataset.value;
                drop.textContent = dragged.textContent;
                drop.dataset.placed = val;
                dragged.style.visibility = 'hidden';
                const isCorrect = val === drop.dataset.expected;
                drop.style.background = isCorrect ? '#d4efdf' : '#fce4e4';
                checkPuzzle();
            }
        };
    });
}

// Initial bind
bindDragDrop();

function resetPuzzle() {
    buildPuzzle(currentCount);
}

function checkPuzzle() {
    const drops = Array.from(document.querySelectorAll('.puzzle-drop'));
    if (drops.every(d => d.dataset.placed === d.dataset.expected)) {
        clearInterval(timerInterval);
        const s = Math.floor((Date.now() - timerStart) / 1000);
        const timeStr = Math.floor(s/60) + ':' + String(s%60).padStart(2,'0');
        document.getElementById('puzzle-status').innerHTML = '<span class="text-emerald-600 font-bold">🎉 {{ I18n::get("puzzle_completed") }} in ' + timeStr + '!</span>';
        if({{ $isPlayful ? 'true' : 'false' }}) {
            import('https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js').then(({default:confetti})=>{
                confetti({particleCount:100,spread:90,origin:{y:0.7}});
            });
        }
    }
}
</script>
@endsection
