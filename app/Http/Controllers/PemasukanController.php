<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pemasukan;
use App\Models\PemasukanKategori;

class PemasukanController extends Controller
{
    // Tampilkan semua data pemasukan
    public function index()
    {
        // Ambil semua pemasukan + kategori
        $pemasukan = Pemasukan::with('kategori')->get();
        $totalPemasukan = $pemasukan->sum('nominal');

        $kategori = PemasukanKategori::all();

        return view('pemasukan.index', compact('pemasukan', 'totalPemasukan', 'kategori'));
    }

    // Tampilkan form tambah data
    public function create()
    {
        $kategori = PemasukanKategori::all();
        return view('pemasukan.create', compact('kategori'));
    }

    // Simpan data baru
    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'nominal' => 'required|numeric',
            'id_kategori_pemasukan' => 'required',
            'deskripsi' => 'nullable|string',
        ]);

        Pemasukan::create([
            'tanggal' => $request->tanggal,
            'nominal' => $request->nominal,
            'id_kategori_pemasukan' => $request->id_kategori_pemasukan,
            'deskripsi' => $request->deskripsi,
        ]);

        return redirect()->route('pemasukan.index')->with('success', 'Data pemasukan berhasil ditambahkan!');
    }

    // Edit data
    public function edit($id)
    {
        $pemasukan = Pemasukan::findOrFail($id);
        $kategori = PemasukanKategori::all();

        return view('pemasukan.edit', compact('pemasukan', 'kategori'));
    }

    // Update data
    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'nominal' => 'required|numeric',
            'id_kategori_pemasukan' => 'required',
            'deskripsi' => 'nullable|string',
        ]);

        $pemasukan = Pemasukan::findOrFail($id);

        $pemasukan->update([
            'tanggal' => $request->tanggal,
            'nominal' => $request->nominal,
            'id_kategori_pemasukan' => $request->id_kategori_pemasukan,
            'deskripsi' => $request->deskripsi,
        ]);

        return redirect()->route('pemasukan.index')->with('success', 'Data pemasukan berhasil diperbarui!');
    }

    // Hapus data
    public function destroy($id)
    {
        $pemasukan = Pemasukan::findOrFail($id);
        $pemasukan->delete();

        return redirect()->route('pemasukan.index')->with('success', 'Data pemasukan berhasil dihapus!');
    }
}
