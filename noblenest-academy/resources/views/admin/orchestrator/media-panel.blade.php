{{-- Media Studio Panel — included inside the orchestrator page container --}}
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden" id="mediaStudioPanel">
    <details class="group">
        <summary class="flex items-center justify-between px-5 py-4 cursor-pointer select-none hover:bg-gray-50 list-none [&::-webkit-details-marker]:hidden">
            <div>
                <div class="text-xs font-bold uppercase tracking-widest text-violet-600 mb-0.5">Media Studio</div>
                <div class="font-bold text-gray-900 flex items-center gap-1.5">
                    <x-ui.icon name="image" class="text-[var(--color-primary)]" /> Generate Activity Media
                </div>
            </div>
            <svg class="w-4 h-4 text-gray-400 transition-transform group-open:rotate-180 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
            </svg>
        </summary>

        <div class="border-t border-gray-100 p-5 space-y-4">
            <form id="mediaStudioForm" onsubmit="return submitMediaJob(event)">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="sm:col-span-1">
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Activity <span class="text-red-500">*</span></label>
                        <select name="activity_id" id="mediaActivityId"
                                class="block w-full px-3 py-2 rounded-lg border border-gray-300 bg-white text-sm focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none"
                                required>
                            <option value="">— Select activity —</option>
                        </select>
                        <p class="text-xs text-gray-400 mt-1">Focus to load options.</p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Media Type <span class="text-red-500">*</span></label>
                        <select name="media_type" id="mediaType" onchange="filterMediaProviders()"
                                class="block w-full px-3 py-2 rounded-lg border border-gray-300 bg-white text-sm focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none"
                                required>
                            <option value="thumbnail">Thumbnail (Image)</option>
                            <option value="audio">Audio (TTS)</option>
                            <option value="video">Video</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Provider <span class="text-red-500">*</span></label>
                        <select name="provider_id" id="mediaProvider"
                                class="block w-full px-3 py-2 rounded-lg border border-gray-300 bg-white text-sm focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none"
                                required>
                            <option value="">— Select provider —</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Custom Prompt (optional)</label>
                    <textarea name="prompt" id="mediaPrompt" rows="2"
                              class="block w-full px-3 py-2 rounded-lg border border-gray-300 bg-white text-sm focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none resize-none"
                              placeholder="Leave blank for auto-generated prompt based on activity metadata…"></textarea>
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit" id="mediaSubmitBtn"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold bg-violet-600 text-white hover:bg-violet-700 transition">
                        <x-ui.icon name="zap" /> Generate
                    </button>
                    <span id="mediaJobStatus" class="text-sm text-gray-500"></span>
                </div>
            </form>

            {{-- Active media jobs --}}
            <div id="mediaJobsList" class="hidden space-y-2">
                <div class="text-xs font-bold uppercase tracking-widest text-violet-600">Active Media Jobs</div>
                <div id="mediaJobsContainer" class="space-y-2"></div>
            </div>
        </div>
    </details>
</div>

<script>
(function() {
    const CAPABILITY_MAP = { thumbnail: ['image'], audio: ['tts'], video: ['video'] };

    @php
        $providersList = $providers->map(fn($p) => [
            'id'           => $p->id,
            'name'         => $p->name,
            'slug'         => $p->slug,
            'is_active'    => $p->is_active,
            'capabilities' => $p->capabilities ?? [],
            'driver'       => data_get($p->extra_config, 'driver', 'auto'),
        ])->values();
    @endphp
    const allProviders = @json($providersList);

    const DRIVER_CAPS = {
        'openai-image': ['image'], 'stability': ['image'], 'gemini': ['image', 'text'],
        'elevenlabs': ['tts'], 'replicate': ['video', 'image'], 'runway': ['video'],
        'openai': ['text'], 'anthropic': ['text'], 'github': ['text']
    };
    function getProviderCaps(p) {
        return (p.capabilities && p.capabilities.length) ? p.capabilities : (DRIVER_CAPS[p.driver] || []);
    }

    window.filterMediaProviders = function() {
        const type   = document.getElementById('mediaType').value;
        const needed = CAPABILITY_MAP[type] || [];
        const sel    = document.getElementById('mediaProvider');
        sel.innerHTML = '<option value="">— Select provider —</option>';
        allProviders.filter(p => p.is_active).forEach(p => {
            if (needed.some(n => getProviderCaps(p).includes(n))) {
                const opt = document.createElement('option');
                opt.value = p.id;
                opt.textContent = `${p.name} (${p.driver})`;
                sel.appendChild(opt);
            }
        });
    };
    filterMediaProviders();

    // Load activities on focus
    let activityCache = null, activitiesLoaded = false;
    document.getElementById('mediaActivityId').addEventListener('focus', async function() {
        if (activitiesLoaded) return;
        activitiesLoaded = true;
        this.innerHTML = '<option value="">Loading…</option>';
        try {
            const r = await fetch('/api/activities?limit=500', { headers: { 'Accept': 'application/json' } });
            const data = r.ok ? await r.json() : [];
            const list = Array.isArray(data.data) ? data.data : (Array.isArray(data) ? data : []);
            this.innerHTML = `<option value="">— Select activity (${list.length}) —</option>`;
            list.forEach(a => {
                const opt = document.createElement('option');
                opt.value = a.id;
                opt.textContent = `#${a.id} ${a.title} (${a.subject || ''}, age ${a.age_group || ''})`;
                this.appendChild(opt);
            });
        } catch {
            this.innerHTML = '<option value="">— Load failed, enter ID manually —</option>';
        }
    });

    const activePolls = new Map();

    window.submitMediaJob = function(e) {
        e.preventDefault();
        const form   = document.getElementById('mediaStudioForm');
        const btn    = document.getElementById('mediaSubmitBtn');
        const status = document.getElementById('mediaJobStatus');
        const csrf   = document.querySelector('meta[name="csrf-token"]');

        btn.disabled = true;
        btn.innerHTML = '<span class="inline-block w-4 h-4 border-2 border-current border-t-transparent rounded-full animate-spin"></span> Dispatching…';
        status.textContent = '';

        fetch('{{ route("admin.orchestrator.generateMedia") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf.content, 'Accept': 'application/json' },
            body: JSON.stringify({
                activity_id: form.activity_id.value,
                media_type:  form.media_type.value,
                provider_id: form.provider_id.value,
                prompt:      form.prompt.value || undefined,
            })
        })
        .then(r => r.ok ? r.json() : r.json().then(d => { throw new Error(d.message || 'HTTP ' + r.status); }))
        .then(data => {
            status.innerHTML = `<span class="text-emerald-600">✓ Job #${data.job_id} dispatched!</span>`;
            addJobCard(data.job_id, form.media_type.value);
            pollJobStatus(data.job_id);
        })
        .catch(err => {
            status.innerHTML = `<span class="text-red-600">✗ ${err.message}</span>`;
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg> Generate';
        });
        return false;
    };

    function addJobCard(jobId, mediaType) {
        document.getElementById('mediaJobsList').classList.remove('hidden');
        const card = document.createElement('div');
        card.id = 'media-job-' + jobId;
        card.className = 'rounded-xl border border-gray-200 bg-gray-50 p-3';
        card.innerHTML = `
            <div class="flex items-center justify-between gap-2">
                <div class="flex items-center gap-2 text-sm">
                    <span id="media-job-status-${jobId}"
                          class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">queued</span>
                    <span class="font-semibold text-gray-900">#${jobId}</span>
                    <span class="text-gray-500">${mediaType}</span>
                </div>
                <span class="inline-block w-4 h-4 border-2 border-violet-600 border-t-transparent rounded-full animate-spin"
                      id="media-job-spin-${jobId}"></span>
            </div>
            <div id="media-job-result-${jobId}" class="mt-2 text-sm"></div>`;
        document.getElementById('mediaJobsContainer').prepend(card);
    }

    function pollJobStatus(jobId) {
        if (activePolls.has(jobId)) return;
        const interval = setInterval(() => {
            fetch(`/admin/orchestrator/media/${jobId}/status`, { headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(data => {
                const statusEl = document.getElementById('media-job-status-' + jobId);
                const resultEl = document.getElementById('media-job-result-' + jobId);
                const spinEl   = document.getElementById('media-job-spin-' + jobId);
                if (statusEl) {
                    const cls = { completed: 'bg-emerald-100 text-emerald-700', failed: 'bg-red-100 text-red-700', running: 'bg-violet-100 text-violet-700', queued: 'bg-gray-100 text-gray-600' };
                    statusEl.className = `inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium ${cls[data.status] || cls.queued}`;
                    statusEl.textContent = data.status;
                }
                if (['completed', 'failed'].includes(data.status)) {
                    clearInterval(interval); activePolls.delete(jobId);
                    if (spinEl) spinEl.remove();
                    if (data.status === 'completed' && data.result) {
                        const res = data.result;
                        let html = '';
                        if (res.type === 'image' && res.url) html = `<img src="${res.url}" class="rounded max-h-32">`;
                        else if (res.type === 'audio' && res.url) html = `<audio controls class="w-full"><source src="${res.url}" type="audio/mpeg"></audio>`;
                        else if (res.type === 'video' && res.url) html = `<video controls class="w-full max-h-32"><source src="${res.url}"></video>`;
                        else html = `<span class="text-emerald-700 text-xs">${JSON.stringify(res)}</span>`;
                        if (resultEl) resultEl.innerHTML = html;
                    } else if (data.error_message && resultEl) {
                        resultEl.innerHTML = `<span class="text-red-600 text-xs">${data.error_message}</span>`;
                    }
                }
            }).catch(() => {});
        }, 3000);
        activePolls.set(jobId, interval);
    }
})();
</script>
