<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Keuangan;
use App\Models\KategoriKeuangan;
use Illuminate\Validation\ValidationException;

class InfaqJumatController extends Controller
{
    private function getKategoriInfaq()
    {
        return KategoriKeuangan::firstOrCreate(
            ['nama_kategori_keuangan' => 'Infaq Jumat'],
            ['tipe' => 'pemasukan'] // Opsional
        );
    }

    public function index()
    {
        // 1. Hitung Total Infaq Jumat
        $kategori = $this->getKategoriInfaq();
        $totalInfaq = Keuangan::where('id_kategori_keuangan', $kategori->id_kategori_keuangan)->sum('nominal');

        // 2. Kirim ke view
        return view('infaq-jumat', compact('totalInfaq'));
    }

    // ========== DATA TABLE ==========
    public function data(Request $request)
    {
        $search = $request->search;
        $sortBy = $request->sortBy ?? 'tanggal';
        $sortDir = $request->sortDir ?? 'desc';
        $perPage = $request->perPage ?? 10;

        $kategori = $this->getKategoriInfaq();

        $query = Keuangan::where('id_kategori_keuangan', $kategori->id_kategori_keuangan);

        // SEARCH
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('tanggal', 'LIKE', "%$search%")
                  ->orWhere('nominal', 'LIKE', "%$search%");
            });
        }

        $query->orderBy($sortBy, $sortDir);

        $data = $query->paginate($perPage);

        // Mapping kolom ke format JSON lama
        $data->getCollection()->transform(function ($item) {
            return [
                'id_infaq_jumat' => $item->id_keuangan,
                'tanggal_infaq'  => $item->tanggal,
                'nominal_infaq'  => $item->nominal,
            ];
        });

        return response()->json($data);
    }

    // ========== STORE ==========
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'tanggal_infaq' => 'required|date',
                'nominal_infaq' => 'required|integer|min:0',
            ]);

            $kategori = $this->getKategoriInfaq();

            Keuangan::create([
                'tanggal' => $validated['tanggal_infaq'],
                'nominal' => $validated['nominal_infaq'],
                'tipe' => 'pemasukan',
                'id_kategori_keuangan' => $kategori->id_kategori_keuangan,
                'deskripsi' => 'Infaq Jumat'
            ]);

            return response()->json(['message' => 'Data infaq berhasil ditambahkan!'], 201);
        } 
        catch (ValidationException $e) {
            return response()->json(['message' => 'Validasi gagal', 'errors' => $e->errors()], 422);
        }
    }

    // ========== SHOW ==========
    public function show($id)
    {
        $keu = Keuangan::findOrFail($id);

        return response()->json([
            'id_infaq' => $keu->id_keuangan,
            'tanggal_infaq' => $keu->tanggal,
            'nominal_infaq' => $keu->nominal,
        ]);
    }

    // ========== UPDATE ==========
    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'tanggal_infaq' => 'required|date',
                'nominal_infaq' => 'required|integer|min:0',
            ]);

            $keu = Keuangan::findOrFail($id);

            $keu->update([
                'tanggal' => $validated['tanggal_infaq'],
                'nominal' => $validated['nominal_infaq']
            ]);

            return response()->json(['message' => 'Data infaq berhasil diubah!']);
        } 
        catch (ValidationException $e) {
            return response()->json(['message' => 'Validasi gagal', 'errors' => $e->errors()], 422);
        }
    }

    // ========== DELETE ==========
    public function destroy($id)
    {
        $keu = Keuangan::findOrFail($id);
        $keu->delete();

        return response()->json(['message' => 'Data infaq berhasil dihapus!']);
    }
}
