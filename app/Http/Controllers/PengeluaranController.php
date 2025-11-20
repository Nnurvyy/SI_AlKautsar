<?php

namespace App\Http\Controllers;

use App\Models\Keuangan;
use App\Models\KategoriKeuangan;
use Illuminate\Http\Request;

class PengeluaranController extends Controller
{
    public function index()
    {
        // Hitung total pengeluaran
        $totalPengeluaran = Keuangan::where('tipe', 'pengeluaran')->sum('nominal');
        return view('pengeluaran', compact('totalPengeluaran'));
    }

    public function data(Request $request)
    {
        // Load data dengan relasi kategori
        $query = Keuangan::with('kategori')
            ->where('tipe', 'pengeluaran');

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('deskripsi', 'ILIKE', "%{$search}%");
        }

        // Pagination & Sorting
        $data = $query->orderBy('tanggal', 'desc')->paginate(10);
        return response()->json($data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'id_kategori_keuangan' => 'required|exists:kategori_keuangan,id_kategori_keuangan',
            'nominal' => 'required|numeric|min:1',
            'deskripsi' => 'nullable|string|max:255',
        ]);

        $data = $request->all();
        $data['tipe'] = 'pengeluaran'; // Paksa tipe pengeluaran

        Keuangan::create($data);

        return response()->json(['message' => 'Pengeluaran berhasil disimpan.']);
    }

    public function show($id)
    {
        return response()->json(Keuangan::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $keuangan = Keuangan::findOrFail($id);
        
        $request->validate([
            'tanggal' => 'required|date',
            'id_kategori_keuangan' => 'required|exists:kategori_keuangan,id_kategori_keuangan',
            'nominal' => 'required|numeric|min:1',
            'deskripsi' => 'nullable|string|max:255',
        ]);

        $keuangan->update($request->all());

        return response()->json(['message' => 'Data berhasil diperbarui.']);
    }

    public function destroy($id)
    {
        Keuangan::findOrFail($id)->delete();
        return response()->json(['message' => 'Data berhasil dihapus.']);
    }
}