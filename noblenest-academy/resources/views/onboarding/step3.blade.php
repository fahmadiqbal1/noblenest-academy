@extends('layouts.app')

@section('meta_title', 'Learning Goals — Noble Nest Academy')

@section('content')
<div class="min-vh-100 d-flex align-items-center justify-content-center"
     style="background: linear-gradient(135deg, #F5F0FF 0%, #FFFBF0 50%, #FFF7ED 100%);">
    <div class="card border-0 rounded-4" style="max-width: 520px; width: 100%; background:rgba(255,255,255,0.82); border:2px solid rgba(124,58,237,0.10) !important; box-shadow:8px 8px 16px rgba(124,58,237,0.08), -4px -4px 12px rgba(255,255,255,0.6);">
        <div class="card-body p-5">

            {{-- Progress orbs --}}
            <div class="d-flex justify-content-center gap-3 mb-4">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:36px;height:36px;background:var(--nn-success, #10B981);color:#fff;font-weight:700;font-size:0.85rem;">✓</div>
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:36px;height:36px;background:var(--nn-success, #10B981);color:#fff;font-weight:700;font-size:0.85rem;">✓</div>
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:36px;height:36px;background:var(--nn-primary, #7C3AED);color:#fff;font-weight:700;font-size:0.85rem;">3</div>
            </div>

            <div class="text-center mb-4">
                <div class="fs-1 mb-2">🎯</div>
                <h1 class="h3 fw-bold mb-1" style="font-family:'Baloo 2',sans-serif; color:var(--nn-text, #1E1B4B);">What are your goals?</h1>
                <p class="text-muted small">Optional — helps us recommend the best activities.</p>
            </div>

            <form action="{{ route('onboarding.step3.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-semibold">Learning goals (select all that apply)</label>
                    <div class="row g-2">
                        @php
                        $goals = [
                            'language'     => ['label' => 'Language skills',    'icon' => '🗣️'],
                            'motor'        => ['label' => 'Motor development',   'icon' => '🖐️'],
                            'creativity'   => ['label' => 'Creativity',          'icon' => '🎨'],
                            'stem'         => ['label' => 'STEM exploration',    'icon' => '🔬'],
                            'social'       => ['label' => 'Social skills',       'icon' => '🤝'],
                            'cultural'     => ['label' => 'Cultural awareness',  'icon' => '🌍'],
                        ];
                        @endphp
                        @foreach($goals as $key => $goal)
                        <div class="col-6">
                            <input type="checkbox" class="btn-check" name="goals[]"
                                   id="goal_{{ $key }}" value="{{ $key }}">
                            <label class="btn btn-outline-secondary w-100 py-3 rounded-3 text-start"
                                   for="goal_{{ $key }}">
                                <span class="me-1">{{ $goal['icon'] }}</span>
                                <span class="small fw-semibold">{{ $goal['label'] }}</span>
                            </label>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="mb-4">
                    <label for="daily_minutes" class="form-label fw-semibold">
                        Daily learning time
                        <span class="text-muted fw-normal" id="minuteLabel"> (15 min)</span>
                    </label>
                    <input type="range" class="form-range" id="daily_minutes" name="daily_minutes"
                           min="5" max="60" step="5" value="15"
                           oninput="document.getElementById('minuteLabel').textContent = ' (' + this.value + ' min)'">
                    <div class="d-flex justify-content-between small text-muted">
                        <span>5 min</span><span>30 min</span><span>60 min</span>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary fw-bold w-100 py-3 rounded-3 fs-5">
                    🚀 Start Learning!
                </button>

                <div class="text-center mt-3">
                    <a href="{{ route('home') }}" class="text-muted small">Skip and go to home</a>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection
