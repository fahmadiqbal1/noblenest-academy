@extends('layouts.app')

@section('title', 'Journal Entry — ' . \Carbon\Carbon::parse($maternalJournal->entry_date)->format('M j, Y'))

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-lg-3 d-none d-lg-block">
            @include('maternal.partials.sidebar')
        </div>
        <div class="col-lg-9">
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('maternal.journal.index') }}">Journal</a></li>
                    <li class="breadcrumb-item active">{{ \Carbon\Carbon::parse($maternalJournal->entry_date)->format('M j, Y') }}</li>
                </ol>
            </nav>

            <div class="card border-0" style="background:rgba(255,255,255,0.88); border-radius:1.25rem; box-shadow:8px 8px 16px rgba(124,58,237,0.08);">
                <div class="card-body p-4">
                    <h3 style="font-family:'Baloo 2',sans-serif; color:var(--nn-text);">
                        {{ \Carbon\Carbon::parse($maternalJournal->entry_date)->format('l, F j, Y') }}
                    </h3>

                    <div class="row g-3 mt-3">
                        <div class="col-md-3">
                            <div class="card border-0 text-center p-3" style="background:rgba(124,58,237,0.04); border-radius:1rem;">
                                <div style="font-size:1.5rem;">{{ match($maternalJournal->mood) { 'great' => '😊', 'good' => '🙂', 'okay' => '😐', 'low' => '😔', 'bad' => '😢', default => '🙂' } }}</div>
                                <small class="fw-semibold">{{ ucfirst($maternalJournal->mood) }}</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-0 text-center p-3" style="background:rgba(124,58,237,0.04); border-radius:1rem;">
                                <div style="font-size:1.5rem;">⚡</div>
                                <small class="fw-semibold">Energy {{ $maternalJournal->energy_level }}/5</small>
                            </div>
                        </div>
                        @if($maternalJournal->baby_kicks)
                        <div class="col-md-3">
                            <div class="card border-0 text-center p-3" style="background:rgba(124,58,237,0.04); border-radius:1rem;">
                                <div style="font-size:1.5rem;">👶</div>
                                <small class="fw-semibold">{{ $maternalJournal->baby_kicks }} kicks</small>
                            </div>
                        </div>
                        @endif
                        @if($maternalJournal->weight_kg)
                        <div class="col-md-3">
                            <div class="card border-0 text-center p-3" style="background:rgba(124,58,237,0.04); border-radius:1rem;">
                                <div style="font-size:1.5rem;">⚖️</div>
                                <small class="fw-semibold">{{ $maternalJournal->weight_kg }} kg</small>
                            </div>
                        </div>
                        @endif
                    </div>

                    @if($maternalJournal->blood_pressure)
                    <div class="mt-3">
                        <span class="fw-semibold"><i class="bi bi-heart-pulse me-1"></i> Blood Pressure:</span>
                        {{ $maternalJournal->blood_pressure }}
                    </div>
                    @endif

                    @if($maternalJournal->symptoms && count($maternalJournal->symptoms) > 0)
                    <div class="mt-3">
                        <h6 class="fw-semibold">Symptoms</h6>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($maternalJournal->symptoms as $symptom)
                                <span class="badge rounded-pill px-3 py-2 {{ in_array($symptom, ['bleeding', 'severe_headache', 'blurred_vision']) ? 'bg-danger' : '' }}" style="{{ !in_array($symptom, ['bleeding', 'severe_headache', 'blurred_vision']) ? 'background:#FEF3C7; color:#92400E;' : '' }}">
                                    {{ ucfirst(str_replace('_', ' ', $symptom)) }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if($maternalJournal->notes)
                    <div class="mt-4">
                        <h6 class="fw-semibold">Notes</h6>
                        <p class="mb-0">{{ $maternalJournal->notes }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
