<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ToyAchievement extends Model
{
    protected $fillable = [
        'user_id',
        'key',
        'title',
        'icon',
        'tier',
        'progress',
        'unlocked',
        'unlocked_at',
    ];

    protected $casts = [
        'unlocked'    => 'boolean',
        'unlocked_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
