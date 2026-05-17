{{--
    Player: python-sandbox (Phase 4 MVP)

    Loads Pyodide (stdlib only, no preloaded packages) from jsDelivr,
    runs the user's snippet in-browser, prints stdout to a child-safe
    output pane. Initial code is seeded from $activity->instructions
    when it looks like a Python snippet, else a friendly hello-world.
--}}
@php
    $instructions = is_string($activity->instructions ?? null) ? trim($activity->instructions) : '';
    $looksLikePython = $instructions !== '' && (
        str_contains($instructions, 'print(') ||
        str_contains($instructions, 'def ') ||
        str_contains($instructions, 'import ')
    );
    $seed = $looksLikePython ? $instructions : "print(\"Hello, world\")";
    $childParam  = isset($child) && $child ? (int) $child->id : null;
    $completeUrl = $childParam ? route('child.activity.complete', [$childParam, $activity->id]) : null;
@endphp

<x-ui.card padding="md" class="space-y-3"
           x-data="pythonSandbox({{ Js::from([
               'seed' => $seed,
               'completeUrl' => $completeUrl,
               'csrf' => csrf_token(),
           ]) }})" x-init="init()">
    <div class="text-sm text-[var(--color-text-muted)] text-center">
        Edit the Python code and tap <strong>Run</strong>. Output appears below.
    </div>

    <label class="block">
        <span class="sr-only">Python code</span>
        <textarea x-model="code" rows="8" spellcheck="false"
                  class="w-full font-mono text-sm rounded-lg border-2 border-violet-200 bg-white p-3
                         focus:outline-none focus:ring-2 focus:ring-violet-500"
                  aria-label="Python code editor"></textarea>
    </label>

    <div class="flex flex-wrap justify-center gap-2">
        <button type="button" @click="run()"
                :disabled="!ready || running"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg font-semibold bg-emerald-600 text-white hover:bg-emerald-700 disabled:opacity-60">
            <x-ui.icon name="circle-play" class="w-4 h-4" />
            <span x-text="ready ? (running ? 'Running…' : 'Run') : 'Loading Python…'"></span>
        </button>
        <button type="button" @click="reset()"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg font-semibold bg-gray-500 text-white hover:bg-gray-600">
            <x-ui.icon name="rotate-cw" class="w-4 h-4" /> Reset
        </button>
        @if($completeUrl)
        <button type="button" @click="markComplete()"
                :disabled="completing"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg font-semibold bg-violet-600 text-white hover:bg-violet-700 disabled:opacity-60">
            <x-ui.icon name="check-circle" class="w-4 h-4" />
            <span x-text="completed ? 'Completed ✓' : 'Mark complete'"></span>
        </button>
        @endif
    </div>

    <pre x-text="output || (ready ? 'Run your code to see output…' : 'Loading Pyodide from CDN…')"
         class="rounded-lg bg-gray-900 text-emerald-300 text-sm p-3 max-h-60 overflow-auto whitespace-pre-wrap"
         aria-live="polite" aria-label="Python output"></pre>
</x-ui.card>

@push('scripts')
<script src="https://cdn.jsdelivr.net/pyodide/v0.26.4/full/pyodide.js" defer></script>
<script>
function pythonSandbox(config) {
    return {
        code: config.seed || '',
        seed: config.seed || '',
        completeUrl: config.completeUrl,
        csrf: config.csrf,
        output: '',
        ready: false,
        running: false,
        completing: false,
        completed: false,
        pyodide: null,

        async init() {
            // Wait for pyodide.js to be available on window.
            const start = Date.now();
            while (typeof window.loadPyodide !== 'function') {
                if (Date.now() - start > 20000) { this.output = 'Pyodide failed to load.'; return; }
                await new Promise(r => setTimeout(r, 100));
            }
            try {
                this.pyodide = await window.loadPyodide({
                    indexURL: 'https://cdn.jsdelivr.net/pyodide/v0.26.4/full/',
                });
                this.ready = true;
            } catch (e) {
                this.output = 'Failed to initialize Python: ' + (e && e.message ? e.message : e);
            }
        },
        async run() {
            if (!this.ready || !this.pyodide) return;
            this.running = true;
            this.output = '';
            try {
                this.pyodide.setStdout({ batched: (s) => { this.output += s + "\n"; } });
                this.pyodide.setStderr({ batched: (s) => { this.output += s + "\n"; } });
                await this.pyodide.runPythonAsync(this.code);
                if (!this.output) this.output = '(no output)';
            } catch (e) {
                this.output += '\nError: ' + (e && e.message ? e.message : e);
            } finally {
                this.running = false;
            }
        },
        reset() { this.code = this.seed; this.output = ''; this.completed = false; },
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
