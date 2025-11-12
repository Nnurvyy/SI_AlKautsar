<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengeluaran;
use App\Models\KategoriPengeluaran;

class PengeluaranController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil Data Pengeluaran
        $pengeluaran = Pengeluaran::with('kategoriPengeluaran')
            ->latest('tanggal')
            ->paginate(10);

        // 2. Hitung Total
        $totalPengeluaran = Pengeluaran::sum('nominal');

        // 3. Ambil Kategori (Untuk Dropdown & Modal)
        $kategori = KategoriPengeluaran::orderBy('nama_kategori_pengeluaran', 'asc')->get();

        return view('pengeluaran', compact('pengeluaran', 'totalPengeluaran', 'kategori'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_kategori_pengeluaran' => 'required|exists:kategori_pengeluaran,id_kategori_pengeluaran',
            'nominal' => 'required',
            'tanggal' => 'required|date',
            'deskripsi' => 'nullable|string|max:255',
        ]);

        // Bersihkan format rupiah
        $nominalBersih = str_replace(['.', ','], '', $request->nominal);

        $pengeluaran = Pengeluaran::create([
            'id_kategori_pengeluaran' => $request->id_kategori_pengeluaran,
            'nominal' => $nominalBersih,
            'tanggal' => $request->tanggal,
            'deskripsi' => $request->deskripsi,
        ]);

        $pengeluaran->load('kategoriPengeluaran');

        // Return JSON untuk AJAX
        return response()->json([
            'status' => 'success',
            'message' => 'Data pengeluaran berhasil ditambahkan.',
            'data' => $pengeluaran
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'id_kategori_pengeluaran' => 'required|exists:kategori_pengeluaran,id_kategori_pengeluaran',
            'nominal' => 'required',
            'tanggal' => 'required|date',
            'deskripsi' => 'nullable|string|max:255',
        ]);

        $nominalBersih = str_replace(['.', ','], '', $request->nominal);

        $pengeluaran = Pengeluaran::findOrFail($id);
        $pengeluaran->update([
            'id_kategori_pengeluaran' => $request->id_kategori_pengeluaran,
            'nominal' => $nominalBersih,
            'tanggal' => $request->tanggal,
            'deskripsi' => $request->deskripsi,
        ]);

        return redirect()->route('admin.pengeluaran.index')
            ->with('success', 'Data pengeluaran berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $pengeluaran = Pengeluaran::findOrFail($id);
        $pengeluaran->delete();

        return redirect()->route('admin.pengeluaran.index')
            ->with('success', 'Data pengeluaran berhasil dihapus.');
    }

    public function edit($id)
    {
        $pengeluaran = Pengeluaran::findOrFail($id);
        $kategori = KategoriPengeluaran::all();
        return view('pengeluaran.edit', compact('pengeluaran', 'kategori'));
    }
}