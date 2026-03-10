<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // Parent, Child, Admin
        'parent_id', // For child profiles
        'age',
        'preferred_language',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Relationship: Parent has many children
    public function children()
    {
        return $this->hasMany(User::class, 'parent_id');
    }

    // Relationship: Child belongs to parent
    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    /**
     * Get child profiles (COPPA-compliant separate model).
     */
    public function childProfiles()
    {
        return $this->hasMany(ChildProfile::class, 'parent_id');
    }

    /**
     * Check if user is a parent.
     */
    public function isParent(): bool
    {
        return $this->role === 'Parent';
    }

    /**
     * Check if user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'Admin';
    }

    /**
     * Check if user is a teacher.
     */
    public function isTeacher(): bool
    {
        return $this->role === 'Teacher';
    }
}
