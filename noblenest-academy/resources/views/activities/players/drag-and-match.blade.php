{{--
    Player: drag-and-match  (Phase 2 follow-up: inline tap-to-order game)

    Shapes the activity's `instructions` array into a sequencing puzzle:
    show the tiles shuffled, the child taps them in order, the slots fill
    in left-to-right. Phase 2-scaffold linked out to the full puzzle route;
    this version brings the interaction inline on show.blade.php while
    keeping the route as a "play the full version" escape hatch.

    Touch-first (taps, no drag) so it works cleanly on mobile.
--}}
@php
    // Pair / sequence data: prefer activity-supplied instructions, otherwise
    // a friendly A-D fallback. Cap at 4 tiles for the inline mini-version;
    // the full route supports difficulty up to 8.
    $sequence = $activity->instructions;
    if (!is_array($sequence) || count($sequence) < 2) {
        $sequence = ['A', 'B', 'C', 'D'];
    }
    $sequence = array_values(array_slice(array_map(fn ($x) => is_string($x) ? mb_substr($x, 0, 3) : (string) $x, $sequence), 0, 4));
    $tilesJson = json_encode($sequence, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
@endphp

<x-ui.card padding="md" class="space-y-4">
    <div
        x-data="dragAndMatch({{ $tilesJson }})"
        x-init="init()"
        class="space-y-4"
    >
        <div class="flex items-center justify-between gap-2">
            <p class="text-sm text-[var(--color-text-muted)]">
                Tap the tiles in order:
                <span class="font-semibold text-[var(--color-text)]" x-text="`${placed.length} / ${expected.length}`"></span>
            </p>
            <button
                type="button"
                @click="reset()"
                class="inline-flex items-center gap-1 px-3 py-1 text-sm rounded-md text-[var(--color-text-muted)] hover:bg-gray-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2"
                aria-label="Reset puzzle"
            >
                <x-ui.icon name="rotate-cw" class="w-4 h-4" /> Reset
            </button>
        </div>

        {{-- Target slots --}}
        <ol class="flex flex-wrap gap-2 justify-center" :class="{ 'animate-pulse': shaking }" aria-label="Sequence slots">
            <template x-for="(slot, idx) in placed" :key="idx">
                <li
                    class="w-16 h-16 rounded-2xl border-[3px] border-emerald-500 bg-emerald-50 flex items-center justify-center text-2xl font-bold text-emerald-700 shadow-sm"
                    x-text="slot"
                ></li>
            </template>
            <template x-for="idx in (expected.length - placed.length)" :key="'empty-' + idx">
                <li
                    class="w-16 h-16 rounded-2xl border-[3px] border-dashed border-gray-300 bg-gray-50 flex items-center justify-center text-2xl text-gray-300"
                    aria-label="Empty slot"
                >?</li>
            </template>
        </ol>

        {{-- Source tiles --}}
        <div class="flex flex-wrap gap-2 justify-center" role="group" aria-label="Tiles to place">
            <template x-for="tile in shuffled" :key="tile.id">
                <button
                    type="button"
                    @click="place(tile)"
                    :disabled="tile.used || finished"
                    :aria-label="`Place tile ${tile.label}`"
                    class="w-16 h-16 rounded-2xl border-[3px] border-violet-300 bg-violet-50 hover:-translate-y-0.5 hover:shadow-md transition flex items-center justify-center text-2xl font-bold text-violet-700 cursor-pointer disabled:opacity-30 disabled:cursor-not-allowed focus-visible:outline focus-visible:outline-2 focus-visible:outline-violet-500 focus-visible:outline-offset-2"
                    x-text="tile.label"
                ></button>
            </template>
        </div>

        {{-- Success / error states --}}
        <div x-show="finished" x-cloak class="text-center space-y-2" role="status" aria-live="polite">
            <div class="text-4xl" aria-hidden="true">🎉</div>
            <p class="font-display font-bold text-emerald-700">Nice work — you got them in order!</p>
        </div>

        <div class="flex justify-center gap-2 pt-1">
            <a
                href="{{ route('activities.puzzle', $activity) . ($childQuery ?? '') }}"
                class="inline-flex items-center gap-1 text-sm text-violet-600 hover:underline focus-visible:outline focus-visible:outline-2"
            >
                <x-ui.icon name="puzzle-piece" class="w-4 h-4" /> Play the full puzzle →
            </a>
        </div>
    </div>
</x-ui.card>

@push('scripts')
<script>
function dragAndMatch(expected) {
    return {
        expected,
        shuffled: [],
        placed: [],
        shaking: false,
        get finished() {
            return this.placed.length === this.expected.length;
        },
        init() {
            this.reset();
        },
        reset() {
            this.placed = [];
            this.shuffled = this.expected
                .map((label, id) => ({ id, label, used: false }))
                .sort(() => Math.random() - 0.5);
        },
        place(tile) {
            if (tile.used || this.finished) return;
            const expectedHere = this.expected[this.placed.length];
            if (tile.label === expectedHere) {
                tile.used = true;
                this.placed.push(tile.label);
            } else {
                this.shaking = true;
                setTimeout(() => { this.shaking = false; this.reset(); }, 600);
            }
        },
    };
}
</script>
@endpush
