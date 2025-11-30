<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http; // Untuk API WA
use App\Models\Jamaah;
use App\Models\Pengurus;
use App\Models\Keuangan;
use App\Models\TabunganHewanQurban;
use App\Mail\OtpVerificationMail; // Pastikan Mailable ini sudah dibuat
use Laravel\Socialite\Facades\Socialite;
use Exception;
use Carbon\Carbon;

class AuthController extends Controller
{
    // --- HALAMAN UTAMA ---
    public function showWelcomeForm() { return view('auth.welcome'); }
    public function showLoginForm() { return view('auth.login'); }
    public function showRegistrationForm() { return view('auth.register'); }

    // --- LOGIN PROCESS (EMAIL & PASSWORD) ---
    public function loginProcess(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // 1. Cek Pengurus
        $pengurus = Pengurus::where('email', $credentials['email'])->first();
        if ($pengurus && Hash::check($credentials['password'], $pengurus->password)) {
            Auth::guard('pengurus')->login($pengurus, $request->boolean('remember'));
            $request->session()->regenerate();
            return redirect()->intended(route('pengurus.dashboard'));
        }

        // 2. Cek Jamaah
        $jamaah = Jamaah::where('email', $credentials['email'])->first();
        if ($jamaah && Hash::check($credentials['password'], $jamaah->password)) {
            
            // CEK VERIFIKASI: Jika belum verifikasi, minta verifikasi dulu
            if (!$jamaah->is_verified) {
                // Kirim ulang OTP jika perlu (opsional, di sini kita redirect aja)
                return redirect()->route('auth.verify', ['email' => $jamaah->email])
                    ->with('warning', 'Akun Anda belum terverifikasi. Silakan masukkan kode OTP.');
            }

            Auth::guard('jamaah')->login($jamaah, $request->boolean('remember'));
            $request->session()->regenerate();
            return redirect()->intended(route('public.landing'));
        }

        return back()->withErrors(['email' => 'Email atau password salah.'])->onlyInput('email');
    }

    // --- REGISTER PROCESS (EMAIL & PASSWORD) ---
    // Di dalam class AuthController

public function registerProcess(Request $request)
{
    // 1. Validasi Input (HAPUS 'unique:jamaah' dari sini)
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255', // Hapus unique di sini
        'no_hp' => 'required|string|max:15',         // Hapus unique di sini
        'password' => 'required|string|min:8|confirmed',
    ]);

    // 2. Cek Manual Keunikan Data
    $existingUser = Jamaah::where('email', $request->email)
                          ->orWhere('no_hp', $request->no_hp)
                          ->first();

    // Jika user sudah ada DAN sudah verifikasi => Tolak
    if ($existingUser && $existingUser->is_verified) {
        return back()->withInput()->withErrors(['email' => 'Email atau No HP sudah terdaftar dan terverifikasi. Silakan login.']);
    }

    // 3. Generate OTP
    $otp = rand(100000, 999999);
    $dataToSave = [
        'name' => $request->name,
        'email' => $request->email, // Pastikan email konsisten
        'no_hp' => $request->no_hp,
        'password' => Hash::make($request->password),
        'otp_code' => $otp,
        'otp_expires_at' => Carbon::now()->addMinutes(10),
        'is_verified' => false,
    ];

    // 4. Simpan Data (Update jika belum verif, Create jika baru)
    if ($existingUser) {
        // OVERWRITE data lama yang belum verified (Solusi agar tidak error "Duplicate")
        $existingUser->update($dataToSave);
        $jamaah = $existingUser;
    } else {
        // Buat baru
        $jamaah = Jamaah::create($dataToSave);
    }

    // 5. Kirim OTP
    $this->sendOtp($jamaah, $otp);

    // 6. Redirect
    return redirect()->route('auth.verify', ['email' => $jamaah->email])
        ->with('success', 'Kode OTP telah dikirim. Silakan cek Email/WhatsApp.');
}

    // --- GOOGLE AUTHENTICATION ---
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // 1. Cek Pengurus (Login Biasa)
            $pengurus = Pengurus::where('google_id', $googleUser->id)->orWhere('email', $googleUser->email)->first();
            if ($pengurus) {
                if (!$pengurus->google_id) $pengurus->update(['google_id' => $googleUser->id]);
                Auth::guard('pengurus')->login($pengurus);
                return redirect()->intended(route('pengurus.dashboard'));
            }

            // 2. Cek Jamaah
            $jamaah = Jamaah::where('google_id', $googleUser->id)->orWhere('email', $googleUser->email)->first();

            if ($jamaah) {
                // Update Google ID jika belum ada
                if (!$jamaah->google_id) $jamaah->update(['google_id' => $googleUser->id]);

                // CEK KELENGKAPAN DATA: Jika No HP kosong atau Belum Verifikasi
                if (empty($jamaah->no_hp) || !$jamaah->is_verified) {
                    return redirect()->route('auth.complete-data', ['email' => $jamaah->email]);
                }

                Auth::guard('jamaah')->login($jamaah);
                return redirect()->intended(route('public.landing'));

            } else {
                // 3. User Baru via Google -> Simpan Data Dasar -> Redirect ke Lengkapi Data
                $newJamaah = Jamaah::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'password' => null, // Tidak ada password
                    'is_verified' => false, // Wajib verifikasi no hp dulu
                ]);

                return redirect()->route('auth.complete-data', ['email' => $newJamaah->email]);
            }

        } catch (Exception $e) {
            return redirect()->route('login')->withErrors(['email' => 'Gagal login dengan Google. Silakan coba lagi.']);
        }
    }

    // --- LOGOUT ---
    public function logout(Request $request)
    {
        Auth::guard('pengurus')->logout();
        Auth::guard('jamaah')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('public.landing');
    }

    // --- DASHBOARD PENGURUS ---
    public function dashboard()
    {
        $totalPemasukan = Keuangan::where('tipe', 'pemasukan')->sum('nominal');
        $totalPengeluaran = Keuangan::where('tipe', 'pengeluaran')->sum('nominal');
        $saldo = $totalPemasukan - $totalPengeluaran;
        $totalPenabungQurban = TabunganHewanQurban::count();
        $recentTransactions = Keuangan::with('kategori')->orderBy('tanggal', 'desc')->orderBy('created_at', 'desc')->take(5)->get();

        return view('dashboard', compact('totalPemasukan', 'totalPengeluaran', 'saldo', 'totalPenabungQurban', 'recentTransactions'));
    }

    // ==========================================================
    // LOGIC TAMBAHAN: VERIFIKASI OTP & LENGKAPI DATA
    // ==========================================================

    // 1. Halaman Input OTP
    public function showVerifyForm(Request $request)
    {
        $email = $request->query('email');
        $name  = $request->query('name');
        $no_hp = $request->query('no_hp');

        if (!$email) return redirect()->route('login');
        return view('auth.verify_otp', compact('email'));
    }

    // 2. Proses Cek OTP
    public function verifyProcess(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:jamaah,email',
            'otp' => 'required|numeric',
        ]);

        $jamaah = Jamaah::where('email', $request->email)->first();

        // Cek OTP
        if ($jamaah->otp_code != $request->otp) {
            return back()->withErrors(['otp' => 'Kode OTP salah.']);
        }

        // Cek Kadaluarsa
        if (Carbon::now()->gt($jamaah->otp_expires_at)) {
            return back()->withErrors(['otp' => 'Kode OTP sudah kadaluarsa. Silakan minta ulang.']);
        }

        // Sukses Verifikasi
        $jamaah->update([
            'is_verified' => true,
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
            'otp_code' => null,
            'otp_expires_at' => null,
        ]);

        Auth::guard('jamaah')->login($jamaah);
        return redirect()->route('public.landing')->with('success', 'Akun berhasil diverifikasi!');
    }

    // 3. Halaman Lengkapi Data (Khusus Google User yg belum punya HP)
    public function showCompleteDataForm(Request $request)
    {
        $email = $request->query('email');
        if (!$email) return redirect()->route('login');
        return view('auth.complete_data', compact('email')); // Buat view ini (mirip register tapi cuma input HP)
    }

    // 4. Proses Simpan No HP & Kirim OTP (Lanjutan Google)
    public function processCompleteData(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:jamaah,email',
            'no_hp' => 'required|string|max:15',
        ]);

        $jamaah = Jamaah::where('email', $request->email)->first();
        $otp = rand(100000, 999999);

        // Update No HP dan set OTP
        $jamaah->update([
            'no_hp' => $request->no_hp,
            'otp_code' => $otp,
            'otp_expires_at' => Carbon::now()->addMinutes(10),
            'is_verified' => false, 
        ]);

        // Kirim OTP
        $this->sendOtp($jamaah, $otp);

        return redirect()->route('auth.verify', ['email' => $jamaah->email])
            ->with('success', 'Kode OTP verifikasi telah dikirim.');
    }

    // 5. Helper Kirim OTP (Email & WA)
    private function sendOtp($user, $otp)
    {
        // A. Kirim Email
        try {
            Mail::to($user->email)->send(new OtpVerificationMail($otp));
        } catch (Exception $e) {
            // Log error email, jangan stop proses
        }

        // B. Kirim WhatsApp (Contoh pakai Fonnte / Twilio)
        try {
            // Sesuaikan dengan vendor WA Gateway Anda
            // Http::withHeaders(['Authorization' => 'TOKEN_FONNTE'])->post('https://api.fonnte.com/send', [
            //     'target' => $user->no_hp,
            //     'message' => "Kode OTP Smart Masjid: *$otp*. Jangan berikan ke siapa pun.",
            // ]);
        } catch (Exception $e) {
            // Log error WA
        }
    }

    public function resendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $jamaah = Jamaah::where('email', $request->email)->first();

        // Cek apakah user ada
        if (!$jamaah) {
            return back()->withErrors(['email' => 'Email tidak ditemukan.']);
        }

        // Generate OTP Baru
        $otp = rand(100000, 999999);
        
        $jamaah->update([
            'otp_code' => $otp,
            'otp_expires_at' => \Carbon\Carbon::now()->addMinutes(10),
        ]);

        // Panggil fungsi kirim OTP yang sudah ada
        $this->sendOtp($jamaah, $otp);

        return back()->with('success', 'Kode OTP baru berhasil dikirim!');
    }
}