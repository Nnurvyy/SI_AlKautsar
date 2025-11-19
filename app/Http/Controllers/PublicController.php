<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

// --- IMPORT SEMUA MODEL YANG DIPAKAI ---
use App\Models\KhotibJumat;
use App\Models\Kajian;
use App\Models\ProgramDonasi; 
use Illuminate\Support\Facades\Log;

class PublicController extends Controller
{
    public function landingPage()
    {
        return view('landing-page');
    }

    public function jadwalKhotib()
    {
        $jadwalKhotib = KhotibJumat::where('tanggal', '>=', Carbon::today())
            ->orderBy('tanggal', 'asc')
            ->get();

        if ($jadwalKhotib->isEmpty()) {
            $jadwalKhotib = collect([
                (object)[
                    'foto_url' => asset('images/events/abdul-somad.jpg'),
                    'nama_khotib' => 'Ustadz Abdul Somad',
                    'nama_imam' => 'Ustadz Fulan',
                    'tema_khutbah' => 'Pentingnya Ukhuwah',
                    'tanggal' => '2025-11-14'
                ],
            ]);
        }

        return view('public.jadwal-khotib', ['jadwalKhotib' => $jadwalKhotib]);
    }

    public function jadwalKajian()
    {
        $kajianEvent = collect([]); 
        $kajianRutin = collect([]); 
        return view('public.jadwal-kajian', compact('kajianEvent', 'kajianRutin'));
    }
    
    public function artikel()
    {
        $artikel = collect([]); 
        return view('public.artikel', compact('artikel'));
    }

    /**
     * HALAMAN DONASI (FIXED)
     * Mengirim variabel $programs ke view
     */
    public function donasi()
    {
        try {
            $programs = ProgramDonasi::orderBy('tanggal_selesai', 'asc')->get();
        } catch (\Exception $e) {
            $programs = collect([]);
        }

        return view('public.donasi', compact('programs'));
    }

    public function program()
    {
        $program = collect([]); 
        return view('public.program', compact('program'));
    }

    public function tabunganQurbanSaya()
    {
        return view('public.tabungan-qurban-saya', [
            'namaUser' => 'User Test (Dummy)',
            'totalTabungan' => 0,
            'hewanQurban' => []
        ]);
    }

    public function jadwalAdzan(Request $request)
    {
        $request->validate([
            'bulan' => 'sometimes|integer|between:1,12',
            'tahun' => 'sometimes|integer|min:2000',
        ]);

        $kotaId = '1632';
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));

        try {
            $response = Http::get("https://api.myquran.com/v2/sholat/jadwal/{$kotaId}/{$tahun}/{$bulan}");
            $response->throw();

            $data = $response->json();

            if ($data['status'] && isset($data['data'])) {
                $jadwal = $data['data']['jadwal'];
                $lokasi = $data['data']['lokasi'];
            } else {
                $jadwal = [];
                $lokasi = 'Tidak Ditemukan';
                session()->flash('error', 'Data jadwal tidak ditemukan.');
            }

        } catch (\Exception $e) {
            $jadwal = [];
            $lokasi = 'Gagal Mengambil Data';
            session()->flash('error', 'Gagal terhubung ke server jadwal sholat.');
        }

        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $listTahun = range(date('Y') - 1, date('Y') + 1);

        return view('public.jadwal-adzan', compact(
            'jadwal', 'lokasi', 'namaBulan', 'listTahun', 'bulan', 'tahun'
        ));
    }

    public function jadwalAdzanApi(Request $request)
    {
        try {
            $request->validate([
                'bulan' => 'required|integer|between:1,12',
                'tahun' => 'required|integer|min:2000',
            ]);

            $kotaId = '1632';
            $bulan = $request->input('bulan');
            $tahun = $request->input('tahun');

            $response = Http::get("https://api.myquran.com/v2/sholat/jadwal/{$kotaId}/{$tahun}/{$bulan}");
            $response->throw();

            $data = $response->json();

            if ($data && $data['status'] && isset($data['data']['jadwal'])) {
                return response()->json([
                    'success' => true,
                    'jadwal' => $data['data']['jadwal'],
                    'lokasi' => $data['data']['lokasi'],
                ]);
            } else {
                return response()->json(['success' => false, 'message' => 'Format data tidak valid.'], 500);
            }
        } catch (\Exception $e) {
            Log::error('API Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan pada server.'], 500);
        }
    }
}
