<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * ChildProfile Model
 * 
 * Separated from User for COPPA compliance. Children do not have login credentials.
 * A Parent user manages one or more ChildProfile records.
 * 
 * @property int $id
 * @property int $parent_id
 * @property string $name
 * @property string|null $nickname
 * @property Carbon|null $date_of_birth
 * @property string|null $gender
 * @property string $preferred_language
 * @property string|null $avatar_url
 * @property array|null $preferences
 * @property array|null $learning_goals
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read User $parent
 * @property-read int $age_months
 * @property-read string $age_display
 */
class ChildProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'name',
        'nickname',
        'date_of_birth',
        'gender',
        'is_muslim',
        'preferred_language',
        'avatar_url',
        'preferences',
        'learning_goals',
        'age_tier',
        'streak_days',
        'last_activity_date',
    ];

    protected $casts = [
        'date_of_birth'   => 'date',
        'is_muslim'       => 'boolean',
        'preferences'     => 'array',
        'learning_goals'  => 'array',
    ];

    /**
     * The parent (User) who manages this child profile.
     */
    public function parent(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    /**
     * Get the child's age in months (critical for age-staged curricula 0-71 months).
     */
    public function getAgeMonthsAttribute(): ?int
    {
        if (!$this->date_of_birth) {
            return null;
        }
        return (int) $this->date_of_birth->diffInMonths(now());
    }

    /**
     * Get human-readable age display (e.g., "2 years 3 months" or "8 months").
     */
    public function getAgeDisplayAttribute(): ?string
    {
        if (!$this->date_of_birth) {
            return null;
        }

        $years = (int) $this->date_of_birth->diffInYears(now());
        $months = (int) $this->date_of_birth->copy()->addYears($years)->diffInMonths(now());

        if ($years > 0) {
            $display = $years . ' year' . ($years > 1 ? 's' : '');
            if ($months > 0) {
                $display .= ' ' . $months . ' month' . ($months > 1 ? 's' : '');
            }
            return $display;
        }

        return $months . ' month' . ($months !== 1 ? 's' : '');
    }

    /**
     * Get the age bracket for curriculum filtering.
     * Returns: 'infant' (0-12m), 'toddler' (13-36m), 'preschool' (37-60m), 'school' (61-120m)
     */
    public function getAgeBracketAttribute(): ?string
    {
        $months = $this->age_months;
        if ($months === null) {
            return null;
        }

        return match (true) {
            $months <= 12  => 'infant',      // 0-12 months
            $months <= 36  => 'toddler',     // 13-36 months (1-3 years)
            $months <= 60  => 'preschool',   // 37-60 months (3-5 years)
            $months <= 120 => 'school',      // 61-120 months (5-10 years)
            default        => 'school',
        };
    }

    /**
     * Get activities appropriate for this child's age.
     * Automatically gates Quran/Islamic-studies content to Muslim children only.
     */
    public function appropriateActivities()
    {
        $ageMonths = $this->age_months;
        if ($ageMonths === null) {
            return Activity::query();
        }

        $ageYears = $ageMonths / 12; // age_min/age_max stored in years

        $query = Activity::where('age_min', '<=', $ageYears)
                         ->where('age_max', '>=', $ageYears);

        // Hide Quran, Arabic & Islamic-studies activities unless child is in a Muslim household
        if (!$this->is_muslim) {
            $query->whereNotIn('subject', ['quran', 'islamic_studies', 'arabic']);
        }

        return $query;
    }

    /**
     * Activity progress for this child.
     */
    public function activityProgress(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ChildActivityProgress::class);
    }

    /**
     * Milestones this child has achieved or is working towards.
     */
    public function milestones(): BelongsToMany
    {
        return $this->belongsToMany(Milestone::class, 'child_milestone_progress')
            ->withPivot(['status', 'achieved_at', 'parent_note'])
            ->withTimestamps();
    }

    /**
     * Badges this child has earned.
     */
    public function badges(): BelongsToMany
    {
        return $this->belongsToMany(Badge::class, 'child_badges')
            ->withPivot(['awarded_at'])
            ->withTimestamps();
    }

    /**
     * Scope to filter children by parent.
     */
    public function scopeForParent($query, int $parentId)
    {
        return $query->where('parent_id', $parentId);
    }

    /**
     * Scope to filter by age bracket.
     */
    public function scopeAgeBracket($query, string $bracket)
    {
        $ranges = [
            'infant'    => [0, 12],
            'toddler'   => [13, 36],
            'preschool' => [37, 60],
            'school'    => [61, 120],
        ];

        if (!isset($ranges[$bracket])) {
            return $query;
        }

        [$min, $max] = $ranges[$bracket];
        $cutoffMin = now()->subMonths($max);
        $cutoffMax = now()->subMonths($min);

        return $query->whereBetween('date_of_birth', [$cutoffMin, $cutoffMax]);
    }
}
