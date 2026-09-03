<?php

namespace Database\Seeders;

use App\Data\MathCurriculum;
use App\Models\Question;
use App\Models\Topic;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurriculumSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('questions')->truncate();
        DB::table('topics')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $topics = MathCurriculum::topics();
        foreach ($topics as $topic) {
            Topic::create([
                'id' => $topic['id'],
                'slug' => $topic['slug'],
                'name' => $topic['name'],
                'icon' => $topic['icon'] ?? null,
                'description' => $topic['description'] ?? null,
                'grade_level' => $topic['grade_level'],
                'video_url' => $topic['video_url'] ?? null,
                'video_title' => $topic['video_title'] ?? null,
                'is_premium' => $topic['id'] > 6,
            ]);
        }

        foreach (MathCurriculum::questions() as $question) {
            Question::create($question);
        }
    }
}
