<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pemasukan;
use App\Models\KategoriPemasukan;
use App\Models\Donasi; 
use Illuminate\Support\Facades\Log;

class PemasukanController extends Controller
{
    public function index(Request $request)
    {
        $query = Pemasukan::with('kategoriPemasukan')->latest('tanggal');

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('deskripsi', 'ilike', "%{$search}%");
        }

        $pemasukan = $query->paginate(10);
        $totalPemasukan = Pemasukan::sum('nominal');
        $kategori = KategoriPemasukan::orderBy('nama_kategori_pemasukan', 'asc')->get();

        return view('pemasukan', compact('pemasukan', 'totalPemasukan', 'kategori'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_kategori_pemasukan' => 'required',
            'nominal' => 'required',
            'tanggal' => 'required|date',
        ]);

        $nominalBersih = str_replace(['.', ','], '', $request->nominal);

        $pemasukan = Pemasukan::create([
            'id_kategori_pemasukan' => $request->id_kategori_pemasukan,
            'nominal' => $nominalBersih,
            'tanggal' => $request->tanggal,
            'deskripsi' => $request->deskripsi,
        ]);

        $pemasukan->load('kategoriPemasukan');

        return response()->json(['status' => 'success', 'message' => 'Berhasil disimpan.', 'data' => $pemasukan]);
    }

    // === BAGIAN PENTING: SHOW DETAIL (Perbaikan Error) ===
    public function show($id)
    {
        try {
            $pemasukan = Pemasukan::with('kategoriPemasukan')->findOrFail($id);

            $listDonatur = [];
            $namaKategori = strtolower($pemasukan->kategoriPemasukan->nama_kategori_pemasukan ?? '');

            // LOGIKA PENCARIAN:
            // Cari di tabel Donasi yang tanggalnya SAMA dengan tanggal pemasukan
            
            if (str_contains($namaKategori, 'donasi') || str_contains($namaKategori, 'infaq')) {
                
                // Cari donasi yang tanggalnya sama
                $listDonatur = Donasi::whereDate('tanggal_donasi', $pemasukan->tanggal)
                                ->latest()
                                ->get();
                                
                // Kalau kosong, coba cari yang created_at nya sama (fallback)
                if($listDonatur->isEmpty()){
                     $listDonatur = Donasi::whereDate('created_at', $pemasukan->tanggal)->get();
                }
            }

            return response()->json([
                'status' => 'success',
                'data' => [
                    'kategori' => $pemasukan->kategoriPemasukan->nama_kategori_pemasukan ?? 'Umum',
                    'nominal' => $pemasukan->nominal,
                    'tanggal' => \Carbon\Carbon::parse($pemasukan->tanggal)->translatedFormat('d F Y'),
                    'donatur' => $listDonatur
                ]
            ]);

        } catch (\Exception $e) {
            // Log error biar tau kenapa
            Log::error("Error Pemasukan Show: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan'], 404);
        }
    }

    public function edit($id)
    {
        $pemasukan = Pemasukan::findOrFail($id);
        $kategori = KategoriPemasukan::all();
        return view('pemasukan.edit', compact('pemasukan', 'kategori'));
    }

    public function update(Request $request, $id)
    {
        $nominalBersih = str_replace(['.', ','], '', $request->nominal);
        $pemasukan = Pemasukan::findOrFail($id);
        $pemasukan->update([
            'id_kategori_pemasukan' => $request->id_kategori_pemasukan,
            'nominal' => $nominalBersih,
            'tanggal' => $request->tanggal,
            'deskripsi' => $request->deskripsi,
        ]);
        return redirect()->route('admin.pemasukan.index')->with('success', 'Data berhasil diperbarui');
    }

    public function destroy($id)
    {
        $pemasukan = Pemasukan::findOrFail($id);
        $pemasukan->delete();
        return back()->with('success', 'Data berhasil dihapus');
    }
}