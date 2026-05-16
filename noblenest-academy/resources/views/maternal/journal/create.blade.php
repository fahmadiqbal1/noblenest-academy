@extends('layouts.maternal')

@section('title', 'New Journal Entry — Maternal Wellness')

@section('content')
<div class="container py-4">
    <div class="flex flex-wrap">
        <div class="lg:w-3/12 hidden lg:block">
            @include('maternal.partials.sidebar')
        </div>
        <div class="lg:w-9/12">
            <h3 class="mb-4" style="font-family:'Baloo 2',sans-serif; color:var(--nn-text);">
                <x-ui.icon name="pencil" class="me-2" style="color:#7C3AED;" /> New Journal Entry
            </h3>

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm border-0" style="background:rgba(255,255,255,0.88); border-radius:1.25rem; box-shadow:8px 8px 16px rgba(124,58,237,0.08);">
                <div class="p-5 p-4">
                    @if($errors->any())
                        <div class="flex items-start gap-3 p-4 rounded-lg border bg-red-50 border-red-200 text-red-800 border-0 mb-4">
                            <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('maternal.journal.store') }}">
                        @csrf

                        <div class="flex flex-wrap gap-3">
                            <div class="md:w-6/12">
                                <label for="entry_date" class="block text-sm font-medium text-gray-700 mb-1 font-semibold">Date</label>
                                <input type="date" name="entry_date" id="entry_date" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500 rounded-lg" value="{{ old('entry_date', now()->format('Y-m-d')) }}" required max="{{ now()->format('Y-m-d') }}">
                            </div>

                            <div class="md:w-6/12">
                                <label for="mood" class="block text-sm font-medium text-gray-700 mb-1 font-semibold">Mood</label>
                                <select name="mood" id="mood" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500 rounded-lg" required>
                                    <option value="">Select...</option>
                                    @foreach(['great' => '😊 Great', 'good' => '🙂 Good', 'okay' => '😐 Okay', 'low' => '😔 Low', 'bad' => '😢 Bad'] as $val => $label)
                                        <option value="{{ $val }}" {{ old('mood') === $val ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="md:w-4/12">
                                <label for="energy_level" class="block text-sm font-medium text-gray-700 mb-1 font-semibold">Energy Level (1-5)</label>
                                <input type="range" name="energy_level" id="energy_level" class="w-full accent-violet-600" min="1" max="5" value="{{ old('energy_level', 3) }}">
                                <div class="flex justify-between text-sm text-[var(--color-text-muted)]">
                                    <span>Low</span><span>High</span>
                                </div>
                            </div>

                            <div class="md:w-4/12">
                                <label for="baby_kicks" class="block text-sm font-medium text-gray-700 mb-1 font-semibold">Baby Kicks <small class="text-[var(--color-text-muted)]">(optional)</small></label>
                                <input type="number" name="baby_kicks" id="baby_kicks" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500 rounded-lg" value="{{ old('baby_kicks') }}" min="0" placeholder="Count today">
                            </div>

                            <div class="md:w-4/12">
                                <label for="weight_kg" class="block text-sm font-medium text-gray-700 mb-1 font-semibold">Weight (kg) <small class="text-[var(--color-text-muted)]">(optional)</small></label>
                                <input type="number" name="weight_kg" id="weight_kg" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500 rounded-lg" value="{{ old('weight_kg') }}" step="0.1" min="30" max="200">
                            </div>

                            <div class="md:w-6/12">
                                <label for="blood_pressure" class="block text-sm font-medium text-gray-700 mb-1 font-semibold">Blood Pressure <small class="text-[var(--color-text-muted)]">(optional, e.g. 120/80)</small></label>
                                <input type="text" name="blood_pressure" id="blood_pressure" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500 rounded-lg" value="{{ old('blood_pressure') }}" placeholder="120/80" pattern="\d{2,3}/\d{2,3}">
                            </div>

                            <div class="w-full">
                                <label class="block text-sm font-medium text-gray-700 mb-1 font-semibold">Symptoms <small class="text-[var(--color-text-muted)]">(if any)</small></label>
                                <div class="flex flex-wrap gap-2">
                                    @foreach(['nausea', 'headache', 'back_pain', 'fatigue', 'swelling', 'heartburn', 'insomnia', 'cramping', 'dizziness', 'bleeding'] as $symptom)
                                        <div class="w-6/12 md:w-4/12">
                                            <div class="flex items-center gap-2">
                                                <input class="w-4 h-4 rounded border-gray-300 text-violet-600 focus:ring-violet-500" type="checkbox" name="symptoms[]" value="{{ $symptom }}" id="sym_{{ $symptom }}" {{ in_array($symptom, old('symptoms', [])) ? 'checked' : '' }}>
                                                <label class="text-sm {{ in_array($symptom, ['bleeding', 'dizziness']) ? 'text-red-600 font-semibold' : '' }}" for="sym_{{ $symptom }}">{{ ucfirst(str_replace('_', ' ', $symptom)) }}</label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="w-full">
                                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1 font-semibold">Notes <small class="text-[var(--color-text-muted)]">(optional)</small></label>
                                <textarea name="notes" id="notes" rows="3" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500 rounded-lg" placeholder="How are you feeling today?">{{ old('notes') }}</textarea>
                            </div>
                        </div>

                        <div class="mt-4 flex gap-2">
                            <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed rounded-full" style="background:linear-gradient(135deg, #EC4899, #F472B6); color:#fff;">
                                Save Entry <x-ui.icon name="check" class="ms-1" />
                            </button>
                            <a href="{{ route('maternal.journal.index') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed border-2 border-gray-300 text-gray-700 hover:bg-gray-100 rounded-full">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
