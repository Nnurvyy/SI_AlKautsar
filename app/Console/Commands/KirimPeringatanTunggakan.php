<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TabunganHewanQurban;
use App\Models\Pengguna;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class KirimPeringatanTunggakan extends Command
{
    /**
     * Nama dan signature dari console command.
     * Ini yang akan kita panggil: php artisan qurban:kirim-peringatan
     */
    protected $signature = 'qurban:kirim-peringatan';

    /**
     * Deskripsi dari console command.
     */
    protected $description = 'Kirim email peringatan ke penabung qurban yang menunggak bulan lalu.';

    /**
     * Jalankan logic command.
     */
    public function handle()
    {
        $this->info('Memulai pengecekan tunggakan tabungan qurban...');

        // 1. Tentukan periode "bulan lalu"
        $bulanLalu = Carbon::now()->subMonth();
        $awalBulanLalu = $bulanLalu->copy()->startOfMonth();
        $akhirBulanLalu = $bulanLalu->copy()->endOfMonth();

        $this->info("Mencari penunggak yang tidak bayar pada periode: " . $awalBulanLalu->toDateString() . " s/d " . $akhirBulanLalu->toDateString());

        // 2. Cari semua tabungan yang:
        //    a) Belum lunas
        //    b) TIDAK punya setoran di bulan lalu
        $tabunganMenunggak = TabunganHewanQurban::with('pengguna')
            ->whereRaw(
                '(SELECT COALESCE(SUM(nominal), 0) FROM pemasukan_tabungan_qurban WHERE id_tabungan_hewan_qurban = tabungan_hewan_qurban.id_tabungan_hewan_qurban) < total_harga_hewan_qurban'
            )
            ->whereDoesntHave('pemasukanTabunganQurban', function ($query) use ($awalBulanLalu, $akhirBulanLalu) {
                $query->whereBetween('tanggal', [$awalBulanLalu, $akhirBulanLalu]);
            })
            ->get();

        if ($tabunganMenunggak->isEmpty()) {
            $this->info('Tidak ditemukan penunggak untuk bulan lalu. Pekerjaan selesai.');
            return 0;
        }

        $this->info("Ditemukan " . $tabunganMenunggak->count() . " penunggak. Memulai pengiriman email...");

        // 3. Kirim email ke setiap penunggak
        foreach ($tabunganMenunggak as $tabungan) {
            $pengguna = $tabungan->pengguna;

            // Pastikan pengguna ada dan punya email
            if (!$pengguna || !$pengguna->email) {
                Log::warning("Tabungan ID {$tabungan->id_tabungan_hewan_qurban} menunggak tapi tidak memiliki data pengguna/email.");
                continue;
            }

            // Sesuai permintaan Anda: email teks biasa
            $teksEmail = "Halo, {$pengguna->nama}.\n\n"
                . "Kami dari Pengelola E-Masjid Al Kautsar ingin menginformasikan bahwa kami belum menerima setoran untuk tabungan qurban Anda (Hewan: {$tabungan->nama_hewan}) pada bulan {$bulanLalu->translatedFormat('F Y')}.\n\n"
                . "Mohon untuk segera melakukan setoran agar tabungan Anda lunas tepat waktu.\n\n"
                . "Jika Anda merasa ini adalah kesalahan atau sudah melakukan pembayaran, silakan hubungi kami.\n\n"
                . "Terima kasih.\n\n"
                . "Salam,\n"
                . "Admin Masjid Al Kautsar";

            try {
                // Menggunakan Mail::raw() untuk mengirim teks biasa
                Mail::raw($teksEmail, function ($message) use ($pengguna) {
                    $message->to($pengguna->email, $pengguna->nama)
                        ->subject('Peringatan Tunggakan Tabungan Qurban')
                        ->from(config('mail.from.address'), config('mail.from.name'));
                });

                $this->info("Email peringatan berhasil dikirim ke: {$pengguna->email}");

            } catch (\Exception $e) {
                $this->error("Gagal mengirim email ke {$pengguna->email}: " . $e->getMessage());
                Log::error("Gagal kirim email tunggakan ke {$pengguna->email}: " . $e->getMessage());
            }
        }

        $this->info('Semua email peringatan telah diproses. Pekerjaan selesai.');
        return 0;
    }
}
