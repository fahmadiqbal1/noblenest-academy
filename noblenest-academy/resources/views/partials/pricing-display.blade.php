@php
    /**
     * Pricing display partial
     * Usage: @include('partials.pricing-display', ['tiers' => $tiers, 'highlight' => 'FAMILY'])
     */
    $highlight = $highlight ?? 'FAMILY';
@endphp
<div class="pricing-display">
    @foreach($tiers as $tier)
        <div class="pricing-card @if($tier->name === $highlight) pricing-card--featured @endif">
            @if($tier->name === $highlight)
                <div class="pricing-card__badge">Most Popular</div>
            @endif
            <div class="pricing-card__name">{{ $tier->display_name ?? $tier->name }}</div>
            <div class="pricing-card__price">
                <span class="pricing-card__currency">$</span>
                <span class="pricing-card__amount">{{ number_format($tier->price_monthly, 2) }}</span>
                <span class="pricing-card__period">/mo</span>
            </div>
            @if($tier->price_yearly)
                <div class="pricing-card__yearly">or ${{ number_format($tier->price_yearly, 2) }}/year</div>
            @endif
            <a href="{{ route('pricing') }}" class="pricing-card__btn">
                Get Started
            </a>
        </div>
    @endforeach
</div>
<style>
.pricing-display { display: flex; flex-wrap: wrap; gap: 1rem; justify-content: center; }
.pricing-card { background: #fff; border: 2px solid #e5e7eb; border-radius: 1.25rem; padding: 1.5rem 1.25rem; text-align: center; min-width: 180px; flex: 1; position: relative; transition: box-shadow 0.2s; }
.pricing-card:hover { box-shadow: 0 8px 32px rgba(0,0,0,0.1); }
.pricing-card--featured { border-color: #0d5c63; box-shadow: 0 8px 32px rgba(13,92,99,0.18); }
.pricing-card__badge { position: absolute; top: -10px; left: 50%; transform: translateX(-50%); background: #0d5c63; color: #fff; font-size: 0.65rem; font-weight: 700; padding: 3px 10px; border-radius: 50px; white-space: nowrap; }
.pricing-card__name { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: #9ca3af; margin-bottom: 0.5rem; }
.pricing-card__price { display: flex; align-items: baseline; justify-content: center; gap: 2px; }
.pricing-card__currency { font-size: 1rem; font-weight: 700; color: #374151; }
.pricing-card__amount { font-size: 2.2rem; font-weight: 800; color: #111827; line-height: 1; }
.pricing-card__period { font-size: 0.8rem; color: #9ca3af; }
.pricing-card__yearly { font-size: 0.72rem; color: #6b7280; margin-top: 4px; }
.pricing-card__btn { display: block; margin-top: 1rem; background: #0d5c63; color: #fff; border-radius: 50px; padding: 0.55rem 1rem; font-size: 0.8rem; font-weight: 700; text-decoration: none; transition: background 0.2s; }
.pricing-card__btn:hover { background: #0a4a50; color: #fff; }
.pricing-card--featured .pricing-card__btn { background: #0d5c63; }
</style>
