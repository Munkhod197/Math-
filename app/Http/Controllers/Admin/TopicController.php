<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TopicController extends Controller
{
    public function index()
    {
        $topics = Topic::orderBy('grade_level')->paginate(25);
        return view('admin.topics.index', compact('topics'));
    }

    public function create()
    {
        return view('admin.topics.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:topics,slug',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:10',
            'grade_level' => 'required|integer|min:1|max:12',
            'video_url' => 'nullable|url',
            'video_title' => 'nullable|string|max:255',
            'video_file' => 'nullable|file|mimetypes:video/mp4,video/quicktime,video/x-msvideo,video/x-matroska|max:102400',
            'is_premium' => 'nullable|boolean',
        ]);

        $data['is_premium'] = (bool) ($data['is_premium'] ?? false);

        if ($request->hasFile('video_file')) {
            $path = $request->file('video_file')->store('topics_videos', 'public');
            $data['video_file'] = $path;
        }

        Topic::create($data);

        return redirect()->route('admin.topics.index')->with('success', 'Сэдэв амжилттай нэмэгдлээ');
    }

    public function edit(Topic $topic)
    {
        return view('admin.topics.edit', compact('topic'));
    }

    public function update(Request $request, Topic $topic)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:topics,slug,' . $topic->id,
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:10',
            'grade_level' => 'required|integer|min:1|max:12',
            'video_url' => 'nullable|url',
            'video_title' => 'nullable|string|max:255',
            'video_file' => 'nullable|file|mimetypes:video/mp4,video/quicktime,video/x-msvideo,video/x-matroska|max:102400',
            'is_premium' => 'nullable|boolean',
        ]);

        $data['is_premium'] = (bool) ($data['is_premium'] ?? false);

        if ($request->hasFile('video_file')) {
            // delete previous file if exists
            if ($topic->video_file) {
                Storage::disk('public')->delete($topic->video_file);
            }

            $path = $request->file('video_file')->store('topics_videos', 'public');
            $data['video_file'] = $path;
        }

        $topic->update($data);

        return redirect()->route('admin.topics.index')->with('success', 'Сэдэв шинэчлэгдлээ');
    }

    public function destroy(Topic $topic)
    {
        $topic->delete();
        return back()->with('success', 'Сэдэв устлаа');
    }
}
