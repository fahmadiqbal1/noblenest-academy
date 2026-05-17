<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolAdminInvite extends Model
{
    protected $fillable = [
        'email',
        'school_name',
        'seats',
        'invite_token',
        'expires_at',
        'accepted_at',
        'accepted_by_user_id',
    ];

    protected $casts = [
        'seats'        => 'integer',
        'expires_at'   => 'datetime',
        'accepted_at'  => 'datetime',
    ];

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function isAccepted(): bool
    {
        return $this->accepted_at !== null;
    }
}
