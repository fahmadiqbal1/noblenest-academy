<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ThematicJourney extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'age_tier', 'emoji',
        'cover_color', 'total_weeks', 'is_published', 'sort_order',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'total_weeks' => 'integer',
        'sort_order' => 'integer',
    ];

    /** Age tiers supported */
    public const TIERS = ['baby', 'toddler', 'preschool', 'school'];

    public function weeklyThemes(): HasMany
    {
        return $this->hasMany(WeeklyTheme::class, 'journey_id')->orderBy('week_number');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(ChildJourneyEnrollment::class, 'journey_id');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeForTier($query, string $tier)
    {
        return $query->where('age_tier', $tier);
    }
}
