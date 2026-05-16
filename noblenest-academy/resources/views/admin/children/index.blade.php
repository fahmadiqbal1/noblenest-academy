@extends('layouts.admin')

@section('title', 'Children')

@section('content')
<div class="w-full px-4 py-4">

    <div class="flex items-center justify-between mb-4">
        <div>
            <h2 class="font-bold mb-0">Children</h2>
            <small class="text-[var(--color-text-muted)]">All registered child profiles</small>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm border-0">
        <div class="p-5 px-4 pt-3">

            {{-- Filters --}}
            <form method="GET" action="{{ route('admin.children.index') }}" class="flex gap-2 flex-wrap mb-3">
                <div class="flex w-full items-stretch" style="max-width:320px">
                    <span class="inline-flex items-center px-3 bg-gray-50 border border-gray-300 bg-white border-end-0"><x-ui.icon name="search" class="text-[var(--color-text-muted)]" /></span>
                    <input type="text" name="q" value="{{ request('q') }}"
                           class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500 border-start-0 ps-0"
                           placeholder="Search by name or nickname…">
                </div>
                <select name="language" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500" style="max-width:180px">
                    <option value="">All Languages</option>
                    @foreach($languages as $lang)
                        <option value="{{ $lang }}" {{ request('language') === $lang ? 'selected' : '' }}>
                            {{ strtoupper($lang) }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed border-2 border-gray-300 text-gray-700 hover:bg-gray-100">Filter</button>
                @if(request()->hasAny(['q','language']))
                    <a href="{{ route('admin.children.index') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed bg-transparent text-violet-600 hover:underline shadow-none text-[var(--color-text-muted)]">Clear</a>
                @endif
            </form>

            {{-- Table --}}
            @if($children->isEmpty())
                <div class="text-center py-5 text-[var(--color-text-muted)]">
                    <x-ui.icon name="users" class="text-5xl block mb-2" />
                    No child profiles found.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm border-collapse table-hover-tw align-middle mb-0">
                        <thead class="bg-gray-50 text-sm uppercase text-[var(--color-text-muted)]">
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
                                        <div class="flex items-center gap-2">
                                            @if($child->avatar_url)
                                                <img src="{{ $child->avatar_url }}" alt=""
                                                     class="rounded-full object-fit-cover"
                                                     style="width:36px;height:36px;flex-shrink:0">
                                            @else
                                                <div class="rounded-full bg-sky-600 bg-opacity-10 text-sky-600 font-bold flex items-center justify-center"
                                                     style="width:36px;height:36px;font-size:.9rem;flex-shrink:0">
                                                    {{ $initials }}
                                                </div>
                                            @endif
                                            <div>
                                                <div class="font-medium">{{ $child->name }}</div>
                                                @if($child->nickname && $child->nickname !== $child->name)
                                                    <small class="text-[var(--color-text-muted)]">"{{ $child->nickname }}"</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-[var(--color-text-muted)] text-sm">
                                        @if($child->date_of_birth)
                                            {{ \Carbon\Carbon::parse($child->date_of_birth)->diffInYears() }} yrs
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="text-[var(--color-text-muted)] text-sm">{{ ucfirst($child->gender ?? '—') }}</td>
                                    <td>
                                        <span title="{{ strtoupper($child->preferred_language ?? '') }}" style="font-size:1.2rem">
                                            {{ $flag }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($child->parent)
                                            <div class="font-medium text-sm">{{ $child->parent->name }}</div>
                                            <div class="text-[var(--color-text-muted)]" style="font-size:.75rem">{{ $child->parent->email }}</div>
                                        @else
                                            <span class="text-[var(--color-text-muted)]">—</span>
                                        @endif
                                    </td>
                                    <td class="text-[var(--color-text-muted)] text-sm">{{ $child->created_at->format('d M Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($children->hasPages())
                    <div class="flex justify-end mt-3">
                        {{ $children->withQueryString()->links() }}
                    </div>
                @endif
            @endif

        </div>
    </div>
</div>
@endsection
