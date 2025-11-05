<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pengguna;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PenggunaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin
        Pengguna::firstOrCreate(
            ['email' => 'admin@alkautsar.com'], // kriteria unik
            [
                'id_pengguna' => Str::uuid(),
                'nama' => 'Admin Pesantren',
                'password' => Hash::make('password123'),
                'role' => 'admin',
            ]
        );

        // Pengguna Publik
        Pengguna::firstOrCreate(
            ['email' => 'hasbi@contoh.com'], // kriteria unik
            [
                'id_pengguna' => Str::uuid(),
                'nama' => 'Hasbi Publik',
                'password' => Hash::make('hasbi123'),
                'role' => 'publik',
            ]
        );
    }
}
