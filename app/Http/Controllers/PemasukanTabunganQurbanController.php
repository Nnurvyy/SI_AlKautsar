<?php

namespace App\Http\Controllers;

use App\Models\PemasukanTabunganQurban;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class PemasukanTabunganQurbanController extends Controller
{
    public function store(Request $request)
    {

        $request->validate([
            'id_tabungan_hewan_qurban' => 'required|exists:tabungan_hewan_qurban,id_tabungan_hewan_qurban',
            'tanggal' => 'required|date',
            'nominal' => 'required|numeric|min:1',
        ]);

        try {


            $orderId = 'MAN-' . time() . '-' . Str::random(3);



            $pemasukan = PemasukanTabunganQurban::create([
                'id_tabungan_hewan_qurban' => $request->id_tabungan_hewan_qurban,
                'order_id'          => $orderId,
                'tanggal'           => $request->tanggal,
                'nominal'           => $request->nominal,
                'metode_pembayaran' => 'tunai',
                'status'            => 'success',
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
