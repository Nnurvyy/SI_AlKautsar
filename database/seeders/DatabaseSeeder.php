<?php

namespace Database\Seeders;

use App\Models\Pemasukan;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PenggunaSeeder::class,
            KategoriPemasukanSeeder::class,
            KategoriPengeluaranSeeder::class,
            PemasukanSeeder::class,
            PengeluaranSeeder::class,
            QurbanSeeder::class,
        ]);
    }
}
