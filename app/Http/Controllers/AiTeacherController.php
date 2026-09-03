<?php

namespace App\Http\Controllers;

use App\Models\AiConversation;
use App\Models\AiMessage;
use App\Services\AiTeacherService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AiTeacherController extends Controller
{
    public function index(Request $request): View
    {
        $conversations = $request->user()
            ->aiConversations()
            ->latest('updated_at')
            ->get();

        $active = $request->filled('conversation')
            ? $conversations->firstWhere('id', (int) $request->input('conversation'))
            : $conversations->first();

        $messages = $active
            ? $active->messages()->oldest()->get()
            : collect();

        return view('ai-teacher.index', compact('conversations', 'active', 'messages'));
    }

    public function send(Request $request, AiTeacherService $teacher): JsonResponse
    {
        $data = $request->validate([
            'conversation_id' => ['nullable', 'integer'],
            'message' => ['required', 'string', 'max:4000'],
            'mode' => ['required', 'in:explain,solve_together,check,hint,practice'],
            'level' => ['required', 'in:simple,normal,detailed'],
            'context' => ['nullable', 'array'],
            'context.page' => ['nullable', 'string', 'max:255'],
            'context.problem' => ['nullable', 'string', 'max:2000'],
            'context.source' => ['nullable', 'string', 'max:80'],
        ]);

        $conversation = $data['conversation_id']
            ? $request->user()->aiConversations()->findOrFail($data['conversation_id'])
            : $request->user()->aiConversations()->create([
                'title' => $this->titleFor($data['message']),
                'topic' => $data['mode'],
            ]);

        AiMessage::create(['conversation_id' => $conversation->id, 'role' => 'user', 'message' => $data['message']]);
        $history = $conversation->messages()->latest()->take(8)->get()->reverse()->map(fn ($item) => ['role' => $item->role, 'message' => $item->message])->values()->all();
        $reply = $teacher->reply(
            $request->user(),
            $data['message'],
            $data['mode'],
            $data['level'],
            $history,
            $data['context'] ?? [],
        );
        $assistant = AiMessage::create(['conversation_id' => $conversation->id, 'role' => 'assistant', 'message' => $reply['message'], 'metadata' => ['source' => $reply['source'], 'mode' => $data['mode'], 'context' => $data['context'] ?? null]]);
        $conversation->touch();

        return response()->json([
            'conversation' => ['id' => $conversation->id, 'title' => $conversation->title],
            'message' => ['id' => $assistant->id, 'role' => 'assistant', 'message' => $assistant->message],
        ]);
    }

    public function show(Request $request, AiConversation $conversation): JsonResponse
    {
        abort_unless($conversation->user_id === $request->user()->id, 404);

        return response()->json(['conversation' => $conversation, 'messages' => $conversation->messages()->oldest()->get()]);
    }

    private function titleFor(string $message): string
    {
        return str($message)->squish()->limit(48, '...')->toString() ?: 'Шинэ ярилцлага';
    }
}
