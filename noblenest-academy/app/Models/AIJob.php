<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AIJob extends Model
{
    use HasFactory;

    protected $table = 'ai_jobs';

    protected $fillable = [
        'type',
        'status',
        'provider',
        'locale',
        'user_id',
        'payload',
        'result',
        'moderation_status',
        'error_message',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'payload'      => 'array',
        'result'       => 'array',
        'started_at'   => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'queued';
    }

    public function isRunning(): bool
    {
        return $this->status === 'running';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function needsModeration(): bool
    {
        return $this->moderation_status === 'pending';
    }
}
