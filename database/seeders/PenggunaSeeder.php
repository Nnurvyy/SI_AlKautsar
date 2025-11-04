<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Pengguna; // 1. (WAJIB) Import model User
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


class PenggunaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Admin
        Pengguna::create([
            'id_pengguna' => Str::uuid(),
            'nama' => 'Admin Pesantren',
            'email' => 'admin@alkautsar.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        // Pengguna Publik
        Pengguna::create([
            'id_pengguna' => Str::uuid(),
            'nama' => 'Hasbi Publik',
            'email' => 'hasbi@contoh.com',
            'password' => Hash::make('hasbi123'),
            'role' => 'publik',
        ]);
    }
}
