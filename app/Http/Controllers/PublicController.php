<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\KhotibJumat;  
use App\Models\Kajian;  
use App\Models\Donasi;
use App\Models\Program;
use App\Models\HewanQurban;
use App\Models\DetailTabunganHewanQurban;
use App\Models\Artikel;
use Carbon\Carbon;          
use Illuminate\Support\Facades\Log;
use App\Models\MasjidProfil;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use App\Models\TabunganHewanQurban; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PublicController extends Controller
{

    public function jadwalKhotib()
    {
        $today = Carbon::today();

        // 1. Ambil SATU data terdekat untuk Highlight (Jumat Ini)
        $khotibJumatIni = KhotibJumat::where('tanggal', '>=', $today)
                            ->orderBy('tanggal', 'asc')
                            ->first();

        // 2. Ambil sisa data untuk List (Jadwal Selanjutnya)
        // Logika: Ambil yang >= hari ini, TAPI kecualikan ID milik $khotibJumatIni
        $querySelanjutnya = KhotibJumat::where('tanggal', '>=', $today)
                            ->orderBy('tanggal', 'asc');

        // Jika ada data Jumat Ini, pastikan ID tersebut tidak muncul lagi di list bawah/samping
        if ($khotibJumatIni) {
            $querySelanjutnya->where('id_khutbah', '!=', $khotibJumatIni->id_khutbah);
        }

        // Paginate 3 item per halaman sesuai request
        $khotibSelanjutnya = $querySelanjutnya->paginate(3);

        return view('public.jadwal-khotib', compact('khotibJumatIni', 'khotibSelanjutnya'));
    }

    public function jadwalKajian()
    {
        // Ambil kajian yang tanggalnya >= hari ini
        $today = Carbon::today();

        $kajianEvent = Kajian::where('tipe', 'event')
            ->whereDate('tanggal_kajian', '>=', now())
            ->orderBy('tanggal_kajian', 'asc')
            ->get();

        // 2. Ambil Kajian Rutin (JANGAN Cek Tanggal, ambil semua)
        $kajianRutin = Kajian::where('tipe', 'rutin')
            // Bisa diurutkan berdasarkan hari atau created_at
            ->orderBy('created_at', 'desc') 
            ->paginate(3); // Sesuaikan pagination

        return view('public.jadwal-kajian', compact('kajianEvent', 'kajianRutin'));
    }
    
    public function artikel()
    {
        // Ambil artikel yang statusnya 'published' (atau ambil semua jika belum pakai status)
        // Urutkan dari yang terbaru
        $artikel = Artikel::where('status_artikel', 'published')
                    ->orderBy('tanggal_terbit_artikel', 'desc')
                    ->paginate(3); // 6 artikel per halaman

        // Kita perlu memproses sinopsis (potongan teks pendek) dari isi_artikel
        // agar tampilan card rapi
        $artikel->getCollection()->transform(function ($item) {
            // Hapus tag HTML dan ambil 100 karakter pertama
            $item->sinopsis = Str::limit(strip_tags($item->isi_artikel), 120);
            return $item;
        });

        return view('public.artikel', compact('artikel'));
    }

    public function getArtikelDetail($id)
    {
        $artikel = Artikel::findOrFail($id);
        
        // Format tanggal biar cantik di JS
        $artikel->formatted_date = \Carbon\Carbon::parse($artikel->tanggal_terbit_artikel)->translatedFormat('d F Y');
        
        // Pastikan URL foto dikirim (menggunakan accessor dari Model)
        $artikel->foto_url_lengkap = $artikel->foto_artikel; 

        return response()->json($artikel);
    }

    public function program()
    {
        // 1. Data untuk Slider: Ambil 5 program yang AKAN DATANG (terdekat)
        // Diurutkan ascending (yang paling dekat tanggalnya muncul duluan)
        $sliderPrograms = Program::where('tanggal_program', '>=', Carbon::now())
                            ->orderBy('tanggal_program', 'asc')
                            ->take(5)
                            ->get();

        // 2. Data untuk List Bawah: Ambil SEMUA program
        // Diurutkan descending (yang paling baru inputnya/tanggalnya di atas)
        // Menggunakan pagination 9 item per halaman
        $semuaProgram = Program::orderBy('tanggal_program', 'desc')->paginate(3);

        return view('public.program', compact('sliderPrograms', 'semuaProgram'));
    }

    public function getProgramDetail($id)
    {
        $program = Program::findOrFail($id);
        
        // Format tanggal
        $program->tanggal_formatted = $program->tanggal_program->translatedFormat('l, d F Y');
        $program->waktu_formatted = $program->tanggal_program->format('H:i') . ' WIB';
        
        // Foto URL (sudah ada accessor di model, tapi kita pastikan di sini juga aman)
        $program->foto_url_lengkap = $program->foto_url; 

        // Status dengan format huruf kapital
        $program->status_label = ucwords($program->status_program);

        return response()->json($program);
    }

    public function donasi()
    {
        $today = Carbon::now()->startOfDay();

        // Ambil donasi yang BELUM berakhir atau UNLIMITED
        // Eager Load 'pemasukan' dengan filter status = 'success'
        $programDonasi = Donasi::withSum(['pemasukan' => function($query) {
                $query->where('status', 'success'); // <--- FILTER PENTING
            }], 'nominal')
            ->where(function($query) use ($today) {
                $query->whereDate('tanggal_selesai', '>=', $today)
                    ->orWhereNull('tanggal_selesai');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(4);

        $programDonasi->getCollection()->transform(function($item) {
            $target = $item->target_dana;
            // Ambil hasil sum yang sudah difilter
            $terkumpul = $item->pemasukan_sum_nominal ?? 0;
            
            $persentase = ($target > 0) ? round(($terkumpul / $target) * 100) : 0;
            
            $item->dana_terkumpul = $terkumpul;
            $item->persentase = min($persentase, 100);
            $item->persentase_asli = $persentase;
            
            $item->gambar_url = $item->foto_donasi 
                ? asset('storage/' . $item->foto_donasi) 
                : asset('images/donasi/default.jpg'); 

            return $item;
        });

        return view('public.donasi', compact('programDonasi'));
    }

    public function getDonasiDetail(Request $request, $id)
    {
        // 1. Ambil Detail Donasi & Sum Nominal (Hanya Success)
        $donasi = Donasi::withSum(['pemasukan' => function($query) {
            $query->where('status', 'success'); // <--- FILTER PENTING
        }], 'nominal')->findOrFail($id);
        
        // 2. Ambil List Donatur / Pesan (Hanya Success)
        $donatur = $donasi->pemasukan()
                        ->where('status', 'success') // <--- FILTER PENTING: Hanya tampilkan yang lunas
                        ->whereNotNull('pesan')
                        ->where('pesan', '!=', '')
                        ->where('pesan', '!=', '-') // Filter pesan default dummy
                        ->orderBy('created_at', 'desc')
                        ->paginate(9);

        // Transformasi Data Donatur (Avatar)
        $donatur->getCollection()->transform(function ($item) {
            $item->avatar_url = $item->avatar_url; 
            return $item;
        });

        $sisaHari = $donasi->sisa_hari;
        
        return response()->json([
            'id_donasi' => $donasi->id_donasi,
            'nama_donasi' => $donasi->nama_donasi,
            'deskripsi' => $donasi->deskripsi,
            'foto_url' => $donasi->foto_donasi ? asset('storage/' . $donasi->foto_donasi) : asset('images/donasi/default.jpg'),
            'target_dana' => $donasi->target_dana,
            'terkumpul' => $donasi->pemasukan_sum_nominal ?? 0, // Ini sekarang hanya menghitung yang sukses
            'persentase' => ($donasi->target_dana > 0) ? min(100, round(($donasi->pemasukan_sum_nominal / $donasi->target_dana) * 100)) : 0,
            'sisa_hari' => $sisaHari,
            'tanggal_selesai_fmt' => $donasi->tanggal_selesai ? Carbon::parse($donasi->tanggal_selesai)->translatedFormat('d F Y') : 'Tanpa Batas Waktu',
            'donatur' => $donatur 
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

    public function tentangKami()
    {
        // Ambil data profil masjid
        $masjidSettings = \App\Models\MasjidProfil::first();

        // Fallback jika data kosong (untuk mencegah error view)
        if (!$masjidSettings) {
            $masjidSettings = new \App\Models\MasjidProfil();
            $masjidSettings->nama_masjid = 'Nama Masjid';
            $masjidSettings->lokasi_nama = 'Alamat Belum Diisi';
        }

        return view('public.tentang-kami', compact('masjidSettings'));
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
