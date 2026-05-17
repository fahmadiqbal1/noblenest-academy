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

    // Phase 4 polish: optional stroke-order JSON guide.
    // Checks both $activity->stroke_order_url and $activity->meta['stroke_order_json_url'].
    $strokeOrderUrl = $activity->stroke_order_url ?? null;
    if (!$strokeOrderUrl) {
        $meta = $activity->meta ?? null;
        if (is_array($meta) && !empty($meta['stroke_order_json_url'])) {
            $strokeOrderUrl = (string) $meta['stroke_order_json_url'];
        }
    }
@endphp

<x-ui.card padding="md" class="space-y-3" dir="auto">
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

    // Phase 4 polish: optional stroke-order JSON guide overlay.
    // Expected shape: { "strokes": [ [[x,y],[x,y],...], ... ] } in canvas
    // coordinates. We draw each stroke as a dotted gray guide path, and
    // expose a coarse overlap % via the status line after Save.
    var guideStrokes = null;
    var strokeOrderUrl = @json($strokeOrderUrl);
    if (strokeOrderUrl) {
        fetch(strokeOrderUrl, { credentials: 'omit' })
            .then(function (r) { return r.ok ? r.json() : null; })
            .then(function (data) {
                if (!data || !Array.isArray(data.strokes)) return;
                guideStrokes = data.strokes;
                drawGuides();
            })
            .catch(function () {});
    }

    function drawGuides() {
        if (!guideStrokes) return;
        var ctx = canvas.getContext('2d');
        ctx.save();
        ctx.strokeStyle = 'rgba(124, 58, 237, 0.45)';
        ctx.lineWidth = 2;
        ctx.setLineDash([4, 6]);
        guideStrokes.forEach(function (stroke) {
            if (!Array.isArray(stroke) || stroke.length < 2) return;
            ctx.beginPath();
            ctx.moveTo(stroke[0][0], stroke[0][1]);
            for (var i = 1; i < stroke.length; i++) ctx.lineTo(stroke[i][0], stroke[i][1]);
            ctx.stroke();
        });
        ctx.restore();
    }

    function computeOverlapPercent() {
        if (!guideStrokes || pad.isEmpty()) return null;
        try {
            var ctx = canvas.getContext('2d');
            var img = ctx.getImageData(0, 0, canvas.width, canvas.height).data;
            var inked = 0;
            for (var i = 3; i < img.length; i += 4) if (img[i] > 16) inked++;
            // Rough heuristic: pixel-coverage vs an estimated guide budget.
            var budget = 0;
            guideStrokes.forEach(function (s) {
                for (var j = 1; j < s.length; j++) {
                    var dx = s[j][0] - s[j-1][0], dy = s[j][1] - s[j-1][1];
                    budget += Math.sqrt(dx*dx + dy*dy) * 4;
                }
            });
            if (budget <= 0) return null;
            return Math.min(100, Math.round((inked / budget) * 100));
        } catch (_) { return null; }
    }

    document.getElementById('nn-tracing-clear').addEventListener('click', function () {
        pad.clear();
        drawGuides();
        if (status) status.textContent = '';
    });
    document.getElementById('nn-tracing-save').addEventListener('click', function () {
        if (pad.isEmpty()) {
            if (status) status.textContent = 'Trace something first!';
            return;
        }
        var overlap = computeOverlapPercent();
        var msg = 'Saved locally — tap "Play the full tracing canvas" to upload progress.';
        if (overlap !== null) msg = 'Stroke overlap: ' + overlap + '%. ' + msg;
        if (status) status.textContent = msg;
    });
});
</script>
@endpush
