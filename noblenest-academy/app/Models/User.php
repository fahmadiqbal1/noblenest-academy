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
        'country_code',
        'referral_code',
        'is_onboarded',
        'terms_accepted_at',
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
            'terms_accepted_at' => 'datetime',
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
    // Child Profiles (COPPA-compliant - separated from users)
    // ------------------------------------------------------------------

    /**
     * Get child profiles managed by this parent user.
     * This is the COPPA-compliant way to manage children.
     */
    public function childProfiles(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\ChildProfile::class, 'parent_id');
    }

    // ------------------------------------------------------------------
    // Legacy: Children as Users (deprecated - use childProfiles instead)
    // ------------------------------------------------------------------

    /**
     * @deprecated Use childProfiles() instead for COPPA compliance
     */
    public function children(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\User::class, 'parent_id');
    }

    // ------------------------------------------------------------------
    // Role helpers
    // ------------------------------------------------------------------

    public function isAdmin(): bool
    {
        return $this->role === 'Admin';
    }

    public function isParent(): bool
    {
        return $this->role === 'Parent';
    }

    public function isTeacher(): bool
    {
        return $this->role === 'Teacher';
    }

    public function isStudent(): bool
    {
        return $this->role === 'Student';
    }

    public function isPractitioner(): bool
    {
        return $this->role === 'Practitioner';
    }

    /**
     * Get subscriptions for this user.
     */
    public function subscriptions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\Subscription::class);
    }

    /**
     * Check if user has an active subscription.
     */
    public function hasActiveSubscription(): bool
    {
        return $this->subscriptions()
            ->where('active', true)
            ->where('ends_at', '>', now())
            ->exists();
    }

    // ------------------------------------------------------------------
    // Maternal profile
    // ------------------------------------------------------------------

    public function maternalProfile(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(\App\Models\MaternalProfile::class);
    }

    public function hasMaternalProfile(): bool
    {
        return $this->maternalProfile()->exists();
    }

    // ------------------------------------------------------------------
    // Practitioner profile
    // ------------------------------------------------------------------

    public function practitionerProfile(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(\App\Models\PractitionerProfile::class);
    }

    public function hasPractitionerProfile(): bool
    {
        return $this->practitionerProfile()->exists();
    }
}
