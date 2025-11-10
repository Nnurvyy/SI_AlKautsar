<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pengguna; // Pastikan Anda menggunakan model Pengguna

class AuthController extends Controller
{
    /**
     * Menampilkan halaman login.
     */
     public function showWelcomeForm()
    {
        // Mengarah ke welcome.blade.php
        return view('auth.welcome'); 
    }

    /**
     * Menampilkan halaman login (Sign In).
     */
    public function showLoginForm()
    {
        // Diubah: Mengarah ke auth/login.blade.php
        return view('auth.login'); 
    }

    /**
     * Menampilkan halaman registrasi (Sign Up).
     */
    public function showRegistrationForm()
    {
        // BARU: Mengarah ke auth/register.blade.php
        return view('auth.register');
    }

    /**
     * Memproses upaya login.
     */
    public function loginProcess(Request $request)
    {
        // 1. Validasi input
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // 2. Coba autentikasi
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // 3. Ambil user yang login
            $user = Auth::user();

            if ($user->role === 'admin') {
                return redirect()->intended(route('admin.dashboard'));
            }
             
            // Jika 'publik' (atau role lainnya), arahkan ke landing page
            return redirect()->intended(route('public.landing'));
        }

        // 4. Jika gagal, kembalikan ke login dengan error
        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    public function dashboard()
    {
        return view('dashboard'); 
    }

    /**
     * Memproses logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Setelah logout, kembali ke landing page
        return redirect()->route('public.landing');
    }
}

