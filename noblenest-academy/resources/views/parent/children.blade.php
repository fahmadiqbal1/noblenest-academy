@extends('layouts.parent')

@section('content')
<div class="container mt-5">
    <h2>{{ I18n::get('my_children') }}</h2>
    @if(session('status'))
        <div class="flex items-start gap-3 p-4 rounded-lg border bg-emerald-50 border-emerald-200 text-emerald-800">{{ session('status') }}</div>
    @endif
    @if(session('success'))
        <div class="flex items-start gap-3 p-4 rounded-lg border bg-emerald-50 border-emerald-200 text-emerald-800">{{ session('success') }}</div>
    @endif
    @if($children->isEmpty())
        <div class="flex items-start gap-3 p-4 rounded-lg border bg-sky-50 border-sky-200 text-sky-800">{{ I18n::get('no_children') }}</div>
    @else
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-4">
        <div class="overflow-x-auto">
            <table class="w-full text-sm border-collapse table-hover-tw align-middle mb-0">
                <thead class="bg-gray-50">
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
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-sky-600 text-gray-900 ms-1">{{ ucfirst($child->age_bracket) }}</span>
                            @endif
                        </td>
                        <td>{{ $child->age_display ?? '-' }}</td>
                        <td>{{ ucfirst($child->gender ?? '-') }}</td>
                        <td>{{ strtoupper($child->preferred_language ?? 'en') }}</td>
                        <td>
                            <div class="flex gap-1">
                                <a href="{{ route('children.edit', $child) }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-3 py-1.5 text-sm border-2 border-gray-300 text-gray-700 hover:bg-gray-100">{{ I18n::get('edit') }}</a>
                                <form action="{{ route('children.destroy', $child) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this child profile?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-3 py-1.5 text-sm border-2 border-red-600 text-red-600 hover:bg-red-600 hover:text-white" type="submit">{{ I18n::get('delete') }}</button>
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
    <a href="{{ route('children.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed bg-violet-600 text-white hover:bg-violet-700">{{ I18n::get('add_child') }}</a>
</div>
@endsection
