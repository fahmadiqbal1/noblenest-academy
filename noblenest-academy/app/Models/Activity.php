<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'age_min',
        'age_max',
        'skill',
        'subject',
        'duration',
        'language',
        'activity_type',
        'media_url',
        'is_rtl',
        'emoji',
        'age_tier',
        'is_free',
        'like_count',
    ];

    protected $casts = [
        'age_min'    => 'integer',
        'age_max'    => 'integer',
        'duration'   => 'integer',
        'is_rtl'     => 'boolean',
        'is_free'    => 'boolean',
        'like_count' => 'integer',
    ];

    /**
     * Modules that contain this activity (many-to-many).
     */
    public function modules()
    {
        return $this->belongsToMany(Module::class, 'activity_module')
                    ->withTimestamps();
    }

    /**
     * Lessons that contain this activity (many-to-many).
     */
    public function lessons()
    {
        return $this->belongsToMany(Lesson::class, 'activity_lesson')
                    ->withPivot('order')
                    ->orderByPivot('order');
    }

    /**
     * Get progress records for this activity.
     */
    public function childProgress()
    {
        return $this->hasMany(ChildActivityProgress::class);
    }

    /**
     * Scope for activities appropriate for a given age (in months).
     */
    public function scopeForAge($query, int $ageMonths)
    {
        return $query->where('age_min', '<=', $ageMonths)
                     ->where('age_max', '>=', $ageMonths);
    }

    /**
     * Scope for activities in a specific skill category.
     */
    public function scopeForSkill($query, string $skill)
    {
        return $query->where('skill', $skill);
    }

    /**
     * Scope for activities in a specific language.
     */
    public function scopeInLanguage($query, string $language)
    {
        return $query->where('language', $language);
    }

    /**
     * Scope for activities within a duration limit.
     */
    public function scopeMaxDuration($query, int $minutes)
    {
        return $query->where('duration', '<=', $minutes);
    }

    /**
     * Check if this activity is appropriate for a given age.
     */
    public function isAppropriateForAge(int $ageMonths): bool
    {
        return $this->age_min <= $ageMonths && $this->age_max >= $ageMonths;
    }
}
