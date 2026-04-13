@extends('layouts.app')

@section('content')
<div class="text-center py-5">
    <div style="font-size:5rem">🔍</div>
    <h1 class="display-4 fw-bold mt-3">Page Not Found</h1>
    <p class="lead text-muted">Oops! The page you're looking for doesn't exist or has been moved.</p>
    <a href="{{ url('/') }}" class="btn btn-primary mt-3"><i class="bi bi-house"></i> Go Home</a>
</div>
@endsection
