<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentQuestion extends Model
{
    use HasFactory;

    protected $fillable = ['battery', 'sequence', 'age_min_months', 'age_max_months', 'prompt', 'options'];
    protected $casts    = ['options' => 'array', 'age_min_months' => 'integer', 'age_max_months' => 'integer'];
}
