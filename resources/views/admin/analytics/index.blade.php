@extends('admin.layout')
@section('title', __('Admin Analytics Dashboard'))
@section('content')
<div class="container py-4">
    <h1 class="mb-4">{{ __('Analytics Dashboard') }}</h1>
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-primary text-white">{{ __('Most Liked Activities') }}</div>
                <ul class="list-group list-group-flush">
                    @foreach($mostLiked as $activity)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ $activity->title }}
                            <span class="badge bg-success">{{ $activity->likes_count }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-info text-white">{{ __('Most Engaged Users') }}</div>
                <ul class="list-group list-group-flush">
                    @foreach($mostEngaged as $user)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ $user->name }}
                            <span class="badge bg-secondary">{{ $user->activity_progress_count }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-warning text-dark">{{ __('Monthly Activity Completions') }}</div>
                <canvas id="monthlyChart"></canvas>
            </div>
        </div>
    </div>
    <form method="POST" action="{{ route('admin.analytics.reportEmail') }}">
        @csrf
        <button class="btn btn-outline-primary">{{ __('Send Monthly Report') }}</button>
    </form>
</div>
@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const monthlyData = @json($monthly);
    const ctx = document.getElementById('monthlyChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: monthlyData.map(m => 'Month ' + m.month),
            datasets: [{
                label: 'Completions',
                data: monthlyData.map(m => m.count),
                backgroundColor: '#ffc107',
            }]
        },
        options: {responsive: true}
    });
</script>
@endpush
