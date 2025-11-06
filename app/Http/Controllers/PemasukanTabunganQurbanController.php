<?php

namespace App\Http\Controllers;

// Gunakan Request baru
use App\Http\Requests\PemasukanQurbanRequest;
use App\Models\PemasukanTabunganQurban;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PemasukanTabunganQurbanController extends Controller
{
    /**
     * Menyimpan data pemasukan baru.
     * Kita hanya butuh store dan destroy untuk modal.
     */
    public function store(PemasukanQurbanRequest $request)
    {
        $validatedData = $request->validated();

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
        // Temukan atau gagal
        $pemasukan = PemasukanTabunganQurban::findOrFail($id);

        $pemasukan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data setoran berhasil dihapus.'
        ]);
    }

    /**
     * Metode lain (index, show, update) sengaja dihapus
     * karena tidak digunakan dalam alur modal ini.
     */
}
