<?php

namespace App\Http\Controllers;

// Gunakan Request baru
use App\Http\Requests\TabunganQurbanRequest;
use App\Models\TabunganHewanQurban;
use App\Models\PemasukanTabunganQurban;
use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class TabunganHewanQurbanController extends Controller
{
    /**
     * Menampilkan halaman index (view)
     */
    public function index(Request $request)
    {
        // Ambil pengguna untuk dropdown di modal
        $penggunaList = Pengguna::where('role', 'publik')->orderBy('nama')->get();

        return view('tabungan-qurban', [
            'penggunaList' => $penggunaList
        ]);
    }

    /**
     * Menyediakan data JSON untuk tabel (Pola KhotibJumatController)
     */
    public function data(Request $request)
    {
        $status = $request->query('status', 'semua');
        $perPage = $request->query('perPage', 10);
        $sortBy = $request->query('sortBy', 'total_terkumpul'); // Sort by total tabungan
        $sortDir = $request->query('sortDir', 'desc');

        // Subquery untuk total terkumpul
        $totalTerkumpulSubquery = PemasukanTabunganQurban::select(DB::raw('COALESCE(SUM(nominal), 0)'))
            ->whereColumn('id_tabungan_hewan_qurban', 'tabungan_hewan_qurban.id_tabungan_hewan_qurban');

        // Subquery untuk cek pembayaran bulan ini
        $startOfMonth = Carbon::now()->startOfMonth();
        $bayarBulanIniSubquery = PemasukanTabunganQurban::select(DB::raw(1))
            ->whereColumn('id_tabungan_hewan_qurban', 'tabungan_hewan_qurban.id_tabungan_hewan_qurban')
            ->where('tanggal', '>=', $startOfMonth)
            ->limit(1);

        $query = TabunganHewanQurban::with('pengguna')
            ->select('tabungan_hewan_qurban.*')
            ->selectSub($totalTerkumpulSubquery, 'total_terkumpul')
            ->selectSub($bayarBulanIniSubquery, 'bayar_bulan_ini');

        // Terapkan Filter Status (Sesuai konfirmasi Anda)
        $query->when($status == 'lunas', function ($q) {
            // 1. Lunas
            $q->whereRaw(
                '(SELECT COALESCE(SUM(nominal), 0) FROM pemasukan_tabungan_qurban WHERE id_tabungan_hewan_qurban = tabungan_hewan_qurban.id_tabungan_hewan_qurban) >= total_harga_hewan_qurban'
            );
        })->when($status == 'menunggak', function ($q) {
            // 2. Menunggak (Belum lunas DAN belum bayar bulan ini)
            $startOfMonth = Carbon::now()->startOfMonth();
            $q->whereRaw(
                '(SELECT COALESCE(SUM(nominal), 0) FROM pemasukan_tabungan_qurban WHERE id_tabungan_hewan_qurban = tabungan_hewan_qurban.id_tabungan_hewan_qurban) < total_harga_hewan_qurban'
            )->whereDoesntHave('pemasukanTabunganQurban', function($sq) use ($startOfMonth) {
                $sq->where('tanggal', '>=', $startOfMonth);
            });
        })->when($status == 'bayar_bulan_ini', function ($q) {
            // 3. Sudah Bayar Bulan Ini (Belum lunas TAPI sudah bayar bulan ini)
            $startOfMonth = Carbon::now()->startOfMonth();
            $q->whereRaw(
                '(SELECT COALESCE(SUM(nominal), 0) FROM pemasukan_tabungan_qurban WHERE id_tabungan_hewan_qurban = tabungan_hewan_qurban.id_tabungan_hewan_qurban) < total_harga_hewan_qurban'
            )->whereHas('pemasukanTabunganQurban', function($sq) use ($startOfMonth) {
                $sq->where('tanggal', '>=', $startOfMonth);
            });
        });

        // Urutkan data
        $allowedSorts = ['total_terkumpul', 'created_at', 'nama_hewan', 'total_harga_hewan_qurban'];
        if (!in_array($sortBy, $allowedSorts)) $sortBy = 'total_terkumpul';
        $sortDir = $sortDir === 'asc' ? 'asc' : 'desc';

        // Jika sorting berdasarkan 'total_terkumpul', kita harus pakai orderByRaw
        if ($sortBy == 'total_terkumpul') {
            $data = $query->orderByRaw('total_terkumpul ' . $sortDir)->paginate($perPage);
        } else {
            $data = $query->orderBy($sortBy, $sortDir)->paginate($perPage);
        }

        return response()->json($data);
    }

    /**
     * Menyimpan data tabungan baru.
     */
    public function store(TabunganQurbanRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['total_tabungan'] = 0; // Tetapkan default

        $tabungan = TabunganHewanQurban::create($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Tabungan qurban berhasil ditambahkan.',
            'data' => $tabungan
        ], Response::HTTP_CREATED);
    }

    /**
     * Menampilkan data spesifik untuk modal (termasuk riwayat setoran).
     */
    public function show(TabunganHewanQurban $tabungan_qurban)
    {
        // Muat relasi untuk modal detail
        $tabungan_qurban->load(['pengguna', 'pemasukanTabunganQurban' => function($query) {
            $query->orderBy('tanggal', 'desc');
        }]);

        return response()->json($tabungan_qurban);
    }

    /**
     * Update data tabungan.
     */
    public function update(TabunganQurbanRequest $request, TabunganHewanQurban $tabungan_qurban)
    {
        $validatedData = $request->validated();
        $tabungan_qurban->update($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Tabungan qurban berhasil diperbarui.',
            'data' => $tabungan_qurban
        ]);
    }

    /**
     * Hapus data tabungan.
     */
    public function destroy(TabunganHewanQurban $tabungan_qurban)
    {
        // Hapus juga semua pemasukan terkait (jika tidak di-set cascade on delete di DB)
        $tabungan_qurban->pemasukanTabunganQurban()->delete();
        $tabungan_qurban->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data tabungan berhasil dihapus.'
        ]);
    }

    /**
     * Membuat dan mengunduh laporan PDF (Pola LapKeuController)
     */
    public function cetakPdf(Request $request)
    {
        $periode = $request->get('periode', 'semua');
        $bulan = $request->get('bulan', date('m'));
        $tahun_bulanan = $request->get('tahun_bulanan', date('Y'));
        $tahun_tahunan = $request->get('tahun_tahunan', date('Y'));
        $tanggal_mulai = $request->get('tanggal_mulai', date('Y-m-01'));
        $tanggal_akhir = $request->get('tanggal_akhir', date('Y-m-d'));

        $query = PemasukanTabunganQurban::with(['tabunganHewanQurban.pengguna'])
            ->orderBy('tanggal', 'asc');

        $periodeTeks = 'Semua Periode';
        $tanggalCetak = Carbon::now()->translatedFormat('d F Y');

        switch ($periode) {
            case 'per_bulan':
                $query->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun_bulanan);
                $periodeTeks = 'Periode: ' . Carbon::create(null, $bulan)->translatedFormat('F') . ' ' . $tahun_bulanan;
                break;
            case 'per_tahun':
                $query->whereYear('tanggal', $tahun_tahunan);
                $periodeTeks = 'Periode: Tahun ' . $tahun_tahunan;
                break;
            case 'rentang_waktu':
                $query->whereBetween('tanggal', [$tanggal_mulai, $tanggal_akhir]);
                $tgl1 = Carbon::parse($tanggal_mulai)->translatedFormat('d M Y');
                $tgl2 = Carbon::parse($tanggal_akhir)->translatedFormat('d M Y');
                $periodeTeks = "Periode: $tgl1 s/d $tgl2";
                break;
        }

        $pemasukanData = $query->get();
        $totalPemasukan = $pemasukanData->sum('nominal');

        $data = [
            'pemasukanData' => $pemasukanData,
            'totalPemasukan' => $totalPemasukan,
            'periodeTeks' => $periodeTeks,
            'tanggalCetak' => $tanggalCetak
        ];

        // Memuat view dari 'resources/views/laporan_qurban_pdf.blade.php'
        $pdf = Pdf::loadView('laporan_qurban_pdf', $data);
        return $pdf->stream('laporan-tabungan-qurban-' . time() . '.pdf');
    }
}
