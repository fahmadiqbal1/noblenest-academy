<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TeacherProfile extends Model
{
    protected $fillable = [
        'user_id',
        'headline',
        'bio',
        'country',
        'timezone',
        'languages_spoken',
        'credentials',
        'cv_path',
        'status',
        'rejection_reason',
        'reviewed_by',
        'reviewed_at',
        'is_edu_email',
        'lesson_count',
        'rating_average',
        'rating_count',
        'payout_rate',
    ];

    protected $casts = [
        'credentials'    => 'array',
        'is_edu_email'   => 'boolean',
        'reviewed_at'    => 'datetime',
        'rating_average' => 'decimal:2',
        'payout_rate'    => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return in_array($this->status, ['approved', 'auto_approved']);
    }
}
