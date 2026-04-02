<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PricingTier extends Model
{
    protected $fillable = [
        'region_code',
        'region_label',
        'country_codes',
        'price_monthly',
        'price_yearly',
        'currency_code',
        'stripe_price_id_monthly',
        'stripe_price_id_yearly',
        'is_active',
    ];

    protected $casts = [
        'country_codes'  => 'array',
        'price_monthly'  => 'decimal:2',
        'price_yearly'   => 'decimal:2',
        'is_active'      => 'boolean',
    ];
}
