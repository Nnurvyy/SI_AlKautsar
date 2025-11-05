<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KategoriPemasukan;
use Illuminate\Support\Str;

class KategoriPemasukanSeeder extends Seeder
{
    public function run(): void
    {
        $data = ['Donasi', 'SPP', 'Infaq'];

        foreach ($data as $nama) {
            KategoriPemasukan::firstOrCreate(
                ['nama_kategori_pemasukan' => $nama],
                ['id_kategori_pemasukan' => Str::uuid()]
            );
        }
    }
}
