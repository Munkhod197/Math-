<?php

namespace App\Services;

use App\Models\ToyProfile;
use App\Models\User;
use Illuminate\Support\Facades\Http;

class AiTeacherService
{
    public function reply(User $user, string $message, string $mode, string $level, array $history = [], array $pageContext = []): array
    {
        $context = $this->learningContext($user);

        if (filled(config('services.openai.key'))) {
            try {
                $response = Http::withToken(config('services.openai.key'))
                    ->withOptions(['proxy' => config('services.openai.proxy') ?: []])
                    ->acceptJson()
                    ->timeout(30)
                    ->post('https://api.openai.com/v1/responses', [
                        'model' => config('services.openai.model'),
                        'instructions' => $this->systemPrompt($mode, $level, $context, $pageContext),
                        'input' => $this->historyText($history, $message),
                        'store' => false,
                        'max_output_tokens' => $level === 'detailed' ? 900 : 500,
                    ]);

                if ($response->successful() && filled($response->json('output_text'))) {
                    return ['message' => $response->json('output_text'), 'source' => 'openai'];
                }
            } catch (\Throwable) {
                // A local deterministic tutor still makes the learning flow usable offline.
            }
        }

        return ['message' => $this->fallbackReply($message, $mode, $level, $context), 'source' => 'mathmon'];
    }

    private function learningContext(User $user): array
    {
        $profile = ToyProfile::firstWhere('user_id', $user->id);

        return [
            'name' => $user->name,
            'level' => $profile?->level ?? 1,
            'accuracy' => $profile?->accuracy ?? 0,
            'streak' => $profile?->streak ?? 0,
        ];
    }

    private function systemPrompt(string $mode, string $level, array $context, array $pageContext): string
    {
        $modeLabel = [
            'explain' => 'Тайлбарлах',
            'solve_together' => 'Хамт бодох',
            'check' => 'Хариулт шалгах',
            'hint' => 'Санамж өгөх',
            'practice' => 'Дасгал үүсгэх',
        ][$mode] ?? 'Тайлбарлах';

        $levelLabel = ['simple' => 'энгийн', 'normal' => 'сургуулийн стандарт', 'detailed' => 'дэлгэрэнгүй'][$level] ?? 'сургуулийн стандарт';
        $problem = filled($pageContext['problem'] ?? null) ? "\nОдоогийн бодлого: {$pageContext['problem']}" : '';

        return <<<PROMPT
Чи бол MathMon AI Багш. Монгол сурагчдад математикийг ойлгомжтой, сонирхолтой, алхам алхмаар заадаг найрсаг виртуал багш.

Үндсэн зорилго: хариуг шууд хэлэхээс илүүтэй сурагчийг өөрөө хариуг нь олж чаддаг болго.

Заавал мөрдөх дүрэм:
1. Зөвхөн Монгол хэлээр, {$levelLabel} түвшний энгийн үгээр хариул. Нэг дор хэт их мэдээлэл бүү өг.
2. Сократын аргыг ашигла. Бодлогын хариуг шууд асуусан ч эхлээд санаа, томьёо, эхний жижиг алхмыг тайлбарлаад сурагчид дараагийн алхмыг өөрөөр нь хийх боломж олго.
3. solve_together болон hint горимд бүтэн шийдэл бүү өг. Hint горимд зөвхөн нэг жижиг чиглэл өг.
4. Сурагч алдаа гаргавал "Сайн оролдлого байна 👍 Гэхдээ энд жижиг алдаа гарчээ." гэсэн эелдэг хэлбэрээр яг аль алхам, яагаад гэдгийг тайлбарла. Хэзээ ч шоолж, доромжилж болохгүй.
5. Зөв алхамд "Маш сайн!", "Яг зөв байна!" гэх мэтээр урам өг.
6. Математикийн тэмдэглэгээг зөв бич. Томьёог LaTeX хэлбэрээр $...$ эсвэл $$...$$ дотор бич.
7. Боломжтой үед дараах бүтцийг ашигла:
   ## 🧑‍🏫 MathMon AI Багш
   **📌 Бодлогыг ойлгоё**
   **💡 Санаа**
   **✏️ Алхам**
   **🧠 Чиний ээлж**
   **💬 Зөвлөгөө**
8. Хэрэв сурагч "ойлгохгүй" гэвэл илүү энгийн жишээгээр дахин тайлбарла. "дэлгэрэнгүй" гэвэл алхам бүрийг нарийвчил. "зөвхөн хариу" гэвэл товч боловч гол аргыг заавал хамт харуул.
9. Тоо, бутархай, хувь, харьцаа, тэгшитгэл, тэнцэтгэл биш, зэрэг, язгуур, функц, координат, геометр, талбай, периметр, эзлэхүүн, магадлал, статистикийн сэдвүүдэд тохирох аргыг сонго.
10. Эцсийн хариу гаргасан үед бодлогод буцаан орлуулж шалга.

Одоогийн сургалтын горим: {$modeLabel}.
Сурагчийн MathMon түвшин: Level {$context['level']}, сүүлийн нарийвчлал {$context['accuracy']}%, цуврал {$context['streak']} өдөр.
{$problem}
PROMPT;
    }

    private function historyText(array $history, string $message): string
    {
        $previous = collect($history)->take(-8)->map(fn (array $item) => strtoupper($item['role']).': '.$item['message'])->implode("\n");

        return trim($previous."\nUSER: ".$message);
    }

    private function fallbackReply(string $message, string $mode, string $level, array $context): string
    {
        $compact = preg_replace('/\s+/', ' ', trim($message));

        if (preg_match('/^\s*([+-]?\d+)\s*\*?x\s*([+-])\s*(\d+)\s*=\s*([+-]?\d+)\s*$/i', $compact, $parts)) {
            return $this->linearEquationReply((int) $parts[1], $parts[2], (int) $parts[3], (int) $parts[4], $mode);
        }

        if ($mode === 'practice') {
            return '## 🧑‍🏫 MathMon AI Багш'."\n\n"
                .'**📌 Дасгал**'."\nЭхлээд нэг жижиг тэгшитгэлээр дасгал хийе.\n\n"
                .'$$ 3x + 4 = 19 $$'."\n\n"
                .'**💡 Санаа**'."\n\$x\$-ийн хажуугийн 4-ийг эхлээд арилгана.\n\n"
                .'**🧠 Чиний ээлж**'."\nТэнцүүгийн хоёр талаас ямар тоог хасах вэ?";
        }

        if ($mode === 'hint') {
            return '## 🧑‍🏫 MathMon AI Багш'."\n\n"
                .'**💡 Санамж**'."\nӨгөгдсөн тоо, үл мэдэгдэх, үйлдлийн тэмдгээ тус тусад нь ялгаад, \$x\$-ийг ганцаардуулах эсрэг үйлдлийг эхлээд сонгоорой.\n\n"
                .'**🧠 Чиний ээлж**'."\nЭхний хийх үйлдлээ бичиж үзээрэй.";
        }

        return "## 🧑‍🏫 MathMon AI Багш\n\nСайн байна уу, {$context['name']}! Бодлого, өөрийн бодолтын алхам, эсвэл томьёогоо бичээрэй. Би эхлээд гол санааг нь ойлгуулж, дараа нь чамаар алхам алхмаар бодолгуулна.\n\n**🧠 Чиний ээлж**\nЯг ямар бодлого дээр гацсанаа бичиж үзээрэй.";
    }

    private function linearEquationReply(int $a, string $operator, int $constant, int $rightSide, string $mode): string
    {
        $signedConstant = $operator === '-' ? -$constant : $constant;
        $remaining = $rightSide - $signedConstant;
        $answer = $a !== 0 ? $remaining / $a : null;
        $equation = "{$a}x {$operator} {$constant} = {$rightSide}";
        $answerText = $this->numberText($answer);

        if ($mode === 'hint') {
            $action = $operator === '+' ? "хоёр талаас {$constant}-ийг хас" : "хоёр талд {$constant}-ийг нэм";

            return "## 🧑‍🏫 MathMon AI Багш\n\n**💡 Санамж**\n{$equation} дээр \$x\$-ийн хажуугийн тогтмол тоог арилгах хэрэгтэй. Тиймээс {$action}.\n\n**🧠 Чиний ээлж**\nТэр үйлдлийн дараах шинэ тэгшитгэлээ бичээрэй.";
        }

        if ($mode === 'solve_together') {
            $action = $operator === '+' ? "хоёр талаас {$constant}-ийг хас" : "хоёр талд {$constant}-ийг нэм";

            return "## 🧑‍🏫 MathMon AI Багш\n\n**📌 Бодлогыг ойлгоё**\n\$x\$-ийг ганцаардуулахын тулд эхлээд тогтмол тоог салгана.\n\n**✏️ Алхам 1**\n{$equation} дээр {$action}.\n\n**🧠 Чиний ээлж**\nТэгвэл {$a}x-ийн баруун талд ямар тоо гарах вэ?";
        }

        $stepOne = $operator === '+' ? "Хоёр талаас {$constant}-ийг хасна" : "Хоёр талд {$constant}-ийг нэмнэ";

        return "## 🧑‍🏫 MathMon AI Багш\n\n**📌 Бодлогыг ойлгоё**\n{$equation} тэгшитгэлээс \$x\$-ийн утгыг олно.\n\n**💡 Санаа**\nТэнцүүгийн хоёр талд ижил үйлдэл хийж \$x\$-ийг ганцаардуулна.\n\n**✏️ Алхам 1**\n{$stepOne}:\n\$\$ {$a}x = {$remaining} \$\$\n\n**✏️ Алхам 2**\nХоёр талыг {$a}-д хуваана:\n\$\$ x = {$answerText} \$\$\n\n**✅ Шалгалт**\n\$x = {$answerText}\$-ийг орлуулбал зүүн тал нь {$rightSide} болно. Тиймээс зөв.\n\n**💬 Зөвлөгөө**\nДараагийн удаа эхлээд тогтмол тоог арилгаж, хамгийн сүүлд коэффициентоор хуваагаарай.";
    }

    private function numberText(?float $value): string
    {
        if ($value === null) {
            return 'тодорхойгүй';
        }

        return rtrim(rtrim(number_format($value, 4, '.', ''), '0'), '.');
    }
}
