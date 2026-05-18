<?php

namespace App\Models;

use App\Services\ActivityRendererResolver;
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
        'subject',
        'language',
        'activity_type',
        'media_url',
        'is_rtl',
        'emoji',
        'is_free',
        'published',
        'like_count',
        'duration_minutes',
        'difficulty',
        'thumbnail_url',
        'audio_url',
        'video_url',
        'instructions',
        'materials_needed',
        'learning_objectives',
        'age_group',
        'is_muslim_only',
        'benefit_explanation',
        'skills_improved',
        'health_benefit',
        'learning_modalities',
        'primary_modality',
        'subtitle_url',
        'interactive_type',
        // Phase 2: Enhanced parental context fields
        'mess_level',
        'safety_warnings',
        'adaptations',
        'cognitive_domain',
        'developmental_domains',
        'materials_cost',
        'parent_involvement',
        'instructions_for_parent',
    ];

    protected $casts = [
        'age_min' => 'integer',
        'age_max' => 'integer',
        'duration_minutes' => 'integer',
        'is_rtl' => 'boolean',
        'is_free' => 'boolean',
        'published' => 'boolean',
        'like_count' => 'integer',
        'is_muslim_only' => 'boolean',
        'materials_needed' => 'array',
        'learning_objectives' => 'array',
        'skills_improved' => 'array',
        'learning_modalities' => 'array',
        // Phase 2: Enhanced parental context fields
        'safety_warnings' => 'array',
        'adaptations' => 'array',
        'developmental_domains' => 'array',
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

    /**
     * Get step-by-step instructions for this activity.
     */
    public function steps()
    {
        return $this->hasMany(ActivityStep::class)->orderBy('step_number');
    }

    /**
     * Get all media assets for this activity.
     */
    public function media()
    {
        return $this->hasMany(ActivityMedia::class)->orderBy('order');
    }

    /**
     * Phase 3: per-locale translations of translatable fields.
     */
    public function translations()
    {
        return $this->hasMany(ActivityTranslation::class);
    }

    /**
     * Helper: translated value of a field for a given locale, or the canonical
     * value on this row if no translation exists. Uses the relationship if it's
     * already eager-loaded; otherwise issues a single SELECT.
     */
    public function localized(string $field, ?string $locale = null): ?string
    {
        $locale ??= app()->getLocale();
        if ($locale === 'en') {
            return $this->{$field} ?? null;
        }
        if ($this->relationLoaded('translations')) {
            $row = $this->translations->firstWhere(fn ($t) => $t->locale === $locale && $t->field === $field);
        } else {
            $row = $this->translations()->where('locale', $locale)->where('field', $field)->first();
        }

        return $row ? $row->value : ($this->{$field} ?? null);
    }

    /**
     * Phase 2: resolve which canonical player renders this activity.
     * Memoised on the instance so repeated calls in a single view render are cheap.
     */
    public function renderer(): string
    {
        if (! isset($this->cachedRenderer)) {
            $this->cachedRenderer = app(ActivityRendererResolver::class)->resolve($this);
        }

        return $this->cachedRenderer;
    }

    /** @var string|null */
    protected $cachedRenderer = null;
}
