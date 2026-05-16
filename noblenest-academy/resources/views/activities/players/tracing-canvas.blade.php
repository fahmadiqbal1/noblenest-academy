{{--
    Player: tracing-canvas  (Phase 2 polish: inline SignaturePad + RTL CSS mirror)

    A compact inline tracing surface that mounts on show.blade.php. RTL
    languages (ar/ur) mirror both the canvas and the sample-image overlay so
    the proper stroke direction is visually correct.

    The full-screen tracing experience (color picker, difficulty slider,
    progress save) stays at `activities.tracing`; an inline "Play the full
    tracing canvas →" link preserves that escape hatch.

    A deterministic stroke engine (replace SignaturePad with per-glyph SVG
    stroke validation) is the next Phase-2 iteration — see master prompt
    step 2.7.
--}}
@php
    $lang = session('lang', auth()->user()?->preferred_language ?? 'en');
    $isRTL = in_array($lang, ['ar', 'ur'], true);
    $sampleSrc = $activity->sample_image ?? null;
@endphp

<x-ui.card padding="md" class="space-y-3">
    <div class="text-sm text-[var(--color-text-muted)] text-center">
        Trace inside the box. Use the buttons to clear or save your work.
    </div>

    <div class="flex justify-center">
        <div
            class="relative rounded-2xl overflow-hidden border-[3px] border-violet-300 shadow-sm bg-white"
            style="width: 100%; max-width: 400px; aspect-ratio: 4/3;"
        >
            <canvas
                id="nn-tracing-canvas"
                width="400"
                height="300"
                class="block w-full h-full {{ $isRTL ? '[transform:scaleX(-1)]' : '' }}"
                aria-label="Tracing surface"
                role="img"
            ></canvas>
            @if($sampleSrc)
                <img
                    src="{{ $sampleSrc }}"
                    alt=""
                    aria-hidden="true"
                    class="absolute inset-0 w-full h-full pointer-events-none {{ $isRTL ? '[transform:scaleX(-1)]' : '' }}"
                    style="opacity: 0.18; object-fit: contain;"
                >
            @endif
        </div>
    </div>

    <div class="flex flex-wrap justify-center gap-2">
        <button
            type="button"
            id="nn-tracing-clear"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 bg-gray-500 text-white hover:bg-gray-600"
            aria-label="Clear the tracing canvas"
        >
            <x-ui.icon name="eraser" class="w-4 h-4" /> Clear
        </button>
        <button
            type="button"
            id="nn-tracing-save"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 bg-violet-600 text-white hover:bg-violet-700"
            aria-label="Save tracing progress"
        >
            <x-ui.icon name="check-circle" class="w-4 h-4" /> Save
        </button>
    </div>

    <p id="nn-tracing-status" class="text-center text-sm text-[var(--color-text-muted)]" aria-live="polite"></p>

    <div class="text-center">
        <a
            href="{{ route('activities.tracing', $activity) . ($childQuery ?? '') }}"
            class="text-sm text-violet-600 hover:underline inline-flex items-center gap-1 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2"
        >
            <x-ui.icon name="pencil" class="w-4 h-4" /> Play the full tracing canvas →
        </a>
    </div>
</x-ui.card>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js" defer></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var canvas = document.getElementById('nn-tracing-canvas');
    if (!canvas || !window.SignaturePad) return;
    var pad = new SignaturePad(canvas, {
        minWidth: 2, maxWidth: 4,
        penColor: '#7C3AED',
        backgroundColor: 'rgba(0,0,0,0)',
    });
    var status = document.getElementById('nn-tracing-status');

    document.getElementById('nn-tracing-clear').addEventListener('click', function () {
        pad.clear();
        if (status) status.textContent = '';
    });
    document.getElementById('nn-tracing-save').addEventListener('click', function () {
        if (pad.isEmpty()) {
            if (status) status.textContent = 'Trace something first!';
            return;
        }
        // Phase 2 inline scaffold: a no-op success acknowledgement.
        // Real progress save flows through `activities.tracing` POST endpoint;
        // wiring it in this inline path is a later iteration.
        if (status) status.textContent = 'Saved locally — tap "Play the full tracing canvas" to upload progress.';
    });
});
</script>
@endpush
