<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TabunganHewanQurban;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Mail\PeringatanTunggakanMail; // <--- Jangan lupa import ini

class KirimPeringatanTunggakan extends Command
{
    protected $signature = 'qurban:kirim-peringatan';
    protected $description = 'Kirim email peringatan ke penabung qurban.';

    public function handle()
    {
        $this->info('Memulai pengecekan tunggakan tabungan qurban...');

        $tabunganList = TabunganHewanQurban::with(['jamaah', 'details.hewan', 'pemasukanTabunganQurban'])
            ->where('saving_type', 'cicilan')
            ->where('status', 'disetujui')
            ->get();

        if ($tabunganList->isEmpty()) {
            $this->info('Tidak ditemukan tabungan cicilan aktif.');
            return 0;
        }

        $penunggakDitemukan = 0;
        $tanggalHariIni = Carbon::now();

        $bar = $this->output->createProgressBar(count($tabunganList));
        $bar->start();

        foreach ($tabunganList as $tabungan) {

            // Validasi data dasar
            if ($tabungan->duration_months <= 0 || !$tabungan->jamaah || $tabungan->total_harga_hewan_qurban <= 0) {
                $bar->advance();
                continue;
            }

            $tanggalMulai = Carbon::parse($tabungan->tanggal_pembuatan);
            $monthsPassed = (($tanggalHariIni->year - $tanggalMulai->year) * 12) + ($tanggalHariIni->month - $tanggalMulai->month);
            $monthsPassed = min($monthsPassed, $tabungan->duration_months);

            if ($monthsPassed <= 0) {
                $bar->advance();
                continue;
            }

            $installmentAmount = round($tabungan->total_harga_hewan_qurban / $tabungan->duration_months);
            $accumulatedTarget = $installmentAmount * $monthsPassed;

            $totalTerkumpul = $tabungan->pemasukanTabunganQurban
                ->where('status', 'success')
                ->sum('nominal');

            if ($totalTerkumpul >= $tabungan->total_harga_hewan_qurban) {
                $bar->advance();
                continue;
            }

            // Jika menunggak
            if ($totalTerkumpul < $accumulatedTarget) {
                $penunggakDitemukan++;
                $jamaah = $tabungan->jamaah;
                $sisaKekurangan = $accumulatedTarget - $totalTerkumpul;

                $listHewanStr = $tabungan->details->map(function ($detail) {
                    $namaHewan = $detail->hewan ? $detail->hewan->nama_hewan : 'Hewan';
                    return "{$detail->jumlah_hewan} ekor {$namaHewan}";
                })->join(', ');

                if ($jamaah->email) {
                    // Siapkan data untuk dikirim ke Mailable
                    $emailData = [
                        'nama_jamaah' => $jamaah->name,
                        'no_tabungan' => $tabungan->id_tabungan_hewan_qurban,
                        'list_hewan' => $listHewanStr,
                        'target_total' => $tabungan->total_harga_hewan_qurban,
                        'bulan_ke' => $monthsPassed,
                        'target_akumulasi' => $accumulatedTarget,
                        'total_terkumpul' => $totalTerkumpul,
                        'sisa_kekurangan' => $sisaKekurangan,
                    ];

                    try {
                        // PERUBAHAN UTAMA DI SINI:
                        // Gunakan class Mailable dan method queue()
                        Mail::to($jamaah->email, $jamaah->name)
                            ->queue(new PeringatanTunggakanMail($emailData));

                        Log::info("Antrian email tunggakan dibuat untuk: {$jamaah->email}");
                    } catch (\Exception $e) {
                        Log::error("Gagal antri email ke {$jamaah->email}: " . $e->getMessage());
                    }
                }
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Selesai. Total {$penunggakDitemukan} email peringatan telah masuk antrian.");

        return 0;
    }
}