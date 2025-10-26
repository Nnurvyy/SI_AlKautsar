<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pemasukan;
use App\Models\PemasukanKategori;
use App\Models\Divisi;
use App\Models\Student;

class PemasukanController extends Controller
{
    // Tampilkan semua data pemasukan
    public function index()
    {
        $pemasukan = Pemasukan::with(['kategori', 'divisi', 'siswa'])->get();
        $totalPemasukan = $pemasukan->sum('nominal');

        $kategori = PemasukanKategori::all();
        $divisi = Divisi::all();
        $siswa = Student::all();

        return view('pemasukan.index', compact('pemasukan', 'totalPemasukan', 'kategori', 'divisi', 'siswa'));
    }

    public function create()
    {
        $kategori = PemasukanKategori::all();
        $divisi = Divisi::all();
        $siswa = Student::all();

        return view('pemasukan.create', compact('kategori', 'divisi', 'siswa'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_divisi' => 'required',
            'id_kategori' => 'required',
            'metode_pembayaran' => 'required',
            'nominal' => 'required|numeric',
            'tanggal_transaksi' => 'required|date',
        ]);

        Pemasukan::create($request->all());

        return redirect()->route('pemasukan.index')->with('success', 'Data pemasukan berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $pemasukan = Pemasukan::findOrFail($id);
        $kategori = PemasukanKategori::all();
        $divisi = Divisi::all();
        $siswa = Student::all();

        return view('pemasukan.edit', compact('pemasukan', 'kategori', 'divisi', 'siswa'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'id_divisi' => 'required',
            'id_kategori' => 'required',
            'metode_pembayaran' => 'required',
            'nominal' => 'required|numeric',
            'tanggal_transaksi' => 'required|date',
        ]);

        $pemasukan = Pemasukan::findOrFail($id);
        $pemasukan->update($request->all());

        return redirect()->route('pemasukan.index')->with('success', 'Data berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $pemasukan = Pemasukan::findOrFail($id);
        $pemasukan->delete();

        return redirect()->route('pemasukan.index')->with('success', 'Data berhasil dihapus!');
    }
}
