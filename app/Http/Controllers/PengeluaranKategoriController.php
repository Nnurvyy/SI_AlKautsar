<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KategoriPengeluaran;

class PengeluaranKategoriController extends Controller
{
    public function index()
    {
        $kategori = KategoriPengeluaran::latest()->paginate(10);
        return view('kategori_pengeluaran.index', compact('kategori'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori_pengeluaran' => 'required|string|max:100|unique:kategori_pengeluaran,nama_kategori_pengeluaran',
        ], [
            'nama_kategori_pengeluaran.required' => 'Nama kategori wajib diisi.',
            'nama_kategori_pengeluaran.unique' => 'Nama kategori ini sudah ada.',
        ]);

        $kategori = KategoriPengeluaran::create([
            'nama_kategori_pengeluaran' => $request->nama_kategori_pengeluaran,
        ]);

        // Jika Request AJAX (dari Modal)
        if ($request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Kategori berhasil ditambahkan.',
                'data' => $kategori
            ]);
        }

        return back()->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $kategori = KategoriPengeluaran::findOrFail($id);
        $request->validate([
            'nama_kategori_pengeluaran' => 'required|string|max:100|unique:kategori_pengeluaran,nama_kategori_pengeluaran,' . $id . ',id_kategori_pengeluaran',
        ]);

        $kategori->update(['nama_kategori_pengeluaran' => $request->nama_kategori_pengeluaran]);

        return back()->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $kategori = KategoriPengeluaran::findOrFail($id);
        
        if ($kategori->pengeluaran()->exists()) { // Pastikan relasi model bernama 'pengeluaran'
             return back()->with('error', 'Kategori tidak bisa dihapus karena sedang digunakan.');
        }

        $kategori->delete();

        return back()->with('success', 'Kategori berhasil dihapus.');
    }

    public function create() { return view('kategori_pengeluaran.create'); }
    public function edit($id) {
        $kategori = KategoriPengeluaran::findOrFail($id);
        return view('kategori_pengeluaran.edit', compact('kategori'));
    }
}