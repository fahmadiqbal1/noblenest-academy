{{-- Media Studio Panel — included in orchestrator/index.blade.php --}}
<div class="orch-card p-3 mt-4" id="mediaStudioPanel">
    <div class="orch-card-header">
        <div>
            <div class="orch-section-title mb-1">Media Studio</div>
            <div class="font-bold"><x-ui.icon name="image" class="text-[var(--color-primary)]" /> Generate Activity Media</div>
        </div>
        <button class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-3 py-1.5 text-sm border-2 border-gray-300 text-gray-700 hover:bg-gray-100" type="button">
            <x-ui.icon name="chevron-down" />
        </button>
    </div>
    <div class="" id="mediaStudioBody">
        <form id="mediaStudioForm" class="orch-form" onsubmit="return submitMediaJob(event)">
            @csrf
            <div class="flex flex-wrap gap-3">
                <div class="md:w-6/12">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Activity <span class="text-red-600">*</span></label>
                    <select name="activity_id" id="mediaActivityId" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500 px-2 py-1 text-sm" required>
                        <option value="">— Select activity —</option>
                    </select>
                    <div class="mt-1 text-sm text-[var(--color-text-muted)]">Type to search or scroll. Loaded via AJAX.</div>
                </div>
                <div class="md:w-3/12">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Media Type <span class="text-red-600">*</span></label>
                    <select name="media_type" id="mediaType" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500 px-2 py-1 text-sm" required onchange="filterMediaProviders()">
                        <option value="thumbnail">Thumbnail (Image)</option>
                        <option value="audio">Audio (TTS)</option>
                        <option value="video">Video</option>
                    </select>
                </div>
                <div class="md:w-3/12">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Provider <span class="text-red-600">*</span></label>
                    <select name="provider_id" id="mediaProvider" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500 px-2 py-1 text-sm" required>
                        <option value="">— Select provider —</option>
                    </select>
                </div>
            </div>
            <div class="mt-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Custom Prompt (optional)</label>
                <textarea name="prompt" id="mediaPrompt" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500 px-2 py-1 text-sm" rows="2"
                    placeholder="Leave blank for auto-generated prompt based on activity metadata..."></textarea>
            </div>
            <div class="mt-3 flex gap-2 items-center">
                <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed bg-violet-600 text-white hover:bg-violet-700 px-3 py-1.5 text-sm" id="mediaSubmitBtn">
                    <x-ui.icon name="zap" /> Generate
                </button>
                <div id="mediaJobStatus" class="text-sm text-[var(--color-text-muted)]"></div>
            </div>
        </form>

        {{-- Active Media Jobs --}}
        <div id="mediaJobsList" class="mt-3" style="display:none">
            <div class="orch-section-title mb-2">Active Media Jobs</div>
            <div id="mediaJobsContainer" class="flex flex-col gap-2"></div>
        </div>
    </div>
</div>

<script>
(function() {
    // Provider capability mapping for filtering
    const CAPABILITY_MAP = {
        thumbnail: ['image'],
        audio: ['tts'],
        video: ['video']
    };

    @php
        $providersList = $providers->map(function($p) {
            return [
                'id' => $p->id,
                'name' => $p->name,
                'slug' => $p->slug,
                'is_active' => $p->is_active,
                'capabilities' => $p->capabilities ?? [],
                'driver' => data_get($p->extra_config, 'driver', 'auto'),
            ];
        })->values();
    @endphp
    const allProviders = @json($providersList);

    // Driver-to-capability fallback (if capabilities array is empty)
    const DRIVER_CAPS = {
        'openai-image': ['image'], 'stability': ['image'], 'gemini': ['image', 'text'],
        'elevenlabs': ['tts'], 'replicate': ['video', 'image'], 'runway': ['video'],
        'openai': ['text'], 'anthropic': ['text'], 'github': ['text']
    };

    function getProviderCaps(p) {
        if (p.capabilities && p.capabilities.length) return p.capabilities;
        return DRIVER_CAPS[p.driver] || [];
    }

    window.filterMediaProviders = function() {
        const type = document.getElementById('mediaType').value;
        const needed = CAPABILITY_MAP[type] || [];
        const sel = document.getElementById('mediaProvider');
        sel.innerHTML = '<option value="">— Select provider —</option>';

        allProviders.filter(p => p.is_active).forEach(p => {
            const caps = getProviderCaps(p);
            if (needed.some(n => caps.includes(n))) {
                const opt = document.createElement('option');
                opt.value = p.id;
                opt.textContent = `${p.name} (${p.driver})`;
                sel.appendChild(opt);
            }
        });
    };

    // Load activities via search
    let activityCache = null;
    async function loadActivities() {
        if (activityCache) return activityCache;
        try {
            const r = await fetch('/api/activities?limit=500', { headers: { 'Accept': 'application/json' } });
            if (!r.ok) throw new Error('HTTP ' + r.status);
            activityCache = await r.json();
            return activityCache;
        } catch {
            // Fallback: just show empty — admin can type an ID
            return [];
        }
    }

    // Populate activity dropdown on focus
    const actSel = document.getElementById('mediaActivityId');
    let activitiesLoaded = false;
    actSel.addEventListener('focus', async function() {
        if (activitiesLoaded) return;
        activitiesLoaded = true;
        actSel.innerHTML = '<option value="">Loading...</option>';
        const activities = await loadActivities();
        actSel.innerHTML = '<option value="">— Select activity (' + activities.length + ') —</option>';
        (Array.isArray(activities.data) ? activities.data : activities).forEach(a => {
            const opt = document.createElement('option');
            opt.value = a.id;
            opt.textContent = `#${a.id} ${a.title} (${a.subject}, age ${a.age_group})`;
            actSel.appendChild(opt);
        });
    });

    // Initialize provider filter
    filterMediaProviders();

    // Track active polling jobs
    const activePolls = new Map();

    window.submitMediaJob = function(e) {
        e.preventDefault();
        const form = document.getElementById('mediaStudioForm');
        const btn = document.getElementById('mediaSubmitBtn');
        const status = document.getElementById('mediaJobStatus');
        const csrfMeta = document.querySelector('meta[name="csrf-token"]');

        const body = {
            activity_id: form.activity_id.value,
            media_type: form.media_type.value,
            provider_id: form.provider_id.value,
            prompt: form.prompt.value || undefined,
        };

        btn.disabled = true;
        btn.innerHTML = '<span class="inline-block w-6 h-6 border-2 border-current border-t-transparent rounded-full animate-spin w-4 h-4"></span> Dispatching...';
        status.textContent = '';

        fetch('{{ route("admin.orchestrator.generateMedia") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfMeta.content,
                'Accept': 'application/json'
            },
            body: JSON.stringify(body)
        })
        .then(r => {
            if (!r.ok) return r.json().then(d => { throw new Error(d.message || 'HTTP ' + r.status); });
            return r.json();
        })
        .then(data => {
            status.innerHTML = '<span class="text-emerald-600"><x-ui.icon name="check-circle" /> Job #' + data.job_id + ' dispatched!</span>';
            addJobCard(data.job_id, body.media_type);
            pollJobStatus(data.job_id);
        })
        .catch(err => {
            status.innerHTML = '<span class="text-red-600"><x-ui.icon name="x-circle" /> ' + err.message + '</span>';
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<x-ui.icon name="zap" /> Generate';
        });

        return false;
    };

    function addJobCard(jobId, mediaType) {
        const container = document.getElementById('mediaJobsContainer');
        const list = document.getElementById('mediaJobsList');
        list.style.display = 'block';

        const card = document.createElement('div');
        card.id = 'media-job-' + jobId;
        card.className = 'job-card p-2';
        card.innerHTML = `
            <div class="flex justify-between items-center">
                <div>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-500" id="media-job-status-${jobId}">queued</span>
                    <span class="font-semibold ms-1">#${jobId}</span>
                    <span class="text-[var(--color-text-muted)] text-sm ms-1">${mediaType}</span>
                </div>
                <span class="inline-block w-6 h-6 border-2 border-current border-t-transparent rounded-full animate-spin w-4 h-4 text-[var(--color-primary)]" id="media-job-spin-${jobId}"></span>
            </div>
            <div id="media-job-result-${jobId}" class="mt-1 text-sm"></div>
        `;
        container.prepend(card);
    }

    function pollJobStatus(jobId) {
        if (activePolls.has(jobId)) return;

        const interval = setInterval(() => {
            fetch(`/admin/orchestrator/media/${jobId}/status`, {
                headers: { 'Accept': 'application/json' }
            })
            .then(r => r.json())
            .then(data => {
                const statusEl = document.getElementById('media-job-status-' + jobId);
                const resultEl = document.getElementById('media-job-result-' + jobId);
                const spinEl = document.getElementById('media-job-spin-' + jobId);

                if (statusEl) {
                    statusEl.textContent = data.status;
                    statusEl.className = 'badge bg-' + ({
                        completed: 'success', failed: 'danger', running: 'primary', queued: 'secondary'
                    }[data.status] || 'secondary');
                }

                if (['completed', 'failed'].includes(data.status)) {
                    clearInterval(interval);
                    activePolls.delete(jobId);
                    if (spinEl) spinEl.style.display = 'none';

                    if (data.status === 'completed' && data.result) {
                        const res = data.result;
                        let html = '';
                        if (res.type === 'image' && res.url) html = `<img src="${res.url}" class="img-fluid rounded" style="max-height:120px">`;
                        else if (res.type === 'audio' && res.url) html = `<audio controls class="w-full"><source src="${res.url}" type="audio/mpeg"></audio>`;
                        else if (res.type === 'video' && res.url) html = `<video controls class="w-full" style="max-height:120px"><source src="${res.url}"></video>`;
                        else html = `<span class="text-emerald-600">${JSON.stringify(res)}</span>`;
                        if (resultEl) resultEl.innerHTML = html;
                    } else if (data.error_message) {
                        if (resultEl) resultEl.innerHTML = `<span class="text-red-600">${data.error_message}</span>`;
                    }
                }
            })
            .catch(() => {});
        }, 3000);

        activePolls.set(jobId, interval);
    }
})();
</script>
