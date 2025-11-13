<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pengguna;
use App\Models\TabunganHewanQurban;
use App\Models\PemasukanTabunganQurban;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class JokoTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Buat Pengguna "Mas Joko"
        // Kita gunakan firstOrCreate untuk menghindari duplikat jika seeder dijalankan lagi
        $userJoko = Pengguna::firstOrCreate(
            ['email' => 'arctant2.5one@gmail.com'],
            [
                'id_pengguna' => (string) Str::uuid(), // Sesuai dengan struktur UUID Anda
                'nama' => 'Mas Joko',
                'password' => Hash::make('password123'), // Password default, bisa diganti
                'role' => 'publik' // Pastikan role 'publik' ada di database Anda
            ]
        );

        $this->command->info('Pengguna "Mas Joko" berhasil dibuat/ditemukan.');

        // 2. Buat Tabungan Qurban untuk Mas Joko
        // Kita buat 1 tabungan kambing yang belum lunas
        $tabunganJoko = TabunganHewanQurban::firstOrCreate(
            ['id_pengguna' => $userJoko->id_pengguna, 'nama_hewan' => 'kambing'],
            [
                'total_hewan' => 1,
                'total_harga_hewan_qurban' => 5000000, // Target 5 Juta
                'total_tabungan' => 0 // Kolom ini ada di model Anda
            ]
        );

        // 3. Buat Setoran yang "Menunggak"
        // Perintah (Command) Anda akan berjalan di bulan NOVEMBER 2025
        // Perintah itu akan mengecek setoran di bulan OKTOBER 2025
        // Kita buat setoran di bulan SEPTEMBER 2025 agar dia terdeteksi menunggak.
        PemasukanTabunganQurban::firstOrCreate(
            [
                'id_tabungan_hewan_qurban' => $tabunganJoko->id_tabungan_hewan_qurban,
                'nominal' => 1000000
            ],
            [
                'tanggal' => Carbon::parse('2025-09-15') // <-- SETORAN TERAKHIR DI SEPTEMBER
            ]
        );

        $this->command->info('Tabungan qurban menunggak untuk Mas Joko berhasil dibuat.');
    }
}
