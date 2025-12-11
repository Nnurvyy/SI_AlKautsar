<?php

namespace App\Providers;

use App\Models\MasjidProfil;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL; 

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        
    }

    public function boot(): void
    {
        if($this->app->environment('production') || str_contains(request()->url(), 'ngrok-free.app')) {
            URL::forceScheme('https');
        }
        if (Schema::hasTable('masjid_profil')) { 
            $settings = MasjidProfil::firstOrCreate([], [
                'nama_masjid' => 'Nama Masjid (Default)',
                'lokasi_nama' => 'Alamat Default, Harap ubah di Pengaturan',
                'lokasi_id_api' => '1218', 
                'lokasi_nama_api' => 'KOTA TASIKMALAYA'
            ]); 
            view()->share('settings', $settings);
        }
    }
}