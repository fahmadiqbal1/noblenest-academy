@extends('layouts.admin')

@section('title', 'Users')

@section('content')
<div class="w-full px-4 py-4">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-4">
        <div>
            <h2 class="font-bold mb-0">Users</h2>
            <small class="text-[var(--color-text-muted)]">Manage platform accounts and roles</small>
        </div>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="flex items-start gap-3 p-4 rounded-lg border bg-emerald-50 border-emerald-200 text-emerald-800" role="alert">
            {{ session('success') }}
            <button type="button" class=""></button>
        </div>
    @endif
    @if(session('error'))
        <div class="flex items-start gap-3 p-4 rounded-lg border bg-red-50 border-red-200 text-red-800" role="alert">
            {{ session('error') }}
            <button type="button" class=""></button>
        </div>
    @endif

    {{-- Role tabs --}}
    @php
        $tabs = ['', 'Admin', 'Teacher', 'Parent', 'Student'];
        $tabLabels = ['' => 'All', 'Admin' => 'Admins', 'Teacher' => 'Teachers', 'Parent' => 'Parents', 'Student' => 'Students'];
        $tabColors  = [
            ''        => 'bg-gray-100 text-gray-700',
            'Admin'   => 'bg-red-100 text-red-700',
            'Teacher' => 'bg-violet-100 text-violet-700',
            'Parent'  => 'bg-emerald-100 text-emerald-700',
            'Student' => 'bg-amber-100 text-amber-700',
        ];
        $currentRole = $roleFilter ?? '';
    @endphp

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm border-0 mb-4">
        <div class="px-5 py-3 border-b border-gray-200 font-semibold bg-white pb-0 pt-3 px-4">
            <ul class="flex border-b border-gray-200 flex-wrap">
                @foreach($tabs as $tab)
                    <li class="">
                        <a class="px-4 py-2 rounded-md hover:bg-gray-50 text-sm font-medium {{ $currentRole === $tab ? 'font-semibold text-[var(--color-text)]' : 'text-[var(--color-text-muted)]' }}"
                           href="{{ route('admin.users.index', array_filter(['role' => $tab ?: null, 'q' => request('q')])) }}">
                            {{ $tabLabels[$tab] }}
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $tabColors[$tab] }} ms-1">
                                {{ $tab ? ($roleCounts[$tab] ?? 0) : $users->total() }}
                            </span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="p-5 px-4 pt-3">
            {{-- Search --}}
            <form method="GET" action="{{ route('admin.users.index') }}" class="mb-3">
                @if($currentRole)
                    <input type="hidden" name="role" value="{{ $currentRole }}">
                @endif
                <div class="flex w-full items-stretch" style="max-width:380px">
                    <span class="inline-flex items-center px-3 bg-gray-50 border border-gray-300 bg-white border-end-0"><x-ui.icon name="search" class="text-[var(--color-text-muted)]" /></span>
                    <input type="text" name="q" value="{{ request('q') }}"
                           class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500 border-start-0 ps-0"
                           placeholder="Search by name or email…">
                    @if(request('q'))
                        <a href="{{ route('admin.users.index', array_filter(['role' => $currentRole ?: null])) }}"
                           class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed border-2 border-gray-300 text-gray-700 hover:bg-gray-100">✕</a>
                    @endif
                </div>
            </form>

            {{-- Table --}}
            @if($users->isEmpty())
                <div class="text-center py-5 text-[var(--color-text-muted)]">
                    <x-ui.icon name="users" class="text-5xl block mb-2" />
                    No users found.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm border-collapse table-hover-tw align-middle mb-0">
                        <thead class="bg-gray-50 text-sm uppercase text-[var(--color-text-muted)]">
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
                                        'Admin'   => 'bg-red-100 text-red-700',
                                        'Teacher' => 'bg-violet-100 text-violet-700',
                                        'Parent'  => 'bg-emerald-100 text-emerald-700',
                                        'Student' => 'bg-amber-100 text-amber-700',
                                        default   => 'bg-gray-100 text-gray-700',
                                    };
                                    $initials = strtoupper(substr($user->name, 0, 1)) . strtoupper(substr(strrchr($user->name, ' ') ?: '', 1, 1));
                                @endphp
                                <tr>
                                    <td>
                                        <div class="flex items-center gap-2">
                                            <div class="rounded-full {{ $roleColor }} font-bold flex items-center justify-center"
                                                 style="width:36px;height:36px;font-size:.8rem;flex-shrink:0">
                                                {{ $initials }}
                                            </div>
                                            <span class="font-medium">{{ $user->name }}</span>
                                        </div>
                                    </td>
                                    <td class="text-[var(--color-text-muted)] text-sm">{{ $user->email }}</td>
                                    <td>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $roleColor }} font-semibold px-3 py-2">
                                            {{ $user->role }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($user->email_verified_at)
                                            <span class="text-emerald-600" title="{{ $user->email_verified_at->format('d M Y') }}">
                                                <x-ui.icon name="badge-check" />
                                            </span>
                                        @else
                                            <span class="text-[var(--color-text-muted)]"><x-ui.icon name="badge-check" /></span>
                                        @endif
                                    </td>
                                    <td class="text-[var(--color-text-muted)] text-sm">{{ strtoupper($user->preferred_language ?? '—') }}</td>
                                    <td class="text-[var(--color-text-muted)] text-sm">{{ $user->created_at->format('d M Y') }}</td>
                                    <td class="text-right">
                                        <button class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-3 py-1.5 text-sm border-2 border-gray-300 text-gray-700 hover:bg-gray-100"
>
                                            Change Role
                                        </button>
                                        <ul class="absolute z-10 mt-2 min-w-[10rem] bg-white border border-gray-200 rounded-lg shadow-lg py-1 hidden dropdown-menu-end shadow-sm">
                                            @foreach(['Admin','Teacher','Parent','Student'] as $role)
                                                @if($role !== $user->role)
                                                    <li>
                                                        <form method="POST"
                                                              action="{{ route('admin.users.updateRole', $user) }}"
                                                              onsubmit="return confirm('Set {{ $user->name }} as {{ $role }}?')">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="hidden" name="role" value="{{ $role }}">
                                                            <button type="submit" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
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
                    <div class="flex justify-end mt-3">
                        {{ $users->withQueryString()->links() }}
                    </div>
                @endif
            @endif
        </div><!-- /card-body -->
    </div><!-- /card -->

</div>
@endsection
