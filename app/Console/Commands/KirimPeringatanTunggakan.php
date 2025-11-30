<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TabunganHewanQurban;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class KirimPeringatanTunggakan extends Command
{
    protected $signature = 'qurban:kirim-peringatan';
    protected $description = 'Kirim email peringatan ke penabung qurban (tipe cicilan) yang tidak memenuhi target akumulasi bulanan.';

    public function handle()
    {
        $this->info('Memulai pengecekan tunggakan tabungan qurban...');

        $tabunganList = TabunganHewanQurban::with(['jamaah', 'details.hewan', 'pemasukanTabunganQurban'])
            ->where('saving_type', 'cicilan')
            ->where('status', 'disetujui') 
            // Kita hapus whereRaw ini agar perhitungan lebih akurat di PHP (karena ada filter success)
            // ->whereRaw('total_tabungan < total_harga_hewan_qurban') 
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
            // Validasi data dasar & Hindari Division by Zero
            if ($tabungan->duration_months <= 0 || !$tabungan->jamaah || $tabungan->total_harga_hewan_qurban <= 0) {
                $bar->advance();
                continue;
            }

            $tanggalMulai = Carbon::parse($tabungan->tanggal_pembuatan);

            // 1. Hitung Bulan Berjalan (Selisih Bulan Kalender)
            $monthsPassed = (($tanggalHariIni->year - $tanggalMulai->year) * 12) + ($tanggalHariIni->month - $tanggalMulai->month);

            // Batasi maksimal durasi (jangan menagih lebih dari durasi kontrak)
            $monthsPassed = min($monthsPassed, $tabungan->duration_months);

            // Jika masih bulan pertama (bulan pendaftaran), beri grace period (tidak ditagih)
            if ($monthsPassed <= 0) {
                $bar->advance();
                continue;
            }

            // 2. Hitung Target Seharusnya
            $installmentAmount = round($tabungan->total_harga_hewan_qurban / $tabungan->duration_months);
            $accumulatedTarget = $installmentAmount * $monthsPassed;

            // 3. [PERBAIKAN PENTING] Hitung Uang Masuk (HANYA SUCCESS)
            // Karena pakai Tripay, kita harus abaikan status 'pending'/'failed'
            $totalTerkumpul = $tabungan->pemasukanTabunganQurban
                ->where('status', 'success') // <--- INI KUNCI PERBAIKANNYA
                ->sum('nominal');

            // Jika sudah lunas total, skip
            if ($totalTerkumpul >= $tabungan->total_harga_hewan_qurban) {
                $bar->advance();
                continue;
            }

            // 4. Cek Apakah Menunggak dari Target Bulanan
            if ($totalTerkumpul < $accumulatedTarget) {
                $penunggakDitemukan++;
                $jamaah = $tabungan->jamaah;
                $sisaKekurangan = $accumulatedTarget - $totalTerkumpul;

                // Format List Hewan
                $listHewanStr = $tabungan->details->map(function($detail) {
                    $namaHewan = $detail->hewan ? $detail->hewan->nama_hewan : 'Hewan';
                    return "{$detail->jumlah_hewan} ekor {$namaHewan}";
                })->join(', ');

                // Kirim Email
                if ($jamaah->email) {
                    $teksEmail = "Assalamu'alaikum Warahmatullahi Wabarakatuh, Sdr/i {$jamaah->name}.\n\n"
                        . "Semoga Anda dalam keadaan sehat walafiat.\n\n"
                        . "Kami menginformasikan status Tabungan Qurban Anda:\n"
                        . "--------------------------------------------------\n"
                        . "No. Tabungan    : " . substr($tabungan->id_tabungan_hewan_qurban, 0, 8) . "\n"
                        . "Rincian Hewan   : {$listHewanStr}\n"
                        . "Target Total    : Rp " . number_format($tabungan->total_harga_hewan_qurban, 0, ',', '.') . "\n"
                        . "Target s/d Bln $monthsPassed : Rp " . number_format($accumulatedTarget, 0, ',', '.') . "\n"
                        . "--------------------------------------------------\n\n"
                        . "Total Setoran Masuk (Verified): Rp " . number_format($totalTerkumpul, 0, ',', '.') . ".\n\n"
                        . "Saat ini terdapat selisih/tunggakan target sebesar: **Rp " . number_format($sisaKekurangan, 0, ',', '.') . "**.\n\n"
                        . "Mohon kesediaannya untuk melakukan setoran agar target Qurban tercapai tepat waktu.\n"
                        . "Abaikan pesan ini jika Anda baru saja melakukan pembayaran (sedang proses verifikasi).\n\n"
                        . "Wassalamu'alaikum,\n"
                        . "Admin Masjid";

                    try {
                        Mail::raw($teksEmail, function ($message) use ($jamaah) {
                            $message->to($jamaah->email, $jamaah->name)
                                ->subject('Peringatan Tunggakan Tabungan Qurban');
                        });
                        Log::info("Email tunggakan terkirim ke: {$jamaah->email}");
                    } catch (\Exception $e) {
                        Log::error("Gagal kirim email ke {$jamaah->email}: " . $e->getMessage());
                    }
                }
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Selesai. Total {$penunggakDitemukan} email peringatan dikirim.");
        
        return 0;
    }
}