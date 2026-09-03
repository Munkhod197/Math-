<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestController extends PageController
{
    public function start()
    {
        return view('tests.start', [
            'topics' => $this->topics(),
            'hasPremium' => $this->userHasPremium(),
        ]);
    }

    public function topicQuizStart(string $topic)
    {
        $topic = $this->findTopicBySlug($topic);

        if (! $topic) {
            abort(404);
        }

        if (! $this->userCanAccessTopic($topic)) {
            return redirect()
                ->route('billing.index')
                ->with('error', 'Энэ сэдвийн шалгалт Premium эрх шаарддаг.');
        }

        return view('tests.topic-start', [
            'topic' => $topic,
        ]);
    }

    public function take(Request $request)
    {
        $maxQuestions = $this->userHasPremium() ? 30 : 5;

        $request->validate([
            'student_name' => 'required|string|max:100',
            'question_count' => "required|integer|min:1|max:{$maxQuestions}",
            'grade_level' => 'required|integer|in:' . $this->topics()->pluck('grade_level')->unique()->implode(','),
        ]);

        $selectedGrade = (int) $request->input('grade_level');
        $selectedTopicIds = $this->topics()
            ->where('grade_level', $selectedGrade)
            ->pluck('id');

        $accessibleTopicIds = $this->accessibleTopicIds($selectedTopicIds);

        if ($accessibleTopicIds->isEmpty()) {
            return redirect()
                ->route('billing.index')
                ->with('error', 'Сонгосон ангийн тест Premium эрх шаарддаг. Эрхээ идэвхжүүлээд бүрэн тест нээгээрэй.');
        }

        return $this->renderTest(
            $request->input('student_name'),
            $accessibleTopicIds,
            min((int) $request->input('question_count', 10), $maxQuestions),
            null,
        );
    }

    public function topicTake(Request $request, string $topic)
    {
        $topic = $this->findTopicBySlug($topic);

        if (! $topic) {
            abort(404);
        }

        if (! $this->userCanAccessTopic($topic)) {
            return redirect()
                ->route('billing.index')
                ->with('error', 'Энэ сэдвийн шалгалт Premium эрх шаарддаг.');
        }

        $request->validate([
            'student_name' => 'required|string|max:100',
        ]);

        return $this->renderTest(
            $request->input('student_name'),
            collect([$topic->id]),
            1,
            $topic,
        );
    }

    public function submit(Request $request)
    {
        $testData = session('active_test');

        if (! $testData || empty($testData['questions'])) {
            return redirect()
                ->route('test.start')
                ->with('error', 'Тестийн хугацаа дууссан эсвэл session цэвэрлэгдсэн байна. Дахин эхлүүлнэ үү.');
        }

        $answers = $request->input('answers', []);
        $topicSlug = $testData['topic_slug'] ?? null;

        $questions = collect($testData['questions'])->map(function ($question) {
            $q = (object) $question;
            $q->topic = $this->findTopicById($q->topic_id);

            return $q;
        });

        $correct = $questions->filter(function ($question) use ($answers) {
            return isset($answers[$question->id]) && $answers[$question->id] === $question->correct_answer;
        });

        $score = $correct->count();
        $total = $questions->count();
        $scorePercent = $total > 0 ? (int) round(($score / $total) * 100) : 0;

        $topicModel = $topicSlug ? $this->findTopicBySlug($topicSlug) : null;
        $grade = $topicModel
            ? $topicModel->name.' - '.$topicModel->grade_level.'-р анги'
            : ($questions->pluck('topic.grade_level')->unique()->count() === 1
                ? $questions->first()->topic->grade_level.'-р анги'
                : 'Сонгосон олон анги');

        $wrongTopicIds = $questions->filter(function ($question) use ($answers) {
            return ! isset($answers[$question->id]) || $answers[$question->id] !== $question->correct_answer;
        })->pluck('topic_id')->unique();

        $weakTopics = $this->topics()->whereIn('id', $wrongTopicIds)->values();

        $answersDetail = $questions->map(function ($question) use ($answers) {
            $userAnswer = $answers[$question->id] ?? null;
            $isCorrect = $userAnswer === $question->correct_answer;
            $answerMap = [
                'A' => $question->option_a,
                'B' => $question->option_b,
                'C' => $question->option_c,
                'D' => $question->option_d,
            ];

            return [
                'topic' => $question->topic->name,
                'question_text' => $question->question_text,
                'image_path' => $question->image_path ?? null,
                'user_answer' => $userAnswer,
                'user_answer_text' => $userAnswer ? ($answerMap[$userAnswer] ?? null) : null,
                'correct_answer' => $question->correct_answer,
                'correct_answer_text' => $answerMap[$question->correct_answer] ?? null,
                'is_correct' => $isCorrect,
                'explanation' => $question->explanation,
            ];
        })->all();

        $result = (object) [
            'student_name' => trim($testData['student_name'] ?? 'Сурагч'),
            'correct_answers' => $score,
            'total_questions' => $total,
            'score_percent' => $scorePercent,
            'grade' => $grade,
        ];

        session()->forget('active_test');

        return view('tests.result', [
            'result' => $result,
            'weakTopics' => $weakTopics,
            'answersDetail' => $answersDetail,
            'topic' => $topicModel,
        ]);
    }

    private function renderTest(string $studentName, $selectedTopicIds, int $questionCount, ?object $topic)
    {
        $topics = $this->topics()->whereIn('id', $selectedTopicIds);
        $available = $this->testQuestionsForTopics($selectedTopicIds);

        $questions = $available
            ->shuffle()
            ->take(min($questionCount, $available->count()))
            ->values()
            ->map(function ($question) use ($topics) {
                $question->topic = $topics->firstWhere('id', $question->topic_id);

                return $question;
            });

        if ($questions->isEmpty()) {
            $redirect = $topic
                ? redirect()->route('test.topic.start', $topic->slug)
                : redirect()->route('test.start');

            return $redirect->with('error', 'Энэ сэдэвт бодлого одоогоор байхгүй байна.');
        }

        session([
            'active_test' => [
                'student_name' => trim($studentName),
                'topic_slug' => $topic?->slug,
                'questions' => $questions->map(fn ($q) => (array) $q)->all(),
            ],
        ]);

        return view('tests.take', [
            'questions' => $questions,
            'studentName' => trim($studentName),
            'topic' => $topic,
        ]);
    }
}
