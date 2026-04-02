@extends('layouts.app')

@section('title', 'Payout Requests')

@section('content')
<div class="container-fluid py-4">
    <h1 class="h3 fw-bold mb-4">Payout Requests</h1>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white fw-bold">Pending Payouts</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
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
                            <td class="fw-semibold">${{ number_format($payout->amount, 2) }}</td>
                            <td>{{ $payout->created_at->format('d M Y') }}</td>
                            <td>
                                <span class="badge {{ $payout->status === 'approved' ? 'bg-success' : ($payout->status === 'pending' ? 'bg-warning text-dark' : 'bg-danger') }}">
                                    {{ ucfirst($payout->status) }}
                                </span>
                            </td>
                            <td>
                                @if($payout->status === 'pending')
                                <form method="POST" action="{{ route('admin.payouts.approve', $payout) }}" class="d-inline">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                </form>
                                <form method="POST" action="{{ route('admin.payouts.reject', $payout) }}" class="d-inline">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-danger">Reject</button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">No payout requests.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="mt-3">{{ $payouts->links() }}</div>
</div>
@endsection
