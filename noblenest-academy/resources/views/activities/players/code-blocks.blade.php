{{--
    Player: code-blocks  (Phase 2 polish: Blockly workspace scaffold)

    Renders a Blockly visual programming workspace via CDN. The toolbox is
    intentionally small (event-driven blocks + math + logic) so beginners
    can wire together their first program without overwhelm. Run/Reset
    buttons execute the generated JavaScript in a sandboxed try/catch and
    print to a child-friendly output panel.

    A persistent program-save flow lands in Phase 3 alongside the STEM 7–10
    track build-out. For now, programs survive a page reload via
    localStorage keyed on the activity id.
--}}
<x-ui.card padding="md" class="space-y-3">
    <div class="text-sm text-[var(--color-text-muted)] text-center">
        Drag blocks together, click <strong>Run</strong>, and see your program speak back.
    </div>

    <div
        id="nn-blockly-{{ $activity->id }}"
        class="rounded-xl border-[3px] border-violet-200 bg-white shadow-sm overflow-hidden"
        style="height: 380px;"
        aria-label="Blockly workspace"
        role="application"
    ></div>

    <div class="flex flex-wrap justify-center gap-2">
        <button type="button" id="nn-blockly-run-{{ $activity->id }}"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 bg-emerald-600 text-white hover:bg-emerald-700">
            <x-ui.icon name="circle-play" class="w-4 h-4" /> Run
        </button>
        <button type="button" id="nn-blockly-reset-{{ $activity->id }}"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 bg-gray-500 text-white hover:bg-gray-600">
            <x-ui.icon name="rotate-cw" class="w-4 h-4" /> Reset
        </button>
    </div>

    <pre id="nn-blockly-output-{{ $activity->id }}"
         class="rounded-lg bg-gray-900 text-emerald-300 text-sm p-3 max-h-40 overflow-auto"
         aria-live="polite"
         aria-label="Program output">Run your blocks to see output…</pre>
</x-ui.card>

@push('scripts')
<script src="https://unpkg.com/blockly/blockly.min.js" defer></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var ACT = {{ (int) $activity->id }};
    var container = document.getElementById('nn-blockly-' + ACT);
    var output    = document.getElementById('nn-blockly-output-' + ACT);
    if (!container || !window.Blockly) return;

    var toolbox = {
        kind: 'flyoutToolbox',
        contents: [
            { kind: 'block', type: 'controls_if' },
            { kind: 'block', type: 'controls_repeat_ext' },
            { kind: 'block', type: 'logic_compare' },
            { kind: 'block', type: 'math_number' },
            { kind: 'block', type: 'math_arithmetic' },
            { kind: 'block', type: 'text' },
            { kind: 'block', type: 'text_print' },
            { kind: 'block', type: 'variables_get' },
            { kind: 'block', type: 'variables_set' },
        ]
    };

    var workspace = Blockly.inject(container, {
        toolbox: toolbox,
        scrollbars: true,
        trashcan: true,
        grid: { spacing: 20, length: 3, colour: '#EDE9FE', snap: true },
    });

    // Restore previous program from localStorage.
    var storageKey = 'nn-blockly-' + ACT;
    try {
        var saved = localStorage.getItem(storageKey);
        if (saved) Blockly.serialization.workspaces.load(JSON.parse(saved), workspace);
    } catch (e) {}

    workspace.addChangeListener(function () {
        try { localStorage.setItem(storageKey, JSON.stringify(Blockly.serialization.workspaces.save(workspace))); } catch (e) {}
    });

    document.getElementById('nn-blockly-run-' + ACT).addEventListener('click', function () {
        if (!Blockly.JavaScript) {
            output.textContent = '(JavaScript generator not loaded.)';
            return;
        }
        output.textContent = '';
        var origLog = console.log;
        console.log = function () {
            output.textContent += Array.prototype.slice.call(arguments).join(' ') + "\n";
            origLog.apply(console, arguments);
        };
        try {
            var code = Blockly.JavaScript.workspaceToCode(workspace);
            new Function(code)();
        } catch (err) {
            output.textContent += 'Error: ' + err.message;
        } finally {
            console.log = origLog;
        }
    });
    document.getElementById('nn-blockly-reset-' + ACT).addEventListener('click', function () {
        workspace.clear();
        try { localStorage.removeItem(storageKey); } catch (e) {}
        output.textContent = 'Run your blocks to see output…';
    });
});
</script>
@endpush
