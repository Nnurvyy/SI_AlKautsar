<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PemasukanTabunganQurban;
use App\Models\TabunganHewanQurban;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // Tambahkan Log untuk debugging

class TabunganPaymentController extends Controller
{
    public function checkout(Request $request)
    {
        try {
            // 1. Cek Login Jamaah
            $user = Auth::guard('jamaah')->user();
            if (!$user) {
                return response()->json(['message' => 'Silakan login terlebih dahulu'], 401);
            }

            // 2. Validasi
            $request->validate([
                'id_tabungan_hewan_qurban' => 'required|exists:tabungan_hewan_qurban,id_tabungan_hewan_qurban',
                'nominal' => 'required|numeric|min:10000', 
                'method' => 'required',
            ]);

            // 3. Ambil data Tabungan Induk (Beserta detail untuk ambil nama hewan)
            $tabungan = TabunganHewanQurban::with('details.hewan') // Load relasi details
                        ->where('id_tabungan_hewan_qurban', $request->id_tabungan_hewan_qurban)
                        ->where('id_jamaah', $user->id)
                        ->firstOrFail();

            // 4. Config Tripay
            $merchantCode = config('services.tripay.merchant_code');
            $apiKey       = config('services.tripay.api_key');
            $privateKey   = config('services.tripay.private_key');
            $apiUrl       = config('services.tripay.api_url');

            // Cek Konfigurasi (Sering lupa diisi di .env)
            if (!$merchantCode || !$apiKey || !$privateKey || !$apiUrl) {
                throw new \Exception('Konfigurasi Tripay belum lengkap di server.');
            }

            $amount       = (int) $request->nominal;
            $merchantRef  = 'TRQ-' . time() . '-' . Str::random(5); 

            // 5. Signature
            $signature = hash_hmac('sha256', $merchantCode . $merchantRef . $amount, $privateKey);

            // [PERBAIKAN] Ambil Nama Hewan dengan Aman
            $namaItem = 'Tabungan Qurban';
            if ($tabungan->details->isNotEmpty()) {
                // Ambil hewan pertama sebagai representasi nama
                $firstDetail = $tabungan->details->first();
                if ($firstDetail->hewan) {
                    $namaItem .= ': ' . $firstDetail->hewan->nama_hewan;
                }
            }

            // 6. Request Body
            $data = [
                'method'         => $request->input('method'),
                'merchant_ref'   => $merchantRef,
                'amount'         => $amount,
                'customer_name'  => $user->name,
                'customer_email' => $user->email,
                'customer_phone' => $user->no_hp ?? '08123456789',
                'order_items'    => [
                    [
                        'sku'      => $tabungan->id_tabungan_hewan_qurban,
                        'name'     => substr($namaItem, 0, 250), // Batasi panjang nama agar tidak error di tripay
                        'price'    => $amount,
                        'quantity' => 1
                    ]
                ],
                'return_url'   => route('public.tabungan-qurban-saya'), // Pastikan route ini ada
                'expired_time' => (time() + (60 * 60)),
                'signature'    => $signature
            ];

            // 7. Kirim ke Tripay
            $response = Http::withToken($apiKey)->post($apiUrl, $data);
            $res = $response->json();

            // Cek Error dari Tripay
            if (!$response->successful() || empty($res['success']) || !$res['success']) {
                // Log error untuk developer
                Log::error('Tripay Error:', ['response' => $res, 'data' => $data]);
                
                return response()->json([
                    'status' => 'error', 
                    'message' => $res['message'] ?? 'Gagal request ke Payment Gateway'
                ], 500);
            }

            // 8. Simpan ke Database
            $dataTripay = $res['data'];
            
            PemasukanTabunganQurban::create([
                'id_tabungan_hewan_qurban' => $tabungan->id_tabungan_hewan_qurban,
                'order_id'          => $merchantRef,
                'tripay_reference'  => $dataTripay['reference'],
                'tanggal'           => now(),
                'nominal'           => $amount,
                'metode_pembayaran' => 'tripay',
                'status'            => 'pending',
                'checkout_url'      => $dataTripay['checkout_url'],
            ]);

            return response()->json([
                'status' => 'success', 
                'checkout_url' => $dataTripay['checkout_url']
            ]);

        } catch (\Exception $e) {
            // Tangkap semua error PHP agar tidak return HTML (penyebab "Unexpected token <")
            Log::error('Checkout Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }
}