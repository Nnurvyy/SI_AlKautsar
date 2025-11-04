<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TabunganHewanQurban;
use App\Models\PemasukanTabunganQurban;
use App\Models\Pengguna; // Import model Pengguna
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use Yajra\DataTables\DataTables;

class TabunganHewanQurbanController extends Controller
{
    /**
     * Menampilkan halaman index (view)
     * (Sebelumnya di TabunganQurbanAdminController)
     */
    public function index(Request $request)
    {
        // Ambil semua pengguna untuk dropdown di modal tambah
        $pengguna = Pengguna::where('role', 'publik')->orderBy('nama')->get();

        return view('tabungan-qurban', [
            'penggunaList' => $pengguna
        ]);
    }

    /**
     * Menyediakan data JSON untuk DataTables
     * (Baru, mengikuti pola Khotib Jumat)
     */
    public function data()
    {
        // Ambil data dengan relasi pengguna dan pemasukan
        $query = TabunganHewanQurban::with(['pengguna', 'pemasukanTabunganQurban'])
            // TAMBAHKAN JOIN INI
            ->join('pengguna', 'pengguna.id_pengguna', '=', 'tabungan_hewan_qurban.id_pengguna')
            ->select('tabungan_hewan_qurban.*');// Penting untuk DataTables

        return DataTables::of($query)
            ->addIndexColumn() // Tambah kolom nomor urut
            ->editColumn('nama_user', function ($row) {
                return $row->pengguna->nama ?? 'N/A';
            })
            ->editColumn('total_hewan', function ($row) {
                return $row->total_hewan . ' ekor';
            })
            ->editColumn('total_harga', function ($row) {
                return 'Rp ' . number_format($row->total_harga_hewan_qurban, 0, ',', '.');
            })
            ->editColumn('total_terkumpul', function ($row) {
                // Hitung total terkumpul dari relasi pemasukan
                $terkumpul = $row->pemasukanTabunganQurban->sum('nominal');
                return 'Rp ' . number_format($terkumpul, 0, ',', '.');
            })
            ->addColumn('sisa_target', function ($row) {
                $terkumpul = $row->pemasukanTabunganQurban->sum('nominal');
                $sisa = $row->total_harga_hewan_qurban - $terkumpul;
                $color = $sisa <= 0 ? 'success' : 'danger';
                return '<span class="text-'.$color.'">Rp ' . number_format($sisa, 0, ',', '.') . '</span>';
            })
            ->addColumn('aksi', function ($row) {
                // Buat tombol aksi
                $btnDetail = '<button class="btn btn-info btn-sm btn-detail" title="Lihat Detail" data-id="' . $row->id_tabungan_hewan_qurban . '"><i class="fas fa-eye"></i></button>';
                $btnUpdate = '<button class="btn btn-warning btn-sm btn-edit" title="Update Tabungan" data-id="' . $row->id_tabungan_hewan_qurban . '"><i class="fas fa-edit"></i></button>';
                $btnDelete = '<button class="btn btn-danger btn-sm btn-delete" title="Hapus Tabungan" data-id="' . $row->id_tabungan_hewan_qurban . '"><i class="fas fa-trash"></i></button>';
                return $btnDetail . ' ' . $btnUpdate . ' ' . $btnDelete;
            })
            ->rawColumns(['sisa_target', 'aksi']) // Kolom yang mengandung HTML
            ->make(true);
    }

    /**
     * Menyimpan data tabungan baru. (Return JSON)
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'id_pengguna' => 'required|string|exists:pengguna,id_pengguna',
            'nama_hewan' => 'required|string|max:20',
            'total_hewan' => 'required|integer|min:1',
            'total_harga_hewan_qurban' => 'required|numeric|min:0',
        ]);

        // Total tabungan awal adalah 0
        $validatedData['total_tabungan'] = 0; // Kolom ini ada di model Anda

        $tabungan = TabunganHewanQurban::create($validatedData);

        return response()->json($tabungan, Response::HTTP_CREATED);
    }

    /**
     * Menampilkan data spesifik untuk modal edit/detail. (Return JSON)
     */
    public function show(string $id)
    {
        // Ambil data lengkap dengan relasi
        $tabungan = TabunganHewanQurban::with(['pengguna', 'pemasukanTabunganQurban' => function($query) {
            $query->orderBy('tanggal', 'desc'); // Urutkan setoran terbaru
        }])->findOrFail($id);

        return response()->json($tabungan);
    }

    /**
     * Update data tabungan. (Return JSON)
     */
    public function update(Request $request, string $id)
    {
        $tabungan = TabunganHewanQurban::findOrFail($id);

        $validatedData = $request->validate([
            'id_pengguna' => 'required|string|exists:pengguna,id_pengguna',
            'nama_hewan' => 'required|string|max:20',
            'total_hewan' => 'required|integer|min:1',
            'total_harga_hewan_qurban' => 'required|numeric|min:0',
        ]);

        $tabungan->update($validatedData);

        return response()->json($tabungan);
    }

    /**
     * Hapus data tabungan. (Return JSON)
     */
    public function destroy(string $id)
    {
        $tabungan = TabunganHewanQurban::findOrFail($id);

        $tabungan->delete(); // Relasi pemasukan akan terhapus (jika di-set cascade di DB)

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
