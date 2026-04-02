@extends('layouts.app')

@section('title', 'My Payouts')

@section('content')
<div class="container py-5">
    <div class="d-flex align-items-center mb-4">
        <h1 class="h3 fw-bold mb-0">Payouts</h1>
        <form method="POST" action="{{ route('teacher.payouts.request') }}" class="ms-auto">
            @csrf
            <button type="submit" class="btn btn-primary fw-bold" {{ ($pendingExists ?? false) ? 'disabled' : '' }}>
                Request Payout
            </button>
        </form>
    </div>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="alert alert-warning">{{ session('error') }}</div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-center p-4">
                <div class="h3 fw-bold text-success">${{ number_format($balance ?? 0, 2) }}</div>
                <div class="text-muted small">Available Balance</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-center p-4">
                <div class="h3 fw-bold text-warning">${{ number_format($pendingAmount ?? 0, 2) }}</div>
                <div class="text-muted small">Pending Payout</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-center p-4">
                <div class="h3 fw-bold text-primary">${{ number_format($totalPaid ?? 0, 2) }}</div>
                <div class="text-muted small">Total Paid Out</div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white fw-bold">Payout History</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
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
                                <span class="badge {{ $payout->status === 'approved' ? 'bg-success' : ($payout->status === 'pending' ? 'bg-warning text-dark' : 'bg-danger') }}">
                                    {{ ucfirst($payout->status) }}
                                </span>
                            </td>
                            <td>{{ $payout->processed_at ? $payout->processed_at->format('d M Y') : '–' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">No payout requests yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
