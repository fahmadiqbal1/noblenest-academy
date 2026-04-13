@extends('layouts.app')
@section('content')
@php
$subjectColors = ['sensory'=>'#f59e0b','motor'=>'#10b981','language'=>'#3b82f6','literacy'=>'#6366f1',
    'numeracy'=>'#ec4899','science'=>'#06b6d4','art'=>'#f97316','music'=>'#8b5cf6',
    'social'=>'#14b8a6','character'=>'#22c55e','etiquette'=>'#a855f7','quran'=>'#059669',
    'islamic'=>'#065f46','arabic'=>'#0891b2','coding'=>'#1d4ed8','robotics'=>'#7c3aed',
    'stem'=>'#0369a1','cultural'=>'#b45309'];
$subjects = ['sensory'=>'🌈 Sensory','motor'=>'🏃 Motor','language'=>'💬 Language',
    'literacy'=>'📖 Literacy','numeracy'=>'🔢 Numeracy','science'=>'🔬 Science',
    'art'=>'🎨 Art','music'=>'🎵 Music','social'=>'🤝 Social','character'=>'💛 Character',
    'etiquette'=>'🎩 Etiquette','quran'=>'📿 Quran','islamic'=>'☪️ Islamic',
    'arabic'=>'ع Arabic','coding'=>'💻 Coding','robotics'=>'🤖 Robotics',
    'stem'=>'🧪 STEM','cultural'=>'🌍 Cultural'];
$actTypeIcons = ['video'=>'📹','tracing'=>'✏️','drawing'=>'🎨','puzzle'=>'🧩','quiz'=>'🧠',
    'story'=>'📖','music'=>'🎵','outdoor'=>'🌿','experiment'=>'🔬','coding'=>'💻'];
$diffColors = ['easy'=>'success','medium'=>'warning','hard'=>'danger'];
$diffIcons  = ['easy'=>'🟢','medium'=>'🟡','hard'=>'🔴'];
$langFlags  = ['en'=>'🇬🇧','fr'=>'🇫🇷','ru'=>'🇷🇺','zh'=>'🇨🇳','es'=>'🇪🇸','ko'=>'🇰🇷','ur'=>'🇵🇰','ar'=>'🇸🇦','multi'=>'🌐'];
@endphp
<style>
.act-card { border-left: 4px solid #e5e7eb; background: #fff; border-radius: 0.9rem; padding: 0.9rem 1.1rem; transition: box-shadow 0.18s, border-left-color 0.18s; position: relative; }
.act-card:hover { box-shadow: 0 8px 32px rgba(24,34,47,0.10); }
.act-card .act-actions { opacity: 0; transition: opacity 0.15s; position: absolute; top: 0.7rem; right: 0.8rem; display: flex; gap: 0.35rem; }
.act-card:hover .act-actions { opacity: 1; }
.act-chip { display: inline-flex; align-items: center; gap: 0.3rem; border-radius: 999px; padding: 0.22rem 0.65rem; font-size: 0.72rem; font-weight: 700; }
.act-filter-bar .form-control, .act-filter-bar .form-select { border-radius: 0.8rem; min-height: 40px; font-size: 0.87rem; }
.act-empty { text-align: center; padding: 4rem 1rem; color: #9ca3af; }
</style>
<div class="container-fluid py-3" style="max-width:1100px">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h1 class="mb-0 text-primary">🎯 Activities</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.activities.create') }}" class="btn btn-success">
                <i class="bi bi-plus-circle"></i> New Activity
            </a>
            <button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#addActivityModal" id="addActivityBtn">
                <i class="bi bi-window-plus"></i> Quick Add
            </button>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Filter bar --}}
    <form class="d-flex flex-wrap gap-2 mb-3 act-filter-bar" method="GET">
        <input type="text" name="q" class="form-control" style="max-width:240px"
            placeholder="Search title…" value="{{ request('q') }}">
        <select name="subject" class="form-select" style="max-width:180px">
            <option value="">All Subjects</option>
            @foreach($subjects as $k => $label)
                <option value="{{ $k }}" @selected(request('subject') === $k)>{{ $label }}</option>
            @endforeach
        </select>
        <select name="type" class="form-select" style="max-width:160px">
            <option value="">All Types</option>
            @foreach($actTypeIcons as $k => $icon)
                <option value="{{ $k }}" @selected(request('type') === $k)>{{ $icon }} {{ ucfirst($k) }}</option>
            @endforeach
        </select>
        <button class="btn btn-outline-primary"><i class="bi bi-funnel"></i> Filter</button>
        @if(request()->hasAny(['q','subject','type']))
            <a href="{{ route('admin.activities.index') }}" class="btn btn-outline-secondary">Clear</a>
        @endif
    </form>

    {{-- Activity cards --}}
    <div class="d-flex flex-column gap-2">
        @forelse($activities as $activity)
        @php
            $color  = $subjectColors[$activity->subject ?? ''] ?? '#9ca3af';
            $typeIc = $actTypeIcons[$activity->activity_type ?? ''] ?? '📌';
            $langFl = $langFlags[$activity->language ?? ''] ?? '';
        @endphp
        <div class="act-card" style="border-left-color: {{ $color }}">
            <div class="d-flex align-items-start gap-3 pe-5">
                {{-- Emoji / type icon --}}
                <div style="font-size:1.9rem;line-height:1;flex-shrink:0;margin-top:2px">
                    {{ $activity->emoji ?: $typeIc }}
                </div>
                <div class="flex-grow-1 min-w-0">
                    <div class="fw-bold" style="font-size:0.97rem">{{ $activity->title }}</div>
                    <div class="text-muted small mt-0 mb-1" style="max-width:560px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                        {{ $activity->description }}
                    </div>
                    <div class="d-flex flex-wrap gap-1 align-items-center">
                        @if($activity->subject)
                        <span class="act-chip" style="background:{{ $color }}22;color:{{ $color }}">
                            {{ $subjects[$activity->subject] ?? $activity->subject }}
                        </span>
                        @endif
                        @if($activity->activity_type)
                        <span class="act-chip bg-light text-dark border">{{ $typeIc }} {{ ucfirst($activity->activity_type) }}</span>
                        @endif
                        @if($activity->age_min !== null && $activity->age_max !== null)
                        <span class="act-chip bg-light text-dark border">👶 {{ $activity->age_min }}–{{ $activity->age_max }}y</span>
                        @endif
                        @if($activity->difficulty)
                        <span class="act-chip bg-{{ $diffColors[$activity->difficulty] ?? 'secondary' }}-subtle text-{{ $diffColors[$activity->difficulty] ?? 'secondary' }}-emphasis border">
                            {{ $diffIcons[$activity->difficulty] ?? '' }} {{ ucfirst($activity->difficulty) }}
                        </span>
                        @endif
                        @if($activity->duration_minutes)
                        <span class="act-chip bg-light text-dark border">⏱ {{ $activity->duration_minutes }}min</span>
                        @endif
                        @if($langFl)
                        <span class="act-chip bg-light text-dark border">{{ $langFl }}</span>
                        @endif
                        @if($activity->is_muslim_only)
                        <span class="act-chip bg-success-subtle text-success-emphasis border">☪️</span>
                        @endif
                        @if($activity->is_free)
                        <span class="act-chip bg-primary-subtle text-primary-emphasis border">🆓 Free</span>
                        @else
                        <span class="act-chip bg-warning-subtle text-warning-emphasis border">💎 Premium</span>
                        @endif
                    </div>
                </div>
            </div>
            {{-- Hover actions --}}
            <div class="act-actions">
                <a href="{{ route('admin.activities.edit', $activity) }}" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-pencil"></i>
                </a>
                <form method="POST" action="{{ route('admin.activities.destroy', $activity) }}" onsubmit="return confirm('Delete this activity?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                </form>
            </div>
        </div>
        @empty
        <div class="act-empty">
            <div style="font-size:3rem">🎯</div>
            <p class="mt-2 fw-semibold">No activities found.</p>
            <a href="{{ route('admin.activities.create') }}" class="btn btn-success">Add your first activity</a>
        </div>
        @endforelse
    </div>

    <div class="mt-3">{{ $activities->withQueryString()->links() }}</div>

    {{-- Bulk upload --}}
    <div class="mt-4 p-3" style="background:rgba(255,255,255,0.7);border-radius:1rem;border:1px solid rgba(0,0,0,0.08)">
        <form method="POST" action="{{ route('admin.activities.bulkUpload') }}" enctype="multipart/form-data" class="d-flex align-items-center flex-wrap gap-2">
            @csrf
            <label class="fw-semibold text-muted small">Bulk Upload (CSV):</label>
            <input type="file" name="file" class="form-control d-inline-block" style="max-width:260px" accept=".csv,.txt" required>
            <button class="btn btn-outline-info btn-sm"><i class="bi bi-upload"></i> Upload CSV</button>
        </form>
    </div>
</div>

{{-- Quick Add Modal --}}
<div class="modal fade" id="addActivityModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.activities.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">✨ Quick Add Activity</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @include('admin.activities.partials.form', ['activity' => null])
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success"><i class="bi bi-plus-circle"></i> Add Activity</button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const params = new URLSearchParams(window.location.search);
    if (params.get('openAdd') === '1') {
        const modal = new bootstrap.Modal(document.getElementById('addActivityModal'));
        modal.show();
        const sub = params.get('subject');
        if (sub) {
            const sel = document.querySelector('#addActivityModal select[name="subject"]');
            if (sel) { sel.value = sub; sel.dispatchEvent(new Event('change')); }
        }
    }
});
</script>
@endsection
@endsection


