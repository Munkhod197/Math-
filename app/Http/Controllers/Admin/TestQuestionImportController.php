<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Topic;
use App\Services\PdfQuestionParser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class TestQuestionImportController extends Controller
{
    private const SESSION_KEY = 'admin_question_import';
    private const STORAGE_DIR = 'app/import-previews';

    public function showUpload()
    {
        $topics = Topic::orderBy('grade_level')->orderBy('name')->get();

        return view('admin.tests.import', compact('topics'));
    }

    public function uploadPdf(Request $request, PdfQuestionParser $parser)
    {
        $data = $request->validate([
            'pdf' => ['required', 'file', 'mimes:pdf', 'max:10240'],
            'topic_id' => ['nullable', 'exists:topics,id'],
        ]);

        $items = collect($parser->parse($data['pdf']))
            ->map(function (array $item) use ($data) {
                $item['topic_id'] = $data['topic_id'] ?? null;
                $item['correct_answer'] = in_array($item['correct_answer'], ['A', 'B', 'C', 'D'], true)
                    ? $item['correct_answer']
                    : null;

                return $item;
            })
            ->values()
            ->all();

        if ($items === []) {
            return back()
                ->withInput()
                ->withErrors(['pdf' => 'PDF-ээс асуулт уншиж чадсангүй. Дугаартай асуулт болон A-D сонголттой файл оруулна уу.']);
        }

        $token = (string) Str::uuid();
        $this->writeImport($token, [
            'filename' => $data['pdf']->getClientOriginalName(),
            'items' => $items,
        ]);

        session([self::SESSION_KEY => $token]);

        return redirect()->route('admin.tests.import.preview');
    }

    public function preview()
    {
        $import = $this->readImport();
        if (! $import) {
            return redirect()->route('admin.tests.import');
        }

        $topics = Topic::orderBy('grade_level')->orderBy('name')->get();
        $items = $import['items'];
        $readyCount = collect($items)->filter(fn (array $item) => $this->isReady($item))->count();

        return view('admin.tests.import-preview', [
            'filename' => $import['filename'],
            'items' => $items,
            'topics' => $topics,
            'readyCount' => $readyCount,
        ]);
    }

    public function updatePreview(Request $request)
    {
        $validated = $this->validateItems($request, requireCorrectAnswer: false);
        $import = $this->readImport();
        if (! $import) {
            return redirect()->route('admin.tests.import');
        }
        $questions = $this->mergeUploadedImages($request, $validated['questions']);

        $this->writeImport((string) session(self::SESSION_KEY), [
            'filename' => $import['filename'] ?? 'import.pdf',
            'items' => $questions,
        ]);

        return redirect()
            ->route('admin.tests.import.preview')
            ->with('success', 'Preview шинэчлэгдлээ.');
    }

    public function save(Request $request)
    {
        $validated = $this->validateItems($request, requireCorrectAnswer: true);
        $questions = $this->mergeUploadedImages($request, $validated['questions']);

        DB::transaction(function () use ($questions) {
            foreach ($questions as $item) {
                Question::create($item);
            }
        });

        $this->deleteImport();

        return redirect()
            ->route('admin.tests.index')
            ->with('success', count($questions) . ' асуулт амжилттай импортлогдлоо.');
    }

    private function validateItems(Request $request, bool $requireCorrectAnswer): array
    {
        return $request->validate([
            'questions' => ['required', 'array', 'min:1'],
            'questions.*.topic_id' => ['required', 'exists:topics,id'],
            'questions.*.question_text' => ['required', 'string'],
            'questions.*.image_path' => ['nullable', 'string', 'max:255'],
            'questions.*.image' => ['nullable', 'image', 'max:5120'],
            'questions.*.option_a' => ['required', 'string'],
            'questions.*.option_b' => ['required', 'string'],
            'questions.*.option_c' => ['required', 'string'],
            'questions.*.option_d' => ['required', 'string'],
            'questions.*.correct_answer' => [
                $requireCorrectAnswer ? 'required' : 'nullable',
                Rule::in(['A', 'B', 'C', 'D']),
            ],
            'questions.*.explanation' => ['nullable', 'string'],
        ]);
    }

    private function mergeUploadedImages(Request $request, array $questions): array
    {
        foreach ($questions as $index => $question) {
            unset($questions[$index]['image']);

            $file = $request->file("questions.$index.image");
            if ($file) {
                $questions[$index]['image_path'] = $file->store('question-images', 'public');
            }
        }

        return $questions;
    }

    private function isReady(array $item): bool
    {
        foreach (['topic_id', 'question_text', 'option_a', 'option_b', 'option_c', 'option_d', 'correct_answer'] as $key) {
            if (blank($item[$key] ?? null)) {
                return false;
            }
        }

        return true;
    }

    private function readImport(): ?array
    {
        $token = session(self::SESSION_KEY);
        if (! is_string($token) || $token === '') {
            return null;
        }

        $path = $this->importPath($token);
        if (! File::exists($path)) {
            return null;
        }

        $data = json_decode((string) File::get($path), true);

        return is_array($data) ? $data : null;
    }

    private function writeImport(string $token, array $payload): void
    {
        File::ensureDirectoryExists(storage_path(self::STORAGE_DIR));

        File::put(
            $this->importPath($token),
            json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR),
        );
    }

    private function deleteImport(): void
    {
        $token = session(self::SESSION_KEY);

        if (is_string($token) && $token !== '') {
            File::delete($this->importPath($token));
        }

        session()->forget(self::SESSION_KEY);
    }

    private function importPath(string $token): string
    {
        return storage_path(self::STORAGE_DIR . '/' . basename($token) . '.json');
    }
}
