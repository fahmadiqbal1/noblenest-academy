<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class MaternalJournal extends Model
{
    protected $fillable = [
        'maternal_profile_id',
        'entry_date',
        'week_number',
        'mood',
        'energy_level',
        'notes',
        'baby_kicks_count',
    ];

    protected $casts = [
        'entry_date'       => 'date',
        'week_number'      => 'integer',
        'energy_level'     => 'integer',
        'baby_kicks_count' => 'integer',
    ];

    // ------------------------------------------------------------------
    // Encrypted field accessors (PHI protection)
    // ------------------------------------------------------------------

    public function getSymptomsAttribute(): ?array
    {
        if (! $this->symptoms_encrypted) {
            return null;
        }

        return json_decode(Crypt::decryptString($this->symptoms_encrypted), true);
    }

    public function setSymptomsAttribute(?array $value): void
    {
        $this->attributes['symptoms_encrypted'] = $value
            ? Crypt::encryptString(json_encode($value))
            : null;
    }

    // ------------------------------------------------------------------
    // Relationships
    // ------------------------------------------------------------------

    public function profile(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(MaternalProfile::class, 'maternal_profile_id');
    }

    // ------------------------------------------------------------------
    // Scopes
    // ------------------------------------------------------------------

    public function scopeForProfile($query, int $profileId)
    {
        return $query->where('maternal_profile_id', $profileId);
    }

    public function scopeForWeek($query, int $week)
    {
        return $query->where('week_number', $week);
    }

    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('entry_date', '>=', now()->subDays($days));
    }

    // ------------------------------------------------------------------
    // Symptom alert keywords
    // ------------------------------------------------------------------

    private const ALERT_SYMPTOMS = [
        'bleeding', 'severe headache', 'blurred vision', 'swelling',
        'reduced movement', 'no movement', 'sharp pain', 'contractions',
        'fluid leaking', 'fever', 'chest pain', 'difficulty breathing',
    ];

    public function hasAlertSymptoms(): bool
    {
        $symptoms = $this->symptoms ?? [];
        $notes    = strtolower($this->notes ?? '');

        foreach (self::ALERT_SYMPTOMS as $alert) {
            if (in_array($alert, array_map('strtolower', $symptoms), true)) {
                return true;
            }
            if (str_contains($notes, $alert)) {
                return true;
            }
        }

        return false;
    }
}
