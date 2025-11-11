<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KategoriPemasukan;

class PemasukanKategoriController extends Controller
{
    /**
     * Menampilkan daftar kategori.
     * PERBAIKAN: Data $kategori harus tetap dikirim agar tidak error 'Undefined variable'.
     */
    public function index()
    {
        // Ambil data kategori untuk ditampilkan di halaman standalone (jika diakses)
        $kategori = KategoriPemasukan::latest()->paginate(10);

        return view('kategori_pemasukan.index', compact('kategori'));
    }

    /**
     * Menampilkan form create (halaman standalone).
     */
    public function create()
    {
        return view('kategori_pemasukan.create');
    }

    /**
     * Menyimpan data kategori baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori_pemasukan' => 'required|string|max:100|unique:kategori_pemasukan,nama_kategori_pemasukan',
        ], [
            'nama_kategori_pemasukan.required' => 'Nama kategori wajib diisi.',
            'nama_kategori_pemasukan.unique' => 'Nama kategori ini sudah ada.',
        ]);

        KategoriPemasukan::create([
            'nama_kategori_pemasukan' => $request->nama_kategori_pemasukan,
        ]);

        // Gunakan back() agar user kembali ke tempat dia mengklik tombol (bisa dari Modal Pemasukan atau halaman Kategori)
        return back()->with('success', 'Kategori berhasil ditambahkan.');
    }

    /**
     * Menampilkan form edit.
     */
    public function edit($id)
    {
        $kategori = KategoriPemasukan::findOrFail($id);
        return view('kategori_pemasukan.edit', compact('kategori'));
    }

    /**
     * Update data kategori.
     */
    public function update(Request $request, $id)
    {
        $kategori = KategoriPemasukan::findOrFail($id);

        $request->validate([
            'nama_kategori_pemasukan' => 'required|string|max:100|unique:kategori_pemasukan,nama_kategori_pemasukan,' . $kategori->id_kategori_pemasukan . ',id_kategori_pemasukan',
        ], [
            'nama_kategori_pemasukan.required' => 'Nama kategori wajib diisi.',
            'nama_kategori_pemasukan.unique' => 'Nama kategori ini sudah ada.',
        ]);

        $kategori->update([
            'nama_kategori_pemasukan' => $request->nama_kategori_pemasukan,
        ]);

        return back()->with('success', 'Kategori berhasil diperbarui.');
    }

    /**
     * Hapus data kategori.
     */
    public function destroy($id)
    {
        $kategori = KategoriPemasukan::findOrFail($id);
        
        // Cek relasi agar data aman
        if ($kategori->pemasukan()->exists()) {
             return back()->with('error', 'Kategori tidak bisa dihapus karena sedang digunakan dalam data Pemasukan.');
        }

        $kategori->delete();

        return back()->with('success', 'Kategori berhasil dihapus.');
    }
}