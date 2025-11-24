<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PemasukanDonasi;
use App\Models\Donasi;
use Illuminate\Support\Str;
use Midtrans\Config;
use Midtrans\Snap;

class DonasiPaymentController extends Controller
{
    public function __construct()
    {
        // Konfigurasi Midtrans
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
    }

    public function checkout(Request $request)
    {
        // 1. Validasi Input dari Frontend
        $request->validate([
            'id_donasi' => 'required',
            'nama' => 'required|string',
            'nominal' => 'required|numeric|min:1000',
        ]);

        // 2. Cek Data Donasi
        $donasi = Donasi::find($request->id_donasi);
        if (!$donasi) {
            return response()->json(['status' => 'error', 'message' => 'Donasi tidak ditemukan.'], 404);
        }

        try {
            // 3. Buat Order ID Unik
            $orderId = 'DON-' . time() . '-' . Str::random(5);

            // 4. Simpan ke Database (Status Pending)
            $pemasukan = PemasukanDonasi::create([
                'id_donasi' => $donasi->id_donasi,
                'order_id' => $orderId,
                'tanggal' => now(),
                'nama_donatur' => $request->nama,
                
                // --- PENTING: SESUAIKAN DENGAN ENUM DI MIGRATION ---
                // Database kamu cuma terima: 'tunai', 'transfer', 'whatsapp'
                // Jadi kita pakai 'transfer' untuk Midtrans.
                'metode_pembayaran' => 'transfer', 
                // --------------------------------------------------

                'nominal' => $request->nominal,
                'status' => 'pending', // Status awal
                'pesan' => $request->pesan ?? '-',
            ]);

            // 5. Siapkan Parameter untuk Midtrans
            $params = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => (int) $request->nominal,
                ],
                'customer_details' => [
                    'first_name' => $request->nama,
                    'email' => $request->email, // Email wajib untuk notifikasi Midtrans
                ],
                'item_details' => [[
                    'id' => $donasi->id_donasi,
                    'price' => (int) $request->nominal,
                    'quantity' => 1,
                    'name' => substr($donasi->nama_donasi, 0, 50), // Midtrans limit nama item 50 char
                ]]
            ];

            // 6. Minta Snap Token ke Midtrans
            $snapToken = Snap::getSnapToken($params);
            
            // 7. Simpan Token ke Database (untuk jaga-jaga)
            $pemasukan->update(['snap_token' => $snapToken]);

            // 8. Kirim Token ke Frontend
            return response()->json(['status' => 'success', 'snap_token' => $snapToken]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error', 
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function callback(Request $request)
    {
        $serverKey = config('midtrans.server_key');
        // Verifikasi tanda tangan keamanan dari Midtrans
        $hashed = hash("sha512", $request->order_id.$request->status_code.$request->gross_amount.$serverKey);

        if ($hashed == $request->signature_key) {
            // Cari transaksi berdasarkan Order ID
            $transaksi = PemasukanDonasi::where('order_id', $request->order_id)->first();
            
            if ($transaksi) {
                // Update status berdasarkan respon Midtrans
                if ($request->transaction_status == 'capture' || $request->transaction_status == 'settlement') {
                    $transaksi->update(['status' => 'success']);
                } elseif (in_array($request->transaction_status, ['expire', 'cancel', 'deny'])) {
                    $transaksi->update(['status' => 'failed']);
                }
            }
        }
        return response()->json(['status' => 'ok']);
    }
}