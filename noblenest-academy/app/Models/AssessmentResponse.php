<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssessmentResponse extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['child_id', 'user_id', 'battery', 'answers', 'scores', 'completed_at'];

    protected $casts = [
        'answers' => 'array',
        'scores' => 'array',
        'completed_at' => 'datetime',
    ];
}
