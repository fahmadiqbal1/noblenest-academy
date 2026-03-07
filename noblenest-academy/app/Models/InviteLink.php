<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class InviteLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_course_id', 'token', 'label', 'max_uses', 'uses', 'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'max_uses'   => 'integer',
        'uses'       => 'integer',
    ];

    public function course(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(TeacherCourse::class, 'teacher_course_id');
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isExhausted(): bool
    {
        return ! is_null($this->max_uses) && $this->uses >= $this->max_uses;
    }

    public function isValid(): bool
    {
        return ! $this->isExpired() && ! $this->isExhausted();
    }

    public static function generateToken(): string
    {
        return Str::random(32);
    }
}
