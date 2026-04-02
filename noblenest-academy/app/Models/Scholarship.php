<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Scholarship extends Model
{
    protected $fillable = [
        'code',
        'granted_by',
        'granted_to',
        'recipient_email',
        'duration_months',
        'is_claimed',
        'claimed_at',
        'expires_at',
        'reason',
    ];

    protected $casts = [
        'is_claimed'  => 'boolean',
        'claimed_at'  => 'datetime',
        'expires_at'  => 'datetime',
        'duration_months' => 'integer',
    ];

    public function grantor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'granted_by');
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'granted_to');
    }
}
