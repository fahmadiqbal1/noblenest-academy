<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherCourseSection extends Model
{
    use HasFactory;

    protected $fillable = ['teacher_course_id', 'title', 'description', 'order'];

    protected $casts = ['order' => 'integer'];

    public function course(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(TeacherCourse::class, 'teacher_course_id');
    }
}
