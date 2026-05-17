{{--
    Player: pronunciation (Phase 4 MVP)

    Web Speech API (browser-only, no server-side Whisper) with Levenshtein
    scoring vs the target phrase. Graceful fallback message on browsers
    that lack SpeechRecognition (Safari iOS, older Firefox).
--}}
@php
    $target = $activity->title;
    if (is_string($activity->instructions) && $activity->instructions !== '') {
        $target = $activity->instructions;
    }
    $childParam = isset($child) && $child ? (int) $child->id : null;
    $completeUrl = $childParam
        ? route('child.activity.complete', [$childParam, $activity->id])
        : null;
@endphp

<x-ui.card padding="md" class="space-y-4"
           x-data="pronunciationPlayer({{ Js::from([
               'target' => $target,
               'completeUrl' => $completeUrl,
               'csrf' => csrf_token(),
           ]) }})" x-init="init()">
    <div class="text-center space-y-2">
        <p class="text-sm text-[var(--color-text-muted)]">Say this out loud:</p>
        <p class="font-display font-black text-2xl text-[var(--color-text)]"
           dir="auto" x-text="target"></p>
    </div>

    <template x-if="!supported">
        <div class="rounded-lg bg-amber-50 border-2 border-amber-200 text-amber-900 text-sm p-3" role="status">
            Your browser doesn't support voice input — tap "Mark complete" when you've practiced out loud.
        </div>
    </template>

    <template x-if="supported">
        <div class="space-y-3">
            <div class="flex justify-center">
                <button type="button" @click="toggleListen()"
                        :class="listening
                            ? 'bg-red-600 hover:bg-red-700 animate-pulse'
                            : 'bg-violet-600 hover:bg-violet-700'"
                        class="inline-flex items-center gap-2 px-5 py-3 rounded-full font-bold text-white shadow-md focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2"
                        :aria-pressed="listening">
                    <span class="text-xl" aria-hidden="true">🎤</span>
                    <span x-text="listening ? 'Listening… tap to stop' : 'Tap and speak'"></span>
                </button>
            </div>

            <div class="rounded-lg bg-gray-50 border-2 border-gray-200 p-3 min-h-[3rem]"
                 role="status" aria-live="polite">
                <p class="text-xs text-gray-500 mb-1">You said:</p>
                <p class="font-semibold text-[var(--color-text)]" dir="auto"
                   x-text="transcript || '…'"></p>
            </div>

            <template x-if="transcript">
                <div class="text-center space-y-1">
                    <p class="text-sm font-bold"
                       :class="score >= 0.8 ? 'text-emerald-700' : (score >= 0.5 ? 'text-amber-700' : 'text-red-700')"
                       x-text="`Match: ${(score * 100).toFixed(0)}%`"></p>
                    <p class="text-xs text-[var(--color-text-muted)]"
                       x-text="score >= 0.8 ? '🌟 Excellent!' : (score >= 0.5 ? '👍 Keep practicing' : 'Try again — speak slowly')"></p>
                </div>
            </template>
        </div>
    </template>

    <div class="flex flex-wrap justify-center gap-2 pt-2">
        <button type="button" @click="reset()"
                class="inline-flex items-center gap-1 px-3 py-2 text-sm rounded-md bg-gray-500 text-white hover:bg-gray-600">
            <x-ui.icon name="rotate-cw" class="w-4 h-4" /> Try again
        </button>
        @if($completeUrl)
        <button type="button" @click="markComplete()"
                :disabled="completing"
                class="inline-flex items-center gap-1 px-4 py-2 text-sm rounded-md bg-emerald-600 text-white hover:bg-emerald-700 disabled:opacity-60">
            <x-ui.icon name="check-circle" class="w-4 h-4" />
            <span x-text="completed ? 'Completed ✓' : 'Mark complete'"></span>
        </button>
        @endif
    </div>
</x-ui.card>

@push('scripts')
<script>
function pronunciationPlayer(config) {
    return {
        target: config.target || '',
        completeUrl: config.completeUrl,
        csrf: config.csrf,
        supported: false,
        listening: false,
        transcript: '',
        score: 0,
        completing: false,
        completed: false,
        recognition: null,

        init() {
            const SR = window.SpeechRecognition || window.webkitSpeechRecognition;
            if (!SR) { this.supported = false; return; }
            this.supported = true;
            this.recognition = new SR();
            this.recognition.continuous = false;
            this.recognition.interimResults = false;
            this.recognition.lang = document.documentElement.lang || 'en-US';
            this.recognition.onresult = (e) => {
                const text = Array.from(e.results).map(r => r[0].transcript).join(' ').trim();
                this.transcript = text;
                this.score = this.computeScore(text, this.target);
            };
            this.recognition.onend = () => { this.listening = false; };
            this.recognition.onerror = () => { this.listening = false; };
        },
        toggleListen() {
            if (!this.recognition) return;
            if (this.listening) { this.recognition.stop(); return; }
            this.transcript = ''; this.score = 0;
            try { this.recognition.start(); this.listening = true; } catch (_) {}
        },
        reset() { this.transcript = ''; this.score = 0; this.completed = false; },
        levenshtein(a, b) {
            a = (a || '').toLowerCase(); b = (b || '').toLowerCase();
            const m = a.length, n = b.length;
            if (!m) return n; if (!n) return m;
            const dp = Array.from({length: m + 1}, () => new Array(n + 1).fill(0));
            for (let i = 0; i <= m; i++) dp[i][0] = i;
            for (let j = 0; j <= n; j++) dp[0][j] = j;
            for (let i = 1; i <= m; i++) for (let j = 1; j <= n; j++) {
                dp[i][j] = a[i-1] === b[j-1]
                    ? dp[i-1][j-1]
                    : 1 + Math.min(dp[i-1][j], dp[i][j-1], dp[i-1][j-1]);
            }
            return dp[m][n];
        },
        computeScore(spoken, target) {
            const t = (target || '').replace(/[^\p{L}\p{N}\s]/gu, '').trim();
            const s = (spoken || '').replace(/[^\p{L}\p{N}\s]/gu, '').trim();
            if (!t) return 0;
            const dist = this.levenshtein(s, t);
            const max = Math.max(s.length, t.length);
            return max ? Math.max(0, 1 - dist / max) : 0;
        },
        async markComplete() {
            if (!this.completeUrl || this.completing) return;
            this.completing = true;
            try {
                const res = await fetch(this.completeUrl, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': this.csrf, 'Accept': 'application/json' },
                });
                if (res.ok) { this.completed = true; }
            } finally { this.completing = false; }
        },
    };
}
</script>
@endpush
