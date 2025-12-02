<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
// Import Class Command Peringatan (Wajib di-import agar ::class terbaca)
use App\Console\Commands\KirimPeringatanTunggakan;

// --- COMMAND BAWAAN LARAVEL (Opsional, boleh dibiarkan) ---
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


// ====================================================
// DAFTAR SCHEDULE (JADWAL OTOMATIS)
// ====================================================

// 1. Hapus Transaksi Pending > 1 Jam
// Dijalankan: Setiap Menit (Laravel akan cek apakah ada data lama tiap menit)
Schedule::command('transaksi:bersihkan-pending')
    ->everyMinute();

// 2. Kirim Email Peringatan Tunggakan Cicilan
// Dijalankan: Setiap tanggal 1, Pukul 08:00 Pagi
// Timezone: Asia/Jakarta (Penting agar email masuk pas pagi di waktu Indonesia)
Schedule::command(KirimPeringatanTunggakan::class)
    ->monthlyOn(1, '08:00')
    ->timezone('Asia/Jakarta');