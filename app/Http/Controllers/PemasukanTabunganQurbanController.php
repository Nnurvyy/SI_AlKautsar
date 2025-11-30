<?php

namespace App\Http\Controllers;

use App\Models\PemasukanTabunganQurban;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str; // Wajib import ini untuk generate random string

class PemasukanTabunganQurbanController extends Controller
{
    /**
     * Menyimpan data pemasukan baru (Setoran Manual).
     */
    public function store(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'id_tabungan_hewan_qurban' => 'required|exists:tabungan_hewan_qurban,id_tabungan_hewan_qurban',
            'tanggal' => 'required|date',
            'nominal' => 'required|numeric|min:1',
        ]);

        try {
            // 2. Buat Order ID Unik (Format: MAN-Timestamp-Random)
            // Ini penting agar data terlihat rapi dan tidak error jika kolom order_id unique
            $orderId = 'MAN-' . time() . '-' . Str::random(3);

            // 3. Simpan Data dengan Status SUCCESS (Hardcode)
            // Karena ini input manual Admin, maka dianggap uang sudah diterima (Tunai/Transfer Langsung)
            $pemasukan = PemasukanTabunganQurban::create([
                'id_tabungan_hewan_qurban' => $request->id_tabungan_hewan_qurban,
                'order_id'          => $orderId,
                'tanggal'           => $request->tanggal,
                'nominal'           => $request->nominal,
                'metode_pembayaran' => 'tunai',   // Default Manual = Tunai
                'status'            => 'success', // WAJIB SUCCESS agar saldo bertambah
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Setoran berhasil disimpan dan saldo bertambah.',
                'data'    => $pemasukan
            ], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Hapus data pemasukan.
     */
    public function destroy(string $id)
    {
        try {
            $pemasukan = PemasukanTabunganQurban::findOrFail($id);
            $pemasukan->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data setoran berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data.'
            ], 500);
        }
    }
}