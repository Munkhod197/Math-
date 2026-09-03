<?php

namespace App\Models;

use App\Data\MathProblemGenerator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'name',
        'icon',
        'description',
        'grade_level',
        'video_url',
        'video_file',
        'video_title',
        'is_premium',
    ];

    protected $casts = [
        'is_premium' => 'boolean',
        'grade_level' => 'integer',
    ];

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function getVideoFileUrlAttribute(): ?string
    {
        if (! $this->video_file) {
            return null;
        }

        return asset('storage/' . $this->video_file);
    }

    /**
     * YouTube embed URL
     */
    public function getYoutubeEmbedUrlAttribute(): ?string
    {
        if (! $this->video_url) {
            return null;
        }

        // youtube.com/watch?v=XXXX
        if (str_contains($this->video_url, 'youtube.com/watch?v=')) {
            return str_replace('watch?v=', 'embed/', $this->video_url);
        }

        // youtu.be/XXXX
        if (str_contains($this->video_url, 'youtu.be/')) {
            $id = last(explode('/', parse_url($this->video_url, PHP_URL_PATH)));
            return 'https://www.youtube.com/embed/' . $id;
        }

        // аль хэдийн embed хэлбэртэй бол шууд буцаана
        if (str_contains($this->video_url, 'youtube.com/embed/')) {
            return $this->video_url;
        }

        return null;
    }

    /**
     * Тухайн сэдэвт хамаарах тест бодлогын тоо
     * (MathProblemGenerator-т topic-ийг дамжуулна)
     */
    public function getTestQuestionsCountAttribute(): int
    {
        return MathProblemGenerator::countForTopic($this);
    }
}