<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // <-- Pastikan ini di-import
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles  // Ini akan berisi 'admin' atau 'publik' dari rute Anda
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Cek apakah user sudah login
        if (!Auth::check()) {
            // Jika belum, lempar ke halaman login
            return redirect()->route('login');
        }

        // 2. Ambil user yang sedang login
        $user = Auth::user();

        // 3. Loop role yang diizinkan (dari file web.php)
        foreach ($roles as $role) {
            
            // 4. Cek apakah role user cocok dengan salah satu role yang diizinkan
            // Ini akan membandingkan dengan kolom 'role' ('admin' or 'publik')
            if ($user->role === $role) {
                // Jika cocok, izinkan request lanjut
                return $next($request);
            }
        }

        // 5. Jika tidak ada role yang cocok, user tidak punya izin.
        // Lempar ke landing page.
        return redirect()->route('public.landing')->with('error', 'Anda tidak memiliki izin akses.');
    }
}