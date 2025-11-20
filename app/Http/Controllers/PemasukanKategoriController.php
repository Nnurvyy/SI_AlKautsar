<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KategoriPemasukan;

class PemasukanKategoriController extends Controller
{
    public function index()
    {
        $kategori = KategoriPemasukan::latest()->paginate(10);
        return view('kategori_pemasukan.index', compact('kategori'));
    }

    public function store(Request $request)
    {
        // Validasi
        $request->validate([
            'nama_kategori_pemasukan' => 'required|string|max:100|unique:kategori_pemasukan,nama_kategori_pemasukan',
        ], [
            'nama_kategori_pemasukan.required' => 'Nama kategori wajib diisi.',
            'nama_kategori_pemasukan.unique' => 'Nama kategori ini sudah ada.',
        ]);

        // Simpan Data
        $kategori = KategoriPemasukan::create([
            'nama_kategori_pemasukan' => $request->nama_kategori_pemasukan,
        ]);

        // PENTING: Cek apakah request dari AJAX (Modal JS)?
        if ($request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Kategori berhasil ditambahkan.',
                'data' => $kategori
            ]);
        }

        // Jika bukan AJAX (Fallback)
        return back()->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $kategori = KategoriPemasukan::findOrFail($id);
        
        $request->validate([
            'nama_kategori_pemasukan' => 'required|string|max:100|unique:kategori_pemasukan,nama_kategori_pemasukan,' . $id . ',id_kategori_pemasukan',
        ]);

        $kategori->update(['nama_kategori_pemasukan' => $request->nama_kategori_pemasukan]);

        return back()->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $kategori = KategoriPemasukan::findOrFail($id);
        
        if ($kategori->pemasukan()->exists()) {
             return back()->with('error', 'Kategori tidak bisa dihapus karena sedang digunakan.');
        }

        $kategori->delete();

        return back()->with('success', 'Kategori berhasil dihapus.');
    }

    // Method tambahan (biarkan saja)
    public function create() { return view('kategori_pemasukan.create'); }
    public function edit($id) {
        $kategori = KategoriPemasukan::findOrFail($id);
        return view('kategori_pemasukan.edit', compact('kategori'));
    }
}