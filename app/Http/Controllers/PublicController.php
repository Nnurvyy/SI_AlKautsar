<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\KhotibJumat;  
use Carbon\Carbon;          
use Illuminate\Support\Facades\Log;
use App\Models\MasjidProfil;
use Illuminate\Validation\ValidationException;

class PublicController extends Controller
{

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
        // 1. Ambil ID Kota default dari database settings
        $settings = MasjidProfil::firstOrCreate([], [
            'nama_masjid' => 'Nama Masjid',
            'lokasi_nama' => 'KOTA TASIKMALAYA', // Default jika DB kosong
            'lokasi_id_api' => '1218' // Default (Tasikmalaya) jika DB kosong
        ]);
        $kotaId = $settings->lokasi_id_api;
        $lokasi = $settings->lokasi_nama;

        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));

        try {
            // 2. Ambil jadwal untuk load halaman awal
            $response = Http::get("https://api.myquran.com/v2/sholat/jadwal/{$kotaId}/{$tahun}/{$bulan}");
            $response->throw(); 

            $data = $response->json();

            if ($data['status'] && isset($data['data'])) {
                $jadwal = $data['data']['jadwal'];
                $lokasi = $data['data']['lokasi']; // Ambil nama lokasi yang benar dari API
            } else {
                $jadwal = [];
                session()->flash('error', 'Data jadwal untuk periode yang dipilih tidak ditemukan.');
            }

        } catch (\Exception $e) {
            $jadwal = [];
            session()->flash('error', 'Gagal terhubung ke server jadwal sholat. Silakan coba lagi nanti.');
        }

        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $listTahun = range(date('Y') - 1, date('Y') + 2);

        // 3. Kirim $kotaId (diganti nama dari $kotaId) ke view
        return view('public.jadwal-adzan', compact(
            'jadwal',
            'lokasi',
            'namaBulan',
            'listTahun',
            'bulan',
            'tahun',
            'kotaId' // <-- PENTING: Kirim ID Kota default
        ));
    }

    /**
     * (DIUBAH) API untuk mengambil data jadwal (digunakan oleh JavaScript)
     */
    public function jadwalAdzanApi(Request $request)
    {
        try {
            // 1. Validasi input dari AJAX, tambahkan 'lokasi_id'
            $request->validate([
                'bulan' => 'required|integer|between:1,12',
                'tahun' => 'required|integer|min:2000',
                'lokasi_id' => 'required|string|max:10', // <-- BARU: Wajibkan ada lokasi_id
            ]);

            // 2. Ambil semua data dari request
            $kotaId = $request->input('lokasi_id'); // <-- BARU
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
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Input tidak valid.', 
                'errors' => $e->errors()
            ], 422);
        } catch (\Illuminate\Http\Client\RequestException $e) {
            Log::error('API myquran.com error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal mengambil data dari sumber eksternal.'], 502);
        } catch (\Exception $e) {
            Log::error('Error in jadwalAdzanApi: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan pada server.'], 500);
        }
    }

    public function landingPage()
    {
        // Ambil profil masjid jika model ada
        $masjidSettings = null;
        if (class_exists(\App\Models\MasjidProfil::class)) {
            $masjidSettings = \App\Models\MasjidProfil::first();
        }

        // Fallback aman jika belum ada data
        if (!$masjidSettings) {
            $masjidSettings = (object)[
                'nama_masjid' => config('app.name', 'Smart‑Masjid'),
                'lokasi_nama' => 'Bandung',
                'foto_masjid'  => null,
            ];
        }

        return view('landing-page', compact('masjidSettings'));
    }
}
