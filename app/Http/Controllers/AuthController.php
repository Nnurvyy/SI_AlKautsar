<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash; // Import Hash
use App\Models\Jamaah;                 // Import Jamaah
use App\Models\Pengurus;               // Import Pengurus
use Laravel\Socialite\Facades\Socialite; // Import Socialite
use Exception;

class AuthController extends Controller
{
    /**
     * Menampilkan halaman selamat datang.
     */
    public function showWelcomeForm()
    {
        return view('auth.welcome');
    }

    /**
     * Menampilkan halaman login (Sign In).
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Menampilkan halaman registrasi (Sign Up).
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Memproses upaya login (Email + Password).
     * Ini adalah logic dari PM Anda.
     */
    public function loginProcess(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // 1. Coba login sebagai pengurus dulu
        // Diganti sedikit agar pakai $credentials
        $pengurus = \App\Models\Pengurus::where('email', $credentials['email'])->first();
        if ($pengurus && Hash::check($credentials['password'], $pengurus->password)) {
            Auth::guard('pengurus')->login($pengurus, $request->boolean('remember'));
            $request->session()->regenerate();
            return redirect()->intended(route('pengurus.dashboard')); // Arahkan ke dashboard pengurus
        }

        // 2. Kalau bukan pengurus, coba jamaah
        $jamaah = \App\Models\Jamaah::where('email', $credentials['email'])->first();
        if ($jamaah && Hash::check($credentials['password'], $jamaah->password)) {
            Auth::guard('jamaah')->login($jamaah, $request->boolean('remember'));
            $request->session()->regenerate();
            // Arahkan ke rute 'jamaah.' (misal: jamaah.qurban) atau landing page
            return redirect()->intended(route('public.landing'));
        }

        // 3. Kalau dua-duanya gagal
        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    /**
     * Memproses registrasi (Email + Password).
     * Registrasi selalu sebagai 'jamaah'.
     */
    public function registerProcess(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:jamaah|unique:pengurus',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $jamaah = Jamaah::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Langsung loginkan sebagai jamaah
        Auth::guard('jamaah')->login($jamaah);

        return redirect()->route('public.landing'); // Arahkan ke landing page
    }


    /**
     * Mengarahkan pengguna ke Google untuk autentikasi.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Menangani callback dari Google.
     * Ini adalah logic utama login/register via Google.
     */
    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Logic:
            // 1. Cari di tabel 'pengurus' berdasarkan google_id
            $pengurus = Pengurus::where('google_id', $googleUser->id)->first();
            if ($pengurus) {
                Auth::guard('pengurus')->login($pengurus);
                $request->session()->regenerate();
                return redirect()->intended(route('pengurus.dashboard'));
            }

            // 2. Cari di tabel 'jamaah' berdasarkan google_id
            $jamaah = Jamaah::where('google_id', $googleUser->id)->first();
            if ($jamaah) {
                Auth::guard('jamaah')->login($jamaah);
                $request->session()->regenerate();
                return redirect()->intended(route('public.landing'));
            }

            // Jika tidak ada google_id, cek berdasarkan email
            // 3. Cek email di 'pengurus'
            $pengurus = Pengurus::where('email', $googleUser->email)->first();
            if ($pengurus) {
                // Email ada, update google_id-nya dan loginkan
                $pengurus->update(['google_id' => $googleUser->id]);
                Auth::guard('pengurus')->login($pengurus);
                $request->session()->regenerate();
                return redirect()->intended(route('pengurus.dashboard'));
            }

            // 4. Cek email di 'jamaah'
            $jamaah = Jamaah::where('email', $googleUser->email)->first();
            if ($jamaah) {
                // Email ada, update google_id-nya dan loginkan
                $jamaah->update(['google_id' => $googleUser->id]);
                Auth::guard('jamaah')->login($jamaah);
                $request->session()->regenerate();
                return redirect()->intended(route('public.landing'));
            }

            // 5. Jika email tidak ada di kedua tabel, daftarkan sebagai 'jamaah' baru
            $newJamaah = Jamaah::create([
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'google_id' => $googleUser->id,
                'password' => null, // Tidak ada password untuk Google login
            ]);

            Auth::guard('jamaah')->login($newJamaah);
            $request->session()->regenerate();
            return redirect()->intended(route('public.landing'));

        } catch (Exception $e) {
            // Tangani error, kembalikan ke login
            return redirect()->route('login')->withErrors(['email' => 'Gagal login dengan Google. Silakan coba lagi.']);
        }
    }


    /**
     * Memproses logout.
     * Harus logout dari kedua guard.
     */
    public function logout(Request $request)
    {
        Auth::guard('pengurus')->logout();
        Auth::guard('jamaah')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Setelah logout, kembali ke landing page
        return redirect()->route('public.landing');
    }

    /**
     * Dashboard untuk PENGURUS.
     * Pastikan view 'dashboard' ada di resources/views/dashboard.blade.php
     * ATAU di resources/views/pengurus/dashboard.blade.php
     */
    public function dashboard()
    {
        // Sesuaikan nama view ini dengan struktur Anda
        // Mungkin 'pengurus.dashboard' atau hanya 'dashboard'
        return view('dashboard');
    }
}
