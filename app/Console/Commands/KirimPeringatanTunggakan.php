<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TabunganHewanQurban;
use App\Models\Jamaah;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class KirimPeringatanTunggakan extends Command
{
    /**
     * Nama dan signature dari console command.
     */
    protected $signature = 'qurban:kirim-peringatan';

    /**
     * Deskripsi dari console command.
     */
    protected $description = 'Kirim email peringatan ke penabung qurban (tipe cicilan) yang tidak memenuhi target akumulasi bulanan.';

    /**
     * Jalankan logic command.
     */
    public function handle()
    {
        $this->info('Memulai pengecekan tunggakan tabungan qurban berdasarkan target akumulasi...');

        // 1. Ambil semua tabungan yang belum lunas dan bertipe 'cicilan'
        $tabunganCicilanAktif = TabunganHewanQurban::with('jamaah')
            ->where('saving_type', 'cicilan')
            ->whereRaw(
                '(SELECT COALESCE(SUM(nominal), 0) FROM pemasukan_tabungan_qurban WHERE id_tabungan_hewan_qurban = tabungan_hewan_qurban.id_tabungan_hewan_qurban) < total_harga_hewan_qurban'
            )
            ->get();

        if ($tabunganCicilanAktif->isEmpty()) {
            $this->info('Tidak ditemukan tabungan cicilan aktif yang belum lunas. Pekerjaan selesai.');
            return 0;
        }

        $penunggakDitemukan = 0;
        $tanggalHariIni = Carbon::now();

        foreach ($tabunganCicilanAktif as $tabungan) {
            // Pastikan durasi cicilan valid
            if ($tabungan->duration_months <= 0) continue;

            // 2. Hitung Angsuran Bulanan (cicilan per bulan)
            $installmentAmount = round($tabungan->total_harga_hewan_qurban / $tabungan->duration_months);

            // 3. Hitung Jumlah Bulan Berlalu (sejak dibuat sampai bulan INI)
            // Menggunakan created_at sebagai tanggal mulai
            $tanggalMulai = Carbon::parse($tabungan->created_at);

            // Hitung selisih bulan (termasuk bulan saat ini sebagai 1 bulan)
            // Misal created_at 15 Nov, hari ini 19 Nov -> 1 bulan berlalu
            // Misal created_at 15 Nov, hari ini 5 Dec -> 2 bulan berlalu (Nov & Dec)
            $monthsPassed = $tanggalHariIni->diffInMonths($tanggalMulai) + 1;

            // Jika tabungan sudah melewati total durasi, tapi belum lunas, anggap semua bulan sudah berlalu
            $monthsPassed = min($monthsPassed, $tabungan->duration_months);

            // 4. Hitung Target Akumulasi yang seharusnya sudah terkumpul
            // Target Akumulasi = Cicilan Bulanan * Bulan Berlalu
            $accumulatedTarget = $installmentAmount * $monthsPassed;

            // 5. Cek Tunggakan: Apakah total tabungan saat ini KURANG dari Target Akumulasi
            $totalTerkumpul = $tabungan->pemasukanTabunganQurban->sum('nominal');

            if ($totalTerkumpul < $accumulatedTarget) {
                // PENUNGGAK DITEMUKAN
                $penunggakDitemukan++;
                $jamaah = $tabungan->jamaah;

                if (!$jamaah || !$jamaah->email) {
                    Log::warning("Tabungan ID {$tabungan->id_tabungan_hewan_qurban} menunggak tapi tidak memiliki data jamaah/email.");
                    continue;
                }

                $sisaKekurangan = $accumulatedTarget - $totalTerkumpul;

                $teksEmail = "Halo, {$jamaah->name}.\n\n"
                    . "Kami dari Pengelola E-Masjid Al Kautsar ingin menginformasikan bahwa tabungan qurban Anda ({$tabungan->nama_hewan}, Target: " . number_format($tabungan->total_harga_hewan_qurban, 0, ',', '.') . " dalam {$tabungan->duration_months} bulan) mengalami tunggakan.\n\n"
                    . "Berdasarkan cicilan bulanan sebesar Rp " . number_format($installmentAmount, 0, ',', '.') . ", target setoran akumulasi Anda hingga bulan ini seharusnya Rp " . number_format($accumulatedTarget, 0, ',', '.') . ".\n"
                    . "Namun, total setoran Anda saat ini baru mencapai Rp " . number_format($totalTerkumpul, 0, ',', '.') . ".\n\n"
                    . "Kekurangan setoran untuk memenuhi target akumulasi Anda adalah **Rp " . number_format($sisaKekurangan, 0, ',', '.') . "**.\n\n"
                    . "Mohon segera melakukan setoran minimal senilai kekurangan di atas agar tabungan Anda kembali normal.\n\n"
                    . "Terima kasih.\n\n"
                    . "Salam,\n"
                    . "Admin Masjid Al Kautsar";

                try {
                    Mail::raw($teksEmail, function ($message) use ($jamaah) {
                        $message->to($jamaah->email, $jamaah->name)
                            ->subject('Peringatan Tunggakan Tabungan Qurban (Target Akumulasi)')
                            ->from(config('mail.from.address'), config('mail.from.name'));
                    });

                    $this->info("Email peringatan berhasil dikirim ke: {$jamaah->email} (Kekurangan: Rp " . number_format($sisaKekurangan, 0, ',', '.') . ")");

                } catch (\Exception $e) {
                    $this->error("Gagal mengirim email ke {$jamaah->email}: " . $e->getMessage());
                    Log::error("Gagal kirim email tunggakan ke {$jamaah->email}: " . $e->getMessage());
                }

            } else {
                // Sudah memenuhi target akumulasi, tidak perlu kirim peringatan
                $this->info("Tabungan ID {$tabungan->id_tabungan_hewan_qurban} (Cicilan) sudah memenuhi target akumulasi. Dilewati.");
            }
        }

        $this->info("Total {$penunggakDitemukan} email peringatan telah diproses. Pekerjaan selesai.");
        return 0;
    }
}
