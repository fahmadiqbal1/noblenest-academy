@extends('layouts.app')

@section('content')
<div class="text-center py-5">
    <div style="font-size:5rem">⚠️</div>
    <h1 class="display-4 fw-bold mt-3">Something Went Wrong</h1>
    <p class="lead text-muted">We're working on fixing this. Please try again in a moment.</p>
    <a href="{{ url('/') }}" class="btn btn-primary mt-3"><i class="bi bi-house"></i> Go Home</a>
</div>
@endsection
