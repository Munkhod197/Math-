<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Topic;
use App\Models\User;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard', [
            'totalUsers' => User::count(),
            'adminUsers' => User::where('is_admin', true)->count(),
            'totalTopics' => Topic::count(),
            'totalQuestions' => Question::count(),
        ]);
    }
}
