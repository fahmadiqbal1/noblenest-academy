<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TeacherCourse extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id', 'title', 'slug', 'description', 'what_you_learn',
        'subject', 'age_min', 'age_max', 'level', 'language',
        'price', 'currency', 'thumbnail', 'syllabus_file',
        'status', 'max_students',
    ];

    protected $casts = [
        'price'    => 'decimal:2',
        'age_min'  => 'integer',
        'age_max'  => 'integer',
    ];

    // ------------------------------------------------------------------
    // Relationships
    // ------------------------------------------------------------------

    public function teacher(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function sections(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TeacherCourseSection::class)->orderBy('order');
    }

    public function enrollments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TeacherEnrollment::class);
    }

    public function activeEnrollments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TeacherEnrollment::class)->where('status', 'active');
    }

    public function classSessions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ClassSession::class)->orderBy('starts_at');
    }

    public function inviteLinks(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(InviteLink::class);
    }

    // ------------------------------------------------------------------
    // Helpers
    // ------------------------------------------------------------------

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function isFree(): bool
    {
        return $this->price == 0;
    }

    public function hasCapacity(): bool
    {
        if (is_null($this->max_students)) {
            return true;
        }

        return $this->activeEnrollments()->count() < $this->max_students;
    }

    public static function generateSlug(string $title): string
    {
        $base  = Str::slug($title);
        $slug  = $base;
        $count = 1;
        while (static::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $count++;
        }

        return $slug;
    }
}
