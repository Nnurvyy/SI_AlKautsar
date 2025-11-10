<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
// 1. TAMBAHKAN INI
use App\Console\Commands\KirimPeringatanTunggakan;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();

        // 2. TAMBAHKAN BARIS INI
        // Menjalankan perintah 'qurban:kirim-peringatan'
        // setiap bulan pada tanggal 1, jam 8:00 pagi.
        $schedule->command(KirimPeringatanTunggakan::class)->monthlyOn(1, '08:00');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}

// * * * * * cd /home/namahosting/proyek-anda && php artisan schedule:run >> /dev/null 2>&1
//untuk menjalankan cron job di server setelah di deploy
