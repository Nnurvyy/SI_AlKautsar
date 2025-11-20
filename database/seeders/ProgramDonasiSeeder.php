<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProgramDonasi;
use Carbon\Carbon;

class ProgramDonasiSeeder extends Seeder
{
    public function run(): void
    {
        ProgramDonasi::create([
            'judul' => 'PEMBANGUNAN MASJID',
            'deskripsi' => 'Bantu perluasan dan renovasi masjid untuk kenyamanan ibadah jamaah.',
            'gambar' => 'images/donasi/pembangunan-masjid.jpg',
            'target_dana' => 150000000,
            'dana_terkumpul' => 75432123,
            'tanggal_selesai' => Carbon::now()->addDays(90), // Sisa 90 hari
        ]);

        ProgramDonasi::create([
            'judul' => 'YATIM, FAKIR MISKIN & DHUAFA',
            'deskripsi' => 'Salurkan sedekah Anda untuk program santunan anak yatim dan dhuafa di sekitar masjid.',
            'gambar' => 'images/donasi/yatim.jpeg',
            'target_dana' => 40000000,
            'dana_terkumpul' => 25123456,
            'tanggal_selesai' => Carbon::now()->addDays(30), // Sisa 30 hari
        ]);

        ProgramDonasi::create([
            'judul' => 'OPERASIONAL MASJID',
            'deskripsi' => 'Dukung kegiatan dakwah dan biaya operasional masjid agar tetap makmur.',
            'gambar' => 'https://via.placeholder.com/800x600/4682B4/ffffff?text=Operasional+Masjid',
            'target_dana' => 20000000,
            'dana_terkumpul' => 5000000,
            'tanggal_selesai' => Carbon::now()->addDays(45), // Sisa 45 hari
        ]);
    }
}