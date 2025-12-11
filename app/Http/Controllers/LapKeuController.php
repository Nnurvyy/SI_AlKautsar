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

        $query = $this->getFilteredQuery($request);



        $statsQuery = $query->clone()->get();

        $totalPemasukan = $statsQuery->where('tipe', 'pemasukan')->sum('nominal');
        $totalPengeluaran = $statsQuery->where('tipe', 'pengeluaran')->sum('nominal');
        $saldo = $totalPemasukan - $totalPengeluaran;



        $transaksi = $query->orderBy('tanggal', 'desc')->paginate(10);




        if ($request->ajax() || $request->wantsJson()) {


            return response()->json(array_merge(
                $transaksi->toArray(),
                [
                    'stat_pemasukan' => $totalPemasukan,
                    'stat_pengeluaran' => $totalPengeluaran,
                    'stat_saldo' => $saldo,
                ]
            ));
        }



        return view('lapkeu', compact(
            'totalPemasukan',
            'totalPengeluaran',
            'saldo'


        ));
    }

    public function exportPdf(Request $request)
    {

        $query = $this->getFilteredQuery($request);


        $transaksi = $query->orderBy('tanggal', 'asc')->get();


        $pemasukanData = $transaksi->where('tipe', 'pemasukan');
        $pengeluaranData = $transaksi->where('tipe', 'pengeluaran');

        $totalPemasukan = $pemasukanData->sum('nominal');
        $totalPengeluaran = $pengeluaranData->sum('nominal');
        $saldo = $totalPemasukan - $totalPengeluaran;


        $tipe = $request->input('tipe_transaksi', 'semua');
        $periodeTeks = $this->getPeriodeText($request);


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


        $pdf = Pdf::loadView('lapkeu_pdf', $data);
        return $pdf->stream('laporan-keuangan-' . time() . '.pdf');
    }

    private function getFilteredQuery(Request $request)
    {

        $query = Keuangan::with('kategori');


        $tipe = $request->input('tipe_transaksi', 'semua');
        if ($tipe == 'pemasukan') {
            $query->where('tipe', 'pemasukan');
        } elseif ($tipe == 'pengeluaran') {
            $query->where('tipe', 'pengeluaran');
        }


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
