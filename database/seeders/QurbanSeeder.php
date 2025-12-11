<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Jamaah;
use App\Models\TabunganHewanQurban;
use App\Models\PemasukanTabunganQurban;
use App\Models\HewanQurban; 
use App\Models\DetailTabunganHewanQurban; 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon; 

class QurbanSeeder extends Seeder
{
        public function run(): void
    {
        
        
        $kambingReguler = HewanQurban::where('nama_hewan', 'kambing')
            ->where('kategori_hewan', 'reguler')
            ->first();

        if (!$kambingReguler) {
            $kambingReguler = HewanQurban::create([
                'id_hewan_qurban' => (string) Str::uuid(),
                'nama_hewan' => 'kambing',
                'kategori_hewan' => 'reguler',
                'harga_hewan' => 3500000,
                'is_active' => true
            ]);
        }

        $sapiPremium = HewanQurban::where('nama_hewan', 'sapi')
            ->where('kategori_hewan', 'premium')
            ->first();

        if (!$sapiPremium) {
            $sapiPremium = HewanQurban::create([
                'id_hewan_qurban' => (string) Str::uuid(),
                'nama_hewan' => 'sapi',
                'kategori_hewan' => 'premium',
                'harga_hewan' => 35000000,
                'is_active' => true
            ]);
        }

        
        $joko = Jamaah::where('email', 'arctant2.5one@gmail.com')->first();

        
        if ($joko) {
            
            $existingTabungan = TabunganHewanQurban::where('id_jamaah', $joko->id)->exists();

            if (!$existingTabungan) {
                DB::transaction(function () use ($joko, $kambingReguler) {
                    
                    $tabunganId = (string) Str::uuid();
                    $jumlahHewan = 1;
                    $totalHarga = $kambingReguler->harga_hewan * $jumlahHewan;

                    
                    
                    $tanggalPembuatan = Carbon::now()->subMonths(2)->toDateString(); 

                    
                    $tabunganJoko = TabunganHewanQurban::create([
                        'id_tabungan_hewan_qurban' => $tabunganId,
                        'id_jamaah' => $joko->id,
                        'status' => 'disetujui', 
                        'saving_type' => 'cicilan',
                        'duration_months' => 10,
                        'total_tabungan' => 0, 
                        'total_harga_hewan_qurban' => $totalHarga,
                        'tanggal_pembuatan' => $tanggalPembuatan, 
                        'created_at' => $tanggalPembuatan, 
                    ]);

                    
                    DetailTabunganHewanQurban::create([
                        'id_tabungan_hewan_qurban' => $tabunganId,
                        'id_hewan_qurban' => $kambingReguler->id_hewan_qurban,
                        'jumlah_hewan' => $jumlahHewan,
                        'harga_per_ekor' => $kambingReguler->harga_hewan,
                        'subtotal' => $totalHarga
                    ]);

                    
                    
                    
                    
                    
                    PemasukanTabunganQurban::create([
                        'id_tabungan_hewan_qurban' => $tabunganId,
                        'tanggal' => Carbon::now()->subDays(30),
                        'nominal' => 1000000
                    ]);
                    
                    PemasukanTabunganQurban::create([
                        'id_tabungan_hewan_qurban' => $tabunganId,
                        'tanggal' => Carbon::now()->subDays(15),
                        'nominal' => 1000000
                    ]);
                    
                    PemasukanTabunganQurban::create([
                        'id_tabungan_hewan_qurban' => $tabunganId,
                        'tanggal' => Carbon::now()->subDays(2),
                        'nominal' => 500000
                    ]);
                });
            }
        }
    }
}