<?php

namespace App\Providers;

use App\Models\MasjidProfil;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL; // PENTING: Tambahkan import ini

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
        // --- 1. FIX GAMBAR NGROK (FORCE HTTPS) ---
        // Jika sedang di Production atau URL mengandung 'ngrok-free.app', paksa pakai HTTPS
        if($this->app->environment('production') || str_contains(request()->url(), 'ngrok-free.app')) {
            URL::forceScheme('https');
        }

        // --- 2. LOGIC LAMA ANDA (SHARE SETTINGS) ---
        // Cek dulu apakah tabel ada untuk menghindari error saat migrate fresh
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