@extends('layouts.admin')

@section('meta_title', 'Manage Practitioners | NobleNest Global Academy')

@section('content')
<h2 class="font-bold mb-4"><x-ui.icon name="shield-check" class="me-2" />Practitioners</h2>

@if(session('success'))
    <div class="flex items-start gap-3 p-4 rounded-lg border bg-emerald-50 border-emerald-200 text-emerald-800">{{ session('success') }}</div>
@endif

<div class="flex gap-2 mb-3">
    <a href="{{ route('admin.practitioners.index') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-3 py-1.5 text-sm {{ !request('status') ? 'bg-violet-600 text-white hover:bg-violet-700' : 'border-2 border-gray-300 text-gray-700 hover:bg-gray-100' }}">All</a>
    <a href="{{ route('admin.practitioners.index', ['status' => 'active']) }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-3 py-1.5 text-sm {{ request('status') === 'active' ? 'bg-emerald-600 text-white hover:bg-emerald-700' : 'border-2 border-emerald-600 text-emerald-600 hover:bg-emerald-600 hover:text-white' }}">Active</a>
    <a href="{{ route('admin.practitioners.index', ['status' => 'suspended']) }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-3 py-1.5 text-sm {{ request('status') === 'suspended' ? 'bg-red-600 text-white hover:bg-red-700' : 'border-2 border-red-600 text-red-600 hover:bg-red-600 hover:text-white' }}">Suspended</a>
</div>

<div class="glass-panel p-4">
    @if($practitioners->isEmpty())
        <p class="text-[var(--color-text-muted)] mb-0">No practitioners registered yet.</p>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm border-collapse table-hover-tw align-middle mb-0">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>License Type</th>
                        <th>Specialization</th>
                        <th>Issuing Body</th>
                        <th>Experience</th>
                        <th>Reviews</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($practitioners as $prac)
                    <tr>
                        <td>
                            <strong>{{ $prac->user->name ?? 'Unknown' }}</strong>
                            <div class="text-[var(--color-text-muted)] text-sm">{{ $prac->user->email ?? '' }}</div>
                        </td>
                        <td>{{ $prac->formattedLicenseType() }}</td>
                        <td>{{ $prac->specialization }}</td>
                        <td>{{ $prac->credential_body }}</td>
                        <td>{{ $prac->years_experience }} yrs</td>
                        <td><span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-50 text-gray-900 border">{{ $prac->verified_content_count }}</span></td>
                        <td>
                            @if($prac->isActive())
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-600">Active</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-600">Suspended</span>
                            @endif
                        </td>
                        <td>
                            @if($prac->isActive())
                                <button type="button" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-3 py-1.5 text-sm border-2 border-red-600 text-red-600 hover:bg-red-600 hover:text-white">
                                    <x-ui.icon name="x-circle" /> Suspend
                                </button>

                                <!-- Suspend Modal -->
                                <div class="fixed inset-0 z-50 hidden" id="suspendModal{{ $prac->id }}" tabindex="-1">
                                    <div class="relative w-full max-w-lg mx-auto mt-12">
                                        <div class="bg-white rounded-xl shadow-xl border border-gray-200">
                                            <form method="POST" action="{{ route('admin.practitioners.suspend', $prac) }}">
                                                @csrf
                                                <div class="px-5 py-3 border-b border-gray-200 font-semibold flex items-center justify-between">
                                                    <h5 class="text-lg font-bold">Suspend {{ $prac->user->name ?? 'Practitioner' }}</h5>
                                                    <button type="button" class=""></button>
                                                </div>
                                                <div class="p-5">
                                                    <label for="suspended_reason_{{ $prac->id }}" class="block text-sm font-medium text-gray-700 mb-1 font-semibold">Reason for suspension</label>
                                                    <textarea class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500" id="suspended_reason_{{ $prac->id }}" name="suspended_reason" rows="3" required placeholder="Explain why this practitioner is being suspended..."></textarea>
                                                </div>
                                                <div class="px-5 py-3 border-t border-gray-200 flex justify-end gap-2">
                                                    <button type="button" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed bg-gray-500 text-white hover:bg-gray-600">Cancel</button>
                                                    <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed bg-red-600 text-white hover:bg-red-700">Suspend</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <form method="POST" action="{{ route('admin.practitioners.unsuspend', $prac) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-3 py-1.5 text-sm border-2 border-emerald-600 text-emerald-600 hover:bg-emerald-600 hover:text-white">
                                        <x-ui.icon name="check-circle" /> Reactivate
                                    </button>
                                </form>
                                @if($prac->suspended_reason)
                                    <span class="text-[var(--color-text-muted)] text-sm block mt-1" title="{{ $prac->suspended_reason }}">
                                        <x-ui.icon name="info" /> {{ Str::limit($prac->suspended_reason, 40) }}
                                    </span>
                                @endif
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $practitioners->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
