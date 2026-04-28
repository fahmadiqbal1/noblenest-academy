@extends('layouts.app')

@section('meta_title', 'Manage Practitioners | NobleNest Global Academy')

@section('content')
<h2 class="fw-bold mb-4"><i class="bi bi-shield-check me-2"></i>Practitioners</h2>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="d-flex gap-2 mb-3">
    <a href="{{ route('admin.practitioners.index') }}" class="btn btn-sm {{ !request('status') ? 'btn-primary' : 'btn-outline-secondary' }}">All</a>
    <a href="{{ route('admin.practitioners.index', ['status' => 'active']) }}" class="btn btn-sm {{ request('status') === 'active' ? 'btn-success' : 'btn-outline-success' }}">Active</a>
    <a href="{{ route('admin.practitioners.index', ['status' => 'suspended']) }}" class="btn btn-sm {{ request('status') === 'suspended' ? 'btn-danger' : 'btn-outline-danger' }}">Suspended</a>
</div>

<div class="glass-panel p-4">
    @if($practitioners->isEmpty())
        <p class="text-muted mb-0">No practitioners registered yet.</p>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
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
                            <div class="text-muted small">{{ $prac->user->email ?? '' }}</div>
                        </td>
                        <td>{{ $prac->formattedLicenseType() }}</td>
                        <td>{{ $prac->specialization }}</td>
                        <td>{{ $prac->credential_body }}</td>
                        <td>{{ $prac->years_experience }} yrs</td>
                        <td><span class="badge bg-light text-dark border">{{ $prac->verified_content_count }}</span></td>
                        <td>
                            @if($prac->isActive())
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Suspended</span>
                            @endif
                        </td>
                        <td>
                            @if($prac->isActive())
                                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#suspendModal{{ $prac->id }}">
                                    <i class="bi bi-x-circle"></i> Suspend
                                </button>

                                <!-- Suspend Modal -->
                                <div class="modal fade" id="suspendModal{{ $prac->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form method="POST" action="{{ route('admin.practitioners.suspend', $prac) }}">
                                                @csrf
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Suspend {{ $prac->user->name ?? 'Practitioner' }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <label for="suspended_reason_{{ $prac->id }}" class="form-label fw-semibold">Reason for suspension</label>
                                                    <textarea class="form-control" id="suspended_reason_{{ $prac->id }}" name="suspended_reason" rows="3" required placeholder="Explain why this practitioner is being suspended..."></textarea>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-danger">Suspend</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <form method="POST" action="{{ route('admin.practitioners.unsuspend', $prac) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-success">
                                        <i class="bi bi-check-circle"></i> Reactivate
                                    </button>
                                </form>
                                @if($prac->suspended_reason)
                                    <span class="text-muted small d-block mt-1" title="{{ $prac->suspended_reason }}">
                                        <i class="bi bi-info-circle"></i> {{ Str::limit($prac->suspended_reason, 40) }}
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
