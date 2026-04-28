<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class MaternalContent extends Model
{
    use HasFactory;

    protected $table = 'maternal_contents';

    protected $fillable = [
        'title',
        'slug',
        'description',
        'content_type',
        'stage',
        'category',
        'cultural_origin',
        'benefit_explanation',
        'skills_improved',
        'health_benefit',
        'safety_notes',
        'contraindications',
        'difficulty',
        'duration_minutes',
        'ingredients_or_materials',
        'instructions',
        'thumbnail_url',
        'video_url',
        'audio_url',
        'subtitle_url',
        'is_free',
        'order',
        'is_published',
        'moderation_status',
        'medical_reviewer_name',
        'reviewed_by_credential',
        'language',
        'is_rtl',
        'emoji',
    ];

    protected $casts = [
        'skills_improved'          => 'array',
        'contraindications'        => 'array',
        'ingredients_or_materials' => 'array',
        'duration_minutes'         => 'integer',
        'is_free'                  => 'boolean',
        'order'                    => 'integer',
        'is_published'             => 'boolean',
        'is_rtl'                   => 'boolean',
    ];

    // ------------------------------------------------------------------
    // Boot
    // ------------------------------------------------------------------

    protected static function booted(): void
    {
        static::creating(function (self $content) {
            if (empty($content->slug)) {
                $content->slug = Str::slug($content->title);
            }
        });
    }

    // ------------------------------------------------------------------
    // Relationships
    // ------------------------------------------------------------------

    public function steps(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(MaternalContentStep::class)->orderBy('step_number');
    }

    public function progressRecords(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(MaternalProgress::class);
    }

    public function contraindicationMatrix(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ContraindicationMatrix::class);
    }

    public function reviews(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ContentReview::class, 'maternal_content_id');
    }

    public function approvedReviews(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->reviews()->where('decision', 'approved');
    }

    public function sideNotes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->reviews()->whereNotNull('side_notes')->where('side_notes', '!=', '');
    }

    // ------------------------------------------------------------------
    // Scopes
    // ------------------------------------------------------------------

    public function scopePublished($query)
    {
        return $query->where('is_published', true)
                     ->where('moderation_status', 'approved');
    }

    public function scopeForStage($query, string $stage)
    {
        return $query->where('stage', $stage);
    }

    public function scopeForCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeForCulture($query, string $culture)
    {
        return $query->where('cultural_origin', $culture);
    }

    public function scopeForContentType($query, string $type)
    {
        return $query->where('content_type', $type);
    }

    public function scopeInLanguage($query, string $language)
    {
        return $query->where('language', $language);
    }

    public function scopeFree($query)
    {
        return $query->where('is_free', true);
    }

    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('title', 'like', "%{$term}%")
              ->orWhere('description', 'like', "%{$term}%");
        });
    }

    // ------------------------------------------------------------------
    // Helpers
    // ------------------------------------------------------------------

    public function isApproved(): bool
    {
        return $this->moderation_status === 'approved';
    }

    public function isSafeFor(MaternalProfile $profile): bool
    {
        $conditions = $profile->health_conditions ?? [];

        if (empty($conditions) || empty($this->contraindications)) {
            return true;
        }

        return empty(array_intersect($conditions, $this->contraindications));
    }
}
