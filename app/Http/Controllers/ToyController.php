<?php

namespace App\Http\Controllers;

use App\Models\ToyProfile;
use App\Models\ToyScore;
use App\Models\ToyAchievement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ToyController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $profile = ToyProfile::forUser($user->id);
        $player = (object) [
            'name' => $user->name,
            'avatar' => strtoupper(mb_substr($user->name, 0, 1)),
            'level' => $profile->level,
            'xp' => $profile->xp,
            'xp_next' => $profile->xp_next,
            'coins' => $profile->coins,
            'accuracy' => $profile->accuracy,
            'streak' => $profile->streak,
            'rank' => $profile->rank,
            'games_completed' => $profile->games_completed,
        ];

        $xpPercent = $player->xp_next > 0
            ? min(100, (int) (($player->xp / $player->xp_next) * 100))
            : 0;

        $featuredGame = [
            'title' => 'Тэгшитгэлийн тулаан',
            'subtitle' => 'Тэгшитгэлийг хурдан бодож, combo үүсгэн оноогоо өсгөөрэй.',
            'difficulty' => '3 амьтай',
            'reward' => '+XP',
            'coins' => '+Coins',
            'duration' => '10 үе',
            'route' => route('toys.algebra'),
        ];

        $categories = [
            ['category' => 'algebra', 'title' => 'Алгебр', 'icon' => 'x', 'difficulty' => 'Тэгшитгэл', 'route' => route('toys.algebra'), 'accent' => 'bg-blue-600', 'badge' => 'Сонголт'],
            ['category' => 'geometry', 'title' => 'Геометр', 'icon' => '△', 'difficulty' => 'Дүрс ба томьёо', 'route' => route('toys.geometry'), 'accent' => 'bg-rose-600', 'badge' => null],
            ['category' => 'logic', 'title' => 'Хэв маяг', 'icon' => '◌', 'difficulty' => 'Логик дараалал', 'route' => route('toys.patterns'), 'accent' => 'bg-violet-600', 'badge' => 'Шинэ'],
            ['category' => 'speed', 'title' => 'Хурдан бодолт', 'icon' => '60', 'difficulty' => '60 секунд', 'route' => route('toys.speedrun'), 'accent' => 'bg-amber-500', 'badge' => 'Sprint'],
        ];

        $leaderboard = ToyScore::query()
            ->select('user_id', DB::raw('MAX(score) as score'))
            ->with('user:id,name')
            ->groupBy('user_id')
            ->orderByDesc('score')
            ->limit(5)
            ->get();

        $achievements = $this->syncAchievements($user->id, $profile);
        $recentScores = ToyScore::query()->where('user_id', $user->id)->latest()->limit(5)->get();

        return view('toys.toys', compact('player', 'xpPercent', 'featuredGame', 'categories', 'leaderboard', 'achievements', 'recentScores'));
    }

    public function speedrun(): View
    {
        return view('toys.speedrun');
    }

    public function patterns(): View
    {
        return view('toys.patterns');
    }

    public function algebra(): View
    {
        return view('toys.algebra');
    }

    public function geometry(): View
    {
        return view('toys.geometry');
    }

    public function storeScore(Request $request): JsonResponse
    {
        $data = $request->validate([
            'mode' => ['required', 'string', 'max:50'],
            'difficulty' => ['required', 'in:easy,normal,hard'],
            'score' => ['required', 'integer', 'min:0', 'max:10000'],
            'correct_answers' => ['nullable', 'integer', 'min:0', 'max:100'],
            'wrong_answers' => ['nullable', 'integer', 'min:0', 'max:100'],
            'max_combo' => ['required', 'integer', 'min:0', 'max:100'],
            'accuracy' => ['required', 'integer', 'min:0', 'max:100'],
            'rounds_played' => ['required', 'integer', 'min:1', 'max:100'],
            'duration_seconds' => ['nullable', 'integer', 'min:0', 'max:3600'],
        ]);

        $user = $request->user();
        $correctAnswers = $data['correct_answers'] ?? (int) round($data['rounds_played'] * $data['accuracy'] / 100);
        $wrongAnswers = $data['wrong_answers'] ?? max(0, $data['rounds_played'] - $correctAnswers);
        $xpEarned = max(5, min(250, intdiv($data['score'], 2)));
        $coinsEarned = max(1, min(100, intdiv($data['score'], 8)));

        ToyScore::create([
            'user_id' => $user->id,
            'mode' => $data['mode'],
            'difficulty' => $data['difficulty'],
            'score' => $data['score'],
            'correct_answers' => $correctAnswers,
            'wrong_answers' => $wrongAnswers,
            'max_combo' => $data['max_combo'],
            'accuracy' => $data['accuracy'],
            'rounds_played' => $data['rounds_played'],
            'duration_seconds' => $data['duration_seconds'] ?? 0,
            'xp_earned' => $xpEarned,
            'coins_earned' => $coinsEarned,
        ]);

        $profile = ToyProfile::forUser($user->id);
        $lastPlayed = $profile->last_played_at;
        if (!$lastPlayed || !$lastPlayed->isToday()) {
            $profile->streak = $lastPlayed && $lastPlayed->isYesterday() ? $profile->streak + 1 : 1;
        }
        $profile->addXp($xpEarned);
        $profile->addCoins($coinsEarned);
        $profile->update(['accuracy' => $data['accuracy']]);
        $unlocked = $this->syncAchievements($user->id, $profile->fresh());

        return response()->json([
            'ok' => true,
            'earned' => ['xp' => $xpEarned, 'coins' => $coinsEarned],
            'level' => $profile->fresh()->level,
            'achievements' => $unlocked->where('unlocked', true)->pluck('title')->values(),
        ]);
    }

    private function syncAchievements(int $userId, ToyProfile $profile)
    {
        $bestScore = (int) ToyScore::where('user_id', $userId)->max('score');
        $bestCombo = (int) ToyScore::where('user_id', $userId)->max('max_combo');

        $definitions = [
            'first_win' => ['Анхны ялалт', 'First game completed', 1, $profile->games_completed >= 1],
            'score_100' => ['100 оноо', '100+ score achieved', min(100, $bestScore), $bestScore >= 100],
            'on_fire' => ['On Fire', '10 correct answers in a row', min(100, $bestCombo * 10), $bestCombo >= 10],
            'game_master' => ['Math Master', '100 games played', min(100, $profile->games_completed), $profile->games_completed >= 100],
            'week_streak' => ['7 өдрийн цуврал', 'Play 7 days in a row', min(100, $profile->streak * 14), $profile->streak >= 7],
        ];

        foreach ($definitions as $key => [$title, $description, $progress, $isUnlocked]) {
            $achievement = ToyAchievement::firstOrNew(['user_id' => $userId, 'key' => $key]);
            $achievement->fill([
                'title' => $title,
                'icon' => null,
                'tier' => $isUnlocked ? 'Gold' : 'Bronze',
                'progress' => $progress,
                'unlocked' => $isUnlocked,
            ]);
            if ($isUnlocked && !$achievement->unlocked_at) {
                $achievement->unlocked_at = now();
            }
            $achievement->save();
        }

        return ToyAchievement::where('user_id', $userId)->orderByDesc('unlocked')->get();
    }
}
