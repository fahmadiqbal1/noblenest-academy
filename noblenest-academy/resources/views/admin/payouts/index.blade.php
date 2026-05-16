@extends('layouts.admin')

@section('title', 'Payout Requests')

@section('content')
<div class="w-full px-4 py-4">
    <h1 class="h3 font-bold mb-4">Payout Requests</h1>

    @if(session('success'))
    <div class="flex items-start gap-3 p-4 rounded-lg border bg-emerald-50 border-emerald-200 text-emerald-800">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm border-0">
        <div class="px-5 py-3 border-b border-gray-200 font-semibold bg-white font-bold">Pending Payouts</div>
        <div class="p-5 p-0">
            <div class="overflow-x-auto">
                <table class="w-full text-sm border-collapse table-hover-tw mb-0">
                    <thead class="bg-gray-50">
                        <tr>
                            <th>Teacher</th>
                            <th>Amount</th>
                            <th>Requested</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payouts as $payout)
                        <tr>
                            <td>{{ $payout->teacher->name ?? '–' }}</td>
                            <td class="font-semibold">${{ number_format($payout->amount, 2) }}</td>
                            <td>{{ $payout->created_at->format('d M Y') }}</td>
                            <td>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $payout->status === 'approved' ? 'bg-success' : ($payout->status 'pending' 'bg-warning text-dark' 'bg-danger') }}">
                                    {{ ucfirst($payout->status) }}
                                </span>
                            </td>
                            <td>
                                @if($payout->status === 'pending')
                                <form method="POST" action="{{ route('admin.payouts.approve', $payout) }}" class="inline">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-3 py-1.5 text-sm bg-emerald-600 text-white hover:bg-emerald-700">Approve</button>
                                </form>
                                <form method="POST" action="{{ route('admin.payouts.reject', $payout) }}" class="inline">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-3 py-1.5 text-sm bg-red-600 text-white hover:bg-red-700">Reject</button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-[var(--color-text-muted)] py-4">No payout requests.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="mt-3">{{ $payouts->links() }}</div>
</div>
@endsection
