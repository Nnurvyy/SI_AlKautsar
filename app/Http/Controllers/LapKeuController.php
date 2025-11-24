<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Keuangan; // Menggunakan Model Keuangan
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class LapKeuController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil Query dasar dengan filter
        $query = $this->getFilteredQuery($request);

        // 2. Hitung Statistik (Berdasarkan data yang difilter)
        // Kita clone query agar perhitungan total tidak terganggu oleh pagination nanti
        $statsQuery = $query->clone()->get();

        $totalPemasukan = $statsQuery->where('tipe', 'pemasukan')->sum('nominal');
        $totalPengeluaran = $statsQuery->where('tipe', 'pengeluaran')->sum('nominal');
        $saldo = $totalPemasukan - $totalPengeluaran;

        // 3. Ambil Data Transaksi dengan Pagination (7 per halaman)
        // Urutkan dari tanggal terbaru
        $transaksi = $query->orderBy('tanggal', 'desc')->paginate(7);

        // 4. Kirim data ke View
        return view('lapkeu', compact(
            'transaksi',
            'totalPemasukan',
            'totalPengeluaran',
            'saldo'
        ));
    }

    public function exportPdf(Request $request)
    {
        // 1. Ambil Query dasar dengan filter yang sama
        $query = $this->getFilteredQuery($request);

        // 2. Ambil semua data (tanpa pagination untuk PDF)
        $transaksi = $query->orderBy('tanggal', 'asc')->get();

        // 3. Hitung Total
        $totalPemasukan = $transaksi->where('tipe', 'pemasukan')->sum('nominal');
        $totalPengeluaran = $transaksi->where('tipe', 'pengeluaran')->sum('nominal');
        $saldo = $totalPemasukan - $totalPengeluaran;

        // 4. Siapkan teks Periode untuk judul PDF
        $periodeTeks = $this->getPeriodeText($request);

        // 5. Siapkan data array
        $data = [
            'transaksi' => $transaksi, // Dikirim sebagai satu variabel transaksi
            'totalPemasukan' => $totalPemasukan,
            'totalPengeluaran' => $totalPengeluaran,
            'saldo' => $saldo,
            'periodeTeks' => $periodeTeks,
            'tanggalCetak' => now()->locale('id')->translatedFormat('d F Y')
        ];
        
        $pdf = Pdf::loadView('lapkeu_pdf', $data); // Pastikan view PDF juga disesuaikan nanti
        return $pdf->download('laporan-keuangan-' . time() . '.pdf');
    }

    /**
     * Logika Filter dipisah agar bisa dipakai di Index dan Export PDF
     */
    private function getFilteredQuery(Request $request)
    {
        // Load relasi kategori agar tidak N+1 problem
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