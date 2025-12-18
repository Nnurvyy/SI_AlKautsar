<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use App\Models\Jamaah;
use App\Models\Pengurus;
use App\Models\Keuangan;
use App\Models\TabunganHewanQurban;
use App\Mail\OtpVerificationMail;
use Laravel\Socialite\Facades\Socialite;
use Exception;
use Carbon\Carbon;

class AuthController extends Controller
{

    public function showWelcomeForm()
    {
        return view('auth.welcome');
    }
    public function showLoginForm()
    {
        return view('auth.login');
    }
    public function showRegistrationForm()
    {
        return view('auth.register');
    }


    public function loginProcess(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $pengurus = Pengurus::where('email', $credentials['email'])->first();
        if ($pengurus && Hash::check($credentials['password'], $pengurus->password)) {

            Auth::guard('pengurus')->login($pengurus, true);
            $request->session()->regenerate();
            return redirect()->intended(route('pengurus.dashboard'));
        }


        $jamaah = Jamaah::where('email', $credentials['email'])->first();
        if ($jamaah && Hash::check($credentials['password'], $jamaah->password)) {


            if (!$jamaah->is_verified) {

                return redirect()->route('auth.verify', ['email' => $jamaah->email])
                    ->with('warning', 'Akun Anda belum terverifikasi. Silakan masukkan kode OTP.');
            }

            Auth::guard('jamaah')->login($jamaah, true);
            $request->session()->regenerate();
            return redirect()->intended(route('public.landing'));
        }

        return back()->withErrors(['email' => 'Email atau password salah.'])->onlyInput('email');
    }




    public function registerProcess(Request $request)
    {

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'no_hp' => 'required|string|max:15',
            'password' => 'required|string|min:8|confirmed',
        ]);


        $existingUser = Jamaah::where('email', $request->email)
            ->orWhere('no_hp', $request->no_hp)
            ->first();


        if ($existingUser && $existingUser->is_verified) {
            return back()->withInput()->withErrors(['email' => 'Email atau No HP sudah terdaftar dan terverifikasi. Silakan login.']);
        }


        $otp = rand(100000, 999999);
        $dataToSave = [
            'name' => $request->name,
            'email' => $request->email,
            'no_hp' => $request->no_hp,
            'password' => Hash::make($request->password),
            'otp_code' => $otp,
            'otp_expires_at' => Carbon::now()->addMinutes(10),
            'is_verified' => false,
        ];


        if ($existingUser) {

            $existingUser->update($dataToSave);
            $jamaah = $existingUser;
        } else {

            $jamaah = Jamaah::create($dataToSave);
        }


        $this->sendOtp($jamaah, $otp);


        return redirect()->route('auth.verify', ['email' => $jamaah->email])
            ->with('success', 'Kode OTP telah dikirim. Silakan cek Email/WhatsApp.');
    }


    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();


            $pengurus = Pengurus::where('google_id', $googleUser->id)->orWhere('email', $googleUser->email)->first();
            if ($pengurus) {
                if (!$pengurus->google_id) $pengurus->update(['google_id' => $googleUser->id]);
                Auth::guard('pengurus')->login($pengurus);
                return redirect()->intended(route('pengurus.dashboard'));
            }


            $jamaah = Jamaah::where('google_id', $googleUser->id)->orWhere('email', $googleUser->email)->first();

            if ($jamaah) {

                if (!$jamaah->google_id) $jamaah->update(['google_id' => $googleUser->id]);


                if (empty($jamaah->no_hp) || !$jamaah->is_verified) {
                    return redirect()->route('auth.complete-data', ['email' => $jamaah->email]);
                }

                Auth::guard('jamaah')->login($jamaah);
                return redirect()->intended(route('public.landing'));
            } else {

                $newJamaah = Jamaah::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'password' => null,
                    'is_verified' => false,
                ]);

                return redirect()->route('auth.complete-data', ['email' => $newJamaah->email]);
            }
        } catch (Exception $e) {
            return redirect()->route('login')->withErrors(['email' => 'Gagal login dengan Google. Silakan coba lagi.']);
        }
    }


    public function logout(Request $request)
    {
        Auth::guard('pengurus')->logout();
        Auth::guard('jamaah')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('public.landing');
    }


    public function dashboard()
    {
        $totalPemasukan = Keuangan::where('tipe', 'pemasukan')->sum('nominal');
        $totalPengeluaran = Keuangan::where('tipe', 'pengeluaran')->sum('nominal');
        $saldo = $totalPemasukan - $totalPengeluaran;
        $totalPenabungQurban = TabunganHewanQurban::count();
        $recentTransactions = Keuangan::with('kategori')->orderBy('tanggal', 'desc')->orderBy('created_at', 'desc')->take(5)->get();

        return view('dashboard', compact('totalPemasukan', 'totalPengeluaran', 'saldo', 'totalPenabungQurban', 'recentTransactions'));
    }


    public function showVerifyForm(Request $request)
    {
        $email = $request->query('email');
        $name  = $request->query('name');
        $no_hp = $request->query('no_hp');

        if (!$email) return redirect()->route('login');
        return view('auth.verify_otp', compact('email'));
    }


    public function verifyProcess(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:jamaah,email',
            'otp' => 'required|numeric',
        ]);

        $jamaah = Jamaah::where('email', $request->email)->first();


        if ($jamaah->otp_code != $request->otp) {
            return back()->withErrors(['otp' => 'Kode OTP salah.']);
        }


        if (Carbon::now()->gt($jamaah->otp_expires_at)) {
            return back()->withErrors(['otp' => 'Kode OTP sudah kadaluarsa. Silakan minta ulang.']);
        }


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


    public function showCompleteDataForm(Request $request)
    {
        $email = $request->query('email');
        if (!$email) return redirect()->route('login');
        return view('auth.complete_data', compact('email'));
    }


    public function processCompleteData(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:jamaah,email',
            'no_hp' => 'required|string|max:15',
        ]);

        $jamaah = Jamaah::where('email', $request->email)->first();


        $jamaah->update([
            'no_hp' => $request->no_hp,
            'is_verified' => true,
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
            'otp_code' => null,
            'otp_expires_at' => null,
        ]);


        Auth::guard('jamaah')->login($jamaah);


        return redirect()->intended(route('public.landing'))
            ->with('success', 'Data berhasil dilengkapi. Selamat datang!');
    }


    private function sendOtp($user, $otp)
    {
        try {
            // 'queue' melempar tugas ke database dan langsung lanjut (user tidak menunggu)
            Mail::to($user->email)->queue(new OtpVerificationMail($otp));
        } catch (Exception $e) {
        }
    }

    public function resendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $jamaah = Jamaah::where('email', $request->email)->first();


        if (!$jamaah) {
            return back()->withErrors(['email' => 'Email tidak ditemukan.']);
        }


        $otp = rand(100000, 999999);

        $jamaah->update([
            'otp_code' => $otp,
            'otp_expires_at' => \Carbon\Carbon::now()->addMinutes(10),
        ]);


        $this->sendOtp($jamaah, $otp);

        return back()->with('success', 'Kode OTP baru berhasil dikirim!');
    }
}
