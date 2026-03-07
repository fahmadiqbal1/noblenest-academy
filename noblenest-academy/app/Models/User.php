<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'parent_id',
        'age',
        'preferred_language',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'age' => 'integer',
        ];
    }

    // ------------------------------------------------------------------
    // Teacher relationships
    // ------------------------------------------------------------------

    public function teacherCourses(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\TeacherCourse::class, 'teacher_id');
    }

    // ------------------------------------------------------------------
    // Student relationships
    // ------------------------------------------------------------------

    public function teacherEnrollments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\TeacherEnrollment::class, 'student_id');
    }

    public function enrolledCourses(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(
            \App\Models\TeacherCourse::class,
            'teacher_enrollments',
            'student_id',
            'teacher_course_id'
        )->wherePivot('status', 'active');
    }

    // ------------------------------------------------------------------
    // Children (existing)
    // ------------------------------------------------------------------

    public function children(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\User::class, 'parent_id');
    }
}
