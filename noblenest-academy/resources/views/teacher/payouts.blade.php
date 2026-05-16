@extends('layouts.teacher')

@section('title', 'My Payouts')

@section('content')
<div class="container py-5">
    <div class="flex items-center mb-4">
        <h1 class="h3 font-bold mb-0">Payouts</h1>
        <form method="POST" action="{{ route('teacher.payouts.request') }}" class="ms-auto">
            @csrf
            <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed bg-violet-600 text-white hover:bg-violet-700 font-bold" {{ ($pendingExists ?? false) ? 'disabled' : '' }}>
                Request Payout
            </button>
        </form>
    </div>

    @if(session('success'))
    <div class="flex items-start gap-3 p-4 rounded-lg border bg-emerald-50 border-emerald-200 text-emerald-800">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="flex items-start gap-3 p-4 rounded-lg border bg-amber-50 border-amber-200 text-amber-800">{{ session('error') }}</div>
    @endif

    <div class="flex flex-wrap gap-3 mb-4">
        <div class="md:w-4/12">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm border-0 text-center p-4">
                <div class="h3 font-bold text-emerald-600">${{ number_format($balance ?? 0, 2) }}</div>
                <div class="text-[var(--color-text-muted)] text-sm">Available Balance</div>
            </div>
        </div>
        <div class="md:w-4/12">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm border-0 text-center p-4">
                <div class="h3 font-bold text-amber-600">${{ number_format($pendingAmount ?? 0, 2) }}</div>
                <div class="text-[var(--color-text-muted)] text-sm">Pending Payout</div>
            </div>
        </div>
        <div class="md:w-4/12">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm border-0 text-center p-4">
                <div class="h3 font-bold text-[var(--color-primary)]">${{ number_format($totalPaid ?? 0, 2) }}</div>
                <div class="text-[var(--color-text-muted)] text-sm">Total Paid Out</div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm border-0">
        <div class="px-5 py-3 border-b border-gray-200 font-semibold bg-white font-bold">Payout History</div>
        <div class="p-5 p-0">
            <div class="overflow-x-auto">
                <table class="w-full text-sm border-collapse table-hover-tw mb-0">
                    <thead class="bg-gray-50">
                        <tr>
                            <th>Date Requested</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Processed</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payouts as $payout)
                        <tr>
                            <td>{{ $payout->created_at->format('d M Y') }}</td>
                            <td>${{ number_format($payout->amount, 2) }}</td>
                            <td>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $payout->status === 'approved' ? 'bg-success' : ($payout->status 'pending' 'bg-warning text-dark' 'bg-danger') }}">
                                    {{ ucfirst($payout->status) }}
                                </span>
                            </td>
                            <td>{{ $payout->processed_at ? $payout->processed_at->format('d M Y') : '–' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-[var(--color-text-muted)] py-4">No payout requests yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
