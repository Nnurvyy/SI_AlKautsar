<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\TabunganHewanQurban;
use Carbon\Carbon;

class QurbanController extends Controller
{
    public function index()
    {
        // 1. Pastikan user sudah login (Double check, meski route sudah diproteksi)
        // Menggunakan guard 'jamaah'
        $user = Auth::guard('jamaah')->user();

        if (!$user) {
            // Jika diakses langsung tanpa login (fallback), arahkan ke login
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // 2. Ambil data tabungan milik user ini
        $hewanQurbanList = TabunganHewanQurban::with(['pemasukanTabunganQurban' => function ($query) {
                                $query->orderBy('tanggal', 'desc');
                            }])
                            ->where('id_jamaah', $user->id) // Filter berdasarkan ID Jamaah yang login
                            ->orderBy('created_at', 'desc')
                            ->get();

        // 3. Olah data untuk ringkasan (Summary)
        $totalTarget = $hewanQurbanList->sum('total_harga_hewan_qurban');
        
        // Hitung total terkumpul dari semua tabungan
        $totalTerkumpul = $hewanQurbanList->reduce(function ($carry, $item) {
            return $carry + $item->pemasukanTabunganQurban->sum('nominal');
        }, 0);

        $totalKekurangan = $totalTarget - $totalTerkumpul;
        $persentase = ($totalTarget > 0) ? ($totalTerkumpul / $totalTarget) * 100 : 0;

        // Hitung jumlah hewan
        $jumlahKambing = $hewanQurbanList->whereIn('nama_hewan', ['kambing', 'domba'])->sum('total_hewan');
        $jumlahSapi = $hewanQurbanList->whereIn('nama_hewan', ['sapi', 'kerbau'])->sum('total_hewan');
        $jumlahUnta = $hewanQurbanList->where('nama_hewan', 'unta')->sum('total_hewan');

        // String Ringkasan Hewan
        $ringkasanArr = [];
        if ($jumlahKambing > 0) $ringkasanArr[] = "$jumlahKambing Kambing/Domba";
        if ($jumlahSapi > 0) $ringkasanArr[] = "$jumlahSapi Sapi/Kerbau";
        if ($jumlahUnta > 0) $ringkasanArr[] = "$jumlahUnta Unta";
        $ringkasanHewanString = empty($ringkasanArr) ? 'Belum ada hewan' : implode(', ', $ringkasanArr);

        // Gabungkan semua riwayat setoran untuk tabel bawah
        $semuaRiwayatSetoran = collect();
        foreach ($hewanQurbanList as $tabungan) {
            foreach ($tabungan->pemasukanTabunganQurban as $setoran) {
                // Tambahkan info nama hewan ke object setoran biar jelas di tabel
                $setoran->nama_hewan_info = $tabungan->nama_hewan; 
                $semuaRiwayatSetoran->push($setoran);
            }
        }
        $semuaRiwayatSetoran = $semuaRiwayatSetoran->sortByDesc('tanggal');

        return view('public.tabungan-qurban-saya', compact(
            'user',
            'hewanQurbanList',
            'totalTarget',
            'totalTerkumpul',
            'totalKekurangan',
            'persentase',
            'ringkasanHewanString',
            'semuaRiwayatSetoran'
        ));
    }
}