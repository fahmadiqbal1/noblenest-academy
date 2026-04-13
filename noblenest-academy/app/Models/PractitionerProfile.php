<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class PractitionerProfile extends Model
{
    protected $fillable = [
        'user_id',
        'license_number',
        'license_type',
        'credential_body',
        'specialization',
        'certificate_path',
        'bio',
        'years_experience',
        'verification_status',
        'suspended_reason',
        'verified_content_count',
    ];

    protected $casts = [
        'years_experience'       => 'integer',
        'verified_content_count' => 'integer',
    ];

    // ------------------------------------------------------------------
    // Encrypted license accessor / mutator
    // ------------------------------------------------------------------

    public function setLicenseNumberAttribute(string $value): void
    {
        $this->attributes['license_number'] = Crypt::encryptString($value);
    }

    public function getLicenseNumberAttribute(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        try {
            return Crypt::decryptString($value);
        } catch (\Throwable) {
            return '[encrypted]';
        }
    }

    // ------------------------------------------------------------------
    // Relationships
    // ------------------------------------------------------------------

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviews(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ContentReview::class);
    }

    // ------------------------------------------------------------------
    // Helpers
    // ------------------------------------------------------------------

    public function isSuspended(): bool
    {
        return $this->verification_status === 'suspended';
    }

    public function isActive(): bool
    {
        return $this->verification_status === 'active';
    }

    public function canReview(): bool
    {
        return $this->isActive() && $this->license_number && $this->license_type;
    }

    public function formattedLicenseType(): string
    {
        return ucwords(str_replace('_', ' ', $this->license_type));
    }
}
