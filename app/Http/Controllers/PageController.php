<?php

namespace App\Http\Controllers;

use App\Data\MathProblemGenerator;
use App\Models\Question;
use App\Models\Topic;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

abstract class PageController extends Controller
{
    protected function topics(): Collection
    {
        $questions = $this->practiceQuestions();

        return collect($this->topicData())->map(function (array $topic) use ($questions) {
            $topic = $this->normalizeArrayText($topic);
            $topicQuestions = $questions->where('topic_id', $topic['id'])->values();

            $topic['questions'] = $topicQuestions;
            $topic['questions_count'] = $topicQuestions->count();

            // Topic object болгоод дамжуулна
            $topicObj = (object) $topic;
            $topic['test_questions_count'] = MathProblemGenerator::countForTopic($topicObj);

            $topic['youtube_embed_url'] = ! empty($topic['video_url'])
                ? $this->embedUrl($topic['video_url'])
                : null;

            // Database-ийн is_premium-ийг шууд ашиглана
            $topic['is_premium'] = (bool) ($topic['is_premium'] ?? false);
            $topic['user_can_access'] = $this->userCanAccessTopic($topicObj);

            return (object) $topic;
        });
    }

    protected function practiceQuestions(): Collection
    {
        return collect($this->questionsData())
            ->map(fn (array $question) => (object) $this->normalizeArrayText($question));
    }

    protected function testQuestionsForTopics(Collection $topicIds): Collection
    {
        $questions = Question::whereIn('topic_id', $topicIds->map(fn ($id) => (int) $id))
            ->get()
            ->map(fn (Question $question) => (object) $this->normalizeArrayText($question->toArray()));

        foreach ($topicIds as $topicId) {
            $topic = $this->findTopicById((int) $topicId);

            if ($topic) {
                $questions = $questions->merge(
                    collect(MathProblemGenerator::forTopic($topic))
                        ->map(fn (array $q) => (object) $this->normalizeArrayText($q))
                );
            }
        }

        return $questions;
    }

    protected function questions(): Collection
    {
        return $this->practiceQuestions();
    }

    protected function findTopicBySlug(string $slug): ?object
    {
        return $this->topics()->firstWhere('slug', $slug);
    }

    protected function findTopicById(int $id): ?object
    {
        return $this->topics()->firstWhere('id', $id);
    }

    protected function embedUrl(string $url): string
    {
        if (str_contains($url, 'youtube.com/watch?v=')) {
            return str_replace('watch?v=', 'embed/', $url);
        }

        if (str_contains($url, 'youtu.be/')) {
            $id = last(explode('/', parse_url($url, PHP_URL_PATH)));
            return 'https://www.youtube.com/embed/' . $id;
        }

        return $url;
    }

    protected function topicData(): array
    {
        return Topic::all()->toArray();
    }

    protected function questionsData(): array
    {
        return Question::all()->toArray();
    }

    /**
     * Хэрэглэгч Premium эрхтэй эсэхийг шалгана
     */
    protected function userHasPremium(): bool
    {
        $user = Auth::user();

        if (! $user || $user->billing_status !== 'active') {
            return false;
        }

        // billing_ends_at байхгүй эсвэл ирээдүйд байвал premium
        return ! $user->billing_ends_at || $user->billing_ends_at->isFuture();
    }

    /**
     * Тухайн сэдэвт хандах эрхтэй эсэхийг шалгана
     * Eloquent Model болон stdClass хоёуланг дэмжинэ
     */
    protected function userCanAccessTopic(object $topic): bool
    {
        $isPremium = (bool) ($topic->is_premium ?? false);

        // Free сэдэв бол хэн ч орно
        if (! $isPremium) {
            return true;
        }

        // Premium сэдэв бол зөвхөн premium хэрэглэгч орно
        return $this->userHasPremium();
    }

    protected function accessibleTopicIds(Collection $topicIds): Collection
    {
        return $this->topics()
            ->whereIn('id', $topicIds->map(fn ($id) => (int) $id))
            ->filter(fn ($topic) => $this->userCanAccessTopic($topic))
            ->pluck('id')
            ->values();
    }

    protected function normalizeArrayText(array $items): array
    {
        foreach ($items as $key => $value) {
            if (is_string($value)) {
                $items[$key] = $this->fixMojibake($value);
            } elseif (is_array($value)) {
                $items[$key] = $this->normalizeArrayText($value);
            }
        }

        return $items;
    }

    protected function fixMojibake(string $value): string
    {
        if (! preg_match('/[ÃÐÑÒÓÂâð]/u', $value)) {
            return $value;
        }

        $fixed = @mb_convert_encoding($value, 'Windows-1252', 'UTF-8');

        return is_string($fixed) && $fixed !== '' ? $fixed : $value;
    }
}
