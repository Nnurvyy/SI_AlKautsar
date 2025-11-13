<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KhotibJumat;
use Carbon\Carbon;
use App\Models\Kajian; // Kita tetap import, tapi tidak dipakai di jadwalKajian

class PublicController extends Controller
{
    public function landingPage()
    {
        return view('landing-page');
    }

    /**
     * Menampilkan halaman list Jadwal Khotib Jumat
     * Rute: /jadwal-khotib
     */
    public function jadwalKhotib()
    {
        // Ini tetap mengambil dari DB karena migrasinya sudah ada
        $jadwalKhotib = KhotibJumat::where('tanggal', '>=', Carbon::today())
                                    ->orderBy('tanggal', 'asc')
                                    ->get();
        
        // Fallback dummy data jika DB khotib kosong
        if ($jadwalKhotib->isEmpty()) {
            $jadwalKhotib = collect([
                (object)['foto_url' => asset('images/events/abdul-somad.jpg'), 'nama_khotib' => 'Ustadz Abdul Somad', 'nama_imam' => 'Ustadz Fulan', 'tema_khutbah' => 'Pentingnya Ukhuwah', 'tanggal' => '2025-11-14'],
                (object)['foto_url' => asset('images/events/aagym.jpg'), 'nama_khotib' => 'KH. Abdullah Gymnastiar', 'nama_imam' => 'Ustadz Fulan', 'tema_khutbah' => 'Manajemen Waktu', 'tanggal' => '2025-11-21'],
            ]);
        }

        return view('public.jadwal-khotib', [
            'jadwalKhotib' => $jadwalKhotib
        ]);
    }


    /**
     * Menampilkan halaman list Jadwal Kajian (Event & Rutin)
     * Rute: /jadwal-kajian
     */
    public function jadwalKajian()
    {
        // (PERBAIKAN) Hapus query ke DB karena kolom 'tipe_kajian' belum ada
        // $kajianEvent = Kajian::where('tipe_kajian', 'event')...
        // $kajianRutin = Kajian::where('tipe_kajian', 'rutin')...

        // (BARU) Gunakan data dummy SEMENTARA, sesuai permintaan
        $kajianEvent = collect([
            (object)['foto_url' => asset('images/events/hannan-attaki.jpeg'), 'nama_penceramah' => 'Ustadz Hannan Attaki', 'tema_kajian' => 'Kajian Akbar', 'tanggal_kajian' => '2025-11-10', 'waktu_kajian' => '19:00'],
            (object)['foto_url' => asset('images/events/adi-hidayat.jpg'), 'nama_penceramah' => 'Ustadz Adi Hidayat', 'tema_kajian' => 'Kajian Spesial', 'tanggal_kajian' => '2025-11-12', 'waktu_kajian' => '19:00'],
        ]);
        
        $kajianRutin = collect([
            (object)['foto_url' => asset('images/events/abdul-somad.jpg'), 'nama_penceramah' => 'Ustadz Abdul Somad', 'tema_kajian' => 'Tafsir Al-Quran', 'tanggal_kajian' => '2025-11-09', 'waktu_kajian' => '05:00'],
            (object)['foto_url' => asset('images/events/aagym.jpg'), 'nama_penceramah' => 'KH. Abdullah Gymnastiar', 'tema_kajian' => 'Manajemen Qolbu', 'tanggal_kajian' => '2025-11-11', 'waktu_kajian' => '18:30'],
        ]);

        return view('public.jadwal-kajian', [
            'kajianEvent' => $kajianEvent,
            'kajianRutin' => $kajianRutin
        ]);
    }
    
    /**
     * (BARU) Menampilkan halaman Artikel
     * Rute: /artikel
     */
    public function artikel()
    {
        // (BARU) Gunakan data dummy SEMENTARA
        $artikel = collect([
            (object)['foto_url' => asset('images/artikel/artikel1.jpg'), 'judul' => 'Artikel Pertama', 'created_at' => '2025-11-07'],
            (object)['foto_url' => asset('images/artikel/artikel2.jpg'), 'judul' => 'Artikel Kedua', 'created_at' => '2025-11-06'],
            (object)['foto_url' => asset('images/artikel/artikel3.jpg'), 'judul' => 'Artikel Ketiga', 'created_at' => '2025-11-05'],
        ]);
        return view('public.artikel', compact('artikel'));
    }
    
    /**
     * (BARU) Menampilkan halaman Donasi
     * Rute: /donasi
     */
    public function donasi()
    {
        // (BARU) Gunakan data dummy SEMENTARA
        $programDonasi = collect([
            (object)['foto_url' => asset('images/donasi/pembangunan-masjid.jpg'), 'judul' => 'PEMBANGUNAN MASJID', 'deskripsi' => 'Bantu perluasan dan renovasi masjid...'],
            (object)['foto_url' => asset('images/donasi/yatim.jpeg'), 'judul' => 'YATIM & DHUAFA', 'deskripsi' => 'Salurkan sedekah Anda untuk...'],
            (object)['foto_url' => 'https://via.placeholder.com/800x600/4682B4/ffffff?text=Operasional+Masjid', 'judul' => 'OPERASIONAL MASJID', 'deskripsi' => 'Dukung kegiatan dakwah...'],
        ]);
        return view('public.donasi', compact('programDonasi'));
    }

    /**
     * (BARU) Menampilkan halaman Program
     * Rute: /program
     */
    public function program()
    {
        // (BARU) Gunakan data dummy SEMENTARA
        $program = collect([
            (object)['foto_url' => 'https://via.placeholder.com/800x600/27ae60/ffffff?text=Workshop+1', 'judul' => 'Workshop Manajemen Masjid Digital', 'tanggal' => '2025-11-15'],
            (object)['foto_url' => 'https://via.placeholder.com/800x600/27ae60/ffffff?text=Program+2', 'judul' => 'Pelatihan Pengurusan Jenazah', 'tanggal' => '2025-11-22'],
        ]);
        return view('public.program', compact('program'));
    }

    
    public function jadwalShalatApi(Request $request)
    {
        return response()->json(['message' => 'Layanan API Jadwal Shalat (Contoh)']);
    }

    public function tabunganQurbanSaya()
    {
        // (DIUBAH) Ganti Auth::user() dengan nama dummy
        $namaUser = 'Muhammad Fulan (Dummy)';

        // Data dummy untuk hewan qurban
        $hewanQurban = [
            (object)[
                'jenis' => 'kambing',
                'status' => 'Menabung',
                'target' => 3500000,
                'terkumpul' => 3000000,
                'riwayatSetoran' => [
                    (object)['tanggal' => '2025-01-10', 'nominal' => 1000000],
                    (object)['tanggal' => '2025-02-15', 'nominal' => 1000000],
                    (object)['tanggal' => '2025-03-05', 'nominal' => 1000000],
                ]
            ],
            (object)[
                'jenis' => 'sapi',
                'status' => 'Menabung',
                'target' => 3500000, // Asumsi 1/7 Sapi
                'terkumpul' => 500000,
                'riwayatSetoran' => [
                    (object)['tanggal' => '2025-03-01', 'nominal' => 500000],
                ]
            ],
            (object)[
                'jenis' => 'kambing',
                'status' => 'Lunas',
                'target' => 3000000,
                'terkumpul' => 3000000,
                'riwayatSetoran' => [
                    (object)['tanggal' => '2024-10-10', 'nominal' => 1000000],
                    (object)['tanggal' => '2024-11-10', 'nominal' => 1000000],
                    (object)['tanggal' => '2024-12-10', 'nominal' => 1000000],
                    (object)['tanggal' => '2025-01-10', 'nominal' => 500000],
                    (object)['tanggal' => '2025-01-15', 'nominal' => 500000],
                ]
            ]
        ];

        // Hitung total tabungan dari data dummy di atas
        $totalTabungan = collect($hewanQurban)->sum('terkumpul');

        // Kirim semua data ke view
        return view('public.tabungan-qurban-saya', compact(
            'namaUser',
            'totalTabungan',
            'hewanQurban'
        ));
    }
}