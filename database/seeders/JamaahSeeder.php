<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Jamaah;
use Illuminate\Support\Facades\Hash;

class JamaahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Jamaah::firstOrCreate(
            ['email' => 'arctant2.5one@gmail.com'],
            [
                'name' => 'joko',
                // Buat password default, misal 'password'
                'password' => Hash::make('password'),
                'no_hp' => '081234567890',
            ]
        );
    }
}
