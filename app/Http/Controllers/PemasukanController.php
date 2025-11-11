<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pemasukan;
use App\Models\KategoriPemasukan;

class PemasukanController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil Data Pemasukan (Pagination)
        $pemasukan = Pemasukan::with('kategoriPemasukan')
            ->latest('tanggal')
            ->paginate(10);

        // 2. Hitung Total Pemasukan
        $totalPemasukan = Pemasukan::sum('nominal');

        // 3. Ambil Data Kategori (Untuk ditampilkan di Modal Tambah & Kelola)
        $kategori = KategoriPemasukan::orderBy('nama_kategori_pemasukan', 'asc')->get();

        return view('pemasukan', compact('pemasukan', 'totalPemasukan', 'kategori'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_kategori_pemasukan' => 'required|exists:kategori_pemasukan,id_kategori_pemasukan',
            'nominal' => 'required',
            'tanggal' => 'required|date',
            'deskripsi' => 'nullable|string|max:255',
        ]);

        // Hapus titik dari format ribuan
        $nominalBersih = str_replace('.', '', $request->nominal);
        $nominalBersih = str_replace(',', '', $nominalBersih);

        Pemasukan::create([
            'id_kategori_pemasukan' => $request->id_kategori_pemasukan,
            'nominal' => $nominalBersih,
            'tanggal' => $request->tanggal,
            'deskripsi' => $request->deskripsi,
        ]);

        // Redirect kembali ke halaman index dengan pesan sukses
        return redirect()->route('admin.pemasukan.index')
            ->with('success', 'Data pemasukan berhasil ditambahkan.');
    }

    // ... (Method edit, update, destroy biarkan seperti sebelumnya atau sesuaikan jika edit masih pisah halaman)
    // Untuk saat ini Edit saya biarkan pisah halaman agar tidak terlalu rumit kodingannya.
    
    public function edit($id)
    {
        $pemasukan = Pemasukan::findOrFail($id);
        $kategori = KategoriPemasukan::all();
        return view('pemasukan.edit', compact('pemasukan', 'kategori'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'id_kategori_pemasukan' => 'required|exists:kategori_pemasukan,id_kategori_pemasukan',
            'nominal' => 'required',
            'tanggal' => 'required|date',
            'deskripsi' => 'nullable|string|max:255',
        ]);

        $nominalBersih = str_replace('.', '', $request->nominal);
        $nominalBersih = str_replace(',', '', $nominalBersih);

        $pemasukan = Pemasukan::findOrFail($id);
        $pemasukan->update([
            'id_kategori_pemasukan' => $request->id_kategori_pemasukan,
            'nominal' => $nominalBersih,
            'tanggal' => $request->tanggal,
            'deskripsi' => $request->deskripsi,
        ]);

        return redirect()->route('admin.pemasukan.index')
            ->with('success', 'Data pemasukan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $pemasukan = Pemasukan::findOrFail($id);
        $pemasukan->delete();

        return redirect()->route('admin.pemasukan.index')
            ->with('success', 'Data pemasukan berhasil dihapus.');
    }
}