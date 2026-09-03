<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'topic_id',
        'question_text',
        'image_path',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'correct_answer',
        'explanation',
    ];

    public function getTextAttribute(): string
    {
        return $this->question_text;
    }

    public function getAAttribute(): string
    {
        return $this->option_a;
    }

    public function getBAttribute(): string
    {
        return $this->option_b;
    }

    public function getCAttribute(): string
    {
        return $this->option_c;
    }

    public function getDAttribute(): string
    {
        return $this->option_d;
    }

    public function getAnswerAttribute(): string
    {
        return $this->correct_answer;
    }

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }
}
