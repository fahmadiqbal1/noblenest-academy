@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    {{-- Hero header --}}
    <div class="relative overflow-hidden rounded-2xl p-6 bg-gradient-to-br from-white via-slate-50 to-teal-50 border border-gray-200 shadow-sm">
        <div class="flex flex-wrap justify-between items-start gap-4 relative z-10">
            <div class="max-w-xl">
                <div class="text-xs font-bold uppercase tracking-widest text-violet-600 mb-1">AI Control Center</div>
                <h1 class="text-2xl font-bold text-[var(--color-primary)] flex items-center gap-2">
                    <x-ui.icon name="bot" /> AI Orchestrator
                </h1>
                <p class="text-sm text-gray-500 mt-1">Connect providers, validate live status, dispatch generation jobs, and manage moderation in one place.</p>
            </div>
            <div class="flex gap-2 flex-wrap">
                <button id="scanBtn" onclick="scanCurriculum()"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold border-2 border-sky-600 text-sky-600 hover:bg-sky-600 hover:text-white transition">
                    <x-ui.icon name="search" /> Scan Curriculum Gaps
                </button>
                <button onclick="openProviderModal()"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold bg-violet-600 text-white hover:bg-violet-700 transition">
                    <x-ui.icon name="plug" /> Add AI Provider
                </button>
            </div>
        </div>
    </div>

    @if(session('status'))
        <div class="flex items-start gap-3 p-4 rounded-lg border bg-emerald-50 border-emerald-200 text-emerald-800 text-sm">{{ session('status') }}</div>
    @endif
    @if(session('error'))
        <div class="flex items-start gap-3 p-4 rounded-lg border bg-red-50 border-red-200 text-red-800 text-sm">{{ session('error') }}</div>
    @endif

    {{-- Stats row --}}
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
        @foreach([
            ['label' => 'Queued',       'value' => $stats['queued'],             'color' => 'text-gray-600'],
            ['label' => 'Running',      'value' => $stats['running'],            'color' => 'text-[var(--color-primary)]'],
            ['label' => 'Completed',    'value' => $stats['completed'],          'color' => 'text-emerald-600'],
            ['label' => 'Failed',       'value' => $stats['failed'],             'color' => 'text-red-600'],
            ['label' => 'Needs Review', 'value' => $stats['pending_moderation'], 'color' => 'text-amber-600'],
        ] as $stat)
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 text-center">
            <div class="text-3xl font-extrabold {{ $stat['color'] }}">{{ $stat['value'] }}</div>
            <div class="text-xs text-gray-500 mt-0.5">{{ $stat['label'] }}</div>
        </div>
        @endforeach
    </div>

    {{-- Scan result + Auto-Fill --}}
    <div id="scanResult" class="hidden space-y-3">
        <div class="flex flex-wrap items-start gap-3 p-4 rounded-lg border bg-sky-50 border-sky-200 text-sky-800 text-sm" id="scanResultContent"></div>

        {{-- Auto-fill panel — shown after a scan with gaps --}}
        <div id="fillGapsPanel" class="hidden bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <div class="text-xs font-bold uppercase tracking-widest text-violet-600 mb-1">Auto-Generate</div>
            <div class="font-bold text-gray-900 flex items-center gap-1.5 mb-3">
                <x-ui.icon name="sparkles" /> Fill Curriculum Gaps with AI
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Provider <span class="text-red-500">*</span></label>
                    <select id="fillProvider"
                            class="block w-full px-3 py-2 rounded-lg border border-gray-300 bg-white text-sm focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none">
                        @foreach($providers->where('is_active', true) as $p)
                            @php $pDriver = data_get($p->extra_config, 'driver', ''); @endphp
                            @if(in_array($pDriver, ['anthropic', 'grok']))
                                <option value="{{ $p->slug }}">{{ $p->name }} ({{ strtoupper($pDriver) }})</option>
                            @endif
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-400 mt-1">Only Grok / Anthropic providers shown.</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Batch Limit</label>
                    <select id="fillLimit"
                            class="block w-full px-3 py-2 rounded-lg border border-gray-300 bg-white text-sm focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none">
                        <option value="5">5 activities</option>
                        <option value="10" selected>10 activities</option>
                        <option value="20">20 activities</option>
                        <option value="30">30 activities</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button id="fillGapsBtn" onclick="fillGaps()"
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold bg-violet-600 text-white hover:bg-violet-700 transition">
                        <x-ui.icon name="sparkles" /> Auto-Fill Gaps
                    </button>
                </div>
            </div>
            <div id="fillGapsResult" class="hidden text-sm"></div>
        </div>
    </div>

    {{-- Main two-column grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Left: Providers + Dispatch --}}
        <div class="space-y-6">

            {{-- Connected Providers --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-xs font-bold uppercase tracking-widest text-violet-600 mb-0.5">Providers</div>
                        <div class="font-bold text-gray-900 flex items-center gap-1.5">
                            <x-ui.icon name="plug" class="text-emerald-600" /> Connected AI Providers
                        </div>
                    </div>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700 border">
                        {{ $providers->count() }} total
                    </span>
                </div>

                <div class="space-y-3">
                    @forelse($providers as $p)
                    <div class="rounded-xl border border-gray-200 p-4 bg-gray-50/50">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="font-semibold text-sm text-gray-900">{{ $p->name }}</span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 border">{{ $p->slug }}</span>
                                    @php
                                        $statusClass = match($p->connection_status) {
                                            'live'       => 'bg-emerald-100 text-emerald-800',
                                            'failed'     => 'bg-red-100 text-red-700',
                                            'configured' => 'bg-violet-100 text-violet-700',
                                            default      => 'bg-gray-100 text-gray-600',
                                        };
                                    @endphp
                                    <span id="status-pill-{{ $p->id }}"
                                          class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-bold uppercase tracking-wide {{ $statusClass }}">
                                        {{ $p->connection_status ?? 'unchecked' }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1.5 leading-snug">{{ $p->connection_message ?: 'Provider added, no verification details yet.' }}</p>
                                <div class="flex flex-wrap gap-1.5 mt-2">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs bg-gray-100 text-gray-600 border">Driver: {{ strtoupper(data_get($p->extra_config, 'driver', 'auto')) }}</span>
                                    @if($p->model)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs bg-gray-100 text-gray-600 border">Model: {{ $p->model }}</span>
                                    @endif
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $p->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                                        {{ $p->is_active ? 'Active' : 'Disabled' }}
                                    </span>
                                    @if($p->last_checked_at)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs bg-gray-100 text-gray-600">Checked {{ $p->last_checked_at->diffForHumans() }}</span>
                                    @endif
                                    @foreach($p->capabilities ?? [] as $cap)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs bg-gray-100 text-gray-600 border">{{ $cap }}</span>
                                    @endforeach
                                </div>
                            </div>
                            <div class="flex gap-1 shrink-0">
                                <button id="verify-btn-{{ $p->id }}"
                                        title="Verify live status"
                                        onclick="verifyProvider({{ $p->id }}, '{{ route('admin.orchestrator.verifyProvider', $p) }}', this)"
                                        class="p-2 rounded-lg border-2 border-violet-600 text-violet-600 hover:bg-violet-600 hover:text-white transition">
                                    <x-ui.icon name="rotate-cw" />
                                </button>
                                <form method="POST" action="{{ route('admin.orchestrator.toggleProvider', $p) }}">
                                    @csrf
                                    <button type="submit" title="{{ $p->is_active ? 'Disable' : 'Enable' }}"
                                            class="p-2 rounded-lg transition {{ $p->is_active ? 'bg-emerald-600 text-white hover:bg-emerald-700' : 'border-2 border-gray-300 text-gray-600 hover:bg-gray-100' }}">
                                        <x-ui.icon name="{{ $p->is_active ? 'check-circle' : 'x-circle' }}" />
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.orchestrator.destroyProvider', $p) }}" onsubmit="return confirm('Remove provider?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 rounded-lg border-2 border-red-600 text-red-600 hover:bg-red-600 hover:text-white transition">
                                        <x-ui.icon name="trash" />
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-gray-400 py-6 text-sm">No providers yet. Add one to get started.</div>
                    @endforelse
                </div>
            </div>

            {{-- Dispatch Job --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <div class="mb-4">
                    <div class="text-xs font-bold uppercase tracking-widest text-violet-600 mb-0.5">Dispatch</div>
                    <div class="font-bold text-gray-900 flex items-center gap-1.5">
                        <x-ui.icon name="send" class="text-[var(--color-primary)]" /> Generate Content
                    </div>
                </div>
                <form method="POST" action="{{ route('admin.orchestrator.dispatch') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Job Type</label>
                        <select name="type" class="block w-full px-3 py-2 rounded-lg border border-gray-300 bg-white text-sm focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none" required>
                            @foreach($jobTypes as $val => $label)
                                <option value="{{ $val }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">AI Provider</label>
                        <select name="provider" class="block w-full px-3 py-2 rounded-lg border border-gray-300 bg-white text-sm focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none">
                            <option value="mock">Mock (no API key needed)</option>
                            @foreach($providers->where('is_active', true) as $p)
                                <option value="{{ $p->slug }}">{{ $p->name }}@if($p->connection_status) · {{ $p->connection_status }}@endif</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Language / Locale</label>
                        <select name="locale" class="block w-full px-3 py-2 rounded-lg border border-gray-300 bg-white text-sm focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none">
                            @foreach($locales as $code => $name)
                                <option value="{{ $code }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Prompt / Brief <span class="text-red-500">*</span></label>
                        <textarea name="prompt" rows="4"
                                  class="block w-full px-3 py-2 rounded-lg border border-gray-300 bg-white text-sm focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none resize-none"
                                  placeholder="e.g. Create a 15-minute tracing activity for numbers 1-5 for age 3-4…" required></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">GitHub Repo URL (optional)</label>
                        <input type="url" name="repo_url"
                               class="block w-full px-3 py-2 rounded-lg border border-gray-300 bg-white text-sm focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none"
                               placeholder="https://github.com/owner/repo">
                        <p class="text-xs text-gray-500 mt-1">Extract educational content from a public GitHub repo.</p>
                    </div>
                    <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold bg-violet-600 text-white hover:bg-violet-700 transition">
                        <x-ui.icon name="zap" /> Dispatch Job
                    </button>
                </form>
            </div>
        </div>

        {{-- Right: Job queue --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 h-full flex flex-col">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <div class="text-xs font-bold uppercase tracking-widest text-violet-600 mb-0.5">Queue</div>
                        <div class="font-bold text-gray-900 flex items-center gap-1.5">
                            <x-ui.icon name="list-checks" /> Job Queue
                        </div>
                    </div>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700 border">
                        {{ $jobs->total() }} total
                    </span>
                </div>

                <div class="space-y-3 overflow-y-auto" style="max-height: 72vh">
                    @forelse($jobs as $job)
                    <div class="rounded-xl border border-gray-200 p-4 bg-gray-50/40">
                        <div class="flex flex-wrap justify-between items-start gap-2">
                            <div class="flex flex-wrap items-center gap-1.5">
                                @php
                                    $jobStatus = match($job->status) {
                                        'completed' => 'bg-emerald-100 text-emerald-700',
                                        'failed'    => 'bg-red-100 text-red-700',
                                        'running'   => 'bg-violet-100 text-violet-700',
                                        default     => 'bg-gray-100 text-gray-600',
                                    };
                                    $modStatus = match($job->moderation_status) {
                                        'approved' => 'bg-emerald-100 text-emerald-700',
                                        'rejected' => 'bg-red-100 text-red-700',
                                        default    => 'bg-amber-100 text-amber-800',
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $jobStatus }}">{{ $job->status }}</span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $modStatus }}">{{ $job->moderation_status }}</span>
                                <span class="font-semibold text-sm text-gray-900">{{ $job->type }}</span>
                                <span class="text-xs text-gray-500">· {{ strtoupper($job->locale) }} · {{ $job->provider }}</span>
                            </div>
                            <div class="text-xs text-gray-400">#{{ $job->id }} · {{ $job->created_at->diffForHumans() }}</div>
                        </div>

                        <p class="mt-1.5 text-xs text-gray-500 flex items-center gap-1">
                            <x-ui.icon name="message-square-quote" />
                            {{ Str::limit($job->payload['prompt'] ?? '—', 120) }}
                        </p>

                        @if($job->result)
                        @php
                            $res     = $job->result;
                            $resType = $res['type'] ?? 'text';
                            $resUrl  = $res['url'] ?? null;
                            $resTxt  = $res['content'] ?? json_encode($res);
                        @endphp
                        <div class="mt-2 text-xs bg-slate-50 rounded-lg p-3 border border-slate-200 overflow-y-auto max-h-36 whitespace-pre-wrap">
                            @if($resType === 'image' && $resUrl)
                                <img src="{{ $resUrl }}" alt="Generated image" class="rounded mb-1.5 max-h-48">
                            @elseif($resType === 'audio' && $resUrl)
                                <audio controls class="w-full mb-1.5"><source src="{{ $resUrl }}" type="audio/mpeg"></audio>
                            @elseif($resType === 'video' && $resUrl)
                                <video controls class="w-full mb-1.5 max-h-48"><source src="{{ $resUrl }}"></video>
                            @endif
                            {{ $resTxt }}
                        </div>
                        @endif

                        @if($job->error_message)
                        <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                            <x-ui.icon name="alert-circle" /> {{ $job->error_message }}
                        </p>
                        @endif

                        <div class="mt-3 flex flex-wrap gap-2">
                            @if($job->status === 'completed' && $job->moderation_status === 'pending')
                                <form method="POST" action="{{ route('admin.orchestrator.approve', $job) }}">
                                    @csrf
                                    <button class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-emerald-600 text-white hover:bg-emerald-700 transition">
                                        <x-ui.icon name="check-circle" /> Approve &amp; Publish
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.orchestrator.reject', $job) }}">
                                    @csrf
                                    <button class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold border-2 border-red-600 text-red-600 hover:bg-red-600 hover:text-white transition">
                                        <x-ui.icon name="x-circle" /> Reject
                                    </button>
                                </form>
                            @endif
                            @if($job->status === 'failed')
                                <form method="POST" action="{{ route('admin.orchestrator.retry', $job) }}">
                                    @csrf
                                    <button class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-amber-500 text-white hover:bg-amber-600 transition">
                                        <x-ui.icon name="rotate-cw" /> Retry
                                    </button>
                                </form>
                            @endif
                            <form method="POST" action="{{ route('admin.orchestrator.destroyJob', $job) }}" onsubmit="return confirm('Delete this job?')">
                                @csrf @method('DELETE')
                                <button class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold border-2 border-gray-300 text-gray-600 hover:bg-gray-100 transition">
                                    <x-ui.icon name="trash" />
                                </button>
                            </form>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-gray-400 py-12">
                        <x-ui.icon name="bot" class="text-4xl block mb-2 mx-auto" />
                        <p class="text-sm">No jobs yet. Use the form to generate your first piece of content!</p>
                    </div>
                    @endforelse
                </div>

                @if($jobs->hasPages())
                <div class="pt-4 border-t border-gray-100 mt-4">{{ $jobs->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</div>

    @include('admin.orchestrator.media-panel')

</div>{{-- /max-w-7xl --}}

{{-- Add Provider Modal --}}
<div id="addProviderModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden" role="dialog" aria-modal="true">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeProviderModal()"></div>
    <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                <x-ui.icon name="plug" /> Add AI Provider
            </h2>
            <button type="button" onclick="closeProviderModal()" class="p-1.5 rounded-lg text-gray-400 hover:bg-gray-100 hover:text-gray-700 transition">
                <x-ui.icon name="x" />
            </button>
        </div>
        <form method="POST" action="{{ route('admin.orchestrator.storeProvider') }}">
            @csrf
            <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
                @if($errors->any())
                    <div class="p-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm">
                        <ul class="list-disc list-inside space-y-0.5">
                            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                        </ul>
                    </div>
                @endif
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Provider Family</label>
                    <select name="driver" id="driverSelect" onchange="onDriverChange(this.value)"
                            class="block w-full px-3 py-2 rounded-lg border border-gray-300 bg-white text-sm focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none">
                        <option value="anthropic">Anthropic Claude</option>
                        <option value="grok">Grok / xAI</option>
                    </select>
                    <p id="driverHelp" class="text-xs text-gray-500 mt-1">Pick the provider family so health checks use the correct API contract.</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Display Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required placeholder="e.g. OpenAI GPT-4o"
                           class="block w-full px-3 py-2 rounded-lg border border-gray-300 bg-white text-sm focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Slug (unique ID) <span class="text-red-500">*</span></label>
                    <input type="text" name="slug" required pattern="[a-z0-9_-]+" placeholder="e.g. openai"
                           class="block w-full px-3 py-2 rounded-lg border border-gray-300 bg-white text-sm focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none">
                    <p class="text-xs text-gray-500 mt-1">Lowercase letters, numbers, hyphens only.</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">API Base URL</label>
                    <input type="url" name="api_base_url" placeholder="https://api.openai.com/v1"
                           class="block w-full px-3 py-2 rounded-lg border border-gray-300 bg-white text-sm focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none">
                    <p class="text-xs text-gray-500 mt-1">Leave blank for OpenAI-compatible default.</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">API Key</label>
                    <input type="password" name="api_key" placeholder="sk-..."
                           class="block w-full px-3 py-2 rounded-lg border border-gray-300 bg-white text-sm focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none">
                    <p class="text-xs text-gray-500 mt-1">Stored encrypted. Leave blank for public APIs.</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Default Model</label>
                    <input type="text" name="model" placeholder="e.g. gpt-4o-mini, claude-3-haiku-20240307"
                           class="block w-full px-3 py-2 rounded-lg border border-gray-300 bg-white text-sm focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-2">Capabilities</label>
                    <div class="flex flex-wrap gap-3">
                        @foreach(['text','image','tts','video','translation','quiz'] as $cap)
                            <label class="flex items-center gap-1.5 text-sm cursor-pointer">
                                <input type="checkbox" name="capabilities[]" value="{{ $cap }}" id="cap_{{ $cap }}"
                                       class="w-4 h-4 rounded border-gray-300 text-violet-600 focus:ring-violet-500">
                                {{ ucfirst($cap) }}
                            </label>
                        @endforeach
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">GitHub Repo URL (for repo extraction)</label>
                    <input type="url" name="repo_url" placeholder="https://github.com/owner/curriculum-repo"
                           class="block w-full px-3 py-2 rounded-lg border border-gray-300 bg-white text-sm focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none">
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 flex justify-end gap-2">
                <button type="button" onclick="closeProviderModal()"
                        class="px-4 py-2 rounded-lg text-sm font-semibold border-2 border-gray-300 text-gray-700 hover:bg-gray-100 transition">
                    Cancel
                </button>
                <button type="submit"
                        class="px-4 py-2 rounded-lg text-sm font-semibold bg-violet-600 text-white hover:bg-violet-700 transition">
                    Add Provider
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
function openProviderModal()  { document.getElementById('addProviderModal').classList.remove('hidden'); document.body.style.overflow = 'hidden'; }
function closeProviderModal() { document.getElementById('addProviderModal').classList.add('hidden');    document.body.style.overflow = ''; }
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeProviderModal(); });

@if($errors->any())
document.addEventListener('DOMContentLoaded', openProviderModal);
@endif

const DRIVER_META = {
    anthropic: { help: 'Anthropic Claude — enter your API key (sk-ant-...). Model: claude-haiku-4-5-20251001 or claude-sonnet-4-6.' },
    grok:      { help: 'Grok / xAI — enter your xAI API key. Model: grok-beta or grok-2. Base URL auto-set to api.x.ai.' },
};
function onDriverChange(val) {
    const meta = DRIVER_META[val] || {};
    document.getElementById('driverHelp').textContent = meta.help || 'Pick the provider family so health checks use the correct API contract.';
}

function scanCurriculum() {
    const btn = document.getElementById('scanBtn');
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    if (!csrfMeta) { alert('CSRF token missing — please reload.'); return; }
    if (btn.dataset.scanning === '1') return;

    btn.dataset.scanning = '1';
    btn.disabled = true;
    btn.innerHTML = '<span class="inline-block w-4 h-4 border-2 border-current border-t-transparent rounded-full animate-spin"></span> Scanning…';

    const controller = new AbortController();
    const timeout = setTimeout(() => controller.abort(), 30000);

    fetch('{{ route('admin.orchestrator.scan') }}', {
        signal: controller.signal,
        headers: { 'X-CSRF-TOKEN': csrfMeta.content, 'Accept': 'application/json' }
    })
    .then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
    .then(data => {
        const panel   = document.getElementById('scanResult');
        const content = document.getElementById('scanResultContent');
        let html = `<strong>Curriculum Scan:</strong> ${data.total_gaps} gap(s) found.<br>${data.suggestion || ''}`;
        if (data.gaps?.length) {
            html += '<ul class="mt-2 list-disc list-inside">';
            data.gaps.slice(0, 10).forEach(g => { html += `<li>Age <b>${g.age ?? (g.age_min ?? '')+'–'+(g.age_max ?? '')}</b>: missing <b>${g.subject || g.skill}</b></li>`; });
            if (data.gaps.length > 10) html += `<li>…and ${data.gaps.length - 10} more</li>`;
            html += '</ul>';
        }
        content.innerHTML = html;
        panel.classList.remove('hidden');
        // Show fill panel if there are gaps
        const fillPanel = document.getElementById('fillGapsPanel');
        if (fillPanel) {
            if (data.total_gaps > 0) fillPanel.classList.remove('hidden');
            else fillPanel.classList.add('hidden');
        }
        panel.scrollIntoView({ behavior: 'smooth' });
    })
    .catch(err => {
        if (err.name === 'AbortError') alert('Scan timed out after 30 seconds. Please try again.');
        else alert('Scan failed: ' + err.message);
    })
    .finally(() => {
        clearTimeout(timeout);
        btn.dataset.scanning = '';
        btn.disabled = false;
        btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35"/></svg> Scan Curriculum Gaps';
    });
}

function fillGaps() {
    const btn       = document.getElementById('fillGapsBtn');
    const resultEl  = document.getElementById('fillGapsResult');
    const csrfMeta  = document.querySelector('meta[name="csrf-token"]');
    const provider  = document.getElementById('fillProvider')?.value;
    const limit     = document.getElementById('fillLimit')?.value || 10;

    if (!provider) { alert('Please select a provider first.'); return; }
    if (btn.dataset.running === '1') return;

    btn.dataset.running = '1';
    btn.disabled = true;
    btn.innerHTML = '<span class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span> Generating…';
    resultEl.className = 'text-sm text-gray-500';
    resultEl.textContent = 'Calling AI agent… this may take 30–120 seconds depending on batch size.';
    resultEl.classList.remove('hidden');

    fetch('{{ route("admin.orchestrator.fillGaps") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfMeta.content,
            'Accept': 'application/json',
        },
        body: JSON.stringify({ provider_slug: provider, limit: parseInt(limit) }),
    })
    .then(r => r.json())
    .then(data => {
        if (data.error) throw new Error(data.error);
        let html = `<span class="text-emerald-700 font-semibold">✓ ${data.generated} activit${data.generated === 1 ? 'y' : 'ies'} generated</span> from ${data.gaps_found} gaps.`;
        if (data.errors?.length) {
            html += `<ul class="mt-1 list-disc list-inside text-red-600 text-xs">`;
            data.errors.forEach(e => { html += `<li>${e}</li>`; });
            html += '</ul>';
        }
        html += ' <a href="/admin/activities" class="underline text-violet-600">View in library →</a>';
        resultEl.innerHTML = html;
        resultEl.className = 'text-sm mt-1';
    })
    .catch(err => {
        resultEl.innerHTML = `<span class="text-red-600">✗ ${err.message}</span>`;
    })
    .finally(() => {
        btn.dataset.running = '';
        btn.disabled = false;
        btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 3l14 9-14 9V3z"/></svg> Auto-Fill Gaps';
    });
}

let _verifyControllers = {};
function verifyProvider(id, url, btn) {
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    if (!csrfMeta) { alert('CSRF token missing — please reload.'); return; }
    if (btn.dataset.verifying === '1') return;

    if (_verifyControllers[id]) _verifyControllers[id].abort();
    const controller = new AbortController();
    _verifyControllers[id] = controller;
    const timeout = setTimeout(() => controller.abort(), 25000);

    btn.dataset.verifying = '1';
    btn.disabled = true;
    btn.innerHTML = '<span class="inline-block w-4 h-4 border-2 border-current border-t-transparent rounded-full animate-spin"></span>';

    fetch(url, {
        method: 'POST',
        signal: controller.signal,
        headers: { 'X-CSRF-TOKEN': csrfMeta.content, 'Accept': 'application/json', 'Content-Type': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        const pill = document.getElementById('status-pill-' + id);
        if (pill) {
            const isOk = data.status === 'live' || data.ok;
            pill.className = 'inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-bold uppercase tracking-wide ' +
                (isOk ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-700');
            pill.textContent = data.status || (isOk ? 'live' : 'error');
        }
        btn.title = data.message || (data.ok ? 'Provider is live.' : 'Check failed.');
    })
    .catch(err => { if (err.name !== 'AbortError') btn.title = 'Verify failed: ' + err.message; })
    .finally(() => {
        clearTimeout(timeout);
        delete _verifyControllers[id];
        btn.dataset.verifying = '';
        btn.disabled = false;
        btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>';
    });
}
</script>
@endsection
