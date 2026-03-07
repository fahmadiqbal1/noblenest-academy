<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ClassSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_course_id', 'teacher_id', 'title', 'description',
        'starts_at', 'duration_minutes', 'status', 'room_id', 'meeting_url',
    ];

    protected $casts = [
        'starts_at'        => 'datetime',
        'duration_minutes' => 'integer',
    ];

    public function course(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(TeacherCourse::class, 'teacher_course_id');
    }

    public function teacher(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function tokens(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SessionToken::class);
    }

    public function isLive(): bool
    {
        return $this->status === 'live';
    }

    public function endsAt(): \Carbon\Carbon
    {
        return $this->starts_at->addMinutes($this->duration_minutes);
    }

    public static function generateRoomId(): string
    {
        return 'room-' . Str::random(12);
    }
}
