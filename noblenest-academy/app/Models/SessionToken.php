<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SessionToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_session_id', 'user_id', 'token', 'role', 'expires_at', 'joined_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'joined_at'  => 'datetime',
    ];

    public function session(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ClassSession::class, 'class_session_id');
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public static function generate(int $sessionId, int $userId, string $role = 'student'): self
    {
        return static::firstOrCreate(
            ['class_session_id' => $sessionId, 'user_id' => $userId],
            [
                'token'      => Str::random(40),
                'role'       => $role,
                'expires_at' => now()->addHours(6),
            ]
        );
    }
}
