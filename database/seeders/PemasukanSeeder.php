<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pemasukan;
use App\Models\KategoriPemasukan;
use Carbon\Carbon;
use Illuminate\Support\Str;

class PemasukanSeeder extends Seeder
{
    public function run(): void
    {
        // Cegah duplikat: kalau sudah ada data, skip
        if (Pemasukan::count() > 0) {
            $this->command->warn('Data pemasukan sudah ada. Seeder dilewati.');
            return;
        }

        // Ambil semua kategori pemasukan
        $kategoriPemasukan = KategoriPemasukan::all();

        // Kalau belum ada kategori, kasih peringatan
        if ($kategoriPemasukan->isEmpty()) {
            $this->command->warn('Kategori Pemasukan kosong. Jalankan KategoriPemasukanSeeder dulu.');
            return;
        }

        $deskripsiList = [
            'Donasi Hamba Allah',
            'Pembayaran SPP Santri',
            'Infaq Jumat Berkah',
            'Sumbangan Pembangunan',
            'Titipan Zakat Mal',
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

            Pemasukan::create([
                'id_pemasukan' => Str::uuid(), // <--- wajib karena UUID
                'tanggal' => $tanggal,
                'nominal' => rand(50, 200) * 10000,
                'id_kategori_pemasukan' => $kategoriPemasukan->random()->id_kategori_pemasukan,
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

            Pemasukan::create([
                'id_pemasukan' => Str::uuid(),
                'tanggal' => $tanggal,
                'nominal' => rand(50, 200) * 10000,
                'id_kategori_pemasukan' => $kategoriPemasukan->random()->id_kategori_pemasukan,
                'deskripsi' => $deskripsiList[array_rand($deskripsiList)] . ' (Bulan Lalu)',
            ]);
        }
    }
}
