<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class LapKeuController extends Controller
{
    public function index()
    {
        return view('lapkeu');
    }

    public function exportPdf(Request $request)
    {
        // 1. Ambil semua input filter
        $tipe = $request->input('tipe_transaksi', 'semua');
        $periode = $request->input('periode', 'semua');
        
        // Ambil input integer
        $bulan = (int)$request->input('bulan');
        $tahun_bulanan = (int)$request->input('tahun_bulanan');
        $tahun_tahunan = (int)$request->input('tahun_tahunan');
        
        // 1. (BARU) Ambil input rentang tanggal
        $tanggal_mulai = $request->input('tanggal_mulai');
        $tanggal_akhir = $request->input('tanggal_akhir');

        // 2. Siapkan query builder
        $pemasukanQuery = Pemasukan::query();
        $pengeluaranQuery = Pengeluaran::query();

        // 3. Variabel untuk judul PDF
        $periodeTeks = "Semua Periode";

        // 4. Terapkan FILTER PERIODE
        if ($periode == 'per_bulan' && $bulan && $tahun_bulanan) {
            $pemasukanQuery->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun_bulanan);
            $pengeluaranQuery->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun_bulanan);
            
            $namaBulan = Carbon::create()->month($bulan)->locale('id')->monthName;
            $periodeTeks = "Periode: " . $namaBulan . " " . $tahun_bulanan;

        } elseif ($periode == 'per_tahun' && $tahun_tahunan) {
            $pemasukanQuery->whereYear('tanggal', $tahun_tahunan);
            $pengeluaranQuery->whereYear('tanggal', $tahun_tahunan);
            $periodeTeks = "Periode: Tahun " . $tahun_tahunan;

        // 2. (BARU) Tambahkan logika untuk rentang waktu
        } elseif ($periode == 'rentang_waktu' && $tanggal_mulai && $tanggal_akhir) {
            // Gunakan whereBetween untuk query rentang tanggal
            $pemasukanQuery->whereBetween('tanggal', [$tanggal_mulai, $tanggal_akhir]);
            $pengeluaranQuery->whereBetween('tanggal', [$tanggal_mulai, $tanggal_akhir]);
            
            // Format teks periode untuk PDF
            $periodeTeks = "Periode: " . 
                           Carbon::parse($tanggal_mulai)->locale('id')->translatedFormat('d F Y') . " - " . 
                           Carbon::parse($tanggal_akhir)->locale('id')->translatedFormat('d F Y');
        }
        
        // 5. Terapkan FILTER TIPE TRANSAKSI
        $pemasukanData = collect();
        $pengeluaranData = collect();

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

        // 7. Siapkan data untuk PDF view
        $data = [
            'pemasukanData' => $pemasukanData,
            'pengeluaranData' => $pengeluaranData,
            'totalPemasukan' => $totalPemasukan,
            'totalPengeluaran' => $totalPengeluaran,
            'saldo' => $saldo,
            'tipe' => $tipe,
            'periodeTeks' => $periodeTeks,
            'tanggalCetak' => now()->locale('id')->translatedFormat('d F Y')
        ];
        
        // 8. Load view PDF
        $pdf = Pdf::loadView('lapkeu_pdf', $data); 
        return $pdf->download('laporan-keuangan-' . time() . '.pdf');
    }
}