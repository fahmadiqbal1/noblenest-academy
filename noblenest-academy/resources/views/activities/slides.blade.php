@extends(isset($child) && $child ? 'layouts.child' : 'layouts.app')

@section('title', $activity->title . ' — Lesson Slides')

@push('head')
<style>
.nn-slides-page { background: #0D0A1E; min-height: 100vh; display:flex; flex-direction:column; }
.nn-slides-topbar {
    display:flex; align-items:center; gap:0.75rem;
    padding: 0.9rem 1.25rem;
    background: rgba(15,10,40,0.95);
    border-bottom: 1px solid rgba(124,58,237,0.25);
    flex-shrink: 0;
    position: sticky; top:0; z-index:20;
}
.nn-slides-topbar-back {
    width:38px; height:38px; border-radius:50%;
    background:rgba(124,58,237,0.2); border:1.5px solid rgba(124,58,237,0.35);
    color:#A78BFA; display:inline-flex; align-items:center; justify-content:center;
    text-decoration:none; font-size:0.95rem; flex-shrink:0;
    transition:all 0.2s;
}
.nn-slides-topbar-back:hover { background:rgba(124,58,237,0.4); color:#fff; }
.nn-slides-topbar-title {
    font-family:'Baloo 2',sans-serif; font-weight:800;
    color:#fff; font-size:1rem; flex:1; min-width:0;
    white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
}
.nn-slides-topbar-counter {
    font-family:'Baloo 2',sans-serif; font-weight:800;
    font-size:0.82rem; color:rgba(255,255,255,0.55); white-space:nowrap; flex-shrink:0;
}

/* Main slides stage */
.nn-slides-stage {
    flex:1; position:relative; overflow:hidden;
    min-height: 55vw;
    max-height: calc(100vh - 120px);
}

/* Individual slide */
.nn-slide-item {
    position:absolute; inset:0;
    display:flex; flex-direction:column;
    align-items:center; justify-content:center;
    padding: clamp(1.5rem, 4vw, 3rem) clamp(1rem, 5vw, 4rem);
    opacity:0; transform:translateX(60px);
    transition: all 0.45s cubic-bezier(0.34,1.56,0.64,1);
    pointer-events:none;
    text-align:center;
}
.nn-slide-item.active {
    opacity:1; transform:translateX(0);
    pointer-events:auto;
}
.nn-slide-item.prev {
    opacity:0; transform:translateX(-60px);
}

.nn-slide-item__num {
    position:absolute; top:1rem; left:1.25rem;
    font-size:0.75rem; font-weight:700;
    color:rgba(255,255,255,0.4);
    font-family:'Baloo 2',sans-serif;
    background:rgba(255,255,255,0.08); padding:0.2rem 0.6rem; border-radius:999px;
}

.nn-slide-item__emoji {
    font-size: clamp(4rem, 10vw, 6rem);
    line-height:1; margin-bottom:1.25rem;
    filter: drop-shadow(0 8px 20px rgba(0,0,0,0.35));
    animation: nn-float 3s ease-in-out infinite;
}
.nn-slide-item__title {
    font-family:'Baloo 2',sans-serif; font-weight:900;
    font-size: clamp(1.4rem, 3.5vw, 2.2rem);
    color:#fff; margin-bottom:0.75rem;
    text-shadow: 0 2px 16px rgba(0,0,0,0.4);
    line-height:1.25;
}
.nn-slide-item__text {
    font-size: clamp(0.95rem, 2vw, 1.15rem);
    color:rgba(255,255,255,0.85); line-height:1.7;
    max-width:580px; font-weight:500;
}
.nn-slide-item__benefit {
    display:inline-flex; align-items:center; gap:0.5rem;
    margin-top:1rem; padding:0.6rem 1.15rem;
    background:rgba(16,185,129,0.15); border-radius:12px;
    border:1.5px solid rgba(16,185,129,0.3);
    font-size:0.85rem; color:rgba(255,255,255,0.85); font-weight:600;
    max-width:520px;
}

/* Bottom nav bar */
.nn-slides-bar {
    display:flex; align-items:center; gap:0.75rem;
    padding: 0.9rem 1.25rem;
    background: rgba(15,10,40,0.98);
    border-top: 1px solid rgba(124,58,237,0.2);
    flex-shrink:0;
}
.nn-slides-bar-btn {
    width:44px; height:44px; border-radius:50%;
    border:2px solid rgba(124,58,237,0.35);
    background:rgba(124,58,237,0.15);
    color:#A78BFA;
    display:inline-flex; align-items:center; justify-content:center;
    cursor:pointer; font-size:1rem;
    transition:all 0.2s; flex-shrink:0;
}
.nn-slides-bar-btn:hover:not(:disabled) {
    background:rgba(124,58,237,0.35); color:#fff; transform:scale(1.1);
}
.nn-slides-bar-btn:disabled { opacity:0.3; cursor:not-allowed; }

.nn-slides-bar-dots {
    flex:1; display:flex; align-items:center; justify-content:center;
    gap:6px; flex-wrap:wrap;
}
.nn-slide-dot-btn {
    width:10px; height:10px; border-radius:5px;
    background:rgba(255,255,255,0.22); border:none; padding:0; cursor:pointer;
    transition:all 0.25s;
}
.nn-slide-dot-btn.active { background:#A78BFA; width:22px; }

/* Complete overlay after last slide */
.nn-slides-complete {
    position:absolute; inset:0;
    background:rgba(5,150,105,0.9);
    display:flex; flex-direction:column; align-items:center; justify-content:center;
    gap:1rem; opacity:0; pointer-events:none;
    transition:opacity 0.4s ease;
    text-align:center; padding:2rem;
    backdrop-filter:blur(8px);
}
.nn-slides-complete.visible { opacity:1; pointer-events:auto; }
.nn-slides-complete h3 {
    font-family:'Baloo 2',sans-serif; font-weight:900;
    font-size:2rem; color:#fff; margin-bottom:0.5rem;
}
.nn-slides-complete p { color:rgba(255,255,255,0.88); font-size:1rem; }
.nn-slides-complete-btn {
    display:inline-flex; align-items:center; gap:0.5rem;
    padding:0.85rem 2rem; border-radius:50px;
    background:#fff; color:#059669;
    font-family:'Baloo 2',sans-serif; font-weight:900; font-size:1.05rem;
    text-decoration:none; transition:transform 0.2s cubic-bezier(0.34,1.56,0.64,1);
    border:none; cursor:pointer;
}
.nn-slides-complete-btn:hover { transform:scale(1.05); color:#059669; text-decoration:none; }
</style>
@endpush

@section('content')
@php
    $steps = $activity->steps->sortBy('step_number')->values();
    $totalSlides = $steps->count();

    $subjectPalettes = [
        'quran'    => ['from' => '#064E3B', 'to' => '#10B981'],
        'arabic'   => ['from' => '#4C1D95', 'to' => '#8B5CF6'],
        'motor'    => ['from' => '#9D174D', 'to' => '#F472B6'],
        'social'   => ['from' => '#0E7490', 'to' => '#67E8F9'],
        'art'      => ['from' => '#9F1239', 'to' => '#FB7185'],
        'literacy' => ['from' => '#1E40AF', 'to' => '#60A5FA'],
        'language' => ['from' => '#1E3A8A', 'to' => '#93C5FD'],
        'math'     => ['from' => '#7F1D1D', 'to' => '#FCA5A5'],
        'science'  => ['from' => '#064E3B', 'to' => '#34D399'],
        'stem'     => ['from' => '#065F46', 'to' => '#6EE7B7'],
        'coding'   => ['from' => '#312E81', 'to' => '#818CF8'],
        'default'  => ['from' => '#4C1D95', 'to' => '#A78BFA'],
    ];
    $pal = $subjectPalettes[$activity->subject ?? 'default'] ?? $subjectPalettes['default'];

    $stepEmojis = [1=>'🎒',2=>'👀',3=>'🤲',4=>'💬',5=>'🎉',6=>'⭐',7=>'🏆',8=>'💡'];

    $childQuery = isset($child) && $child ? '?child=' . $child->id : '';
    $backUrl    = isset($child) && $child
        ? route('activities.show', $activity) . $childQuery
        : route('activities.show', $activity);
@endphp

<div class="nn-slides-page" x-data="slidesApp()" x-init="init()">
    {{-- Top bar --}}
    <div class="nn-slides-topbar">
        <a href="{{ $backUrl }}" class="nn-slides-topbar-back" aria-label="Back">
            <x-ui.icon name="arrow-left" />
        </a>
        <div class="nn-slides-topbar-title">{{ $activity->emoji ?? '🎯' }} {{ $activity->title }}</div>
        <span class="nn-slides-topbar-counter" x-text="(current + 1) + ' / ' + {{ $totalSlides }}"></span>
    </div>

    {{-- Slides stage --}}
    <div class="nn-slides-stage"
         style="background:linear-gradient(135deg,{{ $pal['from'] }},{{ $pal['to'] }});">

        @if($totalSlides === 0)
        {{-- No steps fallback --}}
        <div class="nn-slide-item active">
            <div class="nn-slide-item__emoji">{{ $activity->emoji ?? '🎯' }}</div>
            <div class="nn-slide-item__title">{{ $activity->title }}</div>
            @if($activity->description)
            <div class="nn-slide-item__text">{{ $activity->description }}</div>
            @endif
            @if($activity->benefit_explanation)
            <div class="nn-slide-item__benefit">💡 {{ $activity->benefit_explanation }}</div>
            @endif
        </div>
        @else
        @php $idx = 0; @endphp
        @foreach($steps as $step)
        @php
            $sn   = (int) $step->step_number;
            $emoji = $stepEmojis[$sn] ?? $stepEmojis[(($sn-1)%8)+1];
        @endphp
        <div class="nn-slide-item {{ $idx === 0 ? 'active' : '' }}" data-index="{{ $idx }}">
            <div class="nn-slide-item__num">Step {{ $sn }}</div>
            <div class="nn-slide-item__emoji">{{ $emoji }}</div>
            @if($step->title)
            <div class="nn-slide-item__title">{{ $step->title }}</div>
            @endif
            <div class="nn-slide-item__text">{{ $step->instruction }}</div>
            @if($step->benefit_note)
            <div class="nn-slide-item__benefit">
                <span>💡</span><span>{{ $step->benefit_note }}</span>
            </div>
            @endif
        </div>
        @php $idx++; @endphp
        @endforeach
        @endif

        {{-- Completion overlay --}}
        <div class="nn-slides-complete" :class="{ visible: completed }">
            <div style="font-size:5rem;line-height:1;animation:nn-float 2s ease-in-out infinite;">🎉</div>
            <h3>Amazing Work!</h3>
            <p>You finished all {{ $totalSlides }} steps of <strong>{{ $activity->title }}</strong>!</p>

            @if(isset($child) && $child)
            <form action="{{ route('child.activity.complete', [$child, $activity]) }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" class="nn-slides-complete-btn">
                    <x-ui.icon name="check-circle" />
                    Collect My Badge! 🏆
                </button>
            </form>
            @else
            <a href="{{ $backUrl }}" class="nn-slides-complete-btn">
                <x-ui.icon name="home" /> Back to Activity
            </a>
            @endif
        </div>
    </div>

    {{-- Bottom navigation --}}
    <div class="nn-slides-bar">
        <button class="nn-slides-bar-btn" @click="prev()" :disabled="current === 0" title="Previous">
            <x-ui.icon name="skip-back" />
        </button>
        <button class="nn-slides-bar-btn" @click="playPause()" :title="playing ? 'Pause' : 'Auto-play'">
            <x-ui.icon name="pause" x-show="playing" />
            <x-ui.icon name="play" x-show="!playing" />
        </button>

        <div class="nn-slides-bar-dots">
            @for($i = 0; $i < max($totalSlides, 1); $i++)
            <button class="nn-slide-dot-btn {{ $i === 0 ? 'active' : '' }}"
                    data-dot="{{ $i }}"
                    @click="goTo({{ $i }})"
                    title="Slide {{ $i + 1 }}"></button>
            @endfor
        </div>

        <button class="nn-slides-bar-btn" @click="next()"
                :disabled="current >= {{ max($totalSlides - 1, 0) }} && !completed"
                title="Next">
            <x-ui.icon name="skip-forward" />
        </button>
    </div>
</div>

@push('scripts')
<script>
function slidesApp() {
    return {
        current: 0,
        total: {{ max($totalSlides, 1) }},
        playing: false,
        completed: false,
        timer: null,

        init() {
            this.updateView(0, -1);
        },

        goTo(idx) {
            if (idx < 0 || idx >= this.total) return;
            const old = this.current;
            this.current = idx;
            this.updateView(idx, old);
            if (idx === this.total - 1) {
                setTimeout(() => { this.completed = true; this.playing = false; }, 2200);
            }
        },

        next() {
            if (this.current < this.total - 1) {
                this.goTo(this.current + 1);
            } else {
                this.completed = true;
                this.playing = false;
                clearInterval(this.timer);
            }
        },

        prev() {
            if (this.current > 0) {
                this.completed = false;
                this.goTo(this.current - 1);
            }
        },

        playPause() {
            this.playing = !this.playing;
            if (this.playing) {
                this.timer = setInterval(() => {
                    if (this.current < this.total - 1) {
                        this.next();
                    } else {
                        this.playing = false;
                        clearInterval(this.timer);
                    }
                }, 5000);
            } else {
                clearInterval(this.timer);
            }
        },

        updateView(newIdx, oldIdx) {
            const slides = document.querySelectorAll('.nn-slide-item');
            const dots   = document.querySelectorAll('.nn-slide-dot-btn');

            slides.forEach((el, i) => {
                el.classList.remove('active', 'prev');
                if (i === newIdx) el.classList.add('active');
                else if (i === oldIdx) el.classList.add('prev');
            });

            dots.forEach((el, i) => {
                el.classList.toggle('active', i === newIdx);
            });
        }
    };
}
</script>
@endpush
@endsection
