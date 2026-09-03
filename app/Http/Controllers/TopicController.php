<?php

namespace App\Http\Controllers;

use App\Models\Topic;

class TopicController extends PageController
{
    public function index()
    {
        // PageController-ийн enriched topics() ашиглана
        // (user_can_access, questions_count, test_questions_count гэх мэт орно)
        $topics = $this->topics()
            ->groupBy('grade_level')
            ->sortKeys();

        return view('topics.index', [
            'topics'     => $topics,
            'hasPremium' => $this->userHasPremium(),
        ]);
    }

    public function show(string $slug)
    {
        $topic = $this->findTopicBySlug($slug);

        if (! $topic) {
            abort(404);
        }

        if (! $this->userCanAccessTopic($topic)) {
            return redirect()
                ->route('billing.index')
                ->with('error', 'Энэ сэдэв Premium эрх шаарддаг. Эрхээ идэвхжүүлээд бүх хичээл, шалгалтаа нээгээрэй.');
        }

        return view('topics.show', [
            'topic' => $topic,
        ]);
    }
}
