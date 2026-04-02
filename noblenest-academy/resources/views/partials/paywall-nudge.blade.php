@php
    /** @var \App\Models\ChildProfile $child */
@endphp
<div class="paywall-nudge" id="paywall-nudge">
    <div class="paywall-nudge__inner">
        <div class="paywall-nudge__emoji">🔓</div>
        <div>
            <div class="paywall-nudge__title">Unlock Unlimited Learning!</div>
            <div class="paywall-nudge__body">
                {{ $child->name }} has completed 7 free activities. Upgrade to keep the learning momentum going!
            </div>
            <a href="{{ route('pricing') }}" class="paywall-nudge__btn">See Plans</a>
        </div>
    </div>
</div>
<style>
.paywall-nudge { background: linear-gradient(135deg, #fef3c7, #fde68a); border: 2px solid #f59e0b; border-radius: 1.25rem; padding: 1.25rem; margin-top: 1rem; }
.paywall-nudge__inner { display: flex; align-items: flex-start; gap: 0.75rem; }
.paywall-nudge__emoji { font-size: 2rem; flex-shrink: 0; }
.paywall-nudge__title { font-size: 0.95rem; font-weight: 700; color: #92400e; }
.paywall-nudge__body { font-size: 0.8rem; color: #78350f; margin-top: 2px; }
.paywall-nudge__btn { display: inline-block; margin-top: 0.5rem; background: #f59e0b; color: #fff; border-radius: 50px; padding: 0.4rem 1.2rem; font-size: 0.8rem; font-weight: 700; text-decoration: none; }
</style>
