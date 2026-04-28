@extends('layouts.app')

@section('title', 'Admin: Create Maternal Content')

@section('content')
<div class="container py-4">
    <h3 class="mb-4" style="font-family:'Baloo 2',sans-serif;">Create Maternal Content</h3>

    <div class="card border-0" style="background:rgba(255,255,255,0.88); border-radius:1.25rem;">
        <div class="card-body p-4">
            @if($errors->any())
                <div class="alert alert-danger border-0 rounded-3 mb-4">
                    <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.maternal.content.store') }}">
                @csrf
                @include('admin.maternal.content._form')
                <button type="submit" class="btn rounded-pill fw-semibold px-4" style="background:var(--nn-primary); color:#fff;">Create Content</button>
                <a href="{{ route('admin.maternal.content.index') }}" class="btn btn-outline-secondary rounded-pill px-4">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection
