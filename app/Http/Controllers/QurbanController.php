<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\TabunganHewanQurban;
use App\Models\DetailTabunganHewanQurban;
use App\Models\PemasukanTabunganQurban; 
use App\Models\HewanQurban;
use App\Models\MasjidProfil; // <--- WAJIB DITAMBAHKAN
use Illuminate\Support\Str;

class QurbanController extends Controller
{
    /**
     * Halaman Utama Tabungan Jamaah
     */
    public function index()
    {
        // --- [PINDAHAN DARI PUBLIC CONTROLLER] ---
        // 1. Ambil Profil Masjid untuk tombol bantuan WA
        $masjidSettings = MasjidProfil::first(); 

        // Fallback jika data kosong agar tidak error di view
        if (!$masjidSettings) {
            $masjidSettings = new MasjidProfil();
            $masjidSettings->social_whatsapp = ''; 
        }

        // 2. Cek apakah user login
        if (!Auth::guard('jamaah')->check()) {
            return view('public.tabungan-qurban-saya', [
                'user' => null,
                'tabungans' => collect([]),
                'totalAset' => 0,
                'masterHewan' => [],
                'masjidSettings' => $masjidSettings // Kirim ke view
            ]);
        }

        $user = Auth::guard('jamaah')->user();

        // 3. Ambil list tabungan user
        $tabungans = TabunganHewanQurban::with(['details.hewan', 'pemasukanTabunganQurban'])
                        ->where('id_jamaah', $user->id)
                        ->orderBy('created_at', 'desc')
                        ->get();

        // 4. Hitung Total Aset (Hanya yang SUCCESS)
        $totalAset = PemasukanTabunganQurban::whereHas('tabunganHewanQurban', function($q) use ($user){
                            $q->where('id_jamaah', $user->id);
                        })
                        ->where('status', 'success') 
                        ->sum('nominal');

        // 5. Data master hewan untuk modal tambah
        $masterHewan = HewanQurban::where('is_active', true)
                        ->orderBy('nama_hewan')
                        ->get();

        // Kirim semua variabel ke view, termasuk $masjidSettings
        return view('public.tabungan-qurban-saya', compact('user', 'tabungans', 'totalAset', 'masterHewan', 'masjidSettings'));
    }

    /**
     * Logic Buka Tabungan Baru
     */
    public function store(Request $request)
    {
        if (!Auth::guard('jamaah')->check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        
        $user = Auth::guard('jamaah')->user();

        // 1. Cek Maksimal 2 Tabungan Aktif
        $activeCount = TabunganHewanQurban::where('id_jamaah', $user->id)
                        ->whereIn('status', ['menunggu', 'disetujui'])
                        ->count();

        if ($activeCount >= 2) {
            return response()->json(['message' => 'Anda maksimal hanya boleh memiliki 2 tabungan qurban aktif.'], 422);
        }

        // 2. Validasi Input
        $request->validate([
            'saving_type' => 'required|in:bebas,cicilan',
            'duration_months' => 'nullable|integer|min:1',
            'items' => 'required|array|min:1',
            'items.*.id_hewan' => 'required|exists:hewan_qurban,id_hewan_qurban',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $uuid = (string) Str::uuid();
            $grandTotal = 0;
            $itemsToInsert = [];

            // Hitung Grand Total & Siapkan Data
            foreach($request->items as $item) {
                $hewan = HewanQurban::find($item['id_hewan']);
                if(!$hewan) continue;

                $subtotal = $hewan->harga_hewan * $item['qty'];
                $grandTotal += $subtotal;
                
                $itemsToInsert[] = [
                    'hewan' => $hewan,
                    'qty' => $item['qty'],
                    'subtotal' => $subtotal
                ];
            }

            // 3. Create Header Tabungan
            $tabungan = TabunganHewanQurban::create([
                'id_tabungan_hewan_qurban' => $uuid,
                'id_jamaah' => $user->id,
                'status' => 'menunggu', 
                'saving_type' => $request->saving_type,
                'duration_months' => ($request->saving_type == 'cicilan') ? $request->duration_months : null,
                'total_tabungan' => 0,
                'total_harga_hewan_qurban' => $grandTotal,
                'tanggal_pembuatan' => now()
            ]);

            // 4. Create Detail Tabungan
            foreach($itemsToInsert as $data) {
                DetailTabunganHewanQurban::create([
                    'id_tabungan_hewan_qurban' => $uuid,
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

    /**
     * Ambil Data Detail Tabungan (untuk Modal Riwayat)
     */
    public function show($id) {
        if (!Auth::guard('jamaah')->check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user = Auth::guard('jamaah')->user();
        
        $tabungan = TabunganHewanQurban::with(['details.hewan', 'pemasukanTabunganQurban' => function($q){
            $q->orderBy('tanggal', 'desc');
        }])
        ->where('id_jamaah', $user->id) 
        ->findOrFail($id);

        return response()->json($tabungan);
    }
}