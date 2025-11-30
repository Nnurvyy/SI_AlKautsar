<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\TabunganHewanQurban;
use App\Models\DetailTabunganHewanQurban;
use App\Models\HewanQurban;
use Illuminate\Support\Str;

class QurbanController extends Controller
{
    // Halaman Utama Jamaah Tabungan
    public function index()
    {
        $user = Auth::guard('jamaah')->user();

        // Ambil list tabungan user
        $tabungans = TabunganHewanQurban::with(['details.hewan', 'pemasukanTabunganQurban'])
                        ->where('id_jamaah', $user->id)
                        ->orderBy('created_at', 'desc')
                        ->get();

        // Hitung total aset seluruh tabungan
        $totalAset = 0;
        foreach($tabungans as $t) {
            $totalAset += $t->pemasukanTabunganQurban->sum('nominal');
        }

        // Data master hewan untuk modal "Buka Tabungan"
        $masterHewan = HewanQurban::where('is_active', true)->orderBy('nama_hewan')->get();

        return view('public.tabungan-qurban-saya', compact('user', 'tabungans', 'totalAset', 'masterHewan'));
    }

    // Logic Buka Tabungan Baru (Dari Modal Jamaah)
    public function store(Request $request)
    {
        $user = Auth::guard('jamaah')->user();

        // 1. Cek Maksimal 2 Tabungan Aktif (Menunggu atau Disetujui)
        $activeCount = TabunganHewanQurban::where('id_jamaah', $user->id)
                        ->whereIn('status', ['menunggu', 'disetujui'])
                        ->count();

        if ($activeCount >= 2) {
            return response()->json(['message' => 'Anda maksimal hanya boleh memiliki 2 tabungan qurban aktif.'], 422);
        }

        // 2. Validasi Input Array Hewan
        $request->validate([
            'saving_type' => 'required|in:bebas,cicilan',
            'duration_months' => 'nullable|integer|min:1',
            'items' => 'required|array|min:1',
            'items.*.id_hewan' => 'required|exists:hewan_qurban,id_hewan_qurban',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            // Hitung Grand Total Server Side (Keamanan)
            $grandTotal = 0;
            $itemsToInsert = [];

            foreach($request->items as $item) {
                $hewan = HewanQurban::find($item['id_hewan']);
                $subtotal = $hewan->harga_hewan * $item['qty'];
                $grandTotal += $subtotal;
                
                $itemsToInsert[] = [
                    'hewan' => $hewan,
                    'qty' => $item['qty'],
                    'subtotal' => $subtotal
                ];
            }

            // Create Header Tabungan
            $tabungan = TabunganHewanQurban::create([
                'id_jamaah' => $user->id,
                'status' => 'menunggu', // Default Status
                'saving_type' => $request->saving_type,
                'duration_months' => ($request->saving_type == 'cicilan') ? $request->duration_months : null,
                'total_tabungan' => 0,
                'total_harga_hewan_qurban' => $grandTotal
            ]);

            // Create Detail Tabungan
            foreach($itemsToInsert as $data) {
                DetailTabunganHewanQurban::create([
                    'id_tabungan_hewan_qurban' => $tabungan->id_tabungan_hewan_qurban,
                    'id_hewan_qurban' => $data['hewan']->id_hewan_qurban,
                    'jumlah_hewan' => $data['qty'],
                    'harga_per_ekor' => $data['hewan']->harga_hewan,
                    'subtotal' => $data['subtotal']
                ]);
            }

            DB::commit();
            return response()->json(['message' => 'Permintaan tabungan berhasil dibuat. Mohon tunggu persetujuan pengurus.']);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()], 500);
        }
    }

    // Ambil Data Detail Tabungan (untuk Modal Riwayat di View Jamaah)
    public function show($id) {
        $user = Auth::guard('jamaah')->user();
        
        $tabungan = TabunganHewanQurban::with(['details.hewan', 'pemasukanTabunganQurban' => function($q){
            $q->orderBy('tanggal', 'desc');
        }])
        ->where('id_jamaah', $user->id) // Security check owner
        ->findOrFail($id);

        return response()->json($tabungan);
    }
}