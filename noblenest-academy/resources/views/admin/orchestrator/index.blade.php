@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h1 class="mb-0 text-primary"><i class="bi bi-robot"></i> AI Orchestrator</h1>
            <p class="text-muted small mb-0">Your AI agent for curriculum design, content generation, and quality control.</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-info btn-sm" id="scanBtn" onclick="scanCurriculum()">
                <i class="bi bi-search"></i> Scan Curriculum Gaps
            </button>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addProviderModal">
                <i class="bi bi-plug"></i> Add AI Provider
            </button>
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
            <div class="card text-center border-secondary">
                <div class="card-body py-2">
                    <div class="fs-4 fw-bold text-secondary">{{ $stats['queued'] }}</div>
                    <div class="small text-muted">Queued</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="card text-center border-primary">
                <div class="card-body py-2">
                    <div class="fs-4 fw-bold text-primary">{{ $stats['running'] }}</div>
                    <div class="small text-muted">Running</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="card text-center border-success">
                <div class="card-body py-2">
                    <div class="fs-4 fw-bold text-success">{{ $stats['completed'] }}</div>
                    <div class="small text-muted">Completed</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="card text-center border-danger">
                <div class="card-body py-2">
                    <div class="fs-4 fw-bold text-danger">{{ $stats['failed'] }}</div>
                    <div class="small text-muted">Failed</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="card text-center border-warning">
                <div class="card-body py-2">
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
            <div class="card shadow-sm mb-4">
                <div class="card-header fw-bold"><i class="bi bi-plug-fill text-success"></i> Connected AI Providers</div>
                <div class="card-body p-0">
                    @forelse($providers as $p)
                    <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
                        <div>
                            <span class="fw-semibold">{{ $p->name }}</span>
                            <span class="badge bg-secondary ms-1 small">{{ $p->slug }}</span>
                            @if($p->model)
                                <span class="text-muted small ms-1">· {{ $p->model }}</span>
                            @endif
                            <br>
                            <span class="text-muted small">
                                @foreach($p->capabilities ?? [] as $cap)
                                    <span class="badge bg-light text-dark border me-1">{{ $cap }}</span>
                                @endforeach
                            </span>
                        </div>
                        <div class="d-flex gap-1">
                            <form method="POST" action="{{ route('admin.orchestrator.toggleProvider', $p) }}" style="display:inline">
                                @csrf
                                <button class="btn btn-xs btn-{{ $p->is_active ? 'success' : 'outline-secondary' }} btn-sm py-0 px-1" title="{{ $p->is_active ? 'Disable' : 'Enable' }}">
                                    <i class="bi bi-{{ $p->is_active ? 'toggle-on' : 'toggle-off' }}"></i>
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.orchestrator.destroyProvider', $p) }}" onsubmit="return confirm('Remove provider?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-xs btn-outline-danger btn-sm py-0 px-1"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-3 small">No providers yet. Add one to get started.</div>
                    @endforelse
                </div>
            </div>

            {{-- Dispatch Job --}}
            <div class="card shadow-sm">
                <div class="card-header fw-bold"><i class="bi bi-send text-primary"></i> Generate Content</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.orchestrator.dispatch') }}">
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
                                    <option value="{{ $p->slug }}">{{ $p->name }}</option>
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
            <div class="card shadow-sm">
                <div class="card-header fw-bold d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-list-task"></i> Job Queue</span>
                    <span class="badge bg-secondary">{{ $jobs->total() }} total</span>
                </div>
                <div class="card-body p-0" style="max-height: 75vh; overflow-y: auto;">
                    @forelse($jobs as $job)
                    <div class="border-bottom p-3">
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
                        <div class="mt-2 bg-light rounded p-2 small" style="white-space:pre-wrap;max-height:120px;overflow-y:auto;">{{ $job->result['content'] ?? json_encode($job->result) }}</div>
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
                <div class="card-footer">{{ $jobs->links() }}</div>
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
