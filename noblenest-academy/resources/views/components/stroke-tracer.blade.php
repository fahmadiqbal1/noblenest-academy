{{--
    <x-stroke-tracer> — deterministic per-glyph tracing surface.

    Replaces SignaturePad's free-draw with stroke-by-stroke validation:
      - Renders the target glyph as a faint SVG guide overlay.
      - Captures pointer input on a <canvas> stacked above the guide.
      - Samples the expected path with getPointAtLength() and scores the
        user's stroke (avg distance + endpoint-direction check) against
        a tunable tolerance.
      - Advances stroke-by-stroke in the glyph's expected order. RTL
        glyphs (Arabic, Urdu) flip the guide horizontally so stroke order
        reads right-to-left visually.

    Phase 2 scaffold ships with a 7-glyph seed (Latin A, B, C, 0, 1 +
    Arabic ا ب). Adding more glyphs is data-only — extend GLYPHS below.
    Production use needs to author glyph paths per supported alphabet
    (Latin, Cyrillic, Arabic, Urdu, Mandarin pinyin, Korean Hangul).

    Props:
      @prop string $glyph     The glyph key (e.g. 'A', '0', 'ar:ا').
      @prop ?int   $size      Canvas size in px (default 280).
      @prop ?bool  $rtl       Force RTL mirror (overrides glyph data).
--}}
@props(['glyph', 'size' => 280, 'rtl' => null])

@php
    $domId = 'nn-tracer-' . substr(md5($glyph . microtime()), 0, 8);
@endphp

<div
    id="{{ $domId }}"
    class="nn-stroke-tracer relative inline-block rounded-2xl border-[3px] border-violet-300 bg-white shadow-sm"
    style="width: {{ $size }}px; height: {{ $size }}px;"
    data-glyph="{{ $glyph }}"
    @if(! is_null($rtl)) data-rtl="{{ $rtl ? '1' : '0' }}" @endif
    role="region"
    aria-roledescription="Stroke-order tracing surface"
    aria-label="Trace the glyph {{ $glyph }}"
>
    <svg class="nn-tracer-guide pointer-events-none absolute inset-0 w-full h-full" aria-hidden="true"></svg>
    <canvas class="nn-tracer-canvas absolute inset-0 w-full h-full touch-none" width="{{ $size }}" height="{{ $size }}"></canvas>
    <div class="nn-tracer-status absolute bottom-1 left-1 right-1 text-center text-xs font-semibold text-violet-700"
         aria-live="polite"></div>
</div>

@once
@push('scripts')
<script>
(function () {
    const GLYPHS = {
        // Latin letters
        'A': { v: '0 0 200 200', s: ['M 30 180 L 100 20 L 170 180', 'M 60 130 L 140 130'], rtl: false },
        'B': { v: '0 0 200 200', s: ['M 50 20 L 50 180', 'M 50 20 C 140 20 140 100 50 100', 'M 50 100 C 150 100 150 180 50 180'], rtl: false },
        'C': { v: '0 0 200 200', s: ['M 170 40 C 100 0 30 40 30 100 C 30 160 100 200 170 160'], rtl: false },
        // Digits
        '0': { v: '0 0 200 200', s: ['M 100 20 C 30 20 30 180 100 180 C 170 180 170 20 100 20'], rtl: false },
        '1': { v: '0 0 200 200', s: ['M 60 60 L 100 20 L 100 180', 'M 60 180 L 140 180'], rtl: false },
        // Arabic (RTL stroke order: right-to-left baseline drift)
        'ar:ا': { v: '0 0 200 200', s: ['M 100 20 L 100 180'], rtl: true },
        'ar:ب': { v: '0 0 200 200', s: ['M 30 100 C 30 160 170 160 170 100', 'M 100 175 L 100 195'], rtl: true },
    };

    class StrokeTracer {
        constructor(root) {
            this.root = root;
            this.glyph = GLYPHS[root.dataset.glyph];
            if (!this.glyph) { console.warn('Unknown glyph:', root.dataset.glyph); return; }

            this.rtl = (root.dataset.rtl != null)
                ? root.dataset.rtl === '1'
                : !!this.glyph.rtl;

            this.svg    = root.querySelector('.nn-tracer-guide');
            this.canvas = root.querySelector('.nn-tracer-canvas');
            this.status = root.querySelector('.nn-tracer-status');
            this.ctx    = this.canvas.getContext('2d');

            this.strokeIdx = 0;
            this.completed = [];
            this.userPath  = null;

            this.tolerance        = 28;   // px avg distance per sample
            this.endpointDistance = 45;   // px from expected start point

            this.drawGuide();
            this.bindPointer();
            this.updateStatus();
        }

        drawGuide() {
            this.svg.setAttribute('viewBox', this.glyph.v);
            this.svg.innerHTML = '';
            this.glyph.s.forEach((d, i) => {
                const p = document.createElementNS('http://www.w3.org/2000/svg', 'path');
                p.setAttribute('d', d);
                p.setAttribute('fill', 'none');
                p.setAttribute('stroke', i === this.strokeIdx ? '#7C3AED' : '#D8B4FE');
                p.setAttribute('stroke-width', '10');
                p.setAttribute('stroke-linecap', 'round');
                p.setAttribute('stroke-linejoin', 'round');
                p.setAttribute('opacity', i === this.strokeIdx ? '0.45' : '0.18');
                if (this.rtl) p.setAttribute('transform', 'scale(-1,1) translate(-200,0)');
                this.svg.appendChild(p);
            });
        }

        bindPointer() {
            const c = this.canvas;
            const local = (e) => {
                const rect = c.getBoundingClientRect();
                const scaleX = c.width  / rect.width;
                const scaleY = c.height / rect.height;
                return { x: (e.clientX - rect.left) * scaleX, y: (e.clientY - rect.top) * scaleY };
            };

            c.addEventListener('pointerdown', (e) => {
                if (this.strokeIdx >= this.glyph.s.length) return;
                c.setPointerCapture(e.pointerId);
                this.userPath = [local(e)];
                this.ctx.lineCap = 'round'; this.ctx.lineJoin = 'round';
                this.ctx.lineWidth = 6;
                this.ctx.strokeStyle = '#7C3AED';
            });

            c.addEventListener('pointermove', (e) => {
                if (!this.userPath) return;
                const pt = local(e);
                const prev = this.userPath[this.userPath.length - 1];
                this.ctx.beginPath();
                this.ctx.moveTo(prev.x, prev.y); this.ctx.lineTo(pt.x, pt.y);
                this.ctx.stroke();
                this.userPath.push(pt);
            });

            c.addEventListener('pointerup', () => {
                if (!this.userPath) return;
                this.evaluateStroke();
                this.userPath = null;
            });
        }

        evaluateStroke() {
            const expected = this.glyph.s[this.strokeIdx];
            if (!expected) return;

            // Render expected path into the hidden SVG to sample points.
            const tmp = document.createElementNS('http://www.w3.org/2000/svg', 'path');
            tmp.setAttribute('d', expected);
            this.svg.appendChild(tmp);
            const len = tmp.getTotalLength();
            const samples = [];
            const N = 40;
            for (let i = 0; i <= N; i++) {
                const p = tmp.getPointAtLength((i / N) * len);
                // Map viewBox coords to canvas px (assuming square 200×200 viewBox).
                const [vx, vy, vw, vh] = this.glyph.v.split(/\s+/).map(Number);
                let x = ((p.x - vx) / vw) * this.canvas.width;
                const y = ((p.y - vy) / vh) * this.canvas.height;
                if (this.rtl) x = this.canvas.width - x;
                samples.push({ x, y });
            }
            tmp.remove();

            // Endpoint-direction check: did the user start near the expected start?
            const userStart = this.userPath[0];
            const expectedStart = samples[0];
            const startDist = Math.hypot(userStart.x - expectedStart.x, userStart.y - expectedStart.y);
            if (startDist > this.endpointDistance) {
                this.flashStatus('Start near the violet dot ⏺', '#DC2626');
                return this.undoLastUserStroke();
            }

            // Average distance scoring.
            let totalDist = 0;
            this.userPath.forEach((up) => {
                let min = Infinity;
                samples.forEach((sp) => {
                    const d = Math.hypot(up.x - sp.x, up.y - sp.y);
                    if (d < min) min = d;
                });
                totalDist += min;
            });
            const avg = totalDist / this.userPath.length;

            if (avg <= this.tolerance) {
                this.completed.push(this.strokeIdx);
                this.strokeIdx++;
                this.drawGuide();
                this.updateStatus();
            } else {
                this.flashStatus('Almost — try following the line', '#DC2626');
                this.undoLastUserStroke();
            }
        }

        undoLastUserStroke() {
            // Repaint completed strokes only.
            this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
        }

        updateStatus() {
            if (this.strokeIdx >= this.glyph.s.length) {
                this.flashStatus('✓ All strokes complete', '#059669');
                this.root.dispatchEvent(new CustomEvent('strokes:complete', { bubbles: true }));
                return;
            }
            this.flashStatus(`Stroke ${this.strokeIdx + 1} of ${this.glyph.s.length}`, '#7C3AED');
        }

        flashStatus(text, color) {
            this.status.textContent = text;
            this.status.style.color = color;
        }
    }

    window.NobleNestStrokeTracer = StrokeTracer;
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.nn-stroke-tracer').forEach((el) => new StrokeTracer(el));
    });
})();
</script>
@endpush
@endonce
