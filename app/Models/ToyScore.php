<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ToyScore extends Model
{
    protected $fillable = [
        'user_id',
        'mode',
        'difficulty',
        'score',
        'correct_answers',
        'wrong_answers',
        'max_combo',
        'accuracy',
        'rounds_played',
        'duration_seconds',
        'xp_earned',
        'coins_earned',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
