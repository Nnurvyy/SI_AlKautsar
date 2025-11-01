<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KhotibJumat;  // <-- 1. Import model KhotibJumat
use Carbon\Carbon;           // <-- 2. Import Carbon untuk perbandingan tanggal

// Anda perlu membuat model Kajian
// use App\Models\Kajian; 

class PublicController extends Controller
{
    /**
     * Menampilkan Landing Page (Halaman Beranda Publik)
     * Rute: /
     */
    public function landingPage()
    {
        // Ambil 1 jadwal khotib JUMAT INI (yang paling dekat >= hari ini)
        $khotibJumatIni = KhotibJumat::where('tanggal', '>=', Carbon::today())
                                    ->orderBy('tanggal', 'asc')
                                    ->first();
        
        // Kirim data 'khotibJumatIni' ke view
        return view('landing-page', [
            'khotibJumatIni' => $khotibJumatIni
        ]);
    }

    /**
     * Menampilkan halaman list Jadwal Khutbah Jumat
     * Rute: /jadwal-khotib
     */
    public function jadwalKhotib()
    {
        // Ambil SEMUA jadwal khotib yang akan datang (>= hari ini)
        $jadwalKhotib = KhotibJumat::where('tanggal', '>=', Carbon::today())
                                  ->orderBy('tanggal', 'asc')
                                  ->get();

        return view('public.jadwal-khotib', [
            'jadwalKhotib' => $jadwalKhotib
        ]);
    }

    /**
     * Menampilkan halaman list Jadwal Kajian
     * Rute: /jadwal-kajian
     */
    public function jadwalKajian()
    {
        // !! PERHATIAN !!
        // Anda perlu membuat Model dan Migration untuk 'Kajian'
        // Untuk sekarang, saya akan kirim data kosong agar tidak error.
        
        // --- Ganti ini jika Model Kajian sudah ada ---
        $jadwalKajian = collect(); // Data kosong (placeholder)
        
        /* // --- CONTOH JIKA SUDAH ADA MODEL KAJIAN ---
        $jadwalKajian = Kajian::where('tanggal', '>=', Carbon::today())
                               ->orderBy('tanggal', 'asc')
                               ->get();
        */

        return view('public.jadwal-kajian', [
            'jadwalKajian' => $jadwalKajian
        ]);
    }

    /**
     * Bertindak sebagai proxy untuk API Jadwal Shalat
     * Rute: /jadwal-shalat-api
     * * NOTE: JavaScript di landing-page.blade.php Anda sudah memanggil
     * API myquran.com secara langsung. Metode ini adalah alternatif
     * jika Anda ingin JS memanggil server Anda, lalu server Anda memanggil myquran.com.
     * Untuk saat ini, fungsi ini tidak terpakai oleh blade.
     */
    public function jadwalShalatApi(Request $request)
    {
        // Anda bisa pindahkan logika fetch API dari JS ke sini
        // Tapi untuk saat ini, kita biarkan saja.
        
        // Logika fetch dari JS di landing-page.blade.php sudah cukup.
        return response()->json(['message' => 'Layanan API Jadwal Shalat (Contoh)']);
    }
}