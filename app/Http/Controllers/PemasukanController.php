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

        // 3. Ambil Data Kategori (PENTING: Untuk Modal Tambah & Dropdown)
        $kategori = KategoriPemasukan::orderBy('nama_kategori_pemasukan', 'asc')->get();

        return view('pemasukan', compact('pemasukan', 'totalPemasukan', 'kategori'));
    }

    public function store(Request $request)
    {
        // Validasi Input
        $request->validate([
            'id_kategori_pemasukan' => 'required|exists:kategori_pemasukan,id_kategori_pemasukan',
            'nominal' => 'required',
            'tanggal' => 'required|date',
            'deskripsi' => 'nullable|string|max:255',
        ]);

        // Hapus format ribuan (titik/koma) dari input nominal
        $nominalBersih = str_replace(['.', ','], '', $request->nominal);

        // Simpan ke Database
        $pemasukan = Pemasukan::create([
            'id_kategori_pemasukan' => $request->id_kategori_pemasukan,
            'nominal' => $nominalBersih,
            'tanggal' => $request->tanggal,
            'deskripsi' => $request->deskripsi,
        ]);

        // Load relasi kategori agar bisa ditampilkan namanya di Tabel JS
        $pemasukan->load('kategoriPemasukan');

        // PENTING: Return JSON untuk AJAX
        return response()->json([
            'status' => 'success',
            'message' => 'Data pemasukan berhasil ditambahkan.',
            'data' => $pemasukan
        ]);
    }

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

        $nominalBersih = str_replace(['.', ','], '', $request->nominal);

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