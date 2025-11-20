<?php

namespace App\Http\Controllers;

use App\Models\Keuangan;
use App\Models\KategoriKeuangan;
use Illuminate\Http\Request;
use App\Models\Pemasukan;
use App\Models\KategoriPemasukan;
use App\Models\Donasi; 
use Illuminate\Support\Facades\Log;

class PemasukanController extends Controller
{
    public function index()
    {
        // Hitung total pemasukan
        $totalPemasukan = Keuangan::where('tipe', 'pemasukan')->sum('nominal');
        return view('pemasukan', compact('totalPemasukan'));
    }

    public function data(Request $request)
    {
        // Load data dengan relasi kategori
        $query = Keuangan::with('kategori')
            ->where('tipe', 'pemasukan');

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
        $data['tipe'] = 'pemasukan'; // Paksa tipe pemasukan

        Keuangan::create($data);

        return response()->json(['message' => 'Pemasukan berhasil disimpan.']);
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