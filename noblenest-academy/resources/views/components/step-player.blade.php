{{-- Step Player Component — Animated visual slideshow for guided activity walkthrough --}}
{{-- Usage (either form works):
       <x-step-player :steps="$activity->steps" subject="social" activityEmoji="🎯" />
       <x-step-player :activity="$activity" />
     Accepting either prevents the "Undefined $steps" 500 when a caller
     passes :activity instead. Explicit attributes always win over derived. --}}
@props([
    'steps' => null,
    'activity' => null,
    'child' => null,
    'subject' => null,
    'activityEmoji' => null,
])

@php
    if ($steps === null && $activity) {
        $steps = $activity->steps ?? collect();
    }
    $steps = $steps ?? collect();
    $subject = $subject ?? ($activity?->subject ?? 'default');
    $activityEmoji = $activityEmoji ?? ($activity?->emoji ?? '🎯');
    $allSteps = $steps->sortBy('step_number')->values();

    // Lever 1 — runtime browser TTS. Map the learner's locale to a BCP-47
    // tag so SpeechSynthesis reads each step aloud in the right language at
    // $0 (no stored audio, no per-file moderation — step text is already
    // moderated upstream). Falls back to silent auto-advance if the browser
    // has no speechSynthesis or no voice for the language.
    $ttsLocale = $child?->preferred_language
        ?? ($activity?->language ?? app()->getLocale());
    $bcp47 = [
        'en' => 'en-US', 'fr' => 'fr-FR', 'ru' => 'ru-RU', 'zh' => 'zh-CN',
        'es' => 'es-ES', 'ko' => 'ko-KR', 'ur' => 'ur-PK', 'ar' => 'ar-SA',
    ][$ttsLocale] ?? 'en-US';

    // Subject-specific gradient palettes
    $subjectPalettes = [
        'quran'          => ['from' => '#064E3B', 'to' => '#10B981'],
        'arabic'         => ['from' => '#4C1D95', 'to' => '#8B5CF6'],
        'motor'          => ['from' => '#9D174D', 'to' => '#F472B6'],
        'sensory'        => ['from' => '#4C1D95', 'to' => '#A78BFA'],
        'social'         => ['from' => '#0E7490', 'to' => '#67E8F9'],
        'art'            => ['from' => '#9F1239', 'to' => '#FB7185'],
        'creative'       => ['from' => '#831843', 'to' => '#F9A8D4'],
        'literacy'       => ['from' => '#1E40AF', 'to' => '#60A5FA'],
        'language'       => ['from' => '#1E3A8A', 'to' => '#93C5FD'],
        'numeracy'       => ['from' => '#991B1B', 'to' => '#F87171'],
        'math'           => ['from' => '#7F1D1D', 'to' => '#FCA5A5'],
        'science'        => ['from' => '#064E3B', 'to' => '#34D399'],
        'stem'           => ['from' => '#065F46', 'to' => '#6EE7B7'],
        'coding'         => ['from' => '#312E81', 'to' => '#818CF8'],
        'robotics'       => ['from' => '#1E1B4B', 'to' => '#A5B4FC'],
        'cultural'       => ['from' => '#92400E', 'to' => '#FCD34D'],
        'etiquette'      => ['from' => '#581C87', 'to' => '#C084FC'],
        'character'      => ['from' => '#7C2D12', 'to' => '#FB923C'],
        'islamic_studies'=> ['from' => '#064E3B', 'to' => '#6EE7B7'],
        'cognitive'      => ['from' => '#1E3A8A', 'to' => '#93C5FD'],
        'routine'        => ['from' => '#1F2937', 'to' => '#9CA3AF'],
        'default'        => ['from' => '#4C1D95', 'to' => '#A78BFA'],
    ];

    // Step-number emoji sequences (visual simulation icons per lesson phase)
    $stepVisuals = [
        1 => ['emoji' => '🎒', 'label' => 'Get Ready',     'anim' => 'nn-anim-bounce'],
        2 => ['emoji' => '👀', 'label' => 'Watch & Learn',  'anim' => 'nn-anim-pulse'],
        3 => ['emoji' => '🤲', 'label' => 'Try It!',        'anim' => 'nn-anim-wiggle'],
        4 => ['emoji' => '💬', 'label' => 'Talk About It',  'anim' => 'nn-anim-float'],
        5 => ['emoji' => '🎉', 'label' => 'Celebrate!',     'anim' => 'nn-anim-spin'],
        6 => ['emoji' => '⭐', 'label' => 'Do More!',       'anim' => 'nn-anim-pulse'],
        7 => ['emoji' => '🏆', 'label' => 'Master It!',     'anim' => 'nn-anim-bounce'],
        8 => ['emoji' => '💡', 'label' => 'Reflect',        'anim' => 'nn-anim-float'],
    ];

    $pal = $subjectPalettes[$subject] ?? $subjectPalettes['default'];
@endphp

@if($allSteps->isNotEmpty())
<div
    class="nn-step-player"
    x-data="stepPlayer()"
    x-init="init()"
    role="region"
    aria-roledescription="Step-by-step activity walkthrough"
    aria-label="Activity steps"
    @keydown.window.left.prevent="prev()"
    @keydown.window.right.prevent="next()"
    @keydown.window.space.prevent="togglePlay()"
    tabindex="0"
>
    {{-- Screen-reader-only live announcer for step changes --}}
    <p class="sr-only" aria-live="polite" aria-atomic="true"
       x-text="`Step ${currentIndex + 1} of ${steps.length}: ${currentStep?.title || ''}. ${currentStep?.instruction || ''}`"></p>
    <style>
        .nn-step-player { position: relative; border-radius: var(--radius-card, 16px); overflow: hidden; box-shadow: var(--shadow-clay, 0 8px 32px rgba(0,0,0,0.12)); }

        /* Stage */
        .nn-step-player__stage {
            position: relative;
            width: 100%;
            aspect-ratio: 16/9;
            overflow: hidden;
            background: var(--color-brand-900, #1E1B4B);
        }
        /* Real image: Ken Burns pan */
        .nn-step-player__img {
            width: 100%; height: 100%;
            object-fit: cover;
            animation: nnKenBurns 12s ease-in-out infinite alternate;
        }
        @keyframes nnKenBurns {
            0%   { transform: scale(1.0) translate(0, 0); }
            100% { transform: scale(1.18) translate(-3%, -2%); }
        }

        /* Emoji scene background: gradient + shimmer */
        .nn-step-player__scene {
            width: 100%; height: 100%;
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            background: linear-gradient(135deg, var(--scene-from), var(--scene-to));
            position: relative; overflow: hidden;
        }
        .nn-step-player__scene::before {
            content: '';
            position: absolute; inset: 0;
            background: radial-gradient(ellipse at 30% 40%, rgba(255,255,255,0.12) 0%, transparent 60%);
            pointer-events: none;
        }
        /* Floating bubble decorations */
        .nn-scene-bubble {
            position: absolute;
            border-radius: 50%;
            background: rgba(255,255,255,0.08);
            animation: nnBubbleRise 6s ease-in-out infinite;
        }
        .nn-scene-bubble:nth-child(1) { width:80px;  height:80px;  bottom:10%; left:8%;   animation-delay:0s;   animation-duration:7s; }
        .nn-scene-bubble:nth-child(2) { width:50px;  height:50px;  bottom:25%; left:70%;  animation-delay:1.5s; animation-duration:9s; }
        .nn-scene-bubble:nth-child(3) { width:30px;  height:30px;  bottom:50%; left:85%;  animation-delay:3s;   animation-duration:6s; }
        .nn-scene-bubble:nth-child(4) { width:60px;  height:60px;  bottom:5%;  left:45%;  animation-delay:2s;   animation-duration:8s; }
        @keyframes nnBubbleRise {
            0%, 100% { transform: translateY(0) scale(1);   opacity: 0.3; }
            50%       { transform: translateY(-40px) scale(1.1); opacity: 0.6; }
        }

        /* Main emoji */
        .nn-scene-main-emoji { font-size: 4rem; z-index: 2; filter: drop-shadow(0 4px 12px rgba(0,0,0,0.3)); }
        .nn-scene-step-emoji { font-size: 2.2rem; z-index: 2; margin-top: 0.5rem; filter: drop-shadow(0 2px 6px rgba(0,0,0,0.25)); }
        .nn-scene-label {
            z-index: 2;
            font-family: var(--font-display, 'Baloo 2', sans-serif);
            font-weight: 800;
            font-size: 1rem;
            color: rgba(255,255,255,0.9);
            margin-top: 0.5rem;
            text-shadow: 0 2px 8px rgba(0,0,0,0.4);
            letter-spacing: 0.5px;
        }
        /* Support emoji row */
        .nn-scene-support-row { z-index: 2; font-size: 1.5rem; opacity: 0.7; margin-top: 0.25rem; letter-spacing: 4px; }

        /* Step animations */
        .nn-anim-bounce { animation: nnBounce 1.4s ease-in-out infinite; }
        .nn-anim-pulse  { animation: nnPulse 1.8s ease-in-out infinite; }
        .nn-anim-wiggle { animation: nnWiggle 0.8s ease-in-out infinite; }
        .nn-anim-float  { animation: nnFloat 2.4s ease-in-out infinite; }
        .nn-anim-spin   { animation: nnSpin 2s linear infinite; }
        @keyframes nnBounce { 0%, 100% { transform: translateY(0);     } 50% { transform: translateY(-14px); } }
        @keyframes nnPulse  { 0%, 100% { transform: scale(1);          } 50% { transform: scale(1.18); } }
        @keyframes nnWiggle { 0%, 100% { transform: rotate(-8deg);     } 50% { transform: rotate(8deg);  } }
        @keyframes nnFloat  { 0%, 100% { transform: translateY(0) rotate(-3deg); } 50% { transform: translateY(-12px) rotate(3deg); } }
        @keyframes nnSpin   { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }

        /* Overlay text on top of stage */
        .nn-step-player__overlay {
            position: absolute; bottom: 0; left: 0; right: 0;
            background: linear-gradient(transparent, rgba(15,10,40,0.88));
            padding: 1.25rem 1.5rem 1rem;
            color: #fff;
        }
        .nn-step-player__step-label {
            font-family: var(--font-display, 'Baloo 2', sans-serif);
            font-weight: 800; font-size: 1.05rem;
            margin-bottom: 0.25rem;
        }
        .nn-step-player__instruction { font-size: 0.88rem; opacity: 0.88; line-height: 1.5; }
        .nn-step-player__benefit {
            font-size: 0.78rem; opacity: 0.7; margin-top: 0.3rem;
            display: flex; align-items: center; gap: 0.35rem;
        }

        /* Dot navigation */
        .nn-step-player__dots {
            display: flex; gap: 6px; justify-content: center;
            padding: 0.75rem 1rem 0;
            flex-wrap: wrap;
        }
        .nn-step-dot {
            width: 10px; height: 10px; border-radius: 5px;
            background: rgba(255,255,255,0.25);
            border: none; padding: 0; cursor: pointer;
            transition: all 0.25s; flex-shrink: 0;
        }
        .nn-step-dot.active { background: #fff; width: 22px; }

        /* Controls bar */
        .nn-step-player__controls {
            display: flex; align-items: center; gap: 0.75rem;
            padding: 0.85rem 1.25rem;
            background: var(--color-surface-strong, rgba(255,255,255,0.97));
            border-top: 2px solid var(--color-brand-100, #EDE9FE);
        }
        .nn-sp-btn {
            width: 38px; height: 38px;
            border: 2px solid var(--color-brand-100, #EDE9FE); border-radius: 50%;
            background: var(--color-surface-strong, #fff); display: inline-flex;
            align-items: center; justify-content: center;
            cursor: pointer; transition: all 0.2s;
            font-size: 0.9rem; color: var(--color-brand-800, #4C1D95);
        }
        .nn-sp-btn:hover:not(:disabled) { background: var(--color-brand-100, #EDE9FE); transform: scale(1.08); }
        .nn-sp-btn:disabled { opacity: 0.35; cursor: not-allowed; }
        .nn-sp-progress {
            flex: 1; height: 6px;
            background: var(--color-brand-100, #EDE9FE); border-radius: var(--radius-full, 3px); overflow: hidden;
        }
        .nn-sp-progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--color-brand-600, #7C3AED), var(--color-brand-300, #A78BFA));
            border-radius: var(--radius-full, 3px); transition: width 0.35s ease;
        }
        .nn-sp-counter {
            font-family: var(--font-display, 'Baloo 2', sans-serif); font-weight: 700;
            font-size: 0.82rem; color: var(--color-text-muted, #6B7280); white-space: nowrap;
        }

        /* Accessibility: respect the OS-level reduce-motion preference. */
        @media (prefers-reduced-motion: reduce) {
            .nn-step-player__img,
            .nn-scene-bubble,
            .nn-anim-bounce,
            .nn-anim-pulse,
            .nn-anim-wiggle,
            .nn-anim-float,
            .nn-anim-spin {
                animation: none !important;
            }
            .nn-step-player__img { transform: none !important; }
            .nn-sp-btn:hover:not(:disabled) { transform: none !important; }
            .nn-sp-progress-fill { transition: none !important; }
        }
        .sr-only {
            position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px;
            overflow: hidden; clip: rect(0, 0, 0, 0); white-space: nowrap; border: 0;
        }
    </style>

    {{-- Stage --}}
    <div class="nn-step-player__stage">

        {{-- Real image (Ken Burns) if available --}}
        <template x-if="currentStep?.visual_url">
            <img :src="'/storage/' + currentStep.visual_url"
                 class="nn-step-player__img"
                 :alt="currentStep.title"
                 :key="currentIndex">
        </template>

        {{-- Emoji animation scene when no image --}}
        <template x-if="!currentStep?.visual_url">
            <div class="nn-step-player__scene"
                 style="--scene-from:{{ $pal['from'] }};--scene-to:{{ $pal['to'] }};">
                <div class="nn-scene-bubble"></div>
                <div class="nn-scene-bubble"></div>
                <div class="nn-scene-bubble"></div>
                <div class="nn-scene-bubble"></div>

                {{-- Main activity emoji --}}
                <div class="nn-scene-main-emoji nn-anim-bounce"
                     x-text="activityEmoji"></div>

                {{-- Step-phase emoji (changes per step number) --}}
                <div class="nn-scene-step-emoji"
                     :class="currentStepVisual?.anim || 'nn-anim-pulse'"
                     x-text="currentStepVisual?.emoji || '⭐'"></div>

                <div class="nn-scene-label"
                     x-text="currentStepVisual?.label || 'Step ' + (currentIndex + 1)"></div>

                {{-- Step number badge --}}
                <div class="nn-scene-support-row"
                     :aria-hidden="true"
                     x-text="stepDots"></div>
            </div>
        </template>

        {{-- Text overlay --}}
        <div class="nn-step-player__overlay">
            <div class="nn-step-player__step-label"
                 x-text="'Step ' + (currentIndex + 1) + ': ' + (currentStep?.title || '')"></div>
            <div class="nn-step-player__instruction"
                 x-text="currentStep?.instruction || ''"></div>
            <template x-if="currentStep?.benefit_note">
                <div class="nn-step-player__benefit">
                    <span>💡</span>
                    <span x-text="currentStep.benefit_note"></span>
                </div>
            </template>
        </div>
    </div>

    {{-- Dot nav --}}
    <div class="nn-step-player__dots"
         role="tablist"
         aria-label="Jump to step"
         style="background:linear-gradient(135deg,{{ $pal['from'] }},{{ $pal['to'] }});">
        <template x-for="(step, i) in steps" :key="i">
            <button class="nn-step-dot"
                    :class="{ active: i === currentIndex }"
                    @click="goTo(i)"
                    :title="'Step ' + (i+1) + ': ' + step.title"
                    :aria-label="'Go to step ' + (i+1) + ': ' + step.title"
                    :aria-current="i === currentIndex ? 'step' : null"
                    role="tab"></button>
        </template>
    </div>

    {{-- Controls --}}
    <div class="nn-step-player__controls" role="group" aria-label="Step playback controls">
        <button class="nn-sp-btn" @click="prev()" :disabled="currentIndex === 0"
                aria-label="Previous step" title="Previous (left arrow)">
            <x-ui.icon name="skip-back" />
        </button>
        <button class="nn-sp-btn" @click="togglePlay()"
                :aria-label="playing ? 'Pause auto-advance' : 'Play auto-advance'"
                :aria-pressed="playing"
                :title="playing ? 'Pause (space)' : 'Play (space)'">
            <x-ui.icon name="pause" x-show="playing" />
            <x-ui.icon name="play" x-show="!playing" />
        </button>
        <button class="nn-sp-btn" @click="next()"
                :disabled="currentIndex >= steps.length - 1"
                aria-label="Next step" title="Next (right arrow)">
            <x-ui.icon name="skip-forward" />
        </button>

        <button class="nn-sp-btn" @click="toggleVoice()"
                x-show="voiceSupported"
                :aria-pressed="voiceEnabled"
                :aria-label="voiceEnabled ? 'Mute narration' : 'Unmute narration'"
                :title="voiceEnabled ? 'Narration on — tap to mute' : 'Narration off — tap to unmute'">
            <x-ui.icon name="volume-2" x-show="voiceEnabled" />
            <x-ui.icon name="volume-x" x-show="!voiceEnabled" />
        </button>

        <div class="nn-sp-progress"
             role="progressbar"
             aria-label="Progress through steps"
             :aria-valuenow="currentIndex + 1"
             :aria-valuemin="1"
             :aria-valuemax="steps.length">
            <div class="nn-sp-progress-fill"
                 :style="'width:' + ((currentIndex + 1) / steps.length * 100) + '%'"></div>
        </div>
        <span class="nn-sp-counter" aria-hidden="true" x-text="(currentIndex + 1) + ' / ' + steps.length"></span>
    </div>

    {{-- Audio element --}}
    <audio x-ref="audio" @ended="onAudioEnd()" preload="none"></audio>
</div>

<script>
function stepPlayer() {
    @php
        $stepsJson = $allSteps->map(fn($s) => [
            'title'            => $s->title,
            'instruction'      => $s->instruction,
            'benefit_note'     => $s->benefit_note ?? null,
            'visual_url'       => $s->visual_url ?? null,
            'audio_url'        => $s->audio_url ?? null,
            'duration_seconds' => $s->duration_seconds ?? 8,
            'step_number'      => $s->step_number,
        ])->values();

        $stepVisualsMap = [];
        foreach ($allSteps as $idx => $s) {
            $sn  = (int) $s->step_number;
            $key = (($sn - 1) % 8) + 1;
            $viz = $stepVisuals[$sn] ?? $stepVisuals[$key] ?? ['emoji' => '⭐', 'label' => 'Step ' . $sn, 'anim' => 'nn-anim-pulse'];
            $stepVisualsMap[$idx] = $viz;
        }
    @endphp
    return {
        steps: {!! json_encode($stepsJson) !!},
        stepVisuals: {!! json_encode($stepVisualsMap) !!},
        activityEmoji: '{{ $activityEmoji }}',
        currentIndex: 0,
        playing: false,
        autoAdvanceTimer: null,

        // Lever 1 — runtime browser TTS ($0, no stored audio)
        speechLang: @json($bcp47),
        voiceEnabled: true,
        voiceSupported: (typeof window !== 'undefined' && 'speechSynthesis' in window),

        get currentStep() {
            return this.steps[this.currentIndex] || null;
        },
        get currentStepVisual() {
            return this.stepVisuals[this.currentIndex] || null;
        },
        get stepDots() {
            // Build a progress dot string: filled dots + hollow
            const filled = '●'.repeat(this.currentIndex + 1);
            const hollow = '○'.repeat(this.steps.length - this.currentIndex - 1);
            return filled + hollow;
        },

        init() {
            try { this.voiceEnabled = localStorage.getItem('nn-voice') !== 'off'; } catch (e) {}
        },

        goTo(i) {
            this.cancelSpeech();
            this.currentIndex = i;
            if (this.playing) this.playCurrentStep();
        },

        togglePlay() {
            this.playing ? this.pause() : this.play();
        },

        toggleVoice() {
            this.voiceEnabled = !this.voiceEnabled;
            try { localStorage.setItem('nn-voice', this.voiceEnabled ? 'on' : 'off'); } catch (e) {}
            if (!this.voiceEnabled) this.cancelSpeech();
            else if (this.playing) this.playCurrentStep();
        },

        play() {
            this.playing = true;
            this.playCurrentStep();
        },

        pause() {
            this.playing = false;
            clearTimeout(this.autoAdvanceTimer);
            this.cancelSpeech();
            if (this.$refs.audio) this.$refs.audio.pause();
        },

        cancelSpeech() {
            if (this.voiceSupported) {
                try { window.speechSynthesis.cancel(); } catch (e) {}
            }
        },

        // Speak the step with the Web Speech API in the learner's language.
        // Resolves via onend OR a safety timeout (some engines never fire
        // onend), so playback never stalls.
        speak(step) {
            this.cancelSpeech();
            const text = [step.title, step.instruction].filter(Boolean).join('. ');
            if (!text) { this.scheduleAutoAdvance(step.duration_seconds); return; }
            let advanced = false;
            const go = () => { if (!advanced) { advanced = true; if (this.playing) this.next(); } };
            try {
                const u = new SpeechSynthesisUtterance(text);
                u.lang = this.speechLang;
                u.rate = 0.95;
                const v = window.speechSynthesis.getVoices()
                    .find(x => x.lang && x.lang.toLowerCase().startsWith(this.speechLang.slice(0, 2)));
                if (v) u.voice = v;
                u.onend = go;
                u.onerror = go;
                window.speechSynthesis.speak(u);
                // Safety net: ~180 wpm, min the configured duration.
                const est = Math.max((step.duration_seconds || 8), Math.ceil(text.split(/\s+/).length / 3) + 2);
                clearTimeout(this.autoAdvanceTimer);
                this.autoAdvanceTimer = setTimeout(go, est * 1000);
            } catch (e) {
                this.scheduleAutoAdvance(step.duration_seconds);
            }
        },

        playCurrentStep() {
            if (!this.playing) return;
            const step = this.currentStep;
            if (!step) { this.pause(); return; }
            this.cancelSpeech();

            if (step.audio_url && this.$refs.audio) {
                // Pre-generated audio file wins when present (premium tier).
                this.$refs.audio.src = '/storage/' + step.audio_url;
                this.$refs.audio.play().catch(() => {
                    this.scheduleAutoAdvance(step.duration_seconds);
                });
            } else if (this.voiceEnabled && this.voiceSupported) {
                // Lever 1: free runtime narration, all 8 languages.
                this.speak(step);
            } else {
                this.scheduleAutoAdvance(step.duration_seconds);
            }
        },

        scheduleAutoAdvance(seconds) {
            clearTimeout(this.autoAdvanceTimer);
            this.autoAdvanceTimer = setTimeout(() => {
                if (this.playing) this.next();
            }, (seconds || 8) * 1000);
        },

        onAudioEnd() {
            if (this.playing) {
                this.currentIndex < this.steps.length - 1 ? this.next() : this.pause();
            }
        },

        next() {
            if (this.currentIndex < this.steps.length - 1) {
                this.currentIndex++;
                if (this.playing) this.playCurrentStep();
            } else {
                this.pause();
            }
        },

        prev() {
            if (this.currentIndex > 0) {
                this.currentIndex--;
                if (this.playing) this.playCurrentStep();
            }
        }
    };
}
</script>
@endif
