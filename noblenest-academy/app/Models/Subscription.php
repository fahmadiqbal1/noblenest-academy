<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'plan', 'provider', 'provider_id', 'amount', 'currency',
        'starts_at', 'ends_at', 'active', 'status',
        'idempotency_key', 'stripe_subscription_id', 'stripe_customer_id',
        'current_period_end', 'cancel_at', 'paused_at', 'canceled_at',
    ];

    protected $casts = [
        'starts_at'           => 'datetime',
        'ends_at'             => 'datetime',
        'active'              => 'boolean',
        'current_period_end'  => 'datetime',
        'cancel_at'           => 'datetime',
        'paused_at'           => 'datetime',
        'canceled_at'         => 'datetime',
    ];

    public const STATUS_ACTIVE   = 'active';
    public const STATUS_PAST_DUE = 'past_due';
    public const STATUS_CANCELED = 'canceled';
    public const STATUS_PAUSED   = 'paused';

    public function user() { return $this->belongsTo(User::class); }

    // ─────────────────────────────────────────────────────────────────────────
    // Phase 7 — state machine helpers.
    // ─────────────────────────────────────────────────────────────────────────

    public function isActive(): bool   { return $this->status === self::STATUS_ACTIVE; }
    public function isPastDue(): bool  { return $this->status === self::STATUS_PAST_DUE; }
    public function isCanceled(): bool { return $this->status === self::STATUS_CANCELED; }
    public function isPaused(): bool   { return $this->status === self::STATUS_PAUSED; }

    public function markPastDue(): void
    {
        $this->forceFill(['status' => self::STATUS_PAST_DUE])->save();
    }

    public function cancel(): void
    {
        $this->forceFill([
            'status'      => self::STATUS_CANCELED,
            'active'      => false,
            'canceled_at' => now(),
        ])->save();
    }

    public function pause(): void
    {
        $this->forceFill([
            'status'    => self::STATUS_PAUSED,
            'paused_at' => now(),
        ])->save();
    }

    public function resume(): void
    {
        $this->forceFill([
            'status'    => self::STATUS_ACTIVE,
            'active'    => true,
            'paused_at' => null,
        ])->save();
    }

    /**
     * Phase 7 — Prorated plan upgrade.
     *
     * Computes the proration delta via PricingService and writes a Payment
     * row with status='pending_proration'. The real Stripe invoice is issued
     * by Stripe Billing on the next cycle — this records the local intent.
     */
    public function upgradeTo(string $newTierKey, ?string $countryCode = null): \App\Models\Payment
    {
        /** @var \App\Services\PricingService $pricing */
        $pricing = app(\App\Services\PricingService::class);

        $newTier = $pricing->resolveTier($newTierKey, $countryCode);
        $oldTier = $pricing->resolveTier($this->plan ?: 'individual', $countryCode);

        $cycleDays     = 30;
        $daysRemaining = $this->ends_at
            ? max(0, (int) ceil(now()->diffInDays($this->ends_at, false)))
            : $cycleDays;

        $proration = $pricing->computeUpgradeProration($oldTier, $newTier, $daysRemaining, $cycleDays);

        $payment = \App\Models\Payment::create([
            'user_id'             => $this->user_id,
            'provider'            => $this->provider ?? 'stripe',
            'provider_payment_id' => 'proration_' . \Illuminate\Support\Str::random(16),
            'amount'              => $proration['amount'],
            'currency'            => $proration['currency'],
            'status'              => 'pending_proration',
            'paid_at'             => null,
        ]);

        $this->forceFill(['plan' => $newTierKey])->save();

        return $payment;
    }

    /**
     * Get the current drip week (1-4) based on weeks since subscription started.
     */
    public function currentWeek(): int
    {
        if (!$this->starts_at) {
            return 1;
        }

        $weeks = (int) ceil($this->starts_at->diffInDays(now()) / 7);

        return min(max($weeks, 1), 4);
    }

    /**
     * Maximum activity order unlocked by the current drip week.
     * Week 1 = orders 1-5, Week 2 = 1-10, Week 3 = 1-15, Week 4 = 1-20 (all)
     */
    public function maxActivityOrder(): int
    {
        return $this->currentWeek() * 5;
    }
}

