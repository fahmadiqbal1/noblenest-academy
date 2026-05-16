{{--
    Player: assessment  (Phase 2 polish: short interest-and-strength indicator)

    A 5-question kid-friendly questionnaire driven by Alpine. Stored in
    localStorage keyed by activity id — full 30-question adaptive battery
    + parent-facing PDF report land in Phase 3 alongside curriculum push.

    Framed clearly as an "interest indicator", never as a clinical
    assessment. Consent gating for under-13 happens in Phase 5 (Privacy /
    Parental Consent) — the inline path is parent-supervised.
--}}
@php
    $questions = [
        ['prompt' => 'When you have free time, you most enjoy…',
         'options' => ['Building or making things 🧱', 'Reading or telling stories 📖', 'Drawing or painting 🎨', 'Playing outside 🌳']],
        ['prompt' => 'A puzzle that frustrates you for a few minutes — you usually…',
         'options' => ['Stick with it until it works 🧩', 'Ask someone to help 🙋', 'Try a different one for now ↩️', 'Take a break and come back 🌟']],
        ['prompt' => 'You feel most proud when you…',
         'options' => ['Solve a tricky problem 💡', 'Help someone else feel good 🤝', 'Make something beautiful 🎀', 'Win a friendly game 🏆']],
        ['prompt' => 'New idea you want to explore next is closest to…',
         'options' => ['How machines work ⚙️', 'How stories are written ✍️', 'How animals or plants live 🐢', 'How music sounds and feels 🎵']],
        ['prompt' => 'In a group activity, you usually…',
         'options' => ['Lead and organise 👑', 'Listen and contribute thoughtfully 🤔', 'Cheer everyone on 📣', 'Try the boldest plan first 🚀']],
    ];
    $assessJson = json_encode($questions, JSON_UNESCAPED_UNICODE);
@endphp

<x-ui.card padding="lg" class="space-y-4">
    <div class="text-center space-y-1">
        <p class="text-xs font-semibold uppercase tracking-widest text-[var(--color-primary)]">Discovery questionnaire</p>
        <p class="text-sm text-[var(--color-text-muted)]">
            Five questions to help us suggest activities your child might love.
            <span class="block italic mt-1">This is an interest indicator — not a clinical assessment.</span>
        </p>
    </div>

    <div x-data="discoveryAssessment({{ $assessJson }}, {{ (int) $activity->id }})"
         x-init="init()"
         class="space-y-3">

        <div x-show="!finished" x-cloak class="space-y-3">
            <div class="flex items-center justify-between text-xs text-[var(--color-text-muted)]">
                <span x-text="`Question ${idx + 1} of ${questions.length}`"></span>
                <span x-text="`${Math.round(((idx) / questions.length) * 100)}% complete`"></span>
            </div>
            <div class="h-2 w-full bg-gray-200 rounded-full overflow-hidden"
                 role="progressbar"
                 :aria-valuenow="idx + 1"
                 aria-valuemin="1"
                 :aria-valuemax="questions.length">
                <div class="h-full bg-violet-600 transition-all"
                     :style="`width: ${((idx + 1) / questions.length) * 100}%`"></div>
            </div>

            <p class="font-display font-bold text-[var(--color-text)] text-center text-lg"
               x-text="current?.prompt"></p>

            <div class="grid sm:grid-cols-2 gap-2" role="radiogroup" :aria-label="current?.prompt">
                <template x-for="(opt, i) in current?.options" :key="i">
                    <button type="button"
                            @click="answer(i)"
                            class="text-left px-4 py-3 rounded-xl border-[3px] border-violet-200 bg-violet-50 hover:bg-violet-100 text-violet-900 transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2"
                            role="radio"
                            :aria-label="opt"
                            x-text="opt"></button>
                </template>
            </div>
        </div>

        <div x-show="finished" x-cloak class="text-center space-y-3" role="status" aria-live="polite">
            <div class="text-5xl" aria-hidden="true">🧭</div>
            <p class="font-display font-bold text-emerald-700">Thanks for sharing!</p>
            <p class="text-sm text-[var(--color-text-muted)]">
                We'll use your answers to suggest activities aligned with your interests.
                A full report (with parental consent) lands in the next update.
            </p>
            <button type="button" @click="reset()"
                    class="inline-flex items-center gap-1 px-3 py-1.5 text-sm rounded-md bg-violet-600 text-white hover:bg-violet-700">
                <x-ui.icon name="rotate-cw" class="w-4 h-4" /> Retake
            </button>
        </div>
    </div>
</x-ui.card>

@push('scripts')
<script>
function discoveryAssessment(questions, activityId) {
    var storageKey = 'nn-assessment-' + activityId;
    return {
        questions,
        idx: 0,
        answers: [],
        get current() { return this.questions[this.idx] || null; },
        get finished() { return this.idx >= this.questions.length; },
        init() {
            try {
                var saved = JSON.parse(localStorage.getItem(storageKey) || 'null');
                if (saved && Array.isArray(saved.answers) && saved.answers.length === this.questions.length) {
                    this.answers = saved.answers;
                    this.idx = this.questions.length;
                }
            } catch (e) {}
        },
        reset() {
            this.idx = 0;
            this.answers = [];
            try { localStorage.removeItem(storageKey); } catch (e) {}
        },
        answer(i) {
            this.answers.push(i);
            this.idx++;
            try {
                localStorage.setItem(storageKey, JSON.stringify({ answers: this.answers, at: Date.now() }));
            } catch (e) {}
        },
    };
}
</script>
@endpush
