<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Referral extends Model
{
    protected $fillable = [
        'referrer_id',
        'referred_id',
        'code',
        'status',
        'signed_up_at',
        'subscribed_at',
        'reward_amount',
        'reward_issued',
    ];

    protected $casts = [
        'signed_up_at'   => 'datetime',
        'subscribed_at'  => 'datetime',
        'reward_amount'  => 'decimal:2',
        'reward_issued'  => 'boolean',
    ];

    public function referrer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referrer_id');
    }

    public function referred(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_id');
    }
}
