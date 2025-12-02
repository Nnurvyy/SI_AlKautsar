<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PemasukanTabunganQurban;
use App\Models\PemasukanDonasi; // <--- Tambahkan Model Donasi
use Carbon\Carbon;

class HapusTransaksiPending extends Command
{
    /**
     * Nama command sama seperti sebelumnya agar cron job tetap jalan
     */
    protected $signature = 'transaksi:bersihkan-pending';

    protected $description = 'Menghapus transaksi (Qurban & Donasi) yang statusnya pending lebih dari 1 jam';

    public function handle()
    {
        $batasWaktu = Carbon::now()->subHour(); // 1 Jam yang lalu

        // 1. Hapus Pending Qurban
        $qurbanExpired = PemasukanTabunganQurban::where('status', 'pending')
                            ->where('created_at', '<', $batasWaktu)
                            ->get();
        
        $countQurban = 0;
        if ($qurbanExpired->count() > 0) {
            foreach ($qurbanExpired as $t) {
                $t->delete();
                $countQurban++;
            }
        }

        // 2. Hapus Pending Donasi (BARU)
        $donasiExpired = PemasukanDonasi::where('status', 'pending')
                            ->where('created_at', '<', $batasWaktu)
                            ->get();

        $countDonasi = 0;
        if ($donasiExpired->count() > 0) {
            foreach ($donasiExpired as $d) {
                $d->delete();
                $countDonasi++;
            }
        }

        // Info ke Log Console
        if ($countQurban > 0 || $countDonasi > 0) {
            $this->info("Pembersihan Selesai: {$countQurban} Qurban dihapus, {$countDonasi} Donasi dihapus.");
        } else {
            $this->info("Tidak ada transaksi pending lama (Aman).");
        }
    }
}