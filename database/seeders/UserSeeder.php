<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User; // 1. (WAJIB) Import model User
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin Pesantren',
            'email' => 'admin@alkautsar.com',
            'password' => Hash::make('password123') // Gunakan Hash::make() bukan bcrypt()
        ]);
    }
}
