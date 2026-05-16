<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Phase 3 — locale-specific translation of a single Activity field.
 *
 * One row per (activity_id, locale, field). The canonical English content
 * lives on the Activity row itself; this table only carries translations.
 */
class ActivityTranslation extends Model
{
    use HasFactory;

    protected $fillable = ['activity_id', 'locale', 'field', 'value'];

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }
}
