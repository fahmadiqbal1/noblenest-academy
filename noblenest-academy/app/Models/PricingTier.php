<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PricingTier extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'country_code',
        'region_code',
        'region_label',
        'country_codes',
        'price_monthly',
        'price_yearly',
        'interval',
        'features',
        'currency_code',
        'stripe_price_id_monthly',
        'stripe_price_id_yearly',
        'is_active',
    ];

    protected $casts = [
        'country_codes' => 'array',
        'features' => 'array',
        'price_monthly' => 'decimal:2',
        'price_yearly' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Effective price for this tier — yearly amount when interval=yearly,
     * monthly otherwise. Used by PPP / spec-plan flows in Phase 7.
     */
    public function effectivePrice(): float
    {
        if ($this->interval === 'yearly') {
            return (float) $this->price_yearly;
        }

        return (float) $this->price_monthly;
    }
}
