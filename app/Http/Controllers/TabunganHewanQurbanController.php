<?php

namespace App\Http\Controllers;

use App\Models\TabunganHewanQurban;
use App\Models\DetailTabunganHewanQurban;
use App\Models\PemasukanTabunganQurban;
use App\Models\HewanQurban;
use App\Models\Jamaah;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\MasjidProfil; // Pastikan model ini ada untuk kop surat PDF

class TabunganHewanQurbanController extends Controller
{
    /**
     * Menampilkan halaman index admin
     */
    public function index() {
        // Data master hewan & jamaah untuk modal tambah manual oleh admin
        $hewanList = HewanQurban::where('is_active', true)->get();
        $jamaahList = Jamaah::orderBy('name')->get();
        return view('tabungan-qurban', compact('hewanList', 'jamaahList'));
    }

    /**
     * Menyediakan data JSON untuk DataTable (Server Side)
     */
    public function data(Request $request)
    {
        $statusTabungan = $request->query('status_tabungan', 'semua');
        $statusSetoran  = $request->query('status_setoran', 'semua');
        $tipeTabungan   = $request->query('tipe_tabungan', 'semua'); // [BARU]
        $searchNama     = $request->query('search_nama', '');        // [BARU]
        
        $perPage = $request->query('perPage', 10);
        $sortBy  = $request->query('sortBy', 'created_at');
        $sortDir = $request->query('sortDir', 'desc');

        $tblTabungan = (new TabunganHewanQurban)->getTable();
        $tblPemasukan = (new PemasukanTabunganQurban)->getTable();

        // Query Utama
        $query = TabunganHewanQurban::with(['jamaah', 'details.hewan']);

        // [BARU] Filter Pencarian Nama Jamaah
        if (!empty($searchNama)) {
            $query->whereHas('jamaah', function($q) use ($searchNama) {
                $q->where('name', 'like', '%' . $searchNama . '%');
            });
        }

        // [BARU] Filter Tipe Tabungan (Cicilan / Bebas)
        if ($tipeTabungan !== 'semua') {
            $query->where('saving_type', $tipeTabungan);
        }

        // Filter Status Tabungan
        if ($statusTabungan !== 'semua') {
            $query->where('status', $statusTabungan);
        }

        // Filter Status Setoran (Logic Sama seperti sebelumnya)
        if ($statusSetoran !== 'semua') {
            if ($statusSetoran == 'lunas') {
                $query->whereRaw("(SELECT COALESCE(SUM(nominal), 0) FROM $tblPemasukan WHERE $tblPemasukan.id_tabungan_hewan_qurban = $tblTabungan.id_tabungan_hewan_qurban) >= $tblTabungan.total_harga_hewan_qurban");
                $query->where('status', 'disetujui');
            } elseif ($statusSetoran == 'menunggak') {
                $query->where('saving_type', 'cicilan')
                      ->where('status', 'disetujui')
                      ->whereRaw("(SELECT COALESCE(SUM(nominal), 0) FROM $tblPemasukan WHERE $tblPemasukan.id_tabungan_hewan_qurban = $tblTabungan.id_tabungan_hewan_qurban) < $tblTabungan.total_harga_hewan_qurban");
            } elseif ($statusSetoran == 'aktif') {
                 $query->where(function($q) {
                     $q->where('saving_type', 'bebas')->orWhere('saving_type', 'cicilan');
                 })->where('status', 'disetujui');
            }
        }

        // Hitung total terkumpul & Sorting (Sama seperti sebelumnya)
        $query->withSum('pemasukanTabunganQurban as total_terkumpul', 'nominal');

        if ($sortBy == 'total_terkumpul') {
            $query->orderBy('total_terkumpul', $sortDir);
        } else if ($sortBy == 'created_at') {
            $query->orderBy("$tblTabungan.created_at", $sortDir);
        } else {
            $query->orderBy($sortBy, $sortDir);
        }

        $data = $query->paginate($perPage);

        // 4. Transformasi Data (Logika Menunggak Detail)
        $data->getCollection()->transform(function ($item) {
            $item->terkumpul = (float) ($item->total_terkumpul ?? 0);
            $item->total_harga = (float) $item->total_harga_hewan_qurban;
            
            if($item->saving_type == 'cicilan' && $item->duration_months > 0) {
                $item->installment_amount = round($item->total_harga / $item->duration_months);
            } else {
                $item->installment_amount = 0;
            }

            if ($item->terkumpul >= $item->total_harga) {
                $item->finance_status = 'lunas';
                $item->finance_label = 'Lunas';
            } elseif ($item->saving_type == 'bebas') {
                $item->finance_status = 'lancar';
                $item->finance_label = 'Aktif (Bebas)';
            } elseif ($item->saving_type == 'cicilan' && $item->status == 'disetujui') {
                $tglBuat = Carbon::parse($item->tanggal_pembuatan);
                $sekarang = Carbon::now();
                $diffMonth = (($sekarang->year - $tglBuat->year) * 12) + ($sekarang->month - $tglBuat->month);
                
                if ($diffMonth > 0) {
                    $targetIdeal = $item->installment_amount * $diffMonth;
                    if ($item->terkumpul < $targetIdeal) {
                        $item->finance_status = 'menunggak';
                        $item->finance_label = 'Menunggak';
                    } else {
                        $item->finance_status = 'lancar';
                        $item->finance_label = 'Aktif / Lancar';
                    }
                } else {
                    $item->finance_status = 'lancar';
                    $item->finance_label = 'Aktif (Baru)';
                }
            } else {
                $item->finance_status = 'pending'; 
                $item->finance_label = '-';
            }
            return $item;
        });

        return response()->json($data);
    }
    /**
     * Menyimpan data baru (Admin create manual)
     */
    public function store(Request $request)
    {
        // Validasi
        $request->validate([
            'id_jamaah' => 'required|exists:jamaah,id',
            'saving_type' => 'required|in:bebas,cicilan',
            'duration_months' => 'nullable|integer|min:1',
            'hewan_items' => 'required|array',
            'hewan_items.*.id_hewan' => 'required|exists:hewan_qurban,id_hewan_qurban',
            'hewan_items.*.jumlah' => 'required|integer|min:1',
            // VALIDASI TAMBAHAN: Harga Total Manual
            'total_harga_hewan_qurban' => 'required|numeric|min:0'
        ]);

        DB::beginTransaction();
        try {
            $uuid = (string) Str::uuid();
            
            // Hitung kalkulasi sistem (hanya untuk detail, tapi header pakai input admin)
            $systemTotal = 0; 

            // Buat Header
            // PENTING: Gunakan 'total_harga_hewan_qurban' dari REQUEST (Deal-dealan Admin)
            $tabungan = TabunganHewanQurban::create([
                'id_tabungan_hewan_qurban' => $uuid,
                'id_jamaah' => $request->id_jamaah,
                'status' => 'disetujui',
                'saving_type' => $request->saving_type,
                'duration_months' => ($request->saving_type == 'cicilan') ? $request->duration_months : null,
                'total_tabungan' => 0,
                'tanggal_pembuatan' => now()->toDateString(), // Set tanggal hari ini
                'total_harga_hewan_qurban' => $request->total_harga_hewan_qurban // <--- PAKAI INPUT MANUAL
            ]);

            // Buat Details
            foreach ($request->hewan_items as $item) {
                $hewan = HewanQurban::find($item['id_hewan']);
                $subtotal = $hewan->harga_hewan * $item['jumlah'];
                $systemTotal += $subtotal;

                DetailTabunganHewanQurban::create([
                    'id_tabungan_hewan_qurban' => $uuid,
                    'id_hewan_qurban' => $hewan->id_hewan_qurban,
                    'jumlah_hewan' => $item['jumlah'],
                    'harga_per_ekor' => $hewan->harga_hewan,
                    'subtotal' => $subtotal
                ]);
            }
            
            // Note: systemTotal mungkin berbeda dengan request->total_harga_hewan_qurban
            // karena admin melakukan negosiasi/edit harga. Itu diperbolehkan.

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Tabungan berhasil dibuat dengan harga kesepakatan.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal menyimpan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Menampilkan data detail (Show) untuk Modal Admin
     */
    public function show($id)
    {
        $tabungan = TabunganHewanQurban::with(['jamaah', 'details.hewan', 'pemasukanTabunganQurban' => function($q) {
            $q->orderBy('tanggal', 'desc');
        }])->findOrFail($id);

        // Hitung cicilan
        $tabungan->installment_amount = 0;
        if ($tabungan->saving_type === 'cicilan' && $tabungan->duration_months > 0) {
            $tabungan->installment_amount = round($tabungan->total_harga_hewan_qurban / $tabungan->duration_months);
        }

        return response()->json($tabungan);
    }

    /**
     * Update Data Tabungan (Admin Edit)
     */
    public function update(Request $request, $id)
    {
        $tabungan = TabunganHewanQurban::findOrFail($id);

        $request->validate([
            'saving_type' => 'required|in:bebas,cicilan',
            'duration_months' => 'nullable|integer|min:1',
            'hewan_items' => 'required|array',
            'total_harga_hewan_qurban' => 'required|numeric|min:0' // Validasi Harga
        ]);

        DB::beginTransaction();
        try {
            // Update Header termasuk HARGA MANUAL
            $tabungan->update([
                'saving_type' => $request->saving_type,
                'duration_months' => ($request->saving_type == 'cicilan') ? $request->duration_months : null,
                'total_harga_hewan_qurban' => $request->total_harga_hewan_qurban, // <--- UPDATE HARGA MANUAL
            ]);

            // Hapus detail lama & insert ulang
            DetailTabunganHewanQurban::where('id_tabungan_hewan_qurban', $id)->delete();

            foreach ($request->hewan_items as $item) {
                $hewan = HewanQurban::find($item['id_hewan']);
                $subtotal = $hewan->harga_hewan * $item['jumlah'];

                DetailTabunganHewanQurban::create([
                    'id_tabungan_hewan_qurban' => $id,
                    'id_hewan_qurban' => $hewan->id_hewan_qurban,
                    'jumlah_hewan' => $item['jumlah'],
                    'harga_per_ekor' => $hewan->harga_hewan,
                    'subtotal' => $subtotal
                ]);
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Tabungan dan harga berhasil diperbarui.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal update: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update Status Approval (Menunggu -> Disetujui/Ditolak)
     */
    public function updateStatus(Request $request, $id) 
    {
        $request->validate(['status' => 'required|in:disetujui,ditolak']);
        
        $tabungan = TabunganHewanQurban::findOrFail($id);
        $tabungan->status = $request->status;
        $tabungan->save();

        return response()->json(['success' => true, 'message' => 'Status tabungan berhasil diperbarui menjadi ' . $request->status]);
    }

    /**
     * Hapus Data Tabungan
     */
    public function destroy($id)
    {
        $tabungan = TabunganHewanQurban::findOrFail($id);
        // Cascade delete akan menghapus details & pemasukan (pastikan migration foreign key benar)
        // Jika tidak, hapus manual:
        // $tabungan->details()->delete();
        // $tabungan->pemasukanTabunganQurban()->delete();
        $tabungan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data tabungan berhasil dihapus.'
        ]);
    }

    /**
     * Cetak PDF Laporan Keuangan Qurban
     * Menyesuaikan dengan relasi One-to-Many (Detail Hewan)
     */
    public function cetakPdf(Request $request)
    {
        $periode = $request->get('periode', 'semua');
        $bulan = $request->get('bulan', date('m'));
        $tahun_bulanan = $request->get('tahun_bulanan', date('Y'));
        $tahun_tahunan = $request->get('tahun_tahunan', date('Y'));
        $tanggal_mulai = $request->get('tanggal_mulai', date('Y-m-01'));
        $tanggal_akhir = $request->get('tanggal_akhir', date('Y-m-d'));

        // Query Pemasukan
        // Penting: Load relasi details.hewan agar di PDF bisa loop nama hewan
        $query = PemasukanTabunganQurban::with(['tabunganHewanQurban.jamaah', 'tabunganHewanQurban.details.hewan'])
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

        // Ambil profil masjid untuk kop surat
        $settings = MasjidProfil::first(); 
        // Fallback dummy jika null agar PDF tidak error
        if(!$settings) {
            $settings = (object) ['nama_masjid' => 'Masjid Kita', 'alamat' => 'Alamat Masjid'];
        }

        $data = [
            'pemasukanData' => $pemasukanData,
            'totalPemasukan' => $totalPemasukan,
            'periodeTeks' => $periodeTeks,
            'tanggalCetak' => $tanggalCetak,
            'settings' => $settings
        ];

        $pdf = Pdf::loadView('laporan_qurban_pdf', $data);
        // Set paper landscape agar muat detail hewan jika panjang
        $pdf->setPaper('a4', 'landscape'); 
        
        return $pdf->stream('laporan-tabungan-qurban-' . time() . '.pdf');
    }
}