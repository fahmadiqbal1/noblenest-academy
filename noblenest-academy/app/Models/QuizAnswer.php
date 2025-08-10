<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizAnswer extends Model
{
    use HasFactory;
    protected $fillable = [
        'quiz_attempt_id', 'question_id', 'option_id', 'answer_text',
    ];
    public function attempt() { return $this->belongsTo(QuizAttempt::class, 'quiz_attempt_id'); }
    public function question() { return $this->belongsTo(Question::class); }
    public function option() { return $this->belongsTo(Option::class); }
}

