@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2>{{ I18n::get('my_children') }}</h2>
    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
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
                        <th>{{ I18n::get('child_age') }}</th>
                        <th>{{ I18n::get('preferred_language') }}</th>
                        <th>{{ I18n::get('actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($children as $child)
                    <tr>
                        <td>{{ $child->name }}</td>
                        <td>{{ $child->age ?? '-' }}</td>
                        <td>{{ I18n::get($child->preferred_language ?? '-') }}</td>
                        <td>
                            <form action="#" method="POST" class="d-inline">
                                @csrf
                                <button class="btn btn-sm btn-outline-secondary" type="button" onclick="window.location='{{ route('children.edit', $child) }}'">{{ I18n::get('edit') }}</button>
                                <form action="{{ route('children.destroy', $child) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this child?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" type="submit">{{ I18n::get('delete') }}</button>
                                </form>
                            </form>
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
