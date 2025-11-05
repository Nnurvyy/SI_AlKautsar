<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pemasukan;      // <-- 1. Import Model
use App\Models\Pengeluaran;    // <-- 2. Import Model
use Barryvdh\DomPDF\Facade\Pdf;  // <-- 3. Import PDF
use Carbon\Carbon; // <-- Pastikan Carbon di-import

class LapKeuController extends Controller
{
    public function index()
    {
        return view('lapkeu');
    }

    // FUNGSI BARU UNTUK EXPORT
    public function exportPdf(Request $request)
    {
        // 1. Ambil semua input filter dari URL
        $tipe = $request->input('tipe_transaksi', 'semua');
        $periode = $request->input('periode', 'semua');
        
        // --- PERBAIKAN DI SINI: Ubah input menjadi integer ---
        $bulan = (int)$request->input('bulan');
        $tahun_bulanan = (int)$request->input('tahun_bulanan');
        $tahun_tahunan = (int)$request->input('tahun_tahunan');
        // --- AKHIR PERBAIKAN ---

        // 2. Siapkan query builder
        $pemasukanQuery = Pemasukan::query();
        $pengeluaranQuery = Pengeluaran::query();

        // 3. Variabel untuk judul PDF
        $periodeTeks = "Semua Periode";

        // 4. Terapkan FILTER PERIODE
        if ($periode == 'per_bulan' && $bulan && $tahun_bulanan) {
            $pemasukanQuery->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun_bulanan);
            $pengeluaranQuery->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun_bulanan);
            
            // Konversi bulan (angka) ke nama bulan
            // Variabel $bulan sekarang sudah pasti integer
            $namaBulan = Carbon::create()->month($bulan)->locale('id')->monthName;
            $periodeTeks = "Periode: " . $namaBulan . " " . $tahun_bulanan;

        } elseif ($periode == 'per_tahun' && $tahun_tahunan) {
            $pemasukanQuery->whereYear('tanggal', $tahun_tahunan);
            $pengeluaranQuery->whereYear('tanggal', $tahun_tahunan);
            $periodeTeks = "Periode: Tahun " . $tahun_tahunan;
        }
        
        // 5. Terapkan FILTER TIPE TRANSAKSI (setelah filter tanggal)
        $pemasukanData = collect(); // Buat koleksi kosong
        $pengeluaranData = collect(); // Buat koleksi kosong

        if ($tipe == 'pemasukan' || $tipe == 'semua') {
            $pemasukanData = $pemasukanQuery->orderBy('tanggal', 'asc')->get();
        }
        
        if ($tipe == 'pengeluaran' || $tipe == 'semua') {
            $pengeluaranData = $pengeluaranQuery->orderBy('tanggal', 'asc')->get();
        }

        // 6. Hitung Total
        $totalPemasukan = $pemasukanData->sum('nominal');
        $totalPengeluaran = $pengeluaranData->sum('nominal');
        $saldo = $totalPemasukan - $totalPengeluaran;

        // 7. Siapkan semua data untuk dikirim ke PDF view
        $data = [
            'pemasukanData' => $pemasukanData,
            'pengeluaranData' => $pengeluaranData,
            'totalPemasukan' => $totalPemasukan,
            'totalPengeluaran' => $totalPengeluaran,
            'saldo' => $saldo,
            'tipe' => $tipe, // Untuk logika di PDF
            'periodeTeks' => $periodeTeks,
            'tanggalCetak' => now()->locale('id')->translatedFormat('d F Y')
        ];
        
        // 8. Load view PDF dan kirim data, lalu download
        // Pastikan nama view-nya benar (lapkeu_pdf atau laporan_pdf)
        $pdf = Pdf::loadView('lapkeu_pdf', $data); 
        return $pdf->download('laporan-keuangan-' . time() . '.pdf');
    }
}
