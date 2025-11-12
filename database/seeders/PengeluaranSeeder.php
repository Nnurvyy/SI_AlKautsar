<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pengeluaran;
use App\Models\KategoriPengeluaran;
use Carbon\Carbon;
use Illuminate\Support\Str;

class PengeluaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cegah duplikat: kalau sudah ada data, skip
        if (Pengeluaran::count() > 0) {
            $this->command->warn('Data pengeluaran sudah ada. Seeder dilewati.');
            return;
        }

        // Ambil semua kategori pengeluaran
        $kategoriPengeluaran = KategoriPengeluaran::all();

        if ($kategoriPengeluaran->isEmpty()) {
            $this->command->warn('Kategori Pengeluaran kosong. Jalankan KategoriPengeluaranSeeder dulu.');
            return;
        }

        $deskripsiList = [
            'Tagihan Listrik PLN',
            'Gaji Karyawan',
            'Pembelian ATK Kantor',
            'Biaya Konsumsi Rapat',
            'Perbaikan dan Pemeliharaan',
        ];

        // ---------------------------
        // Data bulan ini
        // ---------------------------
        $bulanIni = Carbon::now();
        for ($i = 0; $i < 10; $i++) {
            $tanggal = Carbon::create(
                $bulanIni->year,
                $bulanIni->month,
                rand(1, $bulanIni->day)
            );

            Pengeluaran::create([
                'id_pengeluaran' => Str::uuid(),
                'tanggal' => $tanggal,
                'nominal' => rand(20, 100) * 10000, // (200rb - 1jt)
                'id_kategori_pengeluaran' => $kategoriPengeluaran->random()->id_kategori_pengeluaran,
                'deskripsi' => $deskripsiList[array_rand($deskripsiList)],
            ]);
        }

        // ---------------------------
        // Data bulan lalu
        // ---------------------------
        $bulanLalu = Carbon::now()->subMonth();
        $hariBulanLalu = $bulanLalu->daysInMonth;

        for ($i = 0; $i < 10; $i++) {
            $tanggal = Carbon::create(
                $bulanLalu->year,
                $bulanLalu->month,
                rand(1, $hariBulanLalu)
            );

            Pengeluaran::create([
                'id_pengeluaran' => Str::uuid(), // UUID wajib
                'tanggal' => $tanggal,
                'nominal' => rand(20, 100) * 10000,
                'id_kategori_pengeluaran' => $kategoriPengeluaran->random()->id_kategori_pengeluaran,
                'deskripsi' => $deskripsiList[array_rand($deskripsiList)] . ' (Bulan Lalu)',
            ]);
        }
    }
}
