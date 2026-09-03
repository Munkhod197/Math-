<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Test;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function index()
    {
        $tests = Test::latest()->paginate(25);
        return view('admin.tests.index', compact('tests'));
    }

    public function create()
    {
        return view('admin.tests.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:tests,slug',
            'grade_level' => 'nullable|integer|min:1|max:12',
            'duration_minutes' => 'nullable|integer|min:1|max:600',
            'total_questions' => 'nullable|integer|min:1|max:500',
            'is_published' => 'nullable|boolean',
        ]);

        $data['is_published'] = (bool) ($data['is_published'] ?? false);

        Test::create($data);

        return redirect()->route('admin.tests.index')->with('success', 'Test created');
    }

    public function edit(Test $test)
    {
        return view('admin.tests.edit', compact('test'));
    }

    public function update(Request $request, Test $test)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:tests,slug,' . $test->id,
            'grade_level' => 'nullable|integer|min:1|max:12',
            'duration_minutes' => 'nullable|integer|min:1|max:600',
            'total_questions' => 'nullable|integer|min:1|max:500',
            'is_published' => 'nullable|boolean',
        ]);

        $data['is_published'] = (bool) ($data['is_published'] ?? false);

        $test->update($data);

        return redirect()->route('admin.tests.index')->with('success', 'Test updated');
    }

    public function destroy(Test $test)
    {
        $test->delete();
        return back()->with('success', 'Test deleted');
    }
}
