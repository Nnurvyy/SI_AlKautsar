<?php

namespace App\Providers;
use App\Models\MasjidProfil;
use Illuminate\Support\Facades\Schema;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (Schema::hasTable('masjid_profil')) {

            // Ambil data, atau buat data default jika tabel kosong
            $settings = MasjidProfil::firstOrCreate([], [
                'nama_masjid' => 'Nama Masjid (Default)',
                'lokasi_nama' => 'Alamat Default, Harap ubah di Pengaturan',
                'lokasi_id_api' => '1218', // Default Tasikmalaya
                'lokasi_nama_api' => 'KOTA TASIKMALAYA'
            ]);

            // Bagikan variabel $settings ke SEMUA view
            view()->share('settings', $settings);
        }
    }
}
