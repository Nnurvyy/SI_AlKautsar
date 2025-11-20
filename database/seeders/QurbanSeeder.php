<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Jamaah;
use App\Models\TabunganHewanQurban;
use App\Models\PemasukanTabunganQurban;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class QurbanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Cari jamaah "joko" yang dibuat oleh JamaahSeeder
        $joko = Jamaah::where('email', 'arctant2.5one@gmail.com')->first();

        // 2. Hanya jalankan jika "joko" ditemukan
        if ($joko) {
            // Buat 1 Tabungan Hewan Qurban untuk Joko
            $tabunganJoko = TabunganHewanQurban::create([
                'id_tabungan_hewan_qurban' => (string) Str::uuid(), // Buat UUID untuk tabungan
                'id_jamaah' => $joko->id, // Ambil ID dari joko
                'nama_hewan' => 'kambing',
                'total_hewan' => 1,
                'total_harga_hewan_qurban' => 3500000, // Misal harga kambing 3.5jt
                'total_tabungan' => 0,
                // --- TAMBAHAN WAJIB BARU ---
                'saving_type' => 'cicilan',
                'duration_months' => 10, // 10 bulan cicilan
                // --- AKHIR TAMBAHAN WAJIB BARU ---
            ]);

            // 3. Buat Riwayat Setoran (Pemasukan) untuk tabungan Joko
            PemasukanTabunganQurban::create([
                'id_tabungan_hewan_qurban' => $tabunganJoko->id_tabungan_hewan_qurban,
                'tanggal' => now()->subDays(30),
                'nominal' => 1000000
            ]);
            PemasukanTabunganQurban::create([
                'id_tabungan_hewan_qurban' => $tabunganJoko->id_tabungan_hewan_qurban,
                'tanggal' => now()->subDays(15),
                'nominal' => 1000000
            ]);
            PemasukanTabunganQurban::create([
                'id_tabungan_hewan_qurban' => $tabunganJoko->id_tabungan_hewan_qurban,
                'tanggal' => now()->subDays(2),
                'nominal' => 500000
            ]);
        }
    }
}
