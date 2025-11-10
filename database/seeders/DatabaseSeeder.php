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
            JokoTestSeeder::class,
            KategoriPemasukanSeeder::class,
            KategoriPengeluaranSeeder::class,
            PengeluaranSeeder::class,
            PenggunaSeeder::class,
            PemasukanSeeder::class,
            QurbanSeeder::class,
        ]);
    }
}
