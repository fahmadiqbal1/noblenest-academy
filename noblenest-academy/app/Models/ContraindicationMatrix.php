<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContraindicationMatrix extends Model
{
    protected $table = 'contraindication_matrix';

    protected $fillable = [
        'condition',
        'maternal_content_id',
        'reason',
    ];

    public function content(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(MaternalContent::class, 'maternal_content_id');
    }

    public function scopeForCondition($query, string $condition)
    {
        return $query->where('condition', $condition);
    }
}
