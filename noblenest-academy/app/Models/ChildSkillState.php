<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * ChildSkillState
 *
 * Rolling mastery projection: per child, per cognitive domain, per developmental domain.
 * Updated on every ActivityCompleted event.
 * Used by LearningPathService to:
 *   1. Determine if child has mastered a cognitive domain
 *   2. Detect struggle streaks → trigger EI beat recommendation
 *   3. Adapt difficulty of next activity
 */
class ChildSkillState extends Model
{
    use HasFactory;

    protected $fillable = [
        'child_profile_id',
        'cognitive_domain',
        'developmental_domain',
        'ema_score',
        'ema_confidence',
        'streak_success',
        'streak_struggle',
        'max_streak_struggle',
        'total_attempts',
        'successful_attempts',
        'last_success',
        'last_struggle',
        'last_updated',
    ];

    protected $casts = [
        'ema_score' => 'decimal:3',
        'ema_confidence' => 'decimal:3',
        'streak_success' => 'integer',
        'streak_struggle' => 'integer',
        'max_streak_struggle' => 'integer',
        'total_attempts' => 'integer',
        'successful_attempts' => 'integer',
        'last_success' => 'datetime',
        'last_struggle' => 'datetime',
        'last_updated' => 'datetime',
    ];

    // =========== Relationships ===========

    public function childProfile(): BelongsTo
    {
        return $this->belongsTo(ChildProfile::class, 'child_profile_id');
    }

    // =========== Scopes ===========

    public function scopeForChild($query, ChildProfile|int $child)
    {
        $childId = is_int($child) ? $child : $child->id;

        return $query->where('child_profile_id', $childId);
    }

    public function scopeForDomain($query, string $cognitiveDomain)
    {
        return $query->where('cognitive_domain', $cognitiveDomain);
    }

    public function scopeStruggling($query, float $threshold = 0.5)
    {
        return $query->where('ema_score', '<', $threshold);
    }

    public function scopeMastered($query, float $threshold = 0.8)
    {
        return $query->where('ema_score', '>=', $threshold);
    }

    public function scopeWithStreakStruggle($query)
    {
        return $query->where('streak_struggle', '>', 0);
    }

    // =========== Methods ===========

    /**
     * Update EMA score based on a new mastery result.
     * EMA = (0.3 * new_score) + (0.7 * old_ema_score)
     * Confidence increases with each update (asymptotically approaches 1.0).
     */
    public function updateEMAScore(float $newScore, float $alpha = 0.3): void
    {
        $oldEMA = $this->ema_score ?? 0.5;
        $newEMA = ($alpha * $newScore) + ((1 - $alpha) * $oldEMA);

        // Increase confidence with each update: confidence = 1 - (0.9 ^ attempts)
        $attempts = $this->total_attempts + 1;
        $newConfidence = 1 - (0.9 ** $attempts);

        $this->update([
            'ema_score' => max(0, min(1, $newEMA)), // Clamp to [0, 1]
            'ema_confidence' => max(0, min(1, $newConfidence)),
            'total_attempts' => $attempts,
            'successful_attempts' => $this->successful_attempts + ($newScore >= 0.8 ? 1 : 0),
            'last_updated' => now(),
        ]);
    }

    /**
     * Record a success: increment success streak, reset struggle streak.
     */
    public function recordSuccess(): void
    {
        $this->update([
            'streak_success' => $this->streak_success + 1,
            'streak_struggle' => 0,
            'last_success' => now(),
            'last_updated' => now(),
        ]);
    }

    /**
     * Record a struggle: increment struggle streak, reset success streak.
     */
    public function recordStruggle(): void
    {
        $newStreak = $this->streak_struggle + 1;
        $maxSoFar = max($this->max_streak_struggle, $newStreak);

        $this->update([
            'streak_success' => 0,
            'streak_struggle' => $newStreak,
            'max_streak_struggle' => $maxSoFar,
            'last_struggle' => now(),
            'last_updated' => now(),
        ]);
    }

    /**
     * Is this skill mastered? (EMA score >= 0.8)
     */
    public function isMastered(): bool
    {
        return $this->ema_score >= 0.8;
    }

    /**
     * Is this skill struggling? (EMA score < 0.5)
     */
    public function isStruggling(): bool
    {
        return $this->ema_score < 0.5;
    }

    /**
     * Does this skill have a struggle streak?
     */
    public function hasStruggleStreak(): bool
    {
        return $this->streak_struggle >= 2;
    }

    /**
     * Success rate: successful_attempts / total_attempts (0.0 - 1.0)
     */
    public function successRate(): float
    {
        if ($this->total_attempts === 0) {
            return 0.0;
        }

        return $this->successful_attempts / $this->total_attempts;
    }
}
