<?php

namespace App\Http\Controllers;
use App\Models\PemasukanTabunganQurban;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PemasukanTabunganQurbanController extends Controller
{
    /**
     * Menyimpan data pemasukan baru.
     */
    // GANTI: PemasukanQurbanRequest jadi Request biasa
    public function store(Request $request)
    {
        // TAMBAHKAN VALIDASI DI SINI
        $validatedData = $request->validate([
            'id_tabungan_hewan_qurban' => 'required|exists:tabungan_hewan_qurban,id_tabungan_hewan_qurban',
            'tanggal' => 'required|date',
            'nominal' => 'required|numeric|min:1',
        ]);

        $pemasukan = PemasukanTabunganQurban::create($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Setoran berhasil disimpan.',
            'data' => $pemasukan
        ], Response::HTTP_CREATED);
    }

    /**
     * Hapus data pemasukan.
     */
    public function destroy(string $id)
    {
        $pemasukan = PemasukanTabunganQurban::findOrFail($id);
        $pemasukan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data setoran berhasil dihapus.'
        ]);
    }
}
