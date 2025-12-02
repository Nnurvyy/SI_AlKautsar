<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PemasukanTabunganQurban;
use Carbon\Carbon;

class HapusTransaksiPending extends Command
{
    /**
     * Nama command yang akan dipanggil nanti.
     */
    protected $signature = 'transaksi:bersihkan-pending';

    /**
     * Deskripsi command.
     */
    protected $description = 'Menghapus transaksi qurban yang statusnya pending lebih dari 1 jam';

    /**
     * Eksekusi logic penghapusan.
     */
    public function handle()
    {
        // Cari data yang statusnya 'pending' DAN dibuat lebih dari 1 jam yang lalu
        $batasWaktu = Carbon::now()->subHour(); // Waktu saat ini dikurangi 1 jam

        $transaksiExpired = PemasukanTabunganQurban::where('status', 'pending')
                            ->where('created_at', '<', $batasWaktu)
                            ->get();

        $jumlah = $transaksiExpired->count();

        if ($jumlah > 0) {
            foreach ($transaksiExpired as $transaksi) {
                // Opsional: Hapus file bukti bayar jika ada, sebelum delete record
                $transaksi->delete();
            }
            $this->info("Berhasil menghapus {$jumlah} transaksi pending yang kadaluarsa.");
        } else {
            $this->info("Tidak ada transaksi pending yang perlu dihapus.");
        }
    }
}