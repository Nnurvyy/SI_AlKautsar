<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pengguna;
use App\Models\TabunganHewanQurban;
use App\Models\PemasukanTabunganQurban;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str; // <-- 1. TAMBAHKAN INI

class QurbanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Buat 3 Pengguna dummy
        $userAhmad = Pengguna::firstOrCreate(
            ['email' => 'ahmad.subagja@example.com'],
            [
                'id_pengguna' => (string) Str::uuid(), // <-- 2. TAMBAHKAN INI
                'nama' => 'Ahmad Subagja',
                'password' => Hash::make('password123'),
                'role' => 'publik'
            ]
        );

        $userBudi = Pengguna::firstOrCreate(
            ['email' => 'budi.santoso@example.com'],
            [
                'id_pengguna' => (string) Str::uuid(), // <-- 2. TAMBAHKAN INI
                'nama' => 'Budi Santoso',
                'password' => Hash::make('password123'),
                'role' => 'publik'
            ]
        );

        $userCitra = Pengguna::firstOrCreate(
            ['email' => 'citra.lestari@example.com'],
            [
                'id_pengguna' => (string) Str::uuid(), // <-- 2. TAMBAHKAN INI
                'nama' => 'Citra Lestari',
                'password' => Hash::make('password123'),
                'role' => 'publik'
            ]
        );

        // 2. Buat Tabungan Hewan Qurban

        $tabunganAhmadSapi = TabunganHewanQurban::create([
            'id_pengguna' => $userAhmad->id_pengguna,
            'nama_hewan' => 'sapi',
            'total_hewan' => 1,
            'total_harga_hewan_qurban' => 21000000,
            'total_tabungan' => 0
        ]);

        $tabunganBudiKambing = TabunganHewanQurban::create([
            'id_pengguna' => $userBudi->id_pengguna,
            'nama_hewan' => 'kambing',
            'total_hewan' => 2,
            'total_harga_hewan_qurban' => 6000000,
            'total_tabungan' => 0
        ]);

        $tabunganCitraDomba = TabunganHewanQurban::create([
            'id_pengguna' => $userCitra->id_pengguna,
            'nama_hewan' => 'domba',
            'total_hewan' => 1,
            'total_harga_hewan_qurban' => 3500000,
            'total_tabungan' => 0
        ]);

        // 3. Buat Riwayat Setoran (Pemasukan)

        PemasukanTabunganQurban::create([
            'id_tabungan_hewan_qurban' => $tabunganAhmadSapi->id_tabungan_hewan_qurban,
            'tanggal' => now()->subDays(45),
            'nominal' => 7000000
        ]);
        PemasukanTabunganQurban::create([
            'id_tabungan_hewan_qurban' => $tabunganAhmadSapi->id_tabungan_hewan_qurban,
            'tanggal' => now()->subDays(20),
            'nominal' => 5000000
        ]);
        PemasukanTabunganQurban::create([
            'id_tabungan_hewan_qurban' => $tabunganAhmadSapi->id_tabungan_hewan_qurban,
            'tanggal' => now()->subDays(2),
            'nominal' => 2500000
        ]);

        PemasukanTabunganQurban::create([
            'id_tabungan_hewan_qurban' => $tabunganBudiKambing->id_tabungan_hewan_qurban,
            'tanggal' => now()->subDays(30),
            'nominal' => 1000000
        ]);
        PemasukanTabunganQurban::create([
            'id_tabungan_hewan_qurban' => $tabunganBudiKambing->id_tabungan_hewan_qurban,
            'tanggal' => now()->subDays(10),
            'nominal' => 1000000
        ]);

        PemasukanTabunganQurban::create([
            'id_tabungan_hewan_qurban' => $tabunganCitraDomba->id_tabungan_hewan_qurban,
            'tanggal' => now()->subDays(50),
            'nominal' => 2000000
        ]);
        PemasukanTabunganQurban::create([
            'id_tabungan_hewan_qurban' => $tabunganCitraDomba->id_tabungan_hewan_qurban,
            'tanggal' => now()->subDays(15),
            'nominal' => 1500000
        ]);
    }
}
