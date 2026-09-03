<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class QuestionController extends Controller
{
    public function index()
    {
        $questions = Question::with('topic')->latest()->paginate(25);
        return view('admin.questions.index', compact('questions'));
    }

    public function create()
    {
        $topics = Topic::orderBy('grade_level')->get();
        return view('admin.questions.create', compact('topics'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'topic_id' => 'required|exists:topics,id',
            'text' => 'required|string',
            'a' => 'required|string',
            'b' => 'required|string',
            'c' => 'required|string',
            'd' => 'required|string',
            'answer' => 'required|string|in:A,B,C,D',
            'explanation' => 'nullable|string',
            'image' => 'nullable|image|max:5120',
        ]);

        $imagePath = $request->file('image')?->store('question-images', 'public');

        Question::create([
            'topic_id' => $data['topic_id'],
            'question_text' => $data['text'],
            'image_path' => $imagePath,
            'option_a' => $data['a'],
            'option_b' => $data['b'],
            'option_c' => $data['c'],
            'option_d' => $data['d'],
            'correct_answer' => $data['answer'],
            'explanation' => $data['explanation'] ?? null,
        ]);

        return redirect()->route('admin.questions.index')->with('success', 'Асуулт нэмэгдлээ');
    }

    public function edit(Question $question)
    {
        $topics = Topic::orderBy('grade_level')->get();
        return view('admin.questions.edit', compact('question', 'topics'));
    }

    public function update(Request $request, Question $question)
    {
        $data = $request->validate([
            'topic_id' => 'required|exists:topics,id',
            'text' => 'required|string',
            'a' => 'required|string',
            'b' => 'required|string',
            'c' => 'required|string',
            'd' => 'required|string',
            'answer' => 'required|string|in:A,B,C,D',
            'explanation' => 'nullable|string',
            'image' => 'nullable|image|max:5120',
            'remove_image' => 'nullable|boolean',
        ]);

        $imagePath = $question->image_path;
        if ($request->boolean('remove_image')) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = null;
        }

        if ($request->hasFile('image')) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $request->file('image')->store('question-images', 'public');
        }

        $question->update([
            'topic_id' => $data['topic_id'],
            'question_text' => $data['text'],
            'image_path' => $imagePath,
            'option_a' => $data['a'],
            'option_b' => $data['b'],
            'option_c' => $data['c'],
            'option_d' => $data['d'],
            'correct_answer' => $data['answer'],
            'explanation' => $data['explanation'] ?? null,
        ]);

        return redirect()->route('admin.questions.index')->with('success', 'Асуулт шинэчлэгдлээ');
    }

    public function destroy(Question $question)
    {
        if ($question->image_path) {
            Storage::disk('public')->delete($question->image_path);
        }

        $question->delete();
        return back()->with('success', 'Асуулт устлаа');
    }
}
