<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id', 'plan', 'provider', 'provider_id', 'amount', 'currency', 'starts_at', 'ends_at', 'active',
    ];
    public function user() { return $this->belongsTo(User::class); }
}

