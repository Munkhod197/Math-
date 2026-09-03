<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ToyProfile extends Model
{
    protected $fillable = [
        'user_id',
        'level',
        'xp',
        'xp_next',
        'coins',
        'accuracy',
        'streak',
        'games_completed',
        'rank',
        'study_seconds',
        'last_played_at',
    ];

    protected $casts = [
        'last_played_at' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function addXp(int $amount): void
    {
        $this->xp += $amount;
        $this->games_completed += 1;

        while ($this->xp >= $this->xp_next) {
            $this->xp -= $this->xp_next;
            $this->level += 1;
            $this->xp_next = (int) round($this->xp_next * 1.35);
            $this->rank = self::rankForLevel($this->level);
        }

        $this->last_played_at = now();
        $this->save();
    }

    public function addCoins(int $amount): void
    {
        $this->coins += $amount;
        $this->save();
    }

    public static function rankForLevel(int $level): string
    {
        return match (true) {
            $level >= 30 => 'S',
            $level >= 25 => 'A1',
            $level >= 20 => 'A2',
            $level >= 15 => 'A3',
            $level >= 10 => 'B1',
            $level >= 5  => 'B2',
            default      => 'B3',
        };
    }

    public static function forUser(int $userId): self
    {
        return self::firstOrCreate(
            ['user_id' => $userId],
            [
                'level'           => 1,
                'xp'              => 0,
                'xp_next'         => 100,
                'coins'           => 50,
                'accuracy'        => 0,
                'streak'          => 0,
                'games_completed' => 0,
                'rank'            => 'B3',
            ]
        );
    }
}
