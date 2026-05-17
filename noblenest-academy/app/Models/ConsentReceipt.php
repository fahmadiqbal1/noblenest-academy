<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConsentReceipt extends Model
{
    /** @use HasFactory<\Database\Factories\ConsentReceiptFactory> */
    use HasFactory;

    protected $fillable = [
        'parent_user_id',
        'child_profile_id',
        'document_version',
        'ip',
        'user_agent',
        'signed_at',
        'withdrawn_at',
    ];

    protected function casts(): array
    {
        return [
            'signed_at' => 'datetime',
            'withdrawn_at' => 'datetime',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_user_id');
    }

    public function childProfile(): BelongsTo
    {
        return $this->belongsTo(ChildProfile::class);
    }

    public function isActive(): bool
    {
        return $this->withdrawn_at === null;
    }
}
