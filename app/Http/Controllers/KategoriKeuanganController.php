<?php

namespace App\Http\Controllers;

use App\Models\KategoriKeuangan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule; // Tambahkan ini untuk validasi unique

class KategoriKeuanganController extends Controller
{
    // Ambil data kategori (Otomatis difilter JS berdasarkan tipe halaman)
    public function data(Request $request)
    {
        $query = KategoriKeuangan::query();

        // Jika ada parameter tipe (pemasukan/pengeluaran), filter datanya
        if ($request->has('tipe')) {
            $query->where('tipe', $request->query('tipe'));
        }

        $data = $query->orderBy('nama_kategori_keuangan', 'asc')->get();
        return response()->json($data);
    }

    // Simpan Kategori Baru
    public function store(Request $request)
    {
        $request->validate([
            'tipe' => 'required|in:pemasukan,pengeluaran',
            'nama_kategori_keuangan' => [
                'required',
                'string',
                'max:100',
                // Cek agar nama unik berdasarkan tipenya
                Rule::unique('kategori_keuangan')->where(function ($query) use ($request) {
                    return $query->where('tipe', $request->tipe);
                }),
            ],
        ]);

        $kategori = KategoriKeuangan::create($request->all());

        return response()->json(['message' => 'Kategori berhasil ditambah.', 'data' => $kategori]);
    }

    // Update Kategori
    public function update(Request $request, $id)
    {
        $kategori = KategoriKeuangan::findOrFail($id);
        
        $request->validate([
            'nama_kategori_keuangan' => [
                'required', 
                'string', 
                'max:100',
                // Cek unik tapi abaikan ID dirinya sendiri
                Rule::unique('kategori_keuangan')->where(function ($query) use ($kategori) {
                    return $query->where('tipe', $kategori->tipe);
                })->ignore($kategori->id_kategori_keuangan, 'id_kategori_keuangan'),
            ],
        ]);

        $kategori->update(['nama_kategori_keuangan' => $request->nama_kategori_keuangan]);

        return response()->json(['message' => 'Kategori berhasil diperbarui.']);
    }

    // Hapus Kategori
    public function destroy($id)
    {
        $kategori = KategoriKeuangan::findOrFail($id);
        
        // Opsional: Cek apakah kategori sedang dipakai di transaksi sebelum hapus
        // if($kategori->keuangan()->exists()) { ... return error ... }

        $kategori->delete();

        return response()->json(['message' => 'Kategori berhasil dihapus.']);
    }
}