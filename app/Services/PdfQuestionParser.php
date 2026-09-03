<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;

class PdfQuestionParser
{
    public function parse(UploadedFile $file): array
    {
        $text = $this->normalizeText($this->extractText($file->getRealPath()));

        if ($text === '') {
            return [];
        }

        return collect($this->splitIntoQuestionBlocks($text))
            ->map(fn (string $block) => $this->parseBlock($block))
            ->filter(fn (array $item) => $item['question_text'] !== '')
            ->values()
            ->all();
    }

    private function extractText(string $path): string
    {
        if (class_exists(\Smalot\PdfParser\Parser::class)) {
            try {
                return (new \Smalot\PdfParser\Parser())->parseFile($path)->getText();
            } catch (\Throwable) {
                // Fall through to pdftotext extraction.
            }
        }

        try {
            $result = Process::timeout(20)->run(['pdftotext', '-layout', $path, '-']);

            if ($result->successful()) {
                return $result->output();
            }
        } catch (\Throwable) {
            // pdftotext is optional in local environments.
        }

        return '';
    }

    private function normalizeText(string $text): string
    {
        if (function_exists('mb_check_encoding') && ! mb_check_encoding($text, 'UTF-8')) {
            $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8, Windows-1251, ISO-8859-1');
        }

        $text = str_replace(["\r\n", "\r"], "\n", $text);
        $text = preg_replace('/[ \t]+/', ' ', $text) ?? $text;
        $text = preg_replace('/\n{3,}/', "\n\n", $text) ?? $text;

        return trim($text);
    }

    private function splitIntoQuestionBlocks(string $text): array
    {
        $parts = preg_split('/(?=^\s*\d{1,4}[\.)]\s+)/m', $text, -1, PREG_SPLIT_NO_EMPTY) ?: [];

        if (count($parts) > 1) {
            return $parts;
        }

        return preg_split('/\n\s*\n/', $text, -1, PREG_SPLIT_NO_EMPTY) ?: [$text];
    }

    private function parseBlock(string $block): array
    {
        $block = trim(preg_replace('/^\s*\d{1,4}[\.)]\s*/', '', $block) ?? $block);
        $optionPattern = '/(?:^|\n)\s*([A-DА-Г])[\.)\:\-]\s*(.*?)(?=(?:\n\s*[A-DА-Г][\.)\:\-]\s*)|\n\s*(?:Explanation|Тайлбар|Тайлбарлалт|Бодолт|Зөв хариу|Answer)\b|$)/isu';

        preg_match_all($optionPattern, $block, $matches, PREG_SET_ORDER);

        $options = ['A' => '', 'B' => '', 'C' => '', 'D' => ''];
        foreach ($matches as $match) {
            $letter = $this->normalizeOptionLetter($match[1]);
            if (isset($options[$letter])) {
                $options[$letter] = trim($match[2]);
            }
        }

        $firstOptionPos = null;
        if (preg_match('/(?:^|\n)\s*[A-DА-Г][\.)\:\-]\s*/iu', $block, $first, PREG_OFFSET_CAPTURE)) {
            $firstOptionPos = $first[0][1];
        }

        $questionText = $firstOptionPos === null ? $block : substr($block, 0, $firstOptionPos);
        $questionText = trim($questionText);

        $explanation = null;
        if (preg_match('/(?:Explanation|Тайлбар|Тайлбарлалт|Бодолт)\s*[:\-]\s*(.+)$/isu', $block, $explanationMatch)) {
            $explanation = trim($explanationMatch[1]);
        }

        $correctAnswer = null;
        if (preg_match('/(?:Answer|Зөв хариу)\s*[:\-]\s*([A-DА-Г])/iu', $block, $answerMatch)) {
            $correctAnswer = $this->normalizeOptionLetter($answerMatch[1]);
        }

        return [
            'topic_id' => null,
            'question_text' => $questionText,
            'option_a' => $options['A'],
            'option_b' => $options['B'],
            'option_c' => $options['C'],
            'option_d' => $options['D'],
            'correct_answer' => $correctAnswer,
            'explanation' => $explanation,
        ];
    }

    private function normalizeOptionLetter(string $letter): string
    {
        return match (Str::upper($letter)) {
            'А' => 'A',
            'Б' => 'B',
            'В' => 'C',
            'Г' => 'D',
            default => Str::upper($letter),
        };
    }
}
