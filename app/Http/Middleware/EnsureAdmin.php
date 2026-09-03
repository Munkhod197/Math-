<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (! $user || ! $user->is_admin) {
            return redirect()->route('home')->with('error', 'Та админ хэсэгт нэвтрэх эрхгүй байна.');
        }

        return $next($request);
    }
}
