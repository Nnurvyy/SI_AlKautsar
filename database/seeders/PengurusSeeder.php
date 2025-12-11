<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pengurus;
use Illuminate\Support\Facades\Hash;

class PengurusSeeder extends Seeder
{
    public function run(): void
    {
        Pengurus::create([
            'name' => 'admin',
            'email' => 'pengurus@example.com',
            'password' => Hash::make('password'),
            'google_id' => null,
            'email_verified_at' => now(),
        ]);
    }
}
