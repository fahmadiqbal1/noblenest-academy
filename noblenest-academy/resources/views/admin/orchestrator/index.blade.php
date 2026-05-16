@extends('layouts.admin')

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
    .status-pill--configured { background: rgba(124, 58, 237, 0.10); color: #7C3AED; }
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
        color: #7C3AED;
    }
</style>

<div class="w-full px-4 py-2 py-lg-3">
    <div class="orch-hero">
        <div class="flex justify-between items-start gap-3 flex-wrap relative" style="z-index: 1;">
            <div class="w-full xl:w-7/12 px-0">
                <div class="orch-section-title mb-2">AI control center</div>
                <h1 class="mb-2 text-[var(--color-primary)]"><x-ui.icon name="bot" /> AI Orchestrator</h1>
                <p class="text-[var(--color-text-muted)] mb-0">Connect providers, validate whether they are truly reachable, dispatch generation jobs, and keep moderation and publishing in one place.</p>
            </div>
            <div class="flex gap-2 flex-wrap">
                <button class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed border-2 border-sky-600 text-sky-600 hover:bg-sky-600 hover:text-white px-3 py-1.5 text-sm" id="scanBtn" onclick="scanCurriculum()">
                <x-ui.icon name="search" /> Scan Curriculum Gaps
            </button>
            <button class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed bg-violet-600 text-white hover:bg-violet-700 px-3 py-1.5 text-sm">
                <x-ui.icon name="plug" /> Add AI Provider
            </button>
        </div>
    </div>
    </div>

    @if(session('status'))
        <div class="flex items-start gap-3 p-4 rounded-lg border bg-emerald-50 border-emerald-200 text-emerald-800">{{ session('status') }}<button type="button" class=""></button></div>
    @endif
    @if(session('error'))
        <div class="flex items-start gap-3 p-4 rounded-lg border bg-red-50 border-red-200 text-red-800">{{ session('error') }}<button type="button" class=""></button></div>
    @endif

    {{-- Stats Row --}}
    <div class="flex flex-wrap gap-3 mb-4">
        <div class="w-6/12 md:w-2/12">
            <div class="stat-card text-center">
                <div>
                    <div class="text-2xl font-bold text-gray-500">{{ $stats['queued'] }}</div>
                    <div class="text-sm text-[var(--color-text-muted)]">Queued</div>
                </div>
            </div>
        </div>
        <div class="w-6/12 md:w-2/12">
            <div class="stat-card text-center">
                <div>
                    <div class="text-2xl font-bold text-[var(--color-primary)]">{{ $stats['running'] }}</div>
                    <div class="text-sm text-[var(--color-text-muted)]">Running</div>
                </div>
            </div>
        </div>
        <div class="w-6/12 md:w-2/12">
            <div class="stat-card text-center">
                <div>
                    <div class="text-2xl font-bold text-emerald-600">{{ $stats['completed'] }}</div>
                    <div class="text-sm text-[var(--color-text-muted)]">Completed</div>
                </div>
            </div>
        </div>
        <div class="w-6/12 md:w-2/12">
            <div class="stat-card text-center">
                <div>
                    <div class="text-2xl font-bold text-red-600">{{ $stats['failed'] }}</div>
                    <div class="text-sm text-[var(--color-text-muted)]">Failed</div>
                </div>
            </div>
        </div>
        <div class="w-6/12 md:w-2/12">
            <div class="stat-card text-center">
                <div>
                    <div class="text-2xl font-bold text-amber-600">{{ $stats['pending_moderation'] }}</div>
                    <div class="text-sm text-[var(--color-text-muted)]">Needs Review</div>
                </div>
            </div>
        </div>
    </div>

    <div class="flex flex-wrap gap-4">
        {{-- Left: Provider + Dispatch --}}
        <div class="lg:w-4/12">

            {{-- Connected Providers --}}
            <div class="orch-card p-3 mb-4">
                <div class="orch-card-header">
                    <div>
                        <div class="orch-section-title mb-1">Providers</div>
                        <div class="font-bold"><x-ui.icon name="plug" class="text-emerald-600" /> Connected AI Providers</div>
                    </div>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-900 border">{{ $providers->count() }} total</span>
                </div>
                <div class="provider-stack">
                    @forelse($providers as $p)
                    <div class="provider-card">
                        <div class="provider-card__top">
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="font-semibold">{{ $p->name }}</span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700 border">{{ $p->slug }}</span>
                                    <span class="status-pill status-pill--{{ in_array($p->connection_status, ['live', 'failed', 'configured'], true) ? $p->connection_status : 'unchecked' }}"
                                          id="status-pill-{{ $p->id }}">
                                        <x-ui.icon name="{{ $p->connection_status === 'live' ? 'circle-play' : ($p->connection_status === 'failed' ? 'octagon-alert' : 'clock') }}" />
                                        {{ $p->connection_status ?? 'unchecked' }}
                                    </span>
                                </div>
                                <div class="provider-helper mt-2">{{ $p->connection_message ?: 'Provider added, but no verification details are available yet.' }}</div>
                                <div class="provider-card__meta">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-50 text-gray-900 border">Driver: {{ strtoupper(data_get($p->extra_config, 'driver', 'auto')) }}</span>
                                    @if($p->model)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-50 text-gray-900 border">Model: {{ $p->model }}</span>
                                    @endif
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $p->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $p->is_active ? 'Active' : 'Disabled' }}</span>
                                    @if($p->last_checked_at)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-50 text-gray-900 border">Checked {{ $p->last_checked_at->diffForHumans() }}</span>
                                    @endif
                                    @if($p->last_live_at)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-50 text-gray-900 border">Last live {{ $p->last_live_at->diffForHumans() }}</span>
                                    @endif
                                    @foreach($p->capabilities ?? [] as $cap)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-50 text-gray-900 border">{{ $cap }}</span>
                                    @endforeach
                                </div>
                            </div>
                            <div class="flex gap-1 flex-wrap justify-end">
                                <button class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed border-2 border-violet-600 text-violet-600 hover:bg-violet-600 hover:text-white px-3 py-1.5 text-sm"
                                        id="verify-btn-{{ $p->id }}"
                                        title="Verify live status"
                                        onclick="verifyProvider({{ $p->id }}, '{{ route('admin.orchestrator.verifyProvider', $p) }}', this)">
                                    <x-ui.icon name="rotate-cw" />
                                </button>
                                <form method="POST" action="{{ route('admin.orchestrator.toggleProvider', $p) }}" style="display:inline">
                                    @csrf
                                    <button class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-3 py-1.5 text-sm {{ $p->is_active ? 'bg-emerald-600 text-white hover:bg-emerald-700' : 'border-2 border-gray-300 text-gray-700 hover:bg-gray-100' }}" title="{{ $p->is_active ? 'Disable' : 'Enable' }}">
                                        <x-ui.icon name="{{ $p->is_active ? 'check-circle' : 'x-circle' }}" />
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.orchestrator.destroyProvider', $p) }}" onsubmit="return confirm('Remove provider?')">
                                    @csrf @method('DELETE')
                                    <button class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed border-2 border-red-600 text-red-600 hover:bg-red-600 hover:text-white px-3 py-1.5 text-sm"><x-ui.icon name="trash" /></button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-[var(--color-text-muted)] py-3 text-sm">No providers yet. Add one to get started.</div>
                    @endforelse
                </div>
            </div>

            {{-- Dispatch Job --}}
            <div class="orch-card p-3">
                <div class="orch-card-header">
                    <div>
                        <div class="orch-section-title mb-1">Dispatch</div>
                        <div class="font-bold"><x-ui.icon name="send" class="text-[var(--color-primary)]" /> Generate Content</div>
                    </div>
                </div>
                <div>
                    <form method="POST" action="{{ route('admin.orchestrator.dispatch') }}" class="orch-form">
                        @csrf
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Job Type</label>
                            <select name="type" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500 px-2 py-1 text-sm" required>
                                @foreach($jobTypes as $val => $label)
                                    <option value="{{ $val }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">AI Provider</label>
                            <select name="provider" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500 px-2 py-1 text-sm">
                                <option value="mock">Mock (no API key needed)</option>
                                @foreach($providers->where('is_active', true) as $p)
                                    <option value="{{ $p->slug }}">{{ $p->name }} @if($p->connection_status) · {{ $p->connection_status }} @endif</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Language / Locale</label>
                            <select name="locale" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500 px-2 py-1 text-sm">
                                @foreach($locales as $code => $name)
                                    <option value="{{ $code }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Prompt / Brief <span class="text-red-600">*</span></label>
                            <textarea name="prompt" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500 px-2 py-1 text-sm" rows="4"
                                placeholder="e.g. Create a 15-minute tracing activity for numbers 1-5 for age 3-4..." required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">GitHub Repo URL (optional)</label>
                            <input type="url" name="repo_url" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500 px-2 py-1 text-sm"
                                placeholder="https://github.com/owner/repo">
                            <div class="mt-1 text-sm text-[var(--color-text-muted)]">Extract educational content from a public GitHub repo.</div>
                        </div>
                        <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed bg-violet-600 text-white hover:bg-violet-700 w-full">
                            <x-ui.icon name="zap" /> Dispatch Job
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Right: Job Queue --}}
        <div class="lg:w-8/12">
            <div class="orch-card p-3">
                <div class="orch-card-header">
                    <div>
                        <div class="orch-section-title mb-1">Queue</div>
                        <span class="font-bold"><x-ui.icon name="list-checks" /> Job Queue</span>
                    </div>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-500">{{ $jobs->total() }} total</span>
                </div>
                <div class="job-feed" style="max-height: 75vh; overflow-y: auto; padding-right: 0.25rem;">
                    @forelse($jobs as $job)
                    <div class="job-card">
                        <div class="flex justify-between items-start gap-2 flex-wrap">
                            <div>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $job->status === 'completed' ? 'bg-emerald-100 text-emerald-700' : ($job->status === 'failed' ? 'bg-red-100 text-red-700' : ($job->status === 'running' ? 'bg-violet-100 text-violet-700' : 'bg-gray-100 text-gray-700')) }}">{{ $job->status }}</span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $job->moderation_status === 'approved' ? 'bg-emerald-100 text-emerald-700' : ($job->moderation_status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-800') }} ms-1">
                                    {{ $job->moderation_status }}
                                </span>
                                <span class="font-semibold ms-2">{{ $job->type }}</span>
                                <span class="text-[var(--color-text-muted)] text-sm ms-1">· {{ strtoupper($job->locale) }} · {{ $job->provider }}</span>
                            </div>
                            <div class="text-[var(--color-text-muted)] text-sm">
                                #{{ $job->id }} · {{ $job->created_at->diffForHumans() }}
                            </div>
                        </div>

                        {{-- Prompt --}}
                        <div class="mt-1 text-[var(--color-text-muted)] text-sm">
                            <x-ui.icon name="message-square-quote" />
                            {{ Str::limit($job->payload['prompt'] ?? '—', 120) }}
                        </div>

                        {{-- Result --}}
                        @if($job->result)
                        @php
                            $res     = $job->result;
                            $resType = $res['type'] ?? 'text';
                            $resUrl  = $res['url'] ?? null;
                            $resTxt  = $res['content'] ?? json_encode($res);
                        @endphp
                        <div class="job-card__result mt-2 text-sm">
                            @if($resType === 'image' && $resUrl)
                                <img src="{{ $resUrl }}" alt="Generated image" class="img-fluid rounded mb-1" style="max-height:200px">
                            @elseif($resType === 'audio' && $resUrl)
                                <audio controls class="w-full mb-1"><source src="{{ $resUrl }}" type="audio/mpeg"></audio>
                            @elseif($resType === 'video' && $resUrl)
                                <video controls class="w-full mb-1" style="max-height:200px"><source src="{{ $resUrl }}"></video>
                            @endif
                            <div style="white-space:pre-wrap">{{ $resTxt }}</div>
                        </div>
                        @endif

                        {{-- Error --}}
                        @if($job->error_message)
                        <div class="mt-1 text-red-600 text-sm"><x-ui.icon name="alert-circle" /> {{ $job->error_message }}</div>
                        @endif

                        {{-- Actions --}}
                        <div class="mt-2 flex gap-2 flex-wrap">
                            @if($job->status === 'completed' && $job->moderation_status === 'pending')
                                <form method="POST" action="{{ route('admin.orchestrator.approve', $job) }}">
                                    @csrf
                                    <button class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed bg-emerald-600 text-white hover:bg-emerald-700 px-3 py-1.5 text-sm"><x-ui.icon name="check-circle" /> Approve & Publish</button>
                                </form>
                                <form method="POST" action="{{ route('admin.orchestrator.reject', $job) }}">
                                    @csrf
                                    <button class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed border-2 border-red-600 text-red-600 hover:bg-red-600 hover:text-white px-3 py-1.5 text-sm"><x-ui.icon name="x-circle" /> Reject</button>
                                </form>
                            @endif
                            @if($job->status === 'failed')
                                <form method="POST" action="{{ route('admin.orchestrator.retry', $job) }}">
                                    @csrf
                                    <button class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed bg-amber-500 text-gray-900 hover:bg-amber-600 px-3 py-1.5 text-sm"><x-ui.icon name="rotate-cw" /> Retry</button>
                                </form>
                            @endif
                            <form method="POST" action="{{ route('admin.orchestrator.destroyJob', $job) }}" onsubmit="return confirm('Delete this job?')">
                                @csrf @method('DELETE')
                                <button class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed border-2 border-gray-300 text-gray-700 hover:bg-gray-100 px-3 py-1.5 text-sm"><x-ui.icon name="trash" /></button>
                            </form>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-[var(--color-text-muted)] py-5">
                        <x-ui.icon name="bot" class="text-4xl font-bold block mb-2" />
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
    <div class="flex items-start gap-3 p-4 rounded-lg border bg-sky-50 border-sky-200 text-sky-800" id="scanResultContent"></div>
</div>

@include('admin.orchestrator.media-panel')

{{-- Add Provider Modal --}}
<div class="fixed inset-0 z-50 hidden" id="addProviderModal" tabindex="-1">
    <div class="relative w-full max-w-lg mx-auto mt-12">
        <div class="bg-white rounded-xl shadow-xl border border-gray-200">
            <div class="px-5 py-3 border-b border-gray-200 font-semibold flex items-center justify-between">
                <h5 class="text-lg font-bold"><x-ui.icon name="plug" /> Add AI Provider</h5>
                <button type="button" class=""></button>
            </div>
            <form method="POST" action="{{ route('admin.orchestrator.storeProvider') }}">
                @csrf
                <div class="p-5">
                    @if($errors->any())
                        <div class="flex items-start gap-3 p-4 rounded-lg border bg-red-50 border-red-200 text-red-800 py-2 text-sm mb-3">
                            <ul class="mb-0 ps-3">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Provider Family</label>
                        <select name="driver" id="driverSelect" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500" onchange="onDriverChange(this.value)">
                            <option value="openai">OpenAI compatible (GPT-4o, etc.)</option>
                            <option value="anthropic">Anthropic Claude</option>
                            <option value="gemini">Google Gemini</option>
                            <option value="github">GitHub repository source</option>
                            <option value="stability">Stability AI (image generation)</option>
                            <option value="elevenlabs">ElevenLabs (text-to-speech)</option>
                            <option value="replicate">Replicate (video / image models)</option>
                            <option value="runway">RunwayML (video generation)</option>
                            <option value="openai-image">OpenAI DALL-E (image generation)</option>
                        </select>
                        <div id="driverHelp" class="mt-1 text-sm text-[var(--color-text-muted)]">Pick the provider family so health checks and requests use the correct API contract.</div>
                    </div>
                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Display Name <span class="text-red-600">*</span></label>
                        <input type="text" name="name" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500" placeholder="e.g. OpenAI GPT-4o" required>
                    </div>
                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Slug (unique ID) <span class="text-red-600">*</span></label>
                        <input type="text" name="slug" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500" placeholder="e.g. openai" required pattern="[a-z0-9_-]+">
                        <div class="mt-1 text-sm text-[var(--color-text-muted)]">Lowercase letters, numbers, hyphens only.</div>
                    </div>
                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">API Base URL</label>
                        <input type="url" name="api_base_url" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500" placeholder="https://api.openai.com/v1">
                        <div class="mt-1 text-sm text-[var(--color-text-muted)]">Leave blank for OpenAI-compatible default. Use for Anthropic, Gemini proxies, or self-hosted LLMs.</div>
                    </div>
                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">API Key</label>
                        <input type="password" name="api_key" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500" placeholder="sk-...">
                        <div class="mt-1 text-sm text-[var(--color-text-muted)]">Stored encrypted. Leave blank for public APIs or GitHub repo extraction.</div>
                    </div>
                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Default Model</label>
                        <input type="text" name="model" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500" placeholder="e.g. gpt-4o-mini, claude-3-haiku-20240307">
                    </div>
                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Capabilities</label>
                        <div class="flex flex-wrap gap-2">
                            @foreach(['text','image','tts','video','translation','quiz'] as $cap)
                                <div class="flex items-center gap-2 inline-flex">
                                    <input class="w-4 h-4 rounded border-gray-300 text-violet-600 focus:ring-violet-500" type="checkbox" name="capabilities[]" value="{{ $cap }}" id="cap_{{ $cap }}">
                                    <label class="text-sm" for="cap_{{ $cap }}">{{ ucfirst($cap) }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">GitHub Repo URL (for repo extraction)</label>
                        <input type="url" name="repo_url" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500" placeholder="https://github.com/owner/curriculum-repo">
                    </div>
                </div>
                <div class="px-5 py-3 border-t border-gray-200 flex justify-end gap-2">
                    <button type="button" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed bg-gray-500 text-white hover:bg-gray-600">Cancel</button>
                    <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed bg-violet-600 text-white hover:bg-violet-700">Add Provider</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Fix Bootstrap modal stacking context: .app-main has z-index which traps the modal behind the backdrop
document.addEventListener('DOMContentLoaded', function() {
    var modal = document.getElementById('addProviderModal');
    if (modal) {
        document.body.appendChild(modal);
    }
});

const DRIVER_META = {
    openai:       { help: 'OpenAI-compatible: GPT-4o, GPT-4o-mini, etc. API key: sk-... Base URL optional for proxies.', cap: 'text' },
    anthropic:    { help: 'Anthropic Claude — use key starting with sk-ant-... No base URL needed.', cap: 'text' },
    gemini:       { help: 'Google Gemini — get key from Google AI Studio (aistudio.google.com).', cap: 'text' },
    github:       { help: 'GitHub Repo — paste a public repo URL. No API key needed. Extracts README curriculum content.', cap: 'github' },
    stability:    { help: 'Stability AI — generates images. Key from platform.stability.ai. No base URL needed.', cap: 'image' },
    elevenlabs:   { help: 'ElevenLabs TTS — text-to-speech mp3. Key from elevenlabs.io. Optionally set voice_id in extra_config.', cap: 'tts' },
    replicate:    { help: 'Replicate — runs open-source video/image models (e.g. minimax/video-01). Key from replicate.com.', cap: 'video' },
    runway:       { help: 'RunwayML Gen-4 Turbo — text-to-video. Key from dev.runwayml.com. No base URL needed.', cap: 'video' },
    'openai-image': { help: 'OpenAI DALL-E 3 — image generation using your existing OpenAI key. No base URL needed.', cap: 'image' },
};

function onDriverChange(val) {
    const meta = DRIVER_META[val] || {};
    document.getElementById('driverHelp').textContent = meta.help || 'Pick the provider family so health checks use the correct API contract.';
}

function scanCurriculum() {
    const btn = document.getElementById('scanBtn');
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    if (!csrfMeta) { alert('CSRF token missing — please reload the page.'); return; }
    if (btn.dataset.scanning === '1') return; // debounce

    btn.dataset.scanning = '1';
    btn.disabled = true;
    btn.innerHTML = '<span class="inline-block w-6 h-6 border-2 border-current border-t-transparent rounded-full animate-spin w-4 h-4"></span> Scanning...';

    const controller = new AbortController();
    const timeout = setTimeout(() => controller.abort(), 30000);

    fetch('{{ route('admin.orchestrator.scan') }}', {
        signal: controller.signal,
        headers: { 'X-CSRF-TOKEN': csrfMeta.content, 'Accept': 'application/json' }
    })
    .then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
    .then(data => {
        const panel = document.getElementById('scanResult');
        const content = document.getElementById('scanResultContent');
        let html = `<strong><x-ui.icon name="search" /> Curriculum Scan:</strong> ${data.total_gaps} gap(s) found.<br>${data.suggestion || ''}`;
        if (data.gaps && data.gaps.length) {
            html += '<ul class="mt-2 mb-0">';
            data.gaps.slice(0, 10).forEach(g => {
                html += `<li>Age <b>${g.age}</b>: missing <b>${g.subject || g.skill}</b></li>`;
            });
            if (data.gaps.length > 10) html += `<li>...and ${data.gaps.length - 10} more</li>`;
            html += '</ul>';
        }
        content.innerHTML = html;
        panel.style.display = 'block';
        panel.scrollIntoView({ behavior: 'smooth' });
    })
    .catch(err => {
        if (err.name === 'AbortError') {
            alert('Scan timed out after 30 seconds. Please try again.');
        } else {
            alert('Scan failed: ' + err.message);
        }
    })
    .finally(() => {
        clearTimeout(timeout);
        btn.dataset.scanning = '';
        btn.disabled = false;
        btn.innerHTML = '<x-ui.icon name="search" /> Scan Curriculum Gaps';
    });
}

let _verifyControllers = {};
function verifyProvider(id, url, btn) {
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    if (!csrfMeta) { alert('CSRF token missing — please reload.'); return; }
    if (btn.dataset.verifying === '1') return;

    // Cancel any in-flight request for this provider
    if (_verifyControllers[id]) _verifyControllers[id].abort();
    const controller = new AbortController();
    _verifyControllers[id] = controller;
    const timeout = setTimeout(() => controller.abort(), 25000);

    btn.dataset.verifying = '1';
    btn.disabled = true;
    btn.innerHTML = '<span class="inline-block w-6 h-6 border-2 border-current border-t-transparent rounded-full animate-spin w-4 h-4"></span>';

    fetch(url, {
        method: 'POST',
        signal: controller.signal,
        headers: {
            'X-CSRF-TOKEN': csrfMeta.content,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(r => r.json())
    .then(data => {
        const pill = document.getElementById('status-pill-' + id);
        if (pill) {
            const isOk = data.status === 'live' || data.ok;
            pill.className = 'badge rounded-pill ' + (isOk ? 'bg-success' : 'bg-warning text-dark');
            pill.textContent = data.status || (isOk ? 'live' : 'error');
        }
        const msg = data.message || (data.ok ? 'Provider is live.' : 'Check failed.');
        btn.title = msg;
    })
    .catch(err => {
        if (err.name !== 'AbortError') btn.title = 'Verify failed: ' + err.message;
    })
    .finally(() => {
        clearTimeout(timeout);
        delete _verifyControllers[id];
        btn.dataset.verifying = '';
        btn.disabled = false;
        btn.innerHTML = '<x-ui.icon name="rotate-cw" />';
    });
}

// Auto-reopen Add Provider modal if there were validation errors.
// TODO(phase-5): migrate this modal to Alpine.js; Bootstrap JS is no longer loaded.
@if($errors->any())
document.addEventListener('DOMContentLoaded', function () {
    if (!window.bootstrap || !window.bootstrap.Modal) return;
    new window.bootstrap.Modal(document.getElementById('addProviderModal')).show();
});
@endif
</script>
@endsection
