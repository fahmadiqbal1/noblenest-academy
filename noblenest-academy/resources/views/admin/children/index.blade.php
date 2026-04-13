@extends('layouts.app')

@section('title', 'Children')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h2 class="fw-bold mb-0">Children</h2>
            <small class="text-muted">All registered child profiles</small>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body px-4 pt-3">

            {{-- Filters --}}
            <form method="GET" action="{{ route('admin.children.index') }}" class="d-flex gap-2 flex-wrap mb-3">
                <div class="input-group" style="max-width:320px">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="q" value="{{ request('q') }}"
                           class="form-control border-start-0 ps-0"
                           placeholder="Search by name or nickname…">
                </div>
                <select name="language" class="form-select" style="max-width:180px">
                    <option value="">All Languages</option>
                    @foreach($languages as $lang)
                        <option value="{{ $lang }}" {{ request('language') === $lang ? 'selected' : '' }}>
                            {{ strtoupper($lang) }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-outline-secondary">Filter</button>
                @if(request()->hasAny(['q','language']))
                    <a href="{{ route('admin.children.index') }}" class="btn btn-link text-muted">Clear</a>
                @endif
            </form>

            {{-- Table --}}
            @if($children->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-people fs-1 d-block mb-2"></i>
                    No child profiles found.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small text-uppercase text-muted">
                            <tr>
                                <th>Child</th>
                                <th>Age</th>
                                <th>Gender</th>
                                <th>Language</th>
                                <th>Parent</th>
                                <th>Joined</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($children as $child)
                                @php
                                    $langFlags = [
                                        'en' => '🇬🇧', 'fr' => '🇫🇷', 'ru' => '🇷🇺',
                                        'zh' => '🇨🇳', 'es' => '🇪🇸', 'ko' => '🇰🇷',
                                        'ur' => '🇵🇰', 'ar' => '🇸🇦',
                                    ];
                                    $flag = $langFlags[$child->preferred_language ?? ''] ?? '🌐';
                                    $initials = strtoupper(substr($child->name, 0, 1));
                                @endphp
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            @if($child->avatar_url)
                                                <img src="{{ $child->avatar_url }}" alt=""
                                                     class="rounded-circle object-fit-cover"
                                                     style="width:36px;height:36px;flex-shrink:0">
                                            @else
                                                <div class="rounded-circle bg-info bg-opacity-10 text-info fw-bold d-flex align-items-center justify-content-center"
                                                     style="width:36px;height:36px;font-size:.9rem;flex-shrink:0">
                                                    {{ $initials }}
                                                </div>
                                            @endif
                                            <div>
                                                <div class="fw-medium">{{ $child->name }}</div>
                                                @if($child->nickname && $child->nickname !== $child->name)
                                                    <small class="text-muted">"{{ $child->nickname }}"</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-muted small">
                                        @if($child->date_of_birth)
                                            {{ \Carbon\Carbon::parse($child->date_of_birth)->diffInYears() }} yrs
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="text-muted small">{{ ucfirst($child->gender ?? '—') }}</td>
                                    <td>
                                        <span title="{{ strtoupper($child->preferred_language ?? '') }}" style="font-size:1.2rem">
                                            {{ $flag }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($child->parent)
                                            <div class="fw-medium small">{{ $child->parent->name }}</div>
                                            <div class="text-muted" style="font-size:.75rem">{{ $child->parent->email }}</div>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-muted small">{{ $child->created_at->format('d M Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($children->hasPages())
                    <div class="d-flex justify-content-end mt-3">
                        {{ $children->withQueryString()->links() }}
                    </div>
                @endif
            @endif

        </div>
    </div>
</div>
@endsection
