<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchoolInquiry extends Model
{
    protected $fillable = [
        'school_name',
        'contact_name',
        'contact_email',
        'contact_phone',
        'country',
        'student_count',
        'message',
        'status',
        'assigned_to',
        'admin_note',
    ];

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
