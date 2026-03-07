<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherEnrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id', 'teacher_course_id', 'status',
        'payment_status', 'payment_provider', 'payment_ref',
        'amount_paid', 'currency', 'enrolled_at',
    ];

    protected $casts = [
        'enrolled_at' => 'datetime',
        'amount_paid' => 'decimal:2',
    ];

    public function student(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function course(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(TeacherCourse::class, 'teacher_course_id');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }
}
