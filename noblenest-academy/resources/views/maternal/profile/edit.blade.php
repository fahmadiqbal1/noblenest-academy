@extends('layouts.maternal')

@section('title', 'Edit Maternal Profile')

@section('content')
<div class="container py-4">
    <div class="flex flex-wrap">
        <div class="lg:w-3/12 hidden lg:block">
            @include('maternal.partials.sidebar')
        </div>
        <div class="lg:w-9/12">
            <h3 class="mb-4" style="font-family:'Baloo 2',sans-serif; color:var(--nn-text);">
                <x-ui.icon name="settings" class="me-2" style="color:#7C3AED;" /> Maternal Profile
            </h3>

            @if(session('success'))
                <div class="flex items-start gap-3 p-4 rounded-lg border bg-emerald-50 border-emerald-200 text-emerald-800 border-0 mb-4">{{ session('success') }}</div>
            @endif

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm border-0 mb-4" style="background:rgba(255,255,255,0.88); border-radius:1.25rem; box-shadow:8px 8px 16px rgba(124,58,237,0.08);">
                <div class="p-5 p-4">
                    <form method="POST" action="{{ route('maternal.profile.update') }}">
                        @csrf @method('PUT')

                        <div class="mb-3">
                            <label for="due_date" class="block text-sm font-medium text-gray-700 mb-1 font-semibold">Expected Due Date</label>
                            <input type="date" name="due_date" id="due_date" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500 rounded-lg" value="{{ old('due_date', $profile->due_date?->format('Y-m-d')) }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1 font-semibold">Health Conditions</label>
                            <div class="flex flex-wrap gap-2">
                                @foreach(['gestational_diabetes', 'hypertension', 'preeclampsia', 'anemia', 'thyroid_disorder', 'placenta_previa', 'multiple_pregnancy'] as $condition)
                                    <div class="w-6/12">
                                        <div class="flex items-center gap-2">
                                            <input class="w-4 h-4 rounded border-gray-300 text-violet-600 focus:ring-violet-500" type="checkbox" name="health_conditions[]" value="{{ $condition }}" id="cond_{{ $condition }}" {{ in_array($condition, old('health_conditions', $profile->health_conditions ?? [])) ? 'checked' : '' }}>
                                            <label class="text-sm" for="cond_{{ $condition }}">{{ str_replace('_', ' ', ucfirst($condition)) }}</label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1 font-semibold">Dietary Restrictions</label>
                            <div class="flex flex-wrap gap-2">
                                @foreach(['vegetarian', 'vegan', 'gluten_free', 'dairy_free', 'nut_allergy', 'halal', 'kosher'] as $diet)
                                    <div class="w-6/12">
                                        <div class="flex items-center gap-2">
                                            <input class="w-4 h-4 rounded border-gray-300 text-violet-600 focus:ring-violet-500" type="checkbox" name="dietary_restrictions[]" value="{{ $diet }}" id="diet_{{ $diet }}" {{ in_array($diet, old('dietary_restrictions', $profile->dietary_restrictions ?? [])) ? 'checked' : '' }}>
                                            <label class="text-sm" for="diet_{{ $diet }}">{{ str_replace('_', ' ', ucfirst($diet)) }}</label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed rounded-full" style="background:var(--nn-primary); color:#fff;">
                            Save Changes
                        </button>
                    </form>
                </div>
            </div>

            {{-- Status actions --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm border-0" style="background:rgba(255,255,255,0.88); border-radius:1.25rem;">
                <div class="p-5 p-4">
                    <h6 class="font-semibold mb-3">Journey Status: <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium" style="background:{{ $profile->status === 'active' ? '#ECFDF5' : '#FEF3C7' }}; color:{{ $profile->status === 'active' ? '#065F46' : '#92400E' }};">{{ ucfirst($profile->status) }}</span></h6>

                    <div class="flex flex-wrap gap-2">
                        @if($profile->status === 'active')
                            <form method="POST" action="{{ route('maternal.profile.pause') }}">
                                @csrf
                                <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed border-2 border-amber-500 text-amber-600 hover:bg-amber-500 hover:text-gray-900 px-3 py-1.5 text-sm rounded-full" onclick="return confirm('Are you sure you want to pause your journey?')">
                                    <x-ui.icon name="circle-pause" class="me-1" /> Pause Journey
                                </button>
                            </form>
                        @elseif($profile->status === 'paused')
                            <form method="POST" action="{{ route('maternal.profile.resume') }}">
                                @csrf
                                <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed bg-emerald-600 text-white hover:bg-emerald-700 px-3 py-1.5 text-sm rounded-full">
                                    <x-ui.icon name="circle-play" class="me-1" /> Resume Journey
                                </button>
                            </form>
                        @endif

                        @if(in_array($profile->status, ['active', 'paused']))
                            <form method="POST" action="{{ route('maternal.profile.loss') }}">
                                @csrf
                                <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed border-2 border-gray-300 text-gray-700 hover:bg-gray-100 px-3 py-1.5 text-sm rounded-full" onclick="return confirm('We are deeply sorry. This will pause your journey. You can resume at any time. Are you sure?')">
                                    <x-ui.icon name="heart" class="me-1" /> Report Loss
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
