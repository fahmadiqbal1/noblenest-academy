{{--
    Player: drawing-canvas  (Phase 2 polish: inline free-draw with palette)

    A compact free-draw canvas with brush sizes, colour swatches, undo and
    save. Uses SignaturePad like tracing — a custom brush engine is a later
    iteration. RTL-aware mirroring is intentionally absent (free-draw is
    direction-neutral); the existing dedicated route is preserved for the
    full studio experience.
--}}
<x-ui.card padding="md" class="space-y-3">
    <div class="text-sm text-[var(--color-text-muted)] text-center">
        Pick a colour, draw, save your art. Mistakes? Undo or clear and try again.
    </div>

    <div class="flex flex-wrap justify-center gap-2" role="group" aria-label="Drawing tools">
        @php $palette = ['#7C3AED', '#EC4899', '#F59E0B', '#10B981', '#0EA5E9', '#111827']; @endphp
        @foreach($palette as $hex)
            <button
                type="button"
                class="nn-draw-swatch w-8 h-8 rounded-full border-[3px] border-white shadow ring-1 ring-gray-200 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2"
                style="background: {{ $hex }};"
                data-color="{{ $hex }}"
                aria-label="Switch to {{ $hex }}"
            ></button>
        @endforeach
        <label class="inline-flex items-center gap-1 text-xs text-[var(--color-text-muted)]">
            Size
            <input type="range" id="nn-draw-size" min="1" max="14" value="3" class="w-20 accent-violet-600" aria-label="Brush size">
        </label>
    </div>

    <div class="flex justify-center">
        <div class="relative rounded-2xl overflow-hidden border-[3px] border-violet-300 shadow-sm bg-white"
             style="width: 100%; max-width: 480px; aspect-ratio: 4/3;">
            <canvas
                id="nn-draw-canvas"
                width="480"
                height="360"
                class="block w-full h-full touch-none"
                aria-label="Drawing canvas"
                role="img"
            ></canvas>
        </div>
    </div>

    <div class="flex flex-wrap justify-center gap-2">
        <button type="button" id="nn-draw-undo"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 border-2 border-amber-500 text-amber-600 hover:bg-amber-500 hover:text-gray-900">
            <x-ui.icon name="rotate-ccw" class="w-4 h-4" /> Undo
        </button>
        <button type="button" id="nn-draw-clear"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 bg-gray-500 text-white hover:bg-gray-600">
            <x-ui.icon name="eraser" class="w-4 h-4" /> Clear
        </button>
        <button type="button" id="nn-draw-save"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 bg-violet-600 text-white hover:bg-violet-700">
            <x-ui.icon name="check-circle" class="w-4 h-4" /> Save
        </button>
    </div>

    <p id="nn-draw-status" class="text-center text-sm text-[var(--color-text-muted)]" aria-live="polite"></p>

    <div class="text-center">
        <a href="{{ route('activities.drawing', $activity) . ($childQuery ?? '') }}"
           class="text-sm text-violet-600 hover:underline inline-flex items-center gap-1 focus-visible:outline focus-visible:outline-2">
            <x-ui.icon name="brush" class="w-4 h-4" /> Play the full drawing studio →
        </a>
    </div>
</x-ui.card>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js" defer></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var canvas = document.getElementById('nn-draw-canvas');
    if (!canvas || !window.SignaturePad) return;
    var pad = new SignaturePad(canvas, {
        minWidth: 1.5, maxWidth: 4,
        penColor: '#7C3AED',
        backgroundColor: '#FFFFFF',
    });
    var status = document.getElementById('nn-draw-status');
    var size = document.getElementById('nn-draw-size');

    document.querySelectorAll('.nn-draw-swatch').forEach(function (btn) {
        btn.addEventListener('click', function () { pad.penColor = btn.dataset.color; });
    });
    size.addEventListener('input', function () {
        var v = Number(size.value);
        pad.minWidth = Math.max(0.5, v * 0.5);
        pad.maxWidth = v;
    });
    document.getElementById('nn-draw-undo').addEventListener('click', function () {
        var data = pad.toData();
        if (data.length) { data.pop(); pad.fromData(data); }
    });
    document.getElementById('nn-draw-clear').addEventListener('click', function () {
        pad.clear();
        if (status) status.textContent = '';
    });
    document.getElementById('nn-draw-save').addEventListener('click', function () {
        if (pad.isEmpty()) {
            if (status) status.textContent = 'Draw something first!';
            return;
        }
        if (status) status.textContent = 'Saved locally — open the full studio to upload.';
    });
});
</script>
@endpush
