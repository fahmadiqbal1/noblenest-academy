@extends('layouts.app')

@section('title', 'Users')

@section('content')
<div class="container-fluid py-4">

    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h2 class="fw-bold mb-0">Users</h2>
            <small class="text-muted">Manage platform accounts and roles</small>
        </div>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Role tabs --}}
    @php
        $tabs = ['', 'Admin', 'Teacher', 'Parent', 'Student'];
        $tabLabels = ['' => 'All', 'Admin' => 'Admins', 'Teacher' => 'Teachers', 'Parent' => 'Parents', 'Student' => 'Students'];
        $tabColors  = ['' => 'secondary', 'Admin' => 'danger', 'Teacher' => 'primary', 'Parent' => 'success', 'Student' => 'warning'];
        $currentRole = $roleFilter ?? '';
    @endphp

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white pb-0 pt-3 px-4">
            <ul class="nav nav-tabs border-0">
                @foreach($tabs as $tab)
                    <li class="nav-item">
                        <a class="nav-link {{ $currentRole === $tab ? 'active fw-semibold' : 'text-muted' }}"
                           href="{{ route('admin.users.index', array_filter(['role' => $tab ?: null, 'q' => request('q')])) }}">
                            {{ $tabLabels[$tab] }}
                            <span class="badge bg-{{ $tabColors[$tab] }} bg-opacity-10 text-{{ $tabColors[$tab] }} ms-1">
                                {{ $tab ? ($roleCounts[$tab] ?? 0) : $users->total() }}
                            </span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="card-body px-4 pt-3">
            {{-- Search --}}
            <form method="GET" action="{{ route('admin.users.index') }}" class="mb-3">
                @if($currentRole)
                    <input type="hidden" name="role" value="{{ $currentRole }}">
                @endif
                <div class="input-group" style="max-width:380px">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="q" value="{{ request('q') }}"
                           class="form-control border-start-0 ps-0"
                           placeholder="Search by name or email…">
                    @if(request('q'))
                        <a href="{{ route('admin.users.index', array_filter(['role' => $currentRole ?: null])) }}"
                           class="btn btn-outline-secondary">✕</a>
                    @endif
                </div>
            </form>

            {{-- Table --}}
            @if($users->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-people fs-1 d-block mb-2"></i>
                    No users found.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small text-uppercase text-muted">
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Verified</th>
                                <th>Language</th>
                                <th>Joined</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                @php
                                    $roleColor = match($user->role) {
                                        'Admin'   => 'danger',
                                        'Teacher' => 'primary',
                                        'Parent'  => 'success',
                                        'Student' => 'warning',
                                        default   => 'secondary',
                                    };
                                    $initials = strtoupper(substr($user->name, 0, 1)) . strtoupper(substr(strrchr($user->name, ' ') ?: '', 1, 1));
                                @endphp
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-circle bg-{{ $roleColor }} bg-opacity-10 text-{{ $roleColor }} fw-bold d-flex align-items-center justify-content-center"
                                                 style="width:36px;height:36px;font-size:.8rem;flex-shrink:0">
                                                {{ $initials }}
                                            </div>
                                            <span class="fw-medium">{{ $user->name }}</span>
                                        </div>
                                    </td>
                                    <td class="text-muted small">{{ $user->email }}</td>
                                    <td>
                                        <span class="badge rounded-pill bg-{{ $roleColor }} bg-opacity-10 text-{{ $roleColor }} fw-semibold px-3 py-2">
                                            {{ $user->role }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($user->email_verified_at)
                                            <span class="text-success" title="{{ $user->email_verified_at->format('d M Y') }}">
                                                <i class="bi bi-patch-check-fill"></i>
                                            </span>
                                        @else
                                            <span class="text-muted"><i class="bi bi-patch-check"></i></span>
                                        @endif
                                    </td>
                                    <td class="text-muted small">{{ strtoupper($user->preferred_language ?? '—') }}</td>
                                    <td class="text-muted small">{{ $user->created_at->format('d M Y') }}</td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                                data-bs-toggle="dropdown">
                                            Change Role
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                            @foreach(['Admin','Teacher','Parent','Student'] as $role)
                                                @if($role !== $user->role)
                                                    <li>
                                                        <form method="POST"
                                                              action="{{ route('admin.users.updateRole', $user) }}"
                                                              onsubmit="return confirm('Set {{ $user->name }} as {{ $role }}?')">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="hidden" name="role" value="{{ $role }}">
                                                            <button type="submit" class="dropdown-item">
                                                                {{ $role }}
                                                            </button>
                                                        </form>
                                                    </li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($users->hasPages())
                    <div class="d-flex justify-content-end mt-3">
                        {{ $users->withQueryString()->links() }}
                    </div>
                @endif
            @endif
        </div><!-- /card-body -->
    </div><!-- /card -->

</div>
@endsection
