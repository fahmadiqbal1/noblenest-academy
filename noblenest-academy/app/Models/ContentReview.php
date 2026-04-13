<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContentReview extends Model
{
    protected $fillable = [
        'practitioner_profile_id',
        'maternal_content_id',
        'decision',
        'side_notes',
        'internal_notes',
        'credential_used',
        'credential_number',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    // ------------------------------------------------------------------
    // Relationships
    // ------------------------------------------------------------------

    public function practitioner(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(PractitionerProfile::class, 'practitioner_profile_id');
    }

    public function content(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(MaternalContent::class, 'maternal_content_id');
    }

    // ------------------------------------------------------------------
    // Scopes
    // ------------------------------------------------------------------

    public function scopeWithSideNotes($query)
    {
        return $query->whereNotNull('side_notes')->where('side_notes', '!=', '');
    }

    public function scopeApproved($query)
    {
        return $query->where('decision', 'approved');
    }

    public function scopeForContent($query, int $contentId)
    {
        return $query->where('maternal_content_id', $contentId);
    }
}
