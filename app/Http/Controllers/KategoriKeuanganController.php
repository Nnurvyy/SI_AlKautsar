<?php

namespace App\Http\Controllers;

use App\Models\KategoriKeuangan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class KategoriKeuanganController extends Controller
{
    // Ambil data kategori berdasarkan tipe (untuk list di modal & dropdown)
    public function data(Request $request)
    {
        $tipe = $request->query('tipe'); // 'pemasukan' atau 'pengeluaran'
        $data = KategoriKeuangan::where('tipe', $tipe)->orderBy('nama_kategori_keuangan')->get();
        return response()->json($data);
    }

    // Simpan Kategori Baru
    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori_keuangan' => 'required|string|max:100',
            'tipe' => 'required|in:pemasukan,pengeluaran'
        ]);

        // ID otomatis generate di Model (boot method)
        $kategori = KategoriKeuangan::create($request->all());

        return response()->json(['message' => 'Kategori berhasil ditambah.', 'data' => $kategori]);
    }

    // Update Kategori
    public function update(Request $request, $id)
    {
        $kategori = KategoriKeuangan::findOrFail($id);
        
        $request->validate([
            'nama_kategori_keuangan' => 'required|string|max:100',
        ]);

        $kategori->update(['nama_kategori_keuangan' => $request->nama_kategori_keuangan]);

        return response()->json(['message' => 'Kategori berhasil diperbarui.']);
    }

    // Hapus Kategori
    public function destroy($id)
    {
        $kategori = KategoriKeuangan::findOrFail($id);
        $kategori->delete();

        return response()->json(['message' => 'Kategori berhasil dihapus.']);
    }
}