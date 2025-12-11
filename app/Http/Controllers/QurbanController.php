<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\TabunganHewanQurban;
use App\Models\DetailTabunganHewanQurban;
use App\Models\PemasukanTabunganQurban;
use App\Models\HewanQurban;
use App\Models\MasjidProfil;
use Illuminate\Support\Str;

class QurbanController extends Controller
{
    public function index()
    {


        $masjidSettings = MasjidProfil::first();


        if (!$masjidSettings) {
            $masjidSettings = new MasjidProfil();
            $masjidSettings->social_whatsapp = '';
        }


        if (!Auth::guard('jamaah')->check()) {
            return view('public.tabungan-qurban-saya', [
                'user' => null,
                'tabungans' => collect([]),
                'totalAset' => 0,
                'masterHewan' => [],
                'masjidSettings' => $masjidSettings
            ]);
        }

        $user = Auth::guard('jamaah')->user();


        $tabungans = TabunganHewanQurban::with(['details.hewan', 'pemasukanTabunganQurban'])
            ->where('id_jamaah', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();


        $totalAset = PemasukanTabunganQurban::whereHas('tabunganHewanQurban', function ($q) use ($user) {
            $q->where('id_jamaah', $user->id);
        })
            ->where('status', 'success')
            ->sum('nominal');


        $masterHewan = HewanQurban::where('is_active', true)
            ->orderBy('nama_hewan')
            ->get();


        return view('public.tabungan-qurban-saya', compact('user', 'tabungans', 'totalAset', 'masterHewan', 'masjidSettings'));
    }

    public function store(Request $request)
    {
        if (!Auth::guard('jamaah')->check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user = Auth::guard('jamaah')->user();


        $activeCount = TabunganHewanQurban::where('id_jamaah', $user->id)
            ->whereIn('status', ['menunggu', 'disetujui'])
            ->count();

        if ($activeCount >= 2) {
            return response()->json(['message' => 'Anda maksimal hanya boleh memiliki 2 tabungan qurban aktif.'], 422);
        }


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


            foreach ($request->items as $item) {
                $hewan = HewanQurban::find($item['id_hewan']);
                if (!$hewan) continue;

                $subtotal = $hewan->harga_hewan * $item['qty'];
                $grandTotal += $subtotal;

                $itemsToInsert[] = [
                    'hewan' => $hewan,
                    'qty' => $item['qty'],
                    'subtotal' => $subtotal
                ];
            }


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


            foreach ($itemsToInsert as $data) {
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

    public function show($id)
    {
        if (!Auth::guard('jamaah')->check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user = Auth::guard('jamaah')->user();

        $tabungan = TabunganHewanQurban::with(['details.hewan', 'pemasukanTabunganQurban' => function ($q) {
            $q->orderBy('tanggal', 'desc');
        }])
            ->where('id_jamaah', $user->id)
            ->findOrFail($id);

        return response()->json($tabungan);
    }
}
