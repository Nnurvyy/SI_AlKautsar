<?php

namespace App\Http\Controllers;

use App\Models\Keuangan;
use App\Models\KategoriKeuangan;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

class KeuanganController extends Controller
{
    public function index()
    {
        // Load view awal, data akan di-fetch via AJAX
        return view('keuangan');
    }

    public function data(Request $request)
    {
        // 1. Ambil Query Dasar (Filter Deskripsi, Tipe, Tanggal)
        $query = $this->getFilteredQuery($request);

        // 2. Tentukan Batas Tanggal untuk Saldo Awal
        $periode = $request->input('periode', 'semua');
        $batasTanggal = null;

        if ($periode == 'per_bulan') {
            $bulan = (int)$request->input('bulan');
            $tahun = (int)$request->input('tahun_bulanan');
            $batasTanggal = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth();
        } elseif ($periode == 'per_tahun') {
            $tahun = (int)$request->input('tahun_tahunan');
            $batasTanggal = Carbon::createFromDate($tahun, 1, 1)->startOfYear();
        } elseif ($periode == 'rentang_waktu') {
            $batasTanggal = Carbon::parse($request->input('tanggal_mulai'))->startOfDay();
        }

        // --- LOGIKA BARU: TENTUKAN SALDO AWAL ---
        $saldoAwal = 0;
        $tipeFilter = $request->input('tipe_transaksi'); // Ambil filter tipe

        // Saldo Awal hanya relevan jika kita melihat "Semua" tipe (Buku Besar).
        // Jika user hanya melihat "Pemasukan", Saldo Awal harus 0 agar totalnya murni.
        if (($tipeFilter == 'semua' || empty($tipeFilter)) && $batasTanggal) {
            $masukLalu = Keuangan::where('tipe', 'pemasukan')->where('tanggal', '<', $batasTanggal)->sum('nominal');
            $keluarLalu = Keuangan::where('tipe', 'pengeluaran')->where('tanggal', '<', $batasTanggal)->sum('nominal');
            $saldoAwal = $masukLalu - $keluarLalu;
        }
        
        // 3. AMBIL SEMUA DATA (URUT DARI LAMA KE BARU) UNTUK CALCULATE RUNNING BALANCE
        $allData = $query->orderBy('tanggal', 'asc')->orderBy('created_at', 'asc')->get();

        // 4. LOOPING PHP UNTUK MENEMPELKAN SALDO
        $runningBalance = $saldoAwal;
        $totalPemasukan = 0;
        $totalPengeluaran = 0;

        $allData->transform(function($item) use (&$runningBalance, &$totalPemasukan, &$totalPengeluaran, $tipeFilter) {
            // Hitung Total Statistik (Murni dari data yang tampil)
            if ($item->tipe == 'pemasukan') {
                $totalPemasukan += $item->nominal;
            } else {
                $totalPengeluaran += $item->nominal;
            }

            // Hitung Saldo Berjalan
            // Jika filter 'semua', saldo berjalan naik turun (tambah/kurang)
            // Jika filter spesifik (misal pemasukan doang), saldo hanya akumulasi
            if ($tipeFilter == 'semua' || empty($tipeFilter)) {
                if ($item->tipe == 'pemasukan') {
                    $runningBalance += $item->nominal;
                } else {
                    $runningBalance -= $item->nominal;
                }
            } else {
                // Jika filter spesifik, Saldo Berjalan = Akumulasi Nominal (Running Total)
                $runningBalance += $item->nominal;
            }
            
            $item->saldo_berjalan_formatted = $runningBalance;
            return $item;
        });

        // 5. MANUAL PAGINATION & SORTING DESCENDING (TERBARU DI ATAS)
        $sortedData = $allData->sortByDesc(function ($item) {
            return $item->tanggal . $item->created_at;
        })->values();

        $page = $request->input('page', 1);
        $perPage = 10;
        $offset = ($page * $perPage) - $perPage;

        $paginatedItems = new LengthAwarePaginator(
            $sortedData->slice($offset, $perPage)->values(),
            $sortedData->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // 6. RETURN JSON
        // Jika filter spesifik, Saldo Akhir = Total Nominal (tidak pakai rumus masuk - keluar)
        if ($tipeFilter == 'semua' || empty($tipeFilter)) {
            $saldoAkhirReal = $saldoAwal + ($totalPemasukan - $totalPengeluaran);
        } else {
            // Kalau filter Pemasukan doang, Saldo Akhir = Total Pemasukan
            $saldoAkhirReal = ($tipeFilter == 'pemasukan') ? $totalPemasukan : $totalPengeluaran;
        }

        return response()->json([
            'table_data' => $paginatedItems,
            'stats' => [
                'pemasukan' => $totalPemasukan,
                'pengeluaran' => $totalPengeluaran,
                'saldo' => $saldoAkhirReal
            ]
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'tipe' => 'required|in:pemasukan,pengeluaran',
            'tanggal' => 'required|date',
            'id_kategori_keuangan' => 'required|exists:kategori_keuangan,id_kategori_keuangan',
            'nominal' => 'required|numeric|min:1',
            'deskripsi' => 'nullable|string|max:255',
        ]);

        Keuangan::create($request->all());
        return response()->json(['message' => 'Transaksi berhasil disimpan.']);
    }

    public function show($id)
    {
        return response()->json(Keuangan::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $keuangan = Keuangan::findOrFail($id);
        $request->validate([
            'tipe' => 'required|in:pemasukan,pengeluaran',
            'tanggal' => 'required|date',
            'id_kategori_keuangan' => 'required|exists:kategori_keuangan,id_kategori_keuangan',
            'nominal' => 'required|numeric|min:1',
            'deskripsi' => 'nullable|string|max:255',
        ]);

        $keuangan->update($request->all());
        return response()->json(['message' => 'Transaksi berhasil diperbarui.']);
    }

    public function destroy($id)
    {
        Keuangan::findOrFail($id)->delete();
        return response()->json(['message' => 'Transaksi berhasil dihapus.']);
    }

    // --- FITUR EXPORT PDF (Pindahan dari LapKeu) ---
    public function exportPdf(Request $request)
    {
        // 1. Ambil Query Dasar (Filtered)
        $query = $this->getFilteredQuery($request);
        
        // PENTING: Data PDF harus urut dari tanggal TERLAMA ke TERBARU agar saldo berjalan urut
        $transaksi = $query->orderBy('tanggal', 'asc')->orderBy('created_at', 'asc')->get();

        // 2. Tentukan Batas Tanggal (Untuk hitung saldo awal)
        $periode = $request->input('periode', 'semua');
        $batasTanggal = null;

        if ($periode == 'per_bulan') {
            $bulan = (int)$request->input('bulan');
            $tahun = (int)$request->input('tahun_bulanan');
            $batasTanggal = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth();
        } elseif ($periode == 'per_tahun') {
            $tahun = (int)$request->input('tahun_tahunan');
            $batasTanggal = Carbon::createFromDate($tahun, 1, 1)->startOfYear();
        } elseif ($periode == 'rentang_waktu') {
            $batasTanggal = Carbon::parse($request->input('tanggal_mulai'))->startOfDay();
        }

        // 3. LOGIKA SALDO AWAL (SAMA SEPERTI FUNCTION DATA)
        $saldoAwal = 0;
        $tipeFilter = $request->input('tipe_transaksi'); // Ambil filter tipe

        // Saldo Awal HANYA dihitung jika Tipe = 'semua' (Buku Besar).
        // Jika filter spesifik (hanya Pemasukan/Pengeluaran), Saldo Awal = 0.
        if (($tipeFilter == 'semua' || empty($tipeFilter)) && $batasTanggal) {
            $masukLalu = Keuangan::where('tipe', 'pemasukan')->where('tanggal', '<', $batasTanggal)->sum('nominal');
            $keluarLalu = Keuangan::where('tipe', 'pengeluaran')->where('tanggal', '<', $batasTanggal)->sum('nominal');
            $saldoAwal = $masukLalu - $keluarLalu;
        }

        // 4. LOOPING UNTUK MENEMPELKAN SALDO BERJALAN & HITUNG TOTAL
        // Kita hitung di sini supaya View PDF tinggal menampilkan saja (lebih aman)
        $runningBalance = $saldoAwal;
        $totalPemasukan = 0;
        $totalPengeluaran = 0;

        $transaksi->transform(function($item) use (&$runningBalance, &$totalPemasukan, &$totalPengeluaran, $tipeFilter) {
            // Hitung Total Statistik
            if ($item->tipe == 'pemasukan') {
                $totalPemasukan += $item->nominal;
            } else {
                $totalPengeluaran += $item->nominal;
            }

            // Hitung Saldo Berjalan Per Baris
            if ($tipeFilter == 'semua' || empty($tipeFilter)) {
                // Logika Buku Besar (Masuk - Keluar)
                if ($item->tipe == 'pemasukan') {
                    $runningBalance += $item->nominal;
                } else {
                    $runningBalance -= $item->nominal;
                }
            } else {
                // Logika Filter Spesifik (Akumulasi Murni)
                $runningBalance += $item->nominal;
            }
            
            // Simpan hasil hitungan ke object item
            $item->saldo_berjalan_formatted = $runningBalance;
            return $item;
        });

        // 5. HITUNG SALDO AKHIR REAL (UNTUK SUMMARY CARD)
        if ($tipeFilter == 'semua' || empty($tipeFilter)) {
            $saldoAkhir = $saldoAwal + ($totalPemasukan - $totalPengeluaran);
        } else {
            // Jika filter Pemasukan, Saldo Akhir = Total Pemasukan
            $saldoAkhir = ($tipeFilter == 'pemasukan') ? $totalPemasukan : $totalPengeluaran;
        }

        $data = [
            'transaksi' => $transaksi,
            'saldoAwal' => $saldoAwal,
            'totalPemasukan' => $totalPemasukan,
            'totalPengeluaran' => $totalPengeluaran,
            'saldo' => $saldoAkhir,
            'periodeTeks' => $this->getPeriodeText($request),
            'tanggalCetak' => now()->locale('id')->translatedFormat('d F Y'),
            'tipe' => $tipeFilter ?? 'semua', // Kirim tipe filter ke view
        ];

        $pdf = Pdf::loadView('lapkeu_pdf', $data);
        return $pdf->setPaper('a4', 'landscape')->stream('laporan-keuangan.pdf');
    }

    // --- HELPER FILTER QUERY ---
    private function getFilteredQuery(Request $request)
    {
        $query = Keuangan::with('kategori');

        // 1. Filter Search (Deskripsi)
        if ($request->has('search') && $request->search != '') {
            $query->where('deskripsi', 'ILIKE', "%{$request->search}%");
        }

        // 2. Filter Tipe
        $tipe = $request->input('tipe_transaksi'); 
        // Note: Input di view nanti pakai 'tipe_transaksi' agar beda dengan 'tipe' saat create/edit
        if ($tipe == 'pemasukan') {
            $query->where('tipe', 'pemasukan');
        } elseif ($tipe == 'pengeluaran') {
            $query->where('tipe', 'pengeluaran');
        }

        // 3. Filter Periode Waktu
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
        return "Semua Waktu";
    }
}