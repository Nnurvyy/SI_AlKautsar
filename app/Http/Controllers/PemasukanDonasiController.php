<?php

namespace App\Http\Controllers;

use App\Models\PemasukanDonasi;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PemasukanDonasiController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_donasi' => 'required|exists:donasi,id_donasi',
            'nama_donatur' => 'required|string|max:255',
            'metode_pembayaran' => 'required|in:tunai,transfer,whatsapp',
            'tanggal' => 'required|date',
            'nominal' => 'required|numeric|min:1',
            'pesan' => 'nullable|string'
        ]);

        // TAMBAHAN: Paksa status jadi 'success' untuk input manual
        // Karena pengurus yang input, diasumsikan uang sudah diterima/verifikasi
        $validated['status'] = 'success'; 

        PemasukanDonasi::create($validated);

        return response()->json(['message' => 'Donasi berhasil diterima.'], Response::HTTP_CREATED);
    }

    public function destroy($id)
    {
        $pemasukan = PemasukanDonasi::findOrFail($id);
        $pemasukan->delete();

        return response()->json(['message' => 'Data donasi dihapus.']);
    }
}