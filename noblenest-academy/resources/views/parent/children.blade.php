@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2>{{ I18n::get('my_children') }}</h2>
    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($children->isEmpty())
        <div class="alert alert-info">{{ I18n::get('no_children') }}</div>
    @else
    <div class="card mb-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>{{ I18n::get('child_name') }}</th>
                        <th>Age</th>
                        <th>Gender</th>
                        <th>{{ I18n::get('preferred_language') }}</th>
                        <th>{{ I18n::get('actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($children as $child)
                    <tr>
                        <td>
                            <img src="https://api.dicebear.com/7.x/bottts/svg?seed={{ $child->id }}" style="width:28px;height:28px;border-radius:50%;" class="me-1">
                            {{ $child->name }}
                            @if($child->age_bracket)
                                <span class="badge bg-info text-dark ms-1">{{ ucfirst($child->age_bracket) }}</span>
                            @endif
                        </td>
                        <td>{{ $child->age_display ?? '-' }}</td>
                        <td>{{ ucfirst($child->gender ?? '-') }}</td>
                        <td>{{ strtoupper($child->preferred_language ?? 'en') }}</td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('children.edit', $child) }}" class="btn btn-sm btn-outline-secondary">{{ I18n::get('edit') }}</a>
                                <form action="{{ route('children.destroy', $child) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this child profile?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" type="submit">{{ I18n::get('delete') }}</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
    <a href="{{ route('children.create') }}" class="btn btn-primary">{{ I18n::get('add_child') }}</a>
</div>
@endsection
