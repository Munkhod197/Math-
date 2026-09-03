<?php

namespace App\Data;

class MathProblemGenerator
{
    /** Сэдэв бүрт 12 хэцүү бодлого үүсгэнэ */
    public static function forTopic(object $topic, int $count = 12): array
    {
        $problems = [];
        $generators = self::generatorsForTopic($topic);

        for ($i = 0; $i < $count; $i++) {
            $gen = $generators[$i % count($generators)];
            $problems[] = $gen($topic->id, $i);
        }

        return $problems;
    }

    private static function generatorsForTopic(object $topic): array
    {
        $slug = $topic->slug ?? '';

        if (str_contains($slug, 'geometr') || str_contains($slug, 'geometriin') || str_contains($slug, 'analitik-geometr') || str_contains($slug, 'pifagor')) {
            return self::geometryGeneratorsForGrade($topic->grade_level);
        }

        if (str_contains($slug, 'hemjigdehuun')) {
            return self::measurementGenerators();
        }

        if (str_contains($slug, 'ogogdol') || str_contains($slug, 'medeelel')) {
            return self::dataGenerators();
        }

        return self::generatorsForGrade($topic->grade_level);
    }

    private static function geometryGeneratorsForGrade(int $grade): array
    {
        if ($grade <= 6) {
            return [
                fn ($tid, $i) => self::perimeterArea($tid, $i, false),
                fn ($tid, $i) => self::perimeterArea($tid, $i, true),
                fn ($tid, $i) => self::triangleArea($tid, $i),
            ];
        }

        if ($grade <= 8) {
            return [
                fn ($tid, $i) => self::triangleArea($tid, $i),
                fn ($tid, $i) => self::perimeterArea($tid, $i, false),
                fn ($tid, $i) => self::perimeterArea($tid, $i, true),
                fn ($tid, $i) => self::pythagorean($tid, $i),
            ];
        }

        if ($grade <= 10) {
            return [
                fn ($tid, $i) => self::pythagorean($tid, $i),
                fn ($tid, $i) => self::triangleArea($tid, $i),
                fn ($tid, $i) => self::vectorBasic($tid, $i),
                fn ($tid, $i) => self::perimeterArea($tid, $i, false),
            ];
        }

        return self::generatorsForGrade($grade);
    }

    /** Хэмжигдэхүүн: хугацаа, мөнгө, урт/жин/эзлэхүүний хувиргалт */
    private static function measurementGenerators(): array
    {
        return [
            fn ($tid, $i) => self::timeConversion($tid, $i),
            fn ($tid, $i) => self::moneyProblem($tid, $i),
            fn ($tid, $i) => self::lengthWeightConversion($tid, $i),
        ];
    }

    /** Өгөгдөлтэй ажиллах: Карроллын диаграмм, зурган диаграмм */
    private static function dataGenerators(): array
    {
        return [
            fn ($tid, $i) => self::carrollGrouping($tid, $i),
            fn ($tid, $i) => self::pictogramCount($tid, $i),
        ];
    }

    public static function countForTopic(): int
    {
        return 12;
    }

    private static function generatorsForGrade(int $grade): array
    {
        if ($grade <= 2) {
            return [
                fn ($tid, $i) => self::wordAddSub($tid, $i, 20, 50),
                fn ($tid, $i) => self::multiStepAdd($tid, $i, 10, 30),
                fn ($tid, $i) => self::compareExpression($tid, $i, 100),
                fn ($tid, $i) => self::missingNumber($tid, $i, 20),
            ];
        }
        if ($grade <= 4) {
            return [
                fn ($tid, $i) => self::multiplyDivide($tid, $i, 12),
                fn ($tid, $i) => self::orderOfOps($tid, $i, 50),
                fn ($tid, $i) => self::fractionCompute($tid, $i),
                fn ($tid, $i) => self::perimeterArea($tid, $i, false),
                fn ($tid, $i) => self::decimalCompute($tid, $i),
            ];
        }
        if ($grade <= 6) {
            return [
                fn ($tid, $i) => self::linearEquation($tid, $i, 1),
                fn ($tid, $i) => self::percentProblem($tid, $i),
                fn ($tid, $i) => self::ratioProblem($tid, $i),
                fn ($tid, $i) => self::negativeOps($tid, $i),
                fn ($tid, $i) => self::perimeterArea($tid, $i, true),
            ];
        }
        if ($grade <= 8) {
            return [
                fn ($tid, $i) => self::linearEquation($tid, $i, 2),
                fn ($tid, $i) => self::systemEasy($tid, $i),
                fn ($tid, $i) => self::pythagorean($tid, $i),
                fn ($tid, $i) => self::functionEval($tid, $i),
                fn ($tid, $i) => self::percentHard($tid, $i),
            ];
        }
        if ($grade <= 10) {
            return [
                fn ($tid, $i) => self::quadratic($tid, $i),
                fn ($tid, $i) => self::quadraticSolve($tid, $i),
                fn ($tid, $i) => self::trigBasic($tid, $i),
                fn ($tid, $i) => self::sequence($tid, $i),
                fn ($tid, $i) => self::logBasic($tid, $i),
            ];
        }

        return [
            fn ($tid, $i) => self::limitBasic($tid, $i),
            fn ($tid, $i) => self::derivative($tid, $i),
            fn ($tid, $i) => self::integralBasic($tid, $i),
            fn ($tid, $i) => self::complexBasic($tid, $i),
            fn ($tid, $i) => self::vectorBasic($tid, $i),
        ];
    }

    private static function seed(int $topicId, int $index): int
    {
        return ($topicId * 997 + $index * 131) % 10000;
    }

    private static function make(int $topicId, int $index, string $text, string $answer, string $explanation, ?array $wrong = null): array
    {
        $num = (float) $answer;
        $wrong = $wrong ?? self::distractors($num, 4);
        $options = self::shuffleOptions($answer, $wrong);

        return [
            'id' => $topicId * 10000 + $index + 1,
            'topic_id' => $topicId,
            'question_text' => $text,
            'image_path' => null,
            'option_a' => $options['A'],
            'option_b' => $options['B'],
            'option_c' => $options['C'],
            'option_d' => $options['D'],
            'correct_answer' => $options['correct'],
            'explanation' => $explanation,
        ];
    }

    private static function distractors(float $correct, int $count): array
    {
        $offsets = [1, 2, 3, 5, 7, -1, -2, -3];
        $d = [];
        foreach ($offsets as $o) {
            $v = $correct + $o;
            if ($v != $correct && ! in_array((string) $v, $d, true)) {
                $d[] = self::fmt($v);
            }
            if (count($d) >= $count) {
                break;
            }
        }

        return $d;
    }

    private static function shuffleOptions(string $correct, array $wrong): array
    {
        $correct = self::fmt($correct);
        $pool = array_slice(array_unique(array_merge([$correct], $wrong)), 0, 4);
        while (count($pool) < 4) {
            $pool[] = self::fmt((float) $correct + count($pool) + 3);
        }
        shuffle($pool);
        $letters = ['A', 'B', 'C', 'D'];
        $result = [];
        $correctLetter = 'A';
        foreach ($letters as $i => $letter) {
            $result[$letter] = $pool[$i];
            if ($pool[$i] === $correct) {
                $correctLetter = $letter;
            }
        }
        $result['correct'] = $correctLetter;

        return $result;
    }

    private static function fmt(float $n): string
    {
        if (abs($n - round($n)) < 0.0001) {
            return (string) (int) round($n);
        }

        return rtrim(rtrim(number_format($n, 2, '.', ''), '0'), '.');
    }

    private static function pick(int $seed, int $min, int $max): int
    {
        return $min + ($seed % ($max - $min + 1));
    }

    private static function wordAddSub(int $tid, int $i, int $min, int $max): array
    {
        $s = self::seed($tid, $i);
        $a = self::pick($s, $min, $max);
        $b = self::pick($s >> 2, 5, 30);
        $c = self::pick($s >> 4, 3, 20);
        $ans = $a + $b - $c;

        return self::make(
            $tid, $i,
            "Болд {$a} ном уншсан, дараа нь {$b} ном нэмж уншсан. {$c} номыг буцаасан. Нийт хэдэн ном уншсан бэ?",
            (string) $ans,
            "{$a} + {$b} - {$c} = {$ans}."
        );
    }

    private static function multiStepAdd(int $tid, int $i, int $min, int $max): array
    {
        $s = self::seed($tid, $i);
        $a = self::pick($s, $min, $max);
        $b = self::pick($s >> 3, 2, 15);
        $c = self::pick($s >> 5, 2, 15);
        $ans = $a + $b + $c;

        return self::make($tid, $i, "{$a} + {$b} + {$c} = ?", (string) $ans, "Нийлбэр = {$ans}.");
    }

    private static function compareExpression(int $tid, int $i, int $max): array
    {
        $s = self::seed($tid, $i);
        $a = self::pick($s, 10, $max);
        $b = self::pick($s >> 2, 5, 40);
        $c = self::pick($s >> 4, 2, 20);
        $left = $a + $b;
        $right = $a + $c;
        $ans = $left - $right;

        return self::make(
            $tid, $i,
            "({$a} + {$b}) - ({$a} + {$c}) = ?",
            (string) $ans,
            "({$left}) - ({$right}) = {$ans}."
        );
    }

    private static function missingNumber(int $tid, int $i, int $max): array
    {
        $s = self::seed($tid, $i);
        $x = self::pick($s, 2, $max);
        $b = self::pick($s >> 3, 3, 15);
        $sum = $x + $b;

        return self::make(
            $tid, $i,
            "□ + {$b} = {$sum}. □ = ?",
            (string) $x,
            "{$sum} - {$b} = {$x}."
        );
    }

    private static function multiplyDivide(int $tid, int $i, int $max): array
    {
        $s = self::seed($tid, $i);
        $a = self::pick($s, 2, $max);
        $b = self::pick($s >> 2, 2, 12);
        $c = self::pick($s >> 4, 2, 6);
        $ans = $a * $b + $c;

        $product = $a * $b;

        return self::make(
            $tid, $i,
            "{$a} × {$b} + {$c} = ?",
            (string) $ans,
            "{$a}×{$b}={$product}, +{$c}={$ans}."
        );
    }

    private static function orderOfOps(int $tid, int $i, int $max): array
    {
        $s = self::seed($tid, $i);
        $a = self::pick($s, 2, 9);
        $b = self::pick($s >> 2, 2, 9);
        $c = self::pick($s >> 4, 1, 9);
        $ans = $a + $b * $c;

        $product = $b * $c;

        return self::make(
            $tid, $i,
            "{$a} + {$b} × {$c} = ?",
            (string) $ans,
            "Эхлээд {$b}×{$c}={$product}, дараа +{$a}={$ans}."
        );
    }

    private static function fractionCompute(int $tid, int $i): array
    {
        $s = self::seed($tid, $i);
        $d = self::pick($s, 2, 8);
        $n1 = self::pick($s >> 2, 1, $d - 1);
        $n2 = self::pick($s >> 4, 1, $d - 1);
        $ans = ($n1 + $n2) / $d;
        $text = "{$n1}/{$d} + {$n2}/{$d} = ?";

        return self::make($tid, $i, $text, self::fmt($ans), "({$n1}+{$n2})/{$d} = ".self::fmt($ans).'.');
    }

    private static function decimalCompute(int $tid, int $i): array
    {
        $s = self::seed($tid, $i);
        $a = self::pick($s, 1, 9) / 10;
        $b = self::pick($s >> 2, 1, 9) / 10;
        $ans = $a + $b;

        return self::make(
            $tid, $i,
            self::fmt($a).' + '.self::fmt($b).' = ?',
            self::fmt($ans),
            self::fmt($a).'+'.self::fmt($b).'='.self::fmt($ans).'.'
        );
    }

    private static function perimeterArea(int $tid, int $i, bool $area): array
    {
        $s = self::seed($tid, $i);
        $a = self::pick($s, 3, 15);
        $b = self::pick($s >> 2, 3, 12);
        if ($area) {
            $ans = $a * $b;

            return self::make(
                $tid, $i,
                "Тэгш өнцөгтийн урт {$a} см, өргөн {$b} см. Талбай хэд вэ?",
                (string) $ans,
                "Талбай = {$a}×{$b} = {$ans} см²."
            );
        }
        $ans = 2 * ($a + $b);

        return self::make(
            $tid, $i,
            "Тэгш өнцөгт {$a}×{$b} см. Периметр хэд вэ?",
            (string) $ans,
            "P = 2({$a}+{$b}) = {$ans} см."
        );
    }

    private static function triangleArea(int $tid, int $i): array
    {
        $s = self::seed($tid, $i);
        $base = self::pick($s, 3, 12);
        $height = self::pick($s >> 2, 2, 10);
        $ans = ($base * $height) / 2;

        return self::make(
            $tid, $i,
            "Гурвалжны суурь {$base} см, өндөр {$height} см. Талбай хэд вэ?",
            self::fmt($ans),
            "Талбай = {$base}×{$height}/2 = " . self::fmt($ans) . " см²."
        );
    }

    private static function linearEquation(int $tid, int $i, int $level): array
    {
        $s = self::seed($tid, $i);
        $x = self::pick($s, 2, 15);
        if ($level === 1) {
            $a = self::pick($s >> 2, 2, 9);
            $b = self::pick($s >> 4, 1, 20);
            $c = $a * $x + $b;

            return self::make(
                $tid, $i,
                "{$a}x + {$b} = {$c}. x = ?",
                (string) $x,
                "{$a}x = ".($c - $b).", x = {$x}."
            );
        }
        $a = self::pick($s >> 2, 2, 7);
        $b = self::pick($s >> 4, 1, 10);
        $c = $a * $x - $b;

        return self::make(
            $tid, $i,
            "{$a}x - {$b} = {$c}. x = ?",
            (string) $x,
            "{$a}x = ".($c + $b).", x = {$x}."
        );
    }

    private static function percentProblem(int $tid, int $i): array
    {
        $s = self::seed($tid, $i);
        $base = self::pick($s, 20, 200) * 5;
        $pct = [10, 20, 25, 50][self::pick($s >> 2, 0, 3)];
        $ans = $base * $pct / 100;

        return self::make(
            $tid, $i,
            "{$base}-ийн {$pct}% хэд вэ?",
            (string) $ans,
            "{$base}×{$pct}/100 = {$ans}."
        );
    }

    private static function percentHard(int $tid, int $i): array
    {
        $s = self::seed($tid, $i);
        $original = self::pick($s, 10, 50) * 10;
        $pct = [15, 20, 30][self::pick($s >> 2, 0, 2)];
        $ans = (int) round($original * (100 + $pct) / 100);

        return self::make(
            $tid, $i,
            "Үнэ {$original}₮, {$pct}%-аар өссөн шинэ үнэ хэд вэ?",
            (string) $ans,
            "{$original}×1.{$pct} = {$ans}₮."
        );
    }

    private static function ratioProblem(int $tid, int $i): array
    {
        $s = self::seed($tid, $i);
        $a = self::pick($s, 2, 6);
        $b = self::pick($s >> 2, 2, 8);
        $k = self::pick($s >> 4, 2, 10);
        $part = $a * $k;
        $total = ($a + $b) * $k;
        $other = $b * $k;

        return self::make(
            $tid, $i,
            "{$a}:{$b} харьцаатай. {$a} хэсэг = {$part} бол {$b} хэсэг хэд вэ?",
            (string) $other,
            "1 хэсэг={$k}, {$b}×{$k}={$other}."
        );
    }

    private static function negativeOps(int $tid, int $i): array
    {
        $s = self::seed($tid, $i);
        $a = -self::pick($s, 1, 15);
        $b = self::pick($s >> 2, 1, 20);
        $c = self::pick($s >> 4, 1, 10);
        $ans = $a + $b - $c;

        return self::make(
            $tid, $i,
            "({$a}) + {$b} - {$c} = ?",
            (string) $ans,
            "{$a}+{$b}-{$c}={$ans}."
        );
    }

    private static function systemEasy(int $tid, int $i): array
    {
        $s = self::seed($tid, $i);
        $x = self::pick($s, 2, 9);
        $y = self::pick($s >> 2, 2, 9);
        $s1 = $x + $y;
        $s2 = $x - $y;

        $sum = $s1 + $s2;

        return self::make(
            $tid, $i,
            "x + y = {$s1}, x - y = {$s2}. x = ?",
            (string) $x,
            "Нэмбэл 2x={$sum}, x={$x}."
        );
    }

    private static function pythagorean(int $tid, int $i): array
    {
        $triples = [[3, 4, 5], [5, 12, 13], [8, 15, 17], [6, 8, 10], [9, 12, 15]];
        $s = self::seed($tid, $i);
        [$a, $b, $c] = $triples[self::pick($s, 0, count($triples) - 1)];

        return self::make(
            $tid, $i,
            "Шулууны гурвалжин: хоёр катет {$a} см, {$b} см. Гипотенуз хэд вэ?",
            (string) $c,
            "{$a}²+{$b}²={$c}², c={$c} см."
        );
    }

    private static function functionEval(int $tid, int $i): array
    {
        $s = self::seed($tid, $i);
        $k = self::pick($s, 2, 6);
        $b = self::pick($s >> 2, -5, 10);
        $x = self::pick($s >> 4, 1, 8);
        $ans = $k * $x + $b;

        return self::make(
            $tid, $i,
            "f(x) = {$k}x + ({$b}), f({$x}) = ?",
            (string) $ans,
            "f({$x}) = {$k}×{$x}+({$b}) = {$ans}."
        );
    }

    private static function quadratic(int $tid, int $i): array
    {
        $s = self::seed($tid, $i);
        $x = self::pick($s, 2, 8);
        $ans = $x * $x;

        return self::make(
            $tid, $i,
            "x = {$x} бол x² = ?",
            (string) $ans,
            "{$x}² = {$ans}."
        );
    }

    private static function quadraticSolve(int $tid, int $i): array
    {
        $s = self::seed($tid, $i);
        $r1 = self::pick($s, 2, 8);
        $r2 = self::pick($s >> 2, 2, 8);
        $b = -($r1 + $r2);
        $c = $r1 * $r2;
        $bStr = $b >= 0 ? "+ {$b}" : "- ".abs($b);
        $cStr = $c >= 0 ? "+ {$c}" : "- ".abs($c);

        return self::make(
            $tid, $i,
            "x² {$bStr}x {$cStr} = 0. x-ийн нэг шийд (эерэг) = ?",
            (string) max($r1, $r2),
            "(x-{$r1})(x-{$r2})=0."
        );
    }

    private static function trigBasic(int $tid, int $i): array
    {
        $problems = [
            ['sin 30° = ?', '0.5', '1/2 = 0.5'],
            ['cos 60° = ?', '0.5', 'cos 60° = 0.5'],
            ['tan 45° = ?', '1', 'tan 45° = 1'],
            ['sin 90° = ?', '1', 'sin 90° = 1'],
        ];
        $s = self::seed($tid, $i);
        $p = $problems[self::pick($s, 0, count($problems) - 1)];

        return self::make($tid, $i, $p[0], $p[1], $p[2], ['0', '0.25', '2', '√3/2']);
    }

    private static function sequence(int $tid, int $i): array
    {
        $s = self::seed($tid, $i);
        $a1 = self::pick($s, 2, 10);
        $d = self::pick($s >> 2, 2, 7);
        $n = self::pick($s >> 4, 5, 8);
        $ans = $a1 + ($n - 1) * $d;

        return self::make(
            $tid, $i,
            "Арифметик дараалал: a₁={$a1}, d={$d}. a{$n} = ?",
            (string) $ans,
            "a{$n} = {$a1}+(".($n - 1)."×{$d}) = {$ans}."
        );
    }

    private static function logBasic(int $tid, int $i): array
    {
        $s = self::seed($tid, $i);
        $exp = self::pick($s, 2, 5);
        $base = 10;
        $num = pow($base, $exp);

        return self::make(
            $tid, $i,
            "log₁₀({$num}) = ?",
            (string) $exp,
            "10^{$exp}={$num}."
        );
    }

    private static function limitBasic(int $tid, int $i): array
    {
        $s = self::seed($tid, $i);
        $a = self::pick($s, 2, 9);

        $ans = 2 * $a;

        return self::make(
            $tid, $i,
            "lim(x→{$a}) (x² - {$a}²) / (x - {$a}) = ?",
            (string) $ans,
            "L'Hospital эсвэл хүчин зүйлжүүлбэл 2×{$a}={$ans}."
        );
    }

    private static function derivative(int $tid, int $i): array
    {
        $s = self::seed($tid, $i);
        $k = self::pick($s, 2, 9);

        return self::make(
            $tid, $i,
            "d/dx({$k}x) = ?",
            (string) $k,
            "Тогтмол {$k}-ийг дифференциалчлавал {$k}.",
            [(string) ($k - 1), (string) ($k + 1), '0', '1']
        );
    }

    private static function integralBasic(int $tid, int $i): array
    {
        $s = self::seed($tid, $i);
        $a = self::pick($s, 1, 5);
        $b = self::pick($s >> 2, 1, 4);
        $ans = $a * $b;

        return self::make(
            $tid, $i,
            "∫₀{$b} {$a} dx = ?",
            (string) $ans,
            "{$a}×{$b} = {$ans}."
        );
    }

    private static function complexBasic(int $tid, int $i): array
    {
        $triples = [[3, 4, 5], [5, 12, 13], [8, 15, 17], [6, 8, 10]];
        $s = self::seed($tid, $i);
        [$a, $b, $c] = $triples[self::pick($s, 0, count($triples) - 1)];

        return self::make(
            $tid, $i,
            "|{$a} + {$b}i| = ?",
            (string) $c,
            "√({$a}²+{$b}²) = {$c}.",
            [(string) ($c - 1), (string) ($c + 1), (string) ($a + $b), (string) ($a * $b)]
        );
    }

    private static function vectorBasic(int $tid, int $i): array
    {
        $s = self::seed($tid, $i);
        $x1 = self::pick($s, 1, 6);
        $y1 = self::pick($s >> 2, 1, 6);
        $x2 = self::pick($s >> 4, 1, 6);
        $y2 = self::pick($s >> 6, 1, 6);
        $ans = sqrt(($x2 - $x1) ** 2 + ($y2 - $y1) ** 2);

        return self::make(
            $tid, $i,
            "A({$x1},{$y1}), B({$x2},{$y2}). |AB| = ?",
            self::fmt($ans),
            "√((".($x2 - $x1).")²+(".($y2 - $y1).")²) = ".self::fmt($ans).'.'
        );
    }

    private static function timeConversion(int $tid, int $i): array
    {
        $s = self::seed($tid, $i);
        $h = self::pick($s, 1, 5);
        $m = self::pick($s >> 2, 1, 59);
        $ans = $h * 60 + $m;

        return self::make(
            $tid, $i,
            "{$h} цаг {$m} минутыг минутаар илэрхийл. Хэдэн минут вэ?",
            (string) $ans,
            "{$h}×60+{$m} = {$ans} минут."
        );
    }

    private static function moneyProblem(int $tid, int $i): array
    {
        $s = self::seed($tid, $i);
        $a = self::pick($s, 1, 20) * 100;
        $b = self::pick($s >> 2, 1, 15) * 100;
        $ans = $a + $b;

        return self::make(
            $tid, $i,
            "{$a}₮-той хүн {$b}₮ нэмж авав. Нийт хэдэн төгрөгтэй болох вэ?",
            (string) $ans,
            "{$a}+{$b} = {$ans}₮."
        );
    }

    private static function lengthWeightConversion(int $tid, int $i): array
    {
        $s = self::seed($tid, $i);
        $units = [
            ['метр', 'см', 100],
            ['кг', 'г', 1000],
            ['л', 'мл', 1000],
        ];
        $u = $units[self::pick($s, 0, count($units) - 1)];
        $n = self::pick($s >> 2, 1, 9);
        $ans = $n * $u[2];

        return self::make(
            $tid, $i,
            "{$n} {$u[0]} = ? {$u[1]}",
            (string) $ans,
            "{$n}×{$u[2]} = {$ans} {$u[1]}."
        );
    }

    private static function carrollGrouping(int $tid, int $i): array
    {
        $s = self::seed($tid, $i);
        $a = self::pick($s, 2, 10);
        $b = self::pick($s >> 2, 2, 10);
        $ans = $a + $b;

        return self::make(
            $tid, $i,
            "Карроллын диаграмм дээр нэг баганад {$a}, нөгөөд {$b} зүйл бүртгэгдсэн бол нийт хэд вэ?",
            (string) $ans,
            "{$a}+{$b} = {$ans}."
        );
    }

    private static function pictogramCount(int $tid, int $i): array
    {
        $s = self::seed($tid, $i);
        $v = self::pick($s, 2, 5);
        $n = self::pick($s >> 2, 3, 8);
        $ans = $v * $n;

        return self::make(
            $tid, $i,
            "Зурган диаграмм дээр 1 тэмдэг {$v}-ийг илэрхийлнэ. {$n} тэмдэг байвал нийт хэд вэ?",
            (string) $ans,
            "{$v}×{$n} = {$ans}."
        );
    }
}
