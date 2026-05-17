{{--
    Player: robotics-sim (Phase 4 MVP)

    5x5 grid with a 🤖 emoji. Drive manually with arrow buttons, or write
    a tiny program (one move per line: up/down/left/right) and Run.
    Goal cell highlighted; Mark complete enables once the robot reaches it.
--}}
@php
    $childParam  = isset($child) && $child ? (int) $child->id : null;
    $completeUrl = $childParam ? route('child.activity.complete', [$childParam, $activity->id]) : null;
    $aid = (int) $activity->id;
@endphp

<x-ui.card padding="md" class="space-y-4">
    <div class="text-sm text-[var(--color-text-muted)] text-center">
        Drive the robot to the goal cell. Use the buttons or write a program.
    </div>

    <div id="nn-robo-grid-{{ $aid }}"
         class="grid mx-auto gap-1 select-none"
         style="grid-template-columns: repeat(5, 3rem); grid-template-rows: repeat(5, 3rem); width: max-content;"
         role="grid" aria-label="Robotics simulation grid"></div>

    <div class="flex justify-center gap-1">
        <button type="button" data-dir="up" class="nn-robo-btn-{{ $aid }} px-3 py-2 rounded-md bg-violet-600 text-white">↑</button>
    </div>
    <div class="flex justify-center gap-1">
        <button type="button" data-dir="left"  class="nn-robo-btn-{{ $aid }} px-3 py-2 rounded-md bg-violet-600 text-white">←</button>
        <button type="button" data-dir="down"  class="nn-robo-btn-{{ $aid }} px-3 py-2 rounded-md bg-violet-600 text-white">↓</button>
        <button type="button" data-dir="right" class="nn-robo-btn-{{ $aid }} px-3 py-2 rounded-md bg-violet-600 text-white">→</button>
    </div>

    <label class="block">
        <span class="text-sm font-semibold text-[var(--color-text)]">Step-by-step program (one move per line):</span>
        <textarea id="nn-robo-prog-{{ $aid }}" rows="4" spellcheck="false"
                  class="w-full font-mono text-sm rounded-lg border-2 border-violet-200 bg-white p-2 mt-1"
                  placeholder="up&#10;right&#10;right&#10;down">up
right
right
down</textarea>
    </label>

    <div class="flex flex-wrap justify-center gap-2">
        <button type="button" id="nn-robo-run-{{ $aid }}"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg font-semibold bg-emerald-600 text-white hover:bg-emerald-700">
            <x-ui.icon name="circle-play" class="w-4 h-4" /> Run program
        </button>
        <button type="button" id="nn-robo-reset-{{ $aid }}"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg font-semibold bg-gray-500 text-white hover:bg-gray-600">
            <x-ui.icon name="rotate-cw" class="w-4 h-4" /> Reset
        </button>
        @if($completeUrl)
        <button type="button" id="nn-robo-complete-{{ $aid }}" disabled
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg font-semibold bg-violet-600 text-white hover:bg-violet-700 disabled:opacity-50">
            <x-ui.icon name="check-circle" class="w-4 h-4" /> Mark complete
        </button>
        @endif
    </div>

    <p id="nn-robo-status-{{ $aid }}" class="text-center text-sm text-[var(--color-text-muted)]" aria-live="polite">
        Reach the goal to enable completion.
    </p>
</x-ui.card>

@push('scripts')
<script>
(function () {
    var AID = {{ $aid }};
    var SIZE = 5;
    var grid = document.getElementById('nn-robo-grid-' + AID);
    if (!grid) return;

    var goal = { r: 4, c: 4 };
    var robot = { r: 0, c: 0 };
    var status = document.getElementById('nn-robo-status-' + AID);
    var completeBtn = document.getElementById('nn-robo-complete-' + AID);
    var completeUrl = @json($completeUrl);
    var csrf = @json(csrf_token());
    var reached = false;

    // Build cells.
    var cells = [];
    for (var r = 0; r < SIZE; r++) {
        for (var c = 0; c < SIZE; c++) {
            var cell = document.createElement('div');
            cell.className = 'flex items-center justify-center text-2xl rounded-md border-2 border-violet-100 bg-white';
            cell.style.width = '3rem';
            cell.style.height = '3rem';
            cell.setAttribute('role', 'gridcell');
            cell.dataset.r = r; cell.dataset.c = c;
            cells.push(cell);
            grid.appendChild(cell);
        }
    }
    function cellAt(r, c) { return cells[r * SIZE + c]; }

    function render() {
        cells.forEach(function (el) {
            el.textContent = '';
            el.classList.remove('bg-emerald-100', 'border-emerald-500');
        });
        var g = cellAt(goal.r, goal.c);
        g.classList.add('bg-emerald-100', 'border-emerald-500');
        g.textContent = '🎯';
        cellAt(robot.r, robot.c).textContent = '🤖';
        if (robot.r === goal.r && robot.c === goal.c) {
            reached = true;
            if (status) status.textContent = '🎉 You reached the goal!';
            if (completeBtn) completeBtn.disabled = false;
        }
    }

    function step(dir) {
        var nr = robot.r, nc = robot.c;
        if (dir === 'up')    nr--;
        if (dir === 'down')  nr++;
        if (dir === 'left')  nc--;
        if (dir === 'right') nc++;
        if (nr < 0 || nr >= SIZE || nc < 0 || nc >= SIZE) return false;
        robot.r = nr; robot.c = nc;
        render();
        return true;
    }

    document.querySelectorAll('.nn-robo-btn-' + AID).forEach(function (btn) {
        btn.addEventListener('click', function () { step(btn.dataset.dir); });
    });

    document.getElementById('nn-robo-reset-' + AID).addEventListener('click', function () {
        robot = { r: 0, c: 0 }; reached = false;
        if (completeBtn) completeBtn.disabled = true;
        if (status) status.textContent = 'Reach the goal to enable completion.';
        render();
    });

    document.getElementById('nn-robo-run-' + AID).addEventListener('click', async function () {
        var txt = (document.getElementById('nn-robo-prog-' + AID).value || '').trim();
        var lines = txt.split(/\r?\n/).map(function (s) { return s.trim().toLowerCase(); }).filter(Boolean);
        robot = { r: 0, c: 0 }; reached = false;
        if (completeBtn) completeBtn.disabled = true;
        render();
        for (var i = 0; i < lines.length; i++) {
            if (['up','down','left','right'].indexOf(lines[i]) === -1) {
                if (status) status.textContent = 'Unknown command: ' + lines[i];
                return;
            }
            step(lines[i]);
            await new Promise(function (r) { setTimeout(r, 250); });
            if (reached) break;
        }
    });

    if (completeBtn) {
        completeBtn.addEventListener('click', async function () {
            if (!completeUrl || completeBtn.disabled) return;
            completeBtn.disabled = true;
            try {
                var res = await fetch(completeUrl, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                });
                if (res.ok && status) status.textContent = 'Saved! 🎉';
            } catch (e) { /* swallow */ }
        });
    }

    render();
})();
</script>
@endpush
