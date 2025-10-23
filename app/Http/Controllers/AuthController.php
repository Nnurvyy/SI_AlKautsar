<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class AuthController extends Controller
{
    /**
     * Menampilkan halaman form login.
     */
    public function showLoginForm()
    {
        // Cek jika user SUDAH login, arahkan ke dashboard
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    /**
     * Memproses percobaan login.
     */
    public function loginProcess(Request $request)
    {
        // 1. Validasi input
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 2. Coba lakukan autentikasi
        if (Auth::attempt($credentials)) {
            // Jika berhasil, regenerate session
            $request->session()->regenerate();

            // Redirect ke halaman yang dituju sebelumnya, atau ke dashboard
            return redirect()->intended('dashboard');
        }

        // 3. Jika gagal
        return back()->withErrors([
            'email' => 'Email atau Password yang Anda masukkan salah.',
        ])->onlyInput('email'); // Hanya kembalikan input email
    }

    /**
     * Menampilkan halaman dashboard (setelah login).
     */
    public function dashboard()
    {
        // Cukup tampilkan view dashboard
        // Data user bisa diakses di view dengan Auth::user()
        return view('dashboard');
    }

    /**
     * Memproses logout user.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}