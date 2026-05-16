@extends('layouts.admin')
@section('content')
<div class="container py-4">
    <h1 class="mb-4 text-[var(--color-primary)]">Curriculum Mapping</h1>
    <p class="mb-3">Assign activities to subjects and age groups. Drag activities between subjects or use the dropdowns.</p>
    <form class="flex flex-wrap gap-3 mb-4">
        <div class="md:w-3/12">
            <label class="block text-sm font-medium text-gray-700 mb-1">Filter by Age</label>
            <select class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500" name="age" onchange="this.form.submit()">
                <option value="">All</option>
                @for($i=0;$i<=10;$i++)
                    <option value="{{ $i }}" @if(request('age')==$i) selected @endif>{{ $i }}</option>
                @endfor
            </select>
        </div>
        <div class="md:w-3/12">
            <label class="block text-sm font-medium text-gray-700 mb-1">Filter by Subject</label>
            <input type="text" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500" name="subject" value="{{ request('subject') }}" placeholder="Subject name...">
        </div>
        <div class="md:w-3/12 self-end">
            <button class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed border-2 border-violet-600 text-violet-600 hover:bg-violet-600 hover:text-white">Filter</button>
        </div>
    </form>
    <div class="flex flex-wrap gap-4">
        @foreach($subjects as $subject => $activities)
        <div class="md:w-6/12 lg:w-4/12">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm h-full">
                <div class="px-5 py-3 border-b border-gray-200 font-semibold bg-sky-600 text-white font-bold">{{ ucfirst($subject) }}</div>
                <div class="p-5">
                    <ul class="divide-y divide-gray-200 border border-gray-200 rounded-lg bg-white mb-3 curriculum-draggable" id="subject-{{ Str::slug($subject) }}" data-subject="{{ $subject }}">
                        @foreach($activities as $activity)
                        <li class="px-4 py-3 flex justify-between items-center" data-activity-id="{{ $activity->id }}">
                            <span>{{ $activity->title }}</span>
                            <form method="POST" action="{{ route('admin.curriculum.remove') }}" style="display:inline-block;">
                                @csrf
                                <input type="hidden" name="activity_id" value="{{ $activity->id }}">
                                <input type="hidden" name="subject" value="{{ $subject }}">
                                <button class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-3 py-1.5 text-sm border-2 border-red-600 text-red-600 hover:bg-red-600 hover:text-white"><x-ui.icon name="x" /></button>
                            </form>
                        </li>
                        @endforeach
                    </ul>
                    <form method="POST" action="{{ route('admin.curriculum.add') }}" class="flex gap-2">
                        @csrf
                        <select name="activity_id" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500 px-2 py-1 text-sm">
                            <option value="">Add activity...</option>
                            @foreach($allActivities as $a)
                                @if($a->subject !== $subject)
                                    <option value="{{ $a->id }}">{{ $a->title }}</option>
                                @endif
                            @endforeach
                        </select>
                        <input type="hidden" name="subject" value="{{ $subject }}">
                        <button class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-3 py-1.5 text-sm bg-emerald-600 text-white hover:bg-emerald-700">Add</button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.curriculum-draggable').forEach(function(list) {
        new Sortable(list, {
            group: 'activities',
            animation: 150,
            onAdd: function (evt) {
                const activityId = evt.item.getAttribute('data-activity-id');
                const subject = evt.to.getAttribute('data-subject');
                fetch('{{ route("admin.curriculum.dragAssign") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ activity_id: activityId, subject: subject })
                });
            },
            onRemove: function (evt) {
                const activityId = evt.item.getAttribute('data-activity-id');
                const subject = evt.from.getAttribute('data-subject');
                fetch('{{ route("admin.curriculum.dragRemove") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ activity_id: activityId, subject: subject })
                });
            }
        });
    });
});
</script>
@endpush
