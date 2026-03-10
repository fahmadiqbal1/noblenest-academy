@extends('layouts.app')

@section('content')
<style>
    .orch-hero,
    .orch-card,
    .provider-card,
    .job-card,
    .stat-card {
        background: rgba(255,255,255,0.82);
        border: 1px solid rgba(24, 34, 47, 0.08);
        box-shadow: 0 24px 48px rgba(24, 34, 47, 0.10);
    }
    .orch-hero {
        position: relative;
        overflow: hidden;
        border-radius: 1.75rem;
        padding: 1.8rem;
        margin-bottom: 1.5rem;
        background:
            radial-gradient(circle at 14% 20%, rgba(242,165,65,0.16), transparent 20%),
            radial-gradient(circle at 92% 12%, rgba(13,92,99,0.16), transparent 24%),
            linear-gradient(145deg, rgba(255,255,255,0.96), rgba(238,244,246,0.94));
    }
    .orch-hero::after {
        content: '';
        position: absolute;
        inset: auto -10% -22% auto;
        width: 260px;
        height: 260px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(13,92,99,0.16), transparent 70%);
    }
    .orch-card,
    .provider-card,
    .job-card,
    .stat-card {
        border-radius: 1.4rem;
    }
    .orch-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1rem;
    }
    .stat-card {
        padding: 1.1rem 1.2rem;
        height: 100%;
    }
    .stat-card__value {
        font-size: 2.2rem;
        font-weight: 800;
        line-height: 1;
    }
    .provider-stack {
        display: grid;
        gap: 1rem;
    }
    .provider-card {
        padding: 1rem;
    }
    .provider-card__top {
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        align-items: flex-start;
    }
    .provider-card__meta {
        display: flex;
        flex-wrap: wrap;
        gap: 0.45rem;
        margin-top: 0.85rem;
    }
    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        border-radius: 999px;
        padding: 0.38rem 0.75rem;
        font-size: 0.76rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }
    .status-pill--live { background: rgba(22, 134, 107, 0.12); color: #16866b; }
    .status-pill--failed { background: rgba(196, 69, 54, 0.12); color: #c44536; }
    .status-pill--unchecked,
    .status-pill--configured { background: rgba(13, 92, 99, 0.10); color: #0d5c63; }
    .provider-helper {
        color: #5f6c7b;
        font-size: 0.9rem;
        line-height: 1.55;
    }
    .job-feed {
        display: grid;
        gap: 1rem;
    }
    .job-card {
        padding: 1.15rem;
    }
    .job-card__result {
        white-space: pre-wrap;
        max-height: 150px;
        overflow-y: auto;
        background: rgba(241, 247, 248, 0.86);
        border-radius: 1rem;
        padding: 0.9rem;
        border: 1px solid rgba(24, 34, 47, 0.06);
    }
    .orch-form .form-control,
    .orch-form .form-select {
        border-radius: 1rem;
        border-color: rgba(24, 34, 47, 0.12);
        min-height: 48px;
    }
    .orch-form textarea.form-control {
        min-height: 130px;
    }
    .orch-section-title {
        font-size: 0.78rem;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        font-weight: 800;
        color: #0d5c63;
    }
</style>

<div class="container-fluid py-2 py-lg-3">
    <div class="orch-hero">
        <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap position-relative" style="z-index: 1;">
            <div class="col-12 col-xl-7 px-0">
                <div class="orch-section-title mb-2">AI control center</div>
                <h1 class="mb-2 text-primary"><i class="bi bi-robot"></i> AI Orchestrator</h1>
                <p class="text-muted mb-0">Connect providers, validate whether they are truly reachable, dispatch generation jobs, and keep moderation and publishing in one place.</p>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <button class="btn btn-outline-info btn-sm" id="scanBtn" onclick="scanCurriculum()">
                <i class="bi bi-search"></i> Scan Curriculum Gaps
            </button>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addProviderModal">
                <i class="bi bi-plug"></i> Add AI Provider
            </button>
        </div>
    </div>
    </div>

    @if(session('status'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('status') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    {{-- Stats Row --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-2">
            <div class="stat-card text-center">
                <div>
                    <div class="fs-4 fw-bold text-secondary">{{ $stats['queued'] }}</div>
                    <div class="small text-muted">Queued</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="stat-card text-center">
                <div>
                    <div class="fs-4 fw-bold text-primary">{{ $stats['running'] }}</div>
                    <div class="small text-muted">Running</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="stat-card text-center">
                <div>
                    <div class="fs-4 fw-bold text-success">{{ $stats['completed'] }}</div>
                    <div class="small text-muted">Completed</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="stat-card text-center">
                <div>
                    <div class="fs-4 fw-bold text-danger">{{ $stats['failed'] }}</div>
                    <div class="small text-muted">Failed</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="stat-card text-center">
                <div>
                    <div class="fs-4 fw-bold text-warning">{{ $stats['pending_moderation'] }}</div>
                    <div class="small text-muted">Needs Review</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Left: Provider + Dispatch --}}
        <div class="col-lg-4">

            {{-- Connected Providers --}}
            <div class="orch-card p-3 mb-4">
                <div class="orch-card-header">
                    <div>
                        <div class="orch-section-title mb-1">Providers</div>
                        <div class="fw-bold"><i class="bi bi-plug-fill text-success"></i> Connected AI Providers</div>
                    </div>
                    <span class="badge text-bg-light border">{{ $providers->count() }} total</span>
                </div>
                <div class="provider-stack">
                    @forelse($providers as $p)
                    <div class="provider-card">
                        <div class="provider-card__top">
                            <div>
                                <div class="d-flex flex-wrap align-items-center gap-2">
                                    <span class="fw-semibold">{{ $p->name }}</span>
                                    <span class="badge bg-secondary-subtle text-secondary-emphasis border">{{ $p->slug }}</span>
                                    <span class="status-pill status-pill--{{ in_array($p->connection_status, ['live', 'failed', 'configured'], true) ? $p->connection_status : 'unchecked' }}">
                                        <i class="bi bi-{{ $p->connection_status === 'live' ? 'broadcast-pin' : ($p->connection_status === 'failed' ? 'x-octagon' : 'activity') }}"></i>
                                        {{ $p->connection_status ?? 'unchecked' }}
                                    </span>
                                </div>
                                <div class="provider-helper mt-2">{{ $p->connection_message ?: 'Provider added, but no verification details are available yet.' }}</div>
                                <div class="provider-card__meta">
                                    <span class="badge bg-light text-dark border">Driver: {{ strtoupper(data_get($p->extra_config, 'driver', 'auto')) }}</span>
                                    @if($p->model)
                                        <span class="badge bg-light text-dark border">Model: {{ $p->model }}</span>
                                    @endif
                                    <span class="badge {{ $p->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $p->is_active ? 'Active' : 'Disabled' }}</span>
                                    @if($p->last_checked_at)
                                        <span class="badge bg-light text-dark border">Checked {{ $p->last_checked_at->diffForHumans() }}</span>
                                    @endif
                                    @if($p->last_live_at)
                                        <span class="badge bg-light text-dark border">Last live {{ $p->last_live_at->diffForHumans() }}</span>
                                    @endif
                                    @foreach($p->capabilities ?? [] as $cap)
                                        <span class="badge bg-light text-dark border">{{ $cap }}</span>
                                    @endforeach
                                </div>
                            </div>
                            <div class="d-flex gap-1 flex-wrap justify-content-end">
                                <form method="POST" action="{{ route('admin.orchestrator.verifyProvider', $p) }}">
                                    @csrf
                                    <button class="btn btn-outline-primary btn-sm" title="Verify live status">
                                        <i class="bi bi-arrow-repeat"></i>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.orchestrator.toggleProvider', $p) }}" style="display:inline">
                                    @csrf
                                    <button class="btn btn-sm {{ $p->is_active ? 'btn-success' : 'btn-outline-secondary' }}" title="{{ $p->is_active ? 'Disable' : 'Enable' }}">
                                        <i class="bi bi-{{ $p->is_active ? 'toggle-on' : 'toggle-off' }}"></i>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.orchestrator.destroyProvider', $p) }}" onsubmit="return confirm('Remove provider?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-3 small">No providers yet. Add one to get started.</div>
                    @endforelse
                </div>
            </div>

            {{-- Dispatch Job --}}
            <div class="orch-card p-3">
                <div class="orch-card-header">
                    <div>
                        <div class="orch-section-title mb-1">Dispatch</div>
                        <div class="fw-bold"><i class="bi bi-send text-primary"></i> Generate Content</div>
                    </div>
                </div>
                <div>
                    <form method="POST" action="{{ route('admin.orchestrator.dispatch') }}" class="orch-form">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small">Job Type</label>
                            <select name="type" class="form-select form-select-sm" required>
                                @foreach($jobTypes as $val => $label)
                                    <option value="{{ $val }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small">AI Provider</label>
                            <select name="provider" class="form-select form-select-sm">
                                <option value="mock">Mock (no API key needed)</option>
                                @foreach($providers->where('is_active', true) as $p)
                                    <option value="{{ $p->slug }}">{{ $p->name }} @if($p->connection_status) · {{ $p->connection_status }} @endif</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small">Language / Locale</label>
                            <select name="locale" class="form-select form-select-sm">
                                @foreach($locales as $code => $name)
                                    <option value="{{ $code }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small">Prompt / Brief <span class="text-danger">*</span></label>
                            <textarea name="prompt" class="form-control form-control-sm" rows="4"
                                placeholder="e.g. Create a 15-minute tracing activity for numbers 1-5 for age 3-4..." required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small">GitHub Repo URL (optional)</label>
                            <input type="url" name="repo_url" class="form-control form-control-sm"
                                placeholder="https://github.com/owner/repo">
                            <div class="form-text">Extract educational content from a public GitHub repo.</div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-lightning-charge"></i> Dispatch Job
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Right: Job Queue --}}
        <div class="col-lg-8">
            <div class="orch-card p-3">
                <div class="orch-card-header">
                    <div>
                        <div class="orch-section-title mb-1">Queue</div>
                        <span class="fw-bold"><i class="bi bi-list-task"></i> Job Queue</span>
                    </div>
                    <span class="badge bg-secondary">{{ $jobs->total() }} total</span>
                </div>
                <div class="job-feed" style="max-height: 75vh; overflow-y: auto; padding-right: 0.25rem;">
                    @forelse($jobs as $job)
                    <div class="job-card">
                        <div class="d-flex justify-content-between align-items-start gap-2 flex-wrap">
                            <div>
                                <span class="badge bg-{{
                                    $job->status === 'completed' ? 'success' :
                                    ($job->status === 'failed' ? 'danger' :
                                    ($job->status === 'running' ? 'primary' : 'secondary'))
                                }}">{{ $job->status }}</span>
                                <span class="badge bg-{{ $job->moderation_status === 'approved' ? 'success' : ($job->moderation_status === 'rejected' ? 'danger' : 'warning text-dark') }} ms-1">
                                    {{ $job->moderation_status }}
                                </span>
                                <span class="fw-semibold ms-2">{{ $job->type }}</span>
                                <span class="text-muted small ms-1">· {{ strtoupper($job->locale) }} · {{ $job->provider }}</span>
                            </div>
                            <div class="text-muted small">
                                #{{ $job->id }} · {{ $job->created_at->diffForHumans() }}
                            </div>
                        </div>

                        {{-- Prompt --}}
                        <div class="mt-1 text-muted small">
                            <i class="bi bi-chat-left-quote"></i>
                            {{ Str::limit($job->payload['prompt'] ?? '—', 120) }}
                        </div>

                        {{-- Result --}}
                        @if($job->result)
                        <div class="job-card__result mt-2 small">{{ $job->result['content'] ?? json_encode($job->result) }}</div>
                        @endif

                        {{-- Error --}}
                        @if($job->error_message)
                        <div class="mt-1 text-danger small"><i class="bi bi-exclamation-circle"></i> {{ $job->error_message }}</div>
                        @endif

                        {{-- Actions --}}
                        <div class="mt-2 d-flex gap-2 flex-wrap">
                            @if($job->status === 'completed' && $job->moderation_status === 'pending')
                                <form method="POST" action="{{ route('admin.orchestrator.approve', $job) }}">
                                    @csrf
                                    <button class="btn btn-success btn-sm"><i class="bi bi-check-circle"></i> Approve & Publish</button>
                                </form>
                                <form method="POST" action="{{ route('admin.orchestrator.reject', $job) }}">
                                    @csrf
                                    <button class="btn btn-outline-danger btn-sm"><i class="bi bi-x-circle"></i> Reject</button>
                                </form>
                            @endif
                            @if($job->status === 'failed')
                                <form method="POST" action="{{ route('admin.orchestrator.retry', $job) }}">
                                    @csrf
                                    <button class="btn btn-warning btn-sm"><i class="bi bi-arrow-clockwise"></i> Retry</button>
                                </form>
                            @endif
                            <form method="POST" action="{{ route('admin.orchestrator.destroyJob', $job) }}" onsubmit="return confirm('Delete this job?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-outline-secondary btn-sm"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-robot display-4 d-block mb-2"></i>
                        No jobs yet. Use the form to generate your first piece of content!
                    </div>
                    @endforelse
                </div>
                @if($jobs->hasPages())
                <div class="pt-3">{{ $jobs->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Scan result panel --}}
<div id="scanResult" class="mt-3" style="display:none">
    <div class="alert alert-info" id="scanResultContent"></div>
</div>

{{-- Add Provider Modal --}}
<div class="modal fade" id="addProviderModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-plug"></i> Add AI Provider</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.orchestrator.storeProvider') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Provider Family</label>
                        <select name="driver" class="form-select">
                            <option value="openai">OpenAI compatible</option>
                            <option value="anthropic">Anthropic Claude</option>
                            <option value="gemini">Google Gemini</option>
                            <option value="github">GitHub repository source</option>
                        </select>
                        <div class="form-text">Pick the provider family so health checks and chat requests use the correct API contract.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Display Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. OpenAI GPT-4o" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Slug (unique ID) <span class="text-danger">*</span></label>
                        <input type="text" name="slug" class="form-control" placeholder="e.g. openai" required pattern="[a-z0-9_-]+">
                        <div class="form-text">Lowercase letters, numbers, hyphens only.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">API Base URL</label>
                        <input type="url" name="api_base_url" class="form-control" placeholder="https://api.openai.com/v1">
                        <div class="form-text">Leave blank for OpenAI-compatible default. Use for Anthropic, Gemini proxies, or self-hosted LLMs.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">API Key</label>
                        <input type="password" name="api_key" class="form-control" placeholder="sk-...">
                        <div class="form-text">Stored encrypted. Leave blank for public APIs or GitHub repo extraction.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Default Model</label>
                        <input type="text" name="model" class="form-control" placeholder="e.g. gpt-4o-mini, claude-3-haiku-20240307">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Capabilities</label>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach(['text','image','tts','video','translation','quiz'] as $cap)
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="capabilities[]" value="{{ $cap }}" id="cap_{{ $cap }}">
                                    <label class="form-check-label" for="cap_{{ $cap }}">{{ ucfirst($cap) }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">GitHub Repo URL (for repo extraction)</label>
                        <input type="url" name="repo_url" class="form-control" placeholder="https://github.com/owner/curriculum-repo">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Provider</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function scanCurriculum() {
    const btn = document.getElementById('scanBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Scanning...';
    fetch('{{ route('admin.orchestrator.scan') }}', {
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
    })
    .then(r => r.json())
    .then(data => {
        const panel = document.getElementById('scanResult');
        const content = document.getElementById('scanResultContent');
        let html = `<strong><i class="bi bi-search"></i> Curriculum Scan:</strong> ${data.total_gaps} gap(s) found.<br>${data.suggestion}`;
        if (data.gaps && data.gaps.length) {
            html += '<ul class="mt-2 mb-0">';
            data.gaps.slice(0, 10).forEach(g => {
                html += `<li>Age <b>${g.age}</b>: missing <b>${g.skill}</b></li>`;
            });
            if (data.gaps.length > 10) html += `<li>...and ${data.gaps.length - 10} more</li>`;
            html += '</ul>';
        }
        content.innerHTML = html;
        panel.style.display = 'block';
        panel.scrollIntoView({ behavior: 'smooth' });
    })
    .catch(() => alert('Scan failed. Please try again.'))
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-search"></i> Scan Curriculum Gaps';
    });
}
</script>
@endsection
