@extends('layouts.maternal')

@section('title', 'Maternal Wellness Onboarding — Noble Nest Academy')

@section('content')
<div class="container py-5">
    <div class="flex flex-wrap justify-center">
        <div class="lg:w-7/12">
            <div class="text-center mb-4">
                <div class="inline-flex items-center justify-center mb-3" style="width:72px;height:72px;border-radius:50%;background:linear-gradient(135deg, #EC4899, #F472B6);color:#fff;font-size:2rem;">
                    🤰
                </div>
                <h2 style="font-family:'Baloo 2',sans-serif; color:var(--nn-text);">Begin Your Maternal Wellness Journey</h2>
                <p class="text-[var(--color-text-muted)]">Ancient wisdom meets modern care. Tell us about your pregnancy so we can personalize your experience.</p>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm border-0" style="background:rgba(255,255,255,0.88); border-radius:1.25rem; box-shadow:8px 8px 16px rgba(124,58,237,0.08), -4px -4px 12px rgba(255,255,255,0.6);">
                <div class="p-5 p-4">
                    @if($errors->any())
                        <div class="flex items-start gap-3 p-4 rounded-lg border bg-red-50 border-red-200 text-red-800 border-0 mb-4">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('maternal.onboarding.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="due_date" class="block text-sm font-medium text-gray-700 mb-1 font-semibold">Expected Due Date</label>
                            <input type="date" name="due_date" id="due_date" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500 rounded-lg" value="{{ old('due_date') }}" required min="{{ now()->format('Y-m-d') }}">
                        </div>

                        <div class="mb-3">
                            <label for="pregnancy_week" class="block text-sm font-medium text-gray-700 mb-1 font-semibold">Current Pregnancy Week</label>
                            <input type="number" name="pregnancy_week" id="pregnancy_week" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500 rounded-lg" value="{{ old('pregnancy_week') }}" required min="1" max="42" placeholder="e.g. 12">
                            <small class="text-[var(--color-text-muted)]">How many weeks pregnant are you?</small>
                        </div>

                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1 font-semibold">Health Conditions <small class="text-[var(--color-text-muted)]">(if any)</small></label>
                            <div class="flex flex-wrap gap-2">
                                @foreach(['gestational_diabetes', 'hypertension', 'preeclampsia', 'anemia', 'thyroid_disorder', 'placenta_previa', 'multiple_pregnancy'] as $condition)
                                    <div class="w-6/12">
                                        <div class="flex items-center gap-2">
                                            <input class="w-4 h-4 rounded border-gray-300 text-violet-600 focus:ring-violet-500" type="checkbox" name="health_conditions[]" value="{{ $condition }}" id="cond_{{ $condition }}" {{ in_array($condition, old('health_conditions', [])) ? 'checked' : '' }}>
                                            <label class="text-sm" for="cond_{{ $condition }}">{{ str_replace('_', ' ', ucfirst($condition)) }}</label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <small class="text-[var(--color-text-muted)] mt-1 block">This helps us filter content that may not be suitable for your situation.</small>
                        </div>

                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1 font-semibold">Dietary Restrictions <small class="text-[var(--color-text-muted)]">(optional)</small></label>
                            <div class="flex flex-wrap gap-2">
                                @foreach(['vegetarian', 'vegan', 'gluten_free', 'dairy_free', 'nut_allergy', 'halal', 'kosher'] as $diet)
                                    <div class="w-6/12">
                                        <div class="flex items-center gap-2">
                                            <input class="w-4 h-4 rounded border-gray-300 text-violet-600 focus:ring-violet-500" type="checkbox" name="dietary_restrictions[]" value="{{ $diet }}" id="diet_{{ $diet }}" {{ in_array($diet, old('dietary_restrictions', [])) ? 'checked' : '' }}>
                                            <label class="text-sm" for="diet_{{ $diet }}">{{ str_replace('_', ' ', ucfirst($diet)) }}</label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="mb-4">
                            <div class="flex items-center gap-2">
                                <input class="w-4 h-4 rounded border-gray-300 text-violet-600 focus:ring-violet-500" type="checkbox" name="consent_accepted" id="consent_accepted" value="1" required>
                                <label class="text-sm" for="consent_accepted">
                                    I understand that the content provided is <strong>educational only</strong> and does not replace professional medical advice. I consent to the storage of my health data, which is encrypted and never shared with third parties.
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed w-full rounded-full" style="background:linear-gradient(135deg, #EC4899, #F472B6); color:#fff; font-size:1.05rem;">
                            Start My Journey <x-ui.icon name="arrow-right" class="ms-1" />
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
