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
            // Cek yang belum lunas (total tabungan < harga deal)
            ->whereRaw('total_tabungan < total_harga_hewan_qurban') 
            ->get();

        if ($tabunganList->isEmpty()) {
            $this->info('Tidak ditemukan tabungan cicilan aktif yang belum lunas.');
            return 0;
        }

        $penunggakDitemukan = 0;
        $tanggalHariIni = Carbon::now();

        $bar = $this->output->createProgressBar(count($tabunganList));
        $bar->start();

        foreach ($tabunganList as $tabungan) {
            // Validasi data
            if ($tabungan->duration_months <= 0 || !$tabungan->jamaah) {
                $bar->advance();
                continue;
            }

            // [PERBAIKAN 1] Gunakan tanggal_pembuatan, bukan created_at
            $tanggalMulai = Carbon::parse($tabungan->tanggal_pembuatan);

            // [PERBAIKAN 2] Rumus Selisih Bulan Kalender (Mei ke Juni = 1 Bulan)
            // Logic: ((Tahun Sekarang - Tahun Buat) * 12) + (Bulan Sekarang - Bulan Buat)
            $monthsPassed = (($tanggalHariIni->year - $tanggalMulai->year) * 12) + ($tanggalHariIni->month - $tanggalMulai->month);

            // Cap maksimal agar tidak melebihi durasi kontrak
            $monthsPassed = min($monthsPassed, $tabungan->duration_months);

            // Jika diff <= 0, berarti masih di bulan yang sama saat mendaftar (Grace Period)
            if ($monthsPassed <= 0) {
                $bar->advance();
                continue;
            }

            // Hitung Angsuran & Target
            $installmentAmount = round($tabungan->total_harga_hewan_qurban / $tabungan->duration_months);
            
            // Target: Harusnya sudah bayar untuk bulan-bulan sebelumnya
            $accumulatedTarget = $installmentAmount * $monthsPassed;

            // Hitung Uang Masuk
            $totalTerkumpul = $tabungan->pemasukanTabunganQurban->sum('nominal');

            // Cek Menunggak
            if ($totalTerkumpul < $accumulatedTarget) {
                $penunggakDitemukan++;
                $jamaah = $tabungan->jamaah;
                $sisaKekurangan = $accumulatedTarget - $totalTerkumpul;

                // String Hewan
                $listHewanStr = $tabungan->details->map(function($detail) {
                    $namaHewan = $detail->hewan ? $detail->hewan->nama_hewan : 'Hewan';
                    return "{$detail->jumlah_hewan} ekor {$namaHewan}";
                })->join(', ');

                // Kirim Email
                if ($jamaah->email) {
                    $teksEmail = "Assalamu'alaikum Warahmatullahi Wabarakatuh, Sdr/i {$jamaah->name}.\n\n"
                        . "Semoga Anda dalam keadaan sehat walafiat.\n\n"
                        . "Kami dari Pengelola Tabungan Qurban Masjid Al Kautsar menginformasikan status tabungan Anda:\n"
                        . "--------------------------------------------------\n"
                        . "Tanggal Daftar  : " . $tanggalMulai->format('d M Y') . "\n" // Info Tanggal Ditambah
                        . "Rincian Hewan   : {$listHewanStr}\n"
                        . "Total Deal      : Rp " . number_format($tabungan->total_harga_hewan_qurban, 0, ',', '.') . "\n"
                        . "Durasi          : {$tabungan->duration_months} Bulan\n"
                        . "Cicilan/bulan   : Rp " . number_format($installmentAmount, 0, ',', '.') . "\n"
                        . "--------------------------------------------------\n\n"
                        . "Hingga bulan ke-{$monthsPassed} (sejak pendaftaran), target akumulasi setoran seharusnya: Rp " . number_format($accumulatedTarget, 0, ',', '.') . ".\n"
                        . "Total setoran Anda saat ini: Rp " . number_format($totalTerkumpul, 0, ',', '.') . ".\n\n"
                        . "Terdapat kekurangan/tunggakan sebesar: **Rp " . number_format($sisaKekurangan, 0, ',', '.') . "**.\n\n"
                        . "Mohon kesediaannya untuk melakukan setoran agar target Qurban dapat tercapai tepat waktu.\n\n"
                        . "Wassalamu'alaikum Warahmatullahi Wabarakatuh,\n"
                        . "Admin Masjid Al Kautsar";

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
        $this->info("Selesai. Total {$penunggakDitemukan} jamaah terdeteksi menunggak dan diproses.");
        
        return 0;
    }
}