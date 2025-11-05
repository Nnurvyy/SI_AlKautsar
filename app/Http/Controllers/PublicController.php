<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KhotibJumat;  
use Carbon\Carbon;           

// 1. (WAJIB) Import model Kajian kamu
use App\Models\Kajian; 

class PublicController extends Controller
{
    // ... (fungsi landingPage() dan jadwalKhotib() biarkan saja) ...
    public function landingPage()
    {
        $khotibJumatIni = KhotibJumat::where('tanggal', '>=', Carbon::today())
                                    ->orderBy('tanggal', 'asc')
                                    ->first();
        
        return view('landing-page', [
            'khotibJumatIni' => $khotibJumatIni
        ]);
    }

    public function jadwalKhotib()
    {
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
        // 2. (WAJIB) Hapus kode placeholder dan ganti dengan query Kajian
        
        // --- Hapus/Komentari ini ---
        // $jadwalKajian = collect(); // Data kosong (placeholder)
        
        // --- Ganti dengan ini ---
        $jadwalKajian = Kajian::where('tanggal_kajian', '>=', Carbon::today())
                               ->orderBy('tanggal_kajian', 'asc')
                               ->get();
        // --- Selesai ---

        return view('public.jadwal-kajian', [
            'jadwalKajian' => $jadwalKajian
        ]);
    }
    
    // ... (fungsi jadwalShalatApi() biarkan saja) ...
    public function jadwalShalatApi(Request $request)
    {
        return response()->json(['message' => 'Layanan API Jadwal Shalat (Contoh)']);
    }
}