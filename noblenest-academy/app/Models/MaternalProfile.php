<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;

class MaternalProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'due_date',
        'delivery_date',
        'birth_count',
        'delivery_preference',
        'baby_feeding_method',
        'preferred_language',
        'is_muslim',
        'onboarding_completed',
        'status',
        'emergency_contact_name',
        'emergency_contact_phone',
        'notification_preferences',
        'consent_accepted_at',
    ];

    protected $casts = [
        'due_date'                 => 'date',
        'delivery_date'            => 'date',
        'birth_count'              => 'integer',
        'is_muslim'                => 'boolean',
        'onboarding_completed'     => 'boolean',
        'notification_preferences' => 'array',
        'consent_accepted_at'      => 'datetime',
    ];

    // ------------------------------------------------------------------
    // Encrypted field accessors (PHI protection)
    // ------------------------------------------------------------------

    public function getHealthConditionsAttribute(): ?array
    {
        if (! $this->health_conditions_encrypted) {
            return null;
        }

        return json_decode(Crypt::decryptString($this->health_conditions_encrypted), true);
    }

    public function setHealthConditionsAttribute(?array $value): void
    {
        $this->attributes['health_conditions_encrypted'] = $value
            ? Crypt::encryptString(json_encode($value))
            : null;
    }

    public function getDietaryRestrictionsAttribute(): ?array
    {
        if (! $this->dietary_restrictions_encrypted) {
            return null;
        }

        return json_decode(Crypt::decryptString($this->dietary_restrictions_encrypted), true);
    }

    public function setDietaryRestrictionsAttribute(?array $value): void
    {
        $this->attributes['dietary_restrictions_encrypted'] = $value
            ? Crypt::encryptString(json_encode($value))
            : null;
    }

    // ------------------------------------------------------------------
    // Computed attributes
    // ------------------------------------------------------------------

    public function getCurrentWeekAttribute(): int
    {
        if ($this->delivery_date) {
            return (int) $this->delivery_date->diffInWeeks(now());
        }

        $gestationStart = $this->due_date->subWeeks(40);

        return min(42, max(1, (int) $gestationStart->diffInWeeks(now())));
    }

    public function getTrimesterAttribute(): int
    {
        if ($this->delivery_date) {
            return 4; // postnatal
        }

        $week = $this->current_week;

        if ($week <= 13) return 1;
        if ($week <= 27) return 2;

        return 3;
    }

    public function getStageAttribute(): string
    {
        if ($this->delivery_date) {
            $weeksPostnatal = (int) $this->delivery_date->diffInWeeks(now());

            if ($weeksPostnatal <= 12) return 'postnatal_0_3m';
            if ($weeksPostnatal <= 24) return 'postnatal_3_6m';

            return 'postnatal_6_12m';
        }

        $week = $this->current_week;

        if ($week <= 13) return 'trimester_1';
        if ($week <= 27) return 'trimester_2';
        if ($week <= 35) return 'trimester_3';

        return 'labor_prep';
    }

    public function getWeeksRemainingAttribute(): ?int
    {
        if ($this->delivery_date) {
            return null;
        }

        return max(0, (int) now()->diffInWeeks($this->due_date, false));
    }

    public function getPostnatalWeekAttribute(): ?int
    {
        if (! $this->delivery_date) {
            return null;
        }

        return (int) $this->delivery_date->diffInWeeks(now());
    }

    // ------------------------------------------------------------------
    // Relationships
    // ------------------------------------------------------------------

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function progress(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(MaternalProgress::class);
    }

    public function journals(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(MaternalJournal::class);
    }

    // ------------------------------------------------------------------
    // Scopes
    // ------------------------------------------------------------------

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeForStage($query, string $stage)
    {
        // This scope is for filtering profiles by stage — used in admin
        return $query->where('status', 'active');
    }

    // ------------------------------------------------------------------
    // Status helpers
    // ------------------------------------------------------------------

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isPaused(): bool
    {
        return $this->status === 'paused';
    }

    public function isLoss(): bool
    {
        return $this->status === 'loss';
    }

    public function pause(): void
    {
        $this->update(['status' => 'paused']);
    }

    public function markLoss(): void
    {
        $this->update(['status' => 'loss']);
    }

    public function resume(): void
    {
        $this->update(['status' => 'active']);
    }

    public function markCompleted(): void
    {
        $this->update(['status' => 'completed']);
    }
}
