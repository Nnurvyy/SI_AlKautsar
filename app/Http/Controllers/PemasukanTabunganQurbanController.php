<?php

namespace App\Http\Controllers; // Pastikan namespace Anda benar

use App\Models\PemasukanTabunganQurban;
use App\Models\TabunganHewanQurban;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;

class PemasukanTabunganQurbanController extends Controller
{
    /**
     * Menampilkan semua pemasukan milik pengguna yang login.
     */
    public function index()
    {
        // Ambil semua pemasukan yang 'tabungan'-nya dimiliki oleh user login
        $pemasukan = PemasukanTabunganQurban::whereHas('tabunganHewanQurban', function ($query) {
            $query->where('id_pengguna', Auth::id());
        })->latest()->get();

        return response()->json($pemasukan);
    }

    /**
     * Menyimpan data pemasukan baru.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'id_tabungan_hewan_qurban' => 'required|string|exists:tabungan_hewan_qurban,id_tabungan_hewan_qurban',
            'tanggal' => 'required|date',
            'nominal' => 'required|numeric|min:0',
        ]);

        $pemasukan = PemasukanTabunganQurban::create($validatedData);

        return response()->json($pemasukan, Response::HTTP_CREATED);
    }

    /**
     * Menampilkan 1 data pemasukan spesifik.
     */
    public function show(string $id)
    {
        $pemasukan = PemasukanTabunganQurban::findOrFail($id);

        return response()->json($pemasukan);
    }

    /**
     * Update data pemasukan.
     */
    public function update(Request $request, string $id)
    {
        $pemasukan = PemasukanTabunganQurban::findOrFail($id);

        $validatedData = $request->validate([
            // Anda tidak boleh mengubah 'id_tabungan_hewan_qurban' saat update
            'tanggal' => 'sometimes|required|date',
            'nominal' => 'sometimes|required|numeric|min:0',
        ]);

        $pemasukan->update($validatedData);

        return response()->json($pemasukan);
    }

    /**
     * Hapus data pemasukan.
     */
    public function destroy(string $id)
    {
        $pemasukan = PemasukanTabunganQurban::findOrFail($id);

        $pemasukan->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
