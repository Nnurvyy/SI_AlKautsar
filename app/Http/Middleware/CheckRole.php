<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {

        if (!Auth::check()) {

            return redirect()->route('login');
        }


        $user = Auth::user();


        foreach ($roles as $role) {
            if ($user->role === $role) {

                return $next($request);
            }
        }



        return redirect()->route('public.landing')->with('error', 'Anda tidak memiliki izin akses.');
    }
}
