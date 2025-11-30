<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PemasukanDonasi;
use App\Models\Donasi;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http; // Pastikan ini ada
use Illuminate\Support\Facades\Auth; // Pastikan ini ada

class DonasiPaymentController extends Controller
{
    public function checkout(Request $request)
    {
        // 1. Cek Login Jamaah
        $user = Auth::guard('jamaah')->user();

        // 2. Validasi Input (Email dihapus dari validasi frontend, kita handle di backend)
        $request->validate([
            'id_donasi' => 'required',
            'nominal' => 'required|numeric|min:1000',
            'method' => 'required',
            // Nama hanya wajib jika TIDAK login (jika login, kita ambil dari DB)
            'nama' => $user ? 'nullable' : 'required|string', 
        ]);
        
        $donasi = Donasi::find($request->id_donasi);
        if (!$donasi) return response()->json(['message' => 'Donasi tidak ditemukan'], 404);

        // 3. Tentukan Nama & Email
        if ($user) {
            // Jika Jamaah Login
            $customerName = $user->name;
            $customerEmail = $user->email;
            $idJamaah = $user->id;
        } else {
            // Jika Tamu (Guest)
            $customerName = $request->nama;
            // Tripay WAJIB butuh email. Kita buat dummy email jika user tidak input.
            // Format: guest_{timestamp}_{random}@nomail.com
            $customerEmail = 'guest_' . time() . '_' . Str::random(3) . '@nomail.com';
            $idJamaah = null;
        }

        // 4. Ambil Config Tripay
        $merchantCode = config('services.tripay.merchant_code');
        $apiKey       = config('services.tripay.api_key');
        $privateKey   = config('services.tripay.private_key');
        $amount       = (int) $request->nominal;
        $merchantRef  = 'DON-' . time() . '-' . Str::random(5);

        // 5. Generate Signature
        $signature = hash_hmac('sha256', $merchantCode . $merchantRef . $amount, $privateKey);

        // 6. Data Request ke Tripay
        $data = [
            'method'         => $request->input('method'),
            'merchant_ref'   => $merchantRef,
            'amount'         => $amount,
            'customer_name'  => $customerName,
            'customer_email' => $customerEmail, // Email asli (jamaah) atau dummy (tamu)
            'customer_phone' => $user ? $user->no_hp : '08123456789', // Jika jamaah ada HP pakai itu
            'order_items'    => [
                [
                    'sku'      => $donasi->id_donasi,
                    'name'     => substr($donasi->nama_donasi, 0, 250),
                    'price'    => $amount,
                    'quantity' => 1
                ]
            ],
            'return_url'   => route('public.donasi'),
            'expired_time' => (time() + (24 * 60 * 60)),
            'signature'    => $signature
        ];

        // 7. Kirim Request ke API Tripay
        $url = config('services.tripay.api_url');
        $response = Http::withToken($apiKey)->post($url, $data);
        $res = $response->json();

        if (!$response->successful() || empty($res['success']) || !$res['success']) {
            return response()->json([
                'status' => 'error', 
                'message' => $res['message'] ?? 'Gagal request ke Tripay'
            ], 500);
        }

        // 8. Simpan ke Database
        $dataTripay = $res['data'];
        
        PemasukanDonasi::create([
            'id_donasi' => $donasi->id_donasi,
            'id_jamaah' => $idJamaah, // Simpan ID Jamaah jika ada
            'order_id' => $merchantRef,
            'tripay_reference' => $dataTripay['reference'],
            'tanggal' => now(),
            'nama_donatur' => $customerName,
            'metode_pembayaran' => 'transfer',
            'nominal' => $amount,
            'status' => 'pending',
            'pesan' => $request->pesan ?? '-',
            'checkout_url' => $dataTripay['checkout_url'],
        ]);

        return response()->json([
            'status' => 'success', 
            'checkout_url' => $dataTripay['checkout_url']
        ]);
    }

    public function callback(Request $request)
    {
        // 1. Ambil data callback
        $callbackSignature = $request->server('HTTP_X_CALLBACK_SIGNATURE');
        $json = $request->getContent();
        $data = json_decode($json);

        // 2. Validasi Signature
        $privateKey = config('services.tripay.private_key');
        $signature = hash_hmac('sha256', $json, $privateKey);

        if ($signature !== $callbackSignature) {
            return response()->json(['success' => false, 'message' => 'Invalid Signature'], 400);
        }

        // 3. Proses Status
        if ($request->header('X-Callback-Event') === 'payment_status') {
            $transaksi = PemasukanDonasi::where('order_id', $data->merchant_ref)->first();
            
            if ($transaksi) {
                if ($data->status === 'PAID') {
                    $transaksi->update(['status' => 'success']);
                } elseif (in_array($data->status, ['EXPIRED', 'FAILED', 'REFUND'])) {
                    $transaksi->update(['status' => 'failed']);
                }
            }
        }

        return response()->json(['success' => true]);
    }
}