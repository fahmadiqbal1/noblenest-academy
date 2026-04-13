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

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at'   => 'datetime',
        'active'    => 'boolean',
    ];

    public function user() { return $this->belongsTo(User::class); }

    /**
     * Get the current drip week (1-4) based on weeks since subscription started.
     */
    public function currentWeek(): int
    {
        if (!$this->starts_at) {
            return 1;
        }

        $weeks = (int) ceil($this->starts_at->diffInDays(now()) / 7);

        return min(max($weeks, 1), 4);
    }

    /**
     * Maximum activity order unlocked by the current drip week.
     * Week 1 = orders 1-5, Week 2 = 1-10, Week 3 = 1-15, Week 4 = 1-20 (all)
     */
    public function maxActivityOrder(): int
    {
        return $this->currentWeek() * 5;
    }
}

