@php
    /** @var \App\Models\ChildProfile $child */
    $userRole = auth()->user()->role ?? '';
@endphp
@if(in_array($userRole, ['Parent', 'Student']))
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
.paywall-nudge { background: var(--nn-primary-soft); border: var(--nn-border-w) solid var(--nn-border); border-radius: var(--nn-radius); padding: 1.25rem; margin-top: 1rem; box-shadow: var(--nn-shadow); }
.paywall-nudge__inner { display: flex; align-items: flex-start; gap: 0.75rem; }
.paywall-nudge__emoji { font-size: 2rem; flex-shrink: 0; }
.paywall-nudge__title { font-size: 0.95rem; font-weight: 700; color: var(--nn-text); }
.paywall-nudge__body { font-size: 0.8rem; color: var(--nn-text-muted); margin-top: 2px; }
.paywall-nudge__btn { display: inline-block; margin-top: 0.5rem; background: var(--nn-primary); color: #fff; border-radius: 50px; padding: 0.4rem 1.2rem; font-size: 0.8rem; font-weight: 700; text-decoration: none; }
</style>
@endif
