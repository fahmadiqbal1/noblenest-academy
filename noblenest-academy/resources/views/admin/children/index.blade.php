@extends('layouts.admin')

@section('title', 'Children')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-[var(--color-text)]">Children</h1>
            <p class="text-sm text-[var(--color-text-muted)] mt-0.5">All registered child profiles</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="p-5 space-y-4">

            {{-- Filters --}}
            <form method="GET" action="{{ route('admin.children.index') }}" class="flex flex-wrap gap-2 items-end">
                <div class="flex-1 min-w-[200px] max-w-xs">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Search</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-gray-400">
                            <x-ui.icon name="search" />
                        </span>
                        <input type="text" name="q" value="{{ request('q') }}"
                               class="block w-full pl-9 pr-3 py-2 rounded-lg border border-gray-300 bg-white text-sm focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none"
                               placeholder="Name or nickname…">
                    </div>
                </div>
                <div class="w-44">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Language</label>
                    <select name="language" class="block w-full px-3 py-2 rounded-lg border border-gray-300 bg-white text-sm focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none">
                        <option value="">All Languages</option>
                        @foreach($languages as $lang)
                            <option value="{{ $lang }}" {{ request('language') === $lang ? 'selected' : '' }}>
                                {{ strtoupper($lang) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold border-2 border-gray-300 text-gray-700 hover:bg-gray-100 transition">
                        Filter
                    </button>
                    @if(request()->hasAny(['q', 'language']))
                        <a href="{{ route('admin.children.index') }}" class="inline-flex items-center px-3 py-2 rounded-lg text-sm text-gray-500 hover:text-gray-700 transition">
                            Clear
                        </a>
                    @endif
                </div>
            </form>

            {{-- Table --}}
            @if($children->isEmpty())
                <div class="text-center py-12 text-[var(--color-text-muted)]">
                    <x-ui.icon name="users" class="text-4xl block mb-2 mx-auto" />
                    <p class="text-sm">No child profiles found.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-left">
                            <tr>
                                <th class="px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wide">Child</th>
                                <th class="px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wide">Age</th>
                                <th class="px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wide">Gender</th>
                                <th class="px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wide">Language</th>
                                <th class="px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wide">Parent</th>
                                <th class="px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wide">Joined</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($children as $child)
                                @php
                                    $langFlags = [
                                        'en' => '🇬🇧', 'fr' => '🇫🇷', 'ru' => '🇷🇺',
                                        'zh' => '🇨🇳', 'es' => '🇪🇸', 'ko' => '🇰🇷',
                                        'ur' => '🇵🇰', 'ar' => '🇸🇦',
                                    ];
                                    $flag     = $langFlags[$child->preferred_language ?? ''] ?? '🌐';
                                    $initials = strtoupper(substr($child->name, 0, 1));
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            @if($child->avatar_url)
                                                <img src="{{ $child->avatar_url }}" alt=""
                                                     class="w-9 h-9 rounded-full object-cover shrink-0">
                                            @else
                                                <div class="w-9 h-9 rounded-full bg-sky-100 text-sky-700 font-bold flex items-center justify-center text-sm shrink-0">
                                                    {{ $initials }}
                                                </div>
                                            @endif
                                            <div>
                                                <div class="font-medium text-gray-900">{{ $child->name }}</div>
                                                @if($child->nickname && $child->nickname !== $child->name)
                                                    <div class="text-xs text-gray-500">"{{ $child->nickname }}"</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-gray-500">
                                        @if($child->date_of_birth)
                                            {{ \Carbon\Carbon::parse($child->date_of_birth)->diffInYears() }} yrs
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-gray-500">{{ ucfirst($child->gender ?? '—') }}</td>
                                    <td class="px-4 py-3">
                                        <span title="{{ strtoupper($child->preferred_language ?? '') }}" class="text-xl">{{ $flag }}</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($child->parent)
                                            <div class="font-medium text-gray-900 text-sm">{{ $child->parent->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $child->parent->email }}</div>
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-gray-500">{{ $child->created_at->format('d M Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($children->hasPages())
                    <div class="flex justify-end pt-2">
                        {{ $children->withQueryString()->links() }}
                    </div>
                @endif
            @endif

        </div>
    </div>
</div>
@endsection
