<?php

namespace App\Http\Controllers;

class HomeController extends PageController
{
    public function index()
    {
        $topics = $this->topics()
            ->sortBy('grade_level')
            ->groupBy('grade_level');

        return view('home', [
            'topics' => $topics,
        ]);
    }
}
