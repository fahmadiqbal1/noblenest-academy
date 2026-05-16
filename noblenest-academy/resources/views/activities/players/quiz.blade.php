{{--
    Player: quiz  (Phase 2 polish: inline MCQ Alpine component)

    When the activity has a real `quiz_id`, render an inline preview that
    walks through the first few questions; the existing quizzes.show route
    remains the authoritative scoring surface (auth, persistence, retake
    rules). When no `quiz_id` exists, fall back to a tiny demo MCQ derived
    from learning_objectives so the page never blanks.

    Future iterations will inline full scoring + audio prompts + tap-the-
    picture variants and persist results directly.
--}}
@php
    // If a real quiz is bound, link out; render a brief inline demo regardless.
    $demoQuestions = [
        [
            'q' => 'How does this activity help your child grow?',
            'options' => $activity->learning_objectives ?? [
                'Builds focus and patience',
                'Strengthens fine motor skills',
                'Encourages curiosity and questions',
            ],
            'correct' => 0,
        ],
    ];
    if (!is_array($demoQuestions[0]['options']) || count($demoQuestions[0]['options']) < 2) {
        $demoQuestions[0]['options'] = [
            'Builds focus and patience',
            'Strengthens fine motor skills',
            'Encourages curiosity and questions',
        ];
    }
    $demoJson = json_encode($demoQuestions, JSON_UNESCAPED_UNICODE);
@endphp

<x-ui.card padding="md" class="space-y-4">
    <div x-data="inlineQuiz({{ $demoJson }})" x-init="init()" class="space-y-3">
        <p class="text-sm text-[var(--color-text-muted)] text-center">Quick check-in — tap your answer:</p>

        <div x-show="!finished" x-cloak class="space-y-3">
            <p class="font-display font-bold text-[var(--color-text)] text-center"
               x-text="current?.q"></p>
            <div class="grid gap-2" role="radiogroup" :aria-label="current?.q">
                <template x-for="(opt, i) in current?.options" :key="i">
                    <button
                        type="button"
                        @click="answer(i)"
                        :disabled="locked"
                        :class="optionClass(i)"
                        :aria-checked="selected === i"
                        :aria-label="opt"
                        role="radio"
                        class="w-full text-left px-4 py-3 rounded-xl border-[3px] transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:cursor-not-allowed"
                    >
                        <span class="font-semibold mr-2" x-text="String.fromCharCode(65 + i) + '.'"></span>
                        <span x-text="opt"></span>
                    </button>
                </template>
            </div>
        </div>

        <div x-show="finished" x-cloak class="text-center space-y-2" role="status" aria-live="polite">
            <div class="text-4xl" aria-hidden="true">🌟</div>
            <p class="font-display font-bold text-emerald-700" x-text="`You got ${correctCount} / ${questions.length}`"></p>
            <button type="button" @click="reset()"
                    class="inline-flex items-center gap-1 px-3 py-1.5 text-sm rounded-md bg-violet-600 text-white hover:bg-violet-700">
                <x-ui.icon name="rotate-cw" class="w-4 h-4" /> Try again
            </button>
        </div>

        @if($activity->quiz_id ?? false)
            <div class="text-center pt-1">
                <a href="{{ route('quizzes.show', $activity->quiz_id) . ($childQuery ?? '') }}"
                   class="text-sm text-violet-600 hover:underline inline-flex items-center gap-1">
                    <x-ui.icon name="target" class="w-4 h-4" /> Take the full quiz →
                </a>
            </div>
        @endif
    </div>
</x-ui.card>

@push('scripts')
<script>
function inlineQuiz(questions) {
    return {
        questions,
        idx: 0,
        selected: null,
        locked: false,
        correctCount: 0,
        get current() { return this.questions[this.idx] || null; },
        get finished() { return this.idx >= this.questions.length; },
        init() { this.reset(); },
        reset() {
            this.idx = 0;
            this.selected = null;
            this.locked = false;
            this.correctCount = 0;
        },
        answer(i) {
            if (this.locked) return;
            this.selected = i;
            this.locked = true;
            if (i === this.current.correct) this.correctCount++;
            setTimeout(() => {
                this.idx++;
                this.selected = null;
                this.locked = false;
            }, 800);
        },
        optionClass(i) {
            if (!this.locked) return 'border-violet-200 bg-violet-50 hover:bg-violet-100 text-violet-900';
            if (i === this.current.correct) return 'border-emerald-500 bg-emerald-50 text-emerald-900';
            if (i === this.selected) return 'border-red-500 bg-red-50 text-red-900';
            return 'border-gray-200 bg-gray-50 text-gray-500 opacity-60';
        },
    };
}
</script>
@endpush
