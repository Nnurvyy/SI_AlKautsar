<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KategoriPengeluaran;
use Illuminate\Support\Str;

class KategoriPengeluaranSeeder extends Seeder
{
    public function run(): void
    {
        $data = ['Listrik', 'Gaji Karyawan', 'Operasional Masjid'];

        foreach ($data as $nama) {
            KategoriPengeluaran::firstOrCreate(
                ['nama_kategori_pengeluaran' => $nama],
                ['id_kategori_pengeluaran' => Str::uuid()]
            );
        }
    }
}
