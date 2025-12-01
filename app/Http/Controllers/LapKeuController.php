<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Keuangan; 
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class LapKeuController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil Query dasar dengan filter (Logic dipisah biar rapi)
        $query = $this->getFilteredQuery($request);

        // 2. Hitung Statistik (Pemasukan, Pengeluaran, Saldo)
        // Kita clone query agar perhitungan total sesuai dengan filter yang dipilih user
        $statsQuery = $query->clone()->get();

        $totalPemasukan = $statsQuery->where('tipe', 'pemasukan')->sum('nominal');
        $totalPengeluaran = $statsQuery->where('tipe', 'pengeluaran')->sum('nominal');
        $saldo = $totalPemasukan - $totalPengeluaran;

        // 3. Ambil Data Transaksi dengan Pagination
        // Urutkan dari tanggal terbaru, 10 data per halaman
        $transaksi = $query->orderBy('tanggal', 'desc')->paginate(10);

        // --- PERUBAHAN UTAMA DI SINI ---
        
        // 4. Cek apakah Request adalah AJAX (dari fetch JS)
        if ($request->ajax() || $request->wantsJson()) {
            // Jika AJAX, kembalikan JSON
            // Kita gabungkan data Pagination ($transaksi) dengan data Statistik
            return response()->json(array_merge(
                $transaksi->toArray(), // Mengubah object pagination jadi array (data, links, current_page, dll)
                [
                    'stat_pemasukan' => $totalPemasukan,
                    'stat_pengeluaran' => $totalPengeluaran,
                    'stat_saldo' => $saldo,
                ]
            ));
        }

        // 5. Jika bukan AJAX (Akses pertama kali buka halaman), kembalikan View
        // Pastikan nama view sesuai folder kamu, misal: 'pengurus.laporan_keuangan.index' atau 'lapkeu'
        return view('lapkeu', compact(
            'totalPemasukan',
            'totalPengeluaran',
            'saldo'
            // Kita tidak perlu kirim $transaksi ke view di sini, 
            // karena tabel akan diload otomatis oleh JS saat halaman terbuka.
        ));
    }

    public function exportPdf(Request $request)
    {
        // 1. Ambil Query dasar dengan filter yang sama
        $query = $this->getFilteredQuery($request);

        // 2. Ambil semua data (Urutkan terlama ke terbaru untuk laporan cetak)
        $transaksi = $query->orderBy('tanggal', 'asc')->get();

        // 3. Hitung Total & Pisahkan Data di level Collection (Hemat Query DB)
        $pemasukanData = $transaksi->where('tipe', 'pemasukan');
        $pengeluaranData = $transaksi->where('tipe', 'pengeluaran');

        $totalPemasukan = $pemasukanData->sum('nominal');
        $totalPengeluaran = $pengeluaranData->sum('nominal');
        $saldo = $totalPemasukan - $totalPengeluaran;

        // 4. Ambil input filter untuk judul laporan
        $tipe = $request->input('tipe_transaksi', 'semua');
        $periodeTeks = $this->getPeriodeText($request);

        // 5. Siapkan data array untuk View PDF
        $data = [
            'tipe' => $tipe,
            'pemasukanData' => $pemasukanData,
            'pengeluaranData' => $pengeluaranData,
            'totalPemasukan' => $totalPemasukan,
            'totalPengeluaran' => $totalPengeluaran,
            'saldo' => $saldo,
            'periodeTeks' => $periodeTeks,
            'tanggalCetak' => now()->locale('id')->translatedFormat('d F Y')
        ];
        
        // Pastikan nama view PDF sesuai file kamu
        $pdf = Pdf::loadView('lapkeu_pdf', $data); // Sesuaikan path view pdf
        return $pdf->stream('laporan-keuangan-' . time() . '.pdf'); // stream() agar preview dulu, download() untuk langsung unduh
    }

    /**
     * Helper: Logika Filter Query
     */
    private function getFilteredQuery(Request $request)
    {
        // Load relasi kategori untuk efisiensi
        $query = Keuangan::with('kategori');

        // A. Filter Tipe Transaksi
        $tipe = $request->input('tipe_transaksi', 'semua');
        if ($tipe == 'pemasukan') {
            $query->where('tipe', 'pemasukan');
        } elseif ($tipe == 'pengeluaran') {
            $query->where('tipe', 'pengeluaran');
        }

        // B. Filter Periode
        $periode = $request->input('periode', 'semua');
        $bulan = (int)$request->input('bulan');
        $tahun_bulanan = (int)$request->input('tahun_bulanan');
        $tahun_tahunan = (int)$request->input('tahun_tahunan');
        $tanggal_mulai = $request->input('tanggal_mulai');
        $tanggal_akhir = $request->input('tanggal_akhir');

        if ($periode == 'per_bulan' && $bulan && $tahun_bulanan) {
            $query->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun_bulanan);
        } elseif ($periode == 'per_tahun' && $tahun_tahunan) {
            $query->whereYear('tanggal', $tahun_tahunan);
        } elseif ($periode == 'rentang_waktu' && $tanggal_mulai && $tanggal_akhir) {
            $query->whereBetween('tanggal', [$tanggal_mulai, $tanggal_akhir]);
        }

        return $query;
    }

    /**
     * Helper: Generate Text Periode untuk PDF
     */
    private function getPeriodeText(Request $request)
    {
        $periode = $request->input('periode', 'semua');
        
        if ($periode == 'per_bulan') {
            $bulan = (int)$request->input('bulan');
            $tahun = $request->input('tahun_bulanan');
            return "Periode: " . Carbon::create()->month($bulan)->locale('id')->monthName . " " . $tahun;
        } elseif ($periode == 'per_tahun') {
            return "Periode: Tahun " . $request->input('tahun_tahunan');
        } elseif ($periode == 'rentang_waktu') {
            $start = Carbon::parse($request->input('tanggal_mulai'))->translatedFormat('d F Y');
            $end = Carbon::parse($request->input('tanggal_akhir'))->translatedFormat('d F Y');
            return "Periode: $start - $end";
        }
        
        return "Semua Periode";
    }
}