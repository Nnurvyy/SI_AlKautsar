<?php

namespace App\Http\Controllers;
use App\Models\TabunganHewanQurban;
use App\Models\PemasukanTabunganQurban;
use App\Models\Jamaah;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class TabunganHewanQurbanController extends Controller
{
    /**
     * Menampilkan halaman index (view)
     */
    public function index(Request $request)
    {
        $jamaahList = Jamaah::orderBy('name')->get();

        return view('tabungan-qurban', [
            'jamaahList' => $jamaahList
        ]);
    }

    /**
     * Menyediakan data JSON untuk tabel
     */
    public function data(Request $request)
    {
        $status = $request->query('status', 'semua');
        $perPage = $request->query('perPage', 10);
        $sortBy = $request->query('sortBy', 'total_terkumpul');
        $sortDir = $request->query('sortDir', 'desc');

        // Subquery total terkumpul
        $totalTerkumpulSubquery = PemasukanTabunganQurban::select(DB::raw('COALESCE(SUM(nominal), 0)'))
            ->whereColumn('id_tabungan_hewan_qurban', 'tabungan_hewan_qurban.id_tabungan_hewan_qurban');

        $query = TabunganHewanQurban::with('jamaah')
            ->select('tabungan_hewan_qurban.*')
            ->selectSub($totalTerkumpulSubquery, 'total_terkumpul')
            // Menghitung Angsuran Bulanan untuk tabungan 'cicilan' (TETAP)
            ->selectRaw("
                CASE
                    WHEN saving_type = 'cicilan' AND duration_months IS NOT NULL AND duration_months > 0
                    THEN ROUND(total_harga_hewan_qurban / duration_months)
                    ELSE 0
                END AS installment_amount
            ");

        // Catatan: Kolom 'current_status' yang menggunakan strftime dihapus dari query utama.

        // --- LOGIKA FILTER BARU (Lebih Sederhana) ---
        $query->when($status == 'lunas', function ($q) {
            $q->whereRaw('(SELECT COALESCE(SUM(nominal), 0) FROM pemasukan_tabungan_qurban WHERE id_tabungan_hewan_qurban = tabungan_hewan_qurban.id_tabungan_hewan_qurban) >= total_harga_hewan_qurban');
        })->when($status != 'lunas' && $status != 'semua', function($q) use ($status) {
            // Karena kita tidak bisa menghitung akumulasi target di query untuk semua DB,
            // kita hanya akan filter berdasarkan tipe untuk 'menunggak' dan 'mencicil'.
            // Logika tunggakan sesungguhnya akan dihitung setelah data ditarik.
            if ($status == 'menunggak') {
                $q->where('saving_type', 'cicilan')
                    ->whereRaw('(SELECT COALESCE(SUM(nominal), 0) FROM pemasukan_tabungan_qurban WHERE id_tabungan_hewan_qurban = tabungan_hewan_qurban.id_tabungan_hewan_qurban) < total_harga_hewan_qurban');

            } elseif ($status == 'mencicil') {
                $q->whereRaw('(SELECT COALESCE(SUM(nominal), 0) FROM pemasukan_tabungan_qurban WHERE id_tabungan_hewan_qurban = tabungan_hewan_qurban.id_tabungan_hewan_qurban) < total_harga_hewan_qurban')
                    ->where(function($q2) {
                        $q2->where('saving_type', 'bebas')
                            ->orWhere('saving_type', 'cicilan');
                    });
            }
        });


        // Sorting
        $allowedSorts = ['total_terkumpul', 'created_at', 'nama_hewan', 'total_harga_hewan_qurban'];
        if (!in_array($sortBy, $allowedSorts)) $sortBy = 'total_terkumpul';
        $sortDir = $sortDir === 'asc' ? 'asc' : 'desc';

        if ($sortBy == 'total_terkumpul') {
            // Order by raw subquery result
            $data = $query->orderByRaw('total_terkumpul ' . $sortDir)->paginate($perPage);
        } else {
            $data = $query->orderBy($sortBy, $sortDir)->paginate($perPage);
        }

        // --- PENGHITUNGAN STATUS DI SISI PHP SEBELUM DIKIRIM KE FRONTEND ---
        // Ini memastikan logika tunggakan akumulasi berjalan di semua database
        $data->getCollection()->transform(function ($item) {
            $totalTerkumpul = (float)$item->total_terkumpul;
            $totalHarga = (float)$item->total_harga_hewan_qurban;
            $installmentAmount = (float)$item->installment_amount;

            if ($totalTerkumpul >= $totalHarga) {
                $item->current_status = 'lunas';
                return $item;
            }

            if ($item->saving_type === 'bebas') {
                $item->current_status = 'mencicil'; // Untuk "Bebas" dianggap mencicil/aktif
                return $item;
            }

            // Logika Tunggakan Akumulasi (Hanya untuk tipe 'cicilan' yang belum lunas)
            if ($item->saving_type === 'cicilan' && $installmentAmount > 0) {
                $tanggalMulai = Carbon::parse($item->created_at);
                $tanggalHariIni = Carbon::now();

                // Hitung bulan berlalu (termasuk bulan ini)
                $monthsPassed = $tanggalHariIni->diffInMonths($tanggalMulai) + 1;
                $monthsPassed = min($monthsPassed, $item->duration_months);

                $accumulatedTarget = $installmentAmount * $monthsPassed;

                if ($totalTerkumpul < $accumulatedTarget) {
                    $item->current_status = 'menunggak';
                } else {
                    $item->current_status = 'mencicil';
                }
            } else {
                // Default jika tidak cicilan atau cicilan 0
                $item->current_status = 'mencicil';
            }

            return $item;
        });

        return response()->json($data);
    }

    // ... (Metode store, show, update, destroy, cetakPdf TIDAK BERUBAH) ...

    /**
     * Menyimpan data baru
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'id_jamaah' => 'required|exists:jamaah,id',
            'nama_hewan' => 'required|string',
            'total_hewan' => 'required|integer|min:1',
            'total_harga_hewan_qurban' => 'required|numeric|min:0',
            // --- VALIDASI BARU ---
            'saving_type' => 'required|in:bebas,cicilan',
            'duration_months' => 'nullable|integer|min:1',
            // --- AKHIR VALIDASI BARU ---
        ]);

        // Atur duration_months jika saving_type adalah bebas
        if ($validatedData['saving_type'] === 'bebas') {
            $validatedData['duration_months'] = null;
        } elseif ($validatedData['saving_type'] === 'cicilan' && empty($validatedData['duration_months'])) {
            // Pastikan duration_months ada jika tipe adalah cicilan
            return response()->json(['message' => 'Durasi cicilan wajib diisi.'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $validatedData['id_tabungan_hewan_qurban'] = (string) Str::uuid();
        $validatedData['total_tabungan'] = 0; // Tetap 0, dihitung dari pemasukan

        $tabungan = TabunganHewanQurban::create($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Tabungan qurban berhasil ditambahkan.',
            'data' => $tabungan
        ], Response::HTTP_CREATED);
    }

    /**
     * Menampilkan data spesifik (Show)
     */
    public function show(TabunganHewanQurban $tabungan_qurban)
    {
        $tabungan_qurban->load(['jamaah', 'pemasukanTabunganQurban' => function($query) {
            $query->orderBy('tanggal', 'desc');
        }]);

        // Hitung cicilan bulanan di sini juga untuk modal detail
        $installmentAmount = 0;
        if ($tabungan_qurban->saving_type === 'cicilan' && $tabungan_qurban->duration_months > 0) {
            $installmentAmount = round($tabungan_qurban->total_harga_hewan_qurban / $tabungan_qurban->duration_months);
        }
        $tabungan_qurban->installment_amount = $installmentAmount;

        return response()->json($tabungan_qurban);
    }

    /**
     * Update data
     */
    public function update(Request $request, TabunganHewanQurban $tabungan_qurban)
    {
        $validatedData = $request->validate([
            'id_jamaah' => 'required|exists:jamaah,id',
            'nama_hewan' => 'required|string',
            'total_hewan' => 'required|integer|min:1',
            'total_harga_hewan_qurban' => 'required|numeric|min:0',
            // --- VALIDASI BARU ---
            'saving_type' => 'required|in:bebas,cicilan',
            'duration_months' => 'nullable|integer|min:1',
            // --- AKHIR VALIDASI BARU ---
        ]);

        // Atur duration_months jika saving_type adalah bebas
        if ($validatedData['saving_type'] === 'bebas') {
            $validatedData['duration_months'] = null;
        } elseif ($validatedData['saving_type'] === 'cicilan' && empty($validatedData['duration_months'])) {
            // Pastikan duration_months ada jika tipe adalah cicilan
            return response()->json(['message' => 'Durasi cicilan wajib diisi.'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $tabungan_qurban->update($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Tabungan qurban berhasil diperbarui.',
            'data' => $tabungan_qurban
        ]);
    }

    /**
     * Hapus data
     */
    public function destroy(TabunganHewanQurban $tabungan_qurban)
    {
        $tabungan_qurban->pemasukanTabunganQurban()->delete();
        $tabungan_qurban->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data tabungan berhasil dihapus.'
        ]);
    }

    /**
     * Cetak PDF (Tidak ada perubahan signifikan)
     */
    public function cetakPdf(Request $request)
    {
        $periode = $request->get('periode', 'semua');
        $bulan = $request->get('bulan', date('m'));
        $tahun_bulanan = $request->get('tahun_bulanan', date('Y'));
        $tahun_tahunan = $request->get('tahun_tahunan', date('Y'));
        $tanggal_mulai = $request->get('tanggal_mulai', date('Y-m-01'));
        $tanggal_akhir = $request->get('tanggal_akhir', date('Y-m-d'));

        $query = PemasukanTabunganQurban::with(['tabunganHewanQurban.jamaah'])
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

        $pdf = Pdf::loadView('laporan_qurban_pdf', $data);
        return $pdf->stream('laporan-tabungan-qurban-' . time() . '.pdf');
    }
}
