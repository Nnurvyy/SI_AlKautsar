<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PemasukanController;
use App\Http\Controllers\PengeluaranController;
use App\Http\Controllers\KhotibJumatController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\QurbanController;
use App\Http\Controllers\LapKeuController;


/*
|--------------------------------------------------------------------------
| Rute Publik (Guest / Tamu)
|--------------------------------------------------------------------------
|
| Rute-rute ini bisa diakses oleh siapa saja, baik yang login
| maupun yang tidak (tamu).
|
*/

// Halaman utama (/) sekarang adalah landing page publik
Route::get('/', [PublicController::class, 'landingPage'])->name('public.landing');

// Halaman fitur yang bisa diakses tamu
Route::get('/jadwal-khotib', [PublicController::class, 'jadwalKhotib'])->name('public.jadwal-khotib');
Route::get('/jadwal-kajian', [PublicController::class, 'jadwalKajian'])->name('public.jadwal-kajian');
Route::get('/jadwal-shalat-api', [PublicController::class, 'jadwalShalatApi'])->name('public.jadwal-shalat-api');


/*
|--------------------------------------------------------------------------
| Rute Autentikasi
|--------------------------------------------------------------------------
*/
// Rute untuk menampilkan halaman login
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');

// Rute untuk memproses data login
Route::post('/login', [AuthController::class, 'loginProcess'])->name('login.process');

// Rute untuk logout (harus sudah login untuk logout)
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');


/*
|--------------------------------------------------------------------------
| Rute ADMIN (Berbasis Desktop)
|--------------------------------------------------------------------------
|
| Dilindungi oleh middleware 'auth' DAN 'role:admin'.
| Kita beri prefix 'admin' agar URL-nya menjadi /admin/dashboard, dll.
|
*/
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard (URL: /admin/dashboard)
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    
    // Pemasukan (URL: /admin/pemasukan)
    Route::resource('pemasukan', PemasukanController::class);
    
    // Pengeluaran (URL: /admin/pengeluaran)
    Route::get('/pengeluaran', [PengeluaranController::class, 'index'])->name('pengeluaran');

    // Khotib Jumat (URL: /admin/khotib-jumat)
    Route::resource('khotib-jumat', KhotibJumatController::class);
    Route::get('khotib-jumat-data', [KhotibJumatController::class, 'data'])->name('khotib-jumat.data');

    // Laporan Keuangan
    Route::get('/lapkeu', [LapKeuController::class, 'index'])->name('lapkeu.index');
    Route::get('/lapkeu/export-pdf', [LapKeuController::class, 'exportPdf'])->name('lapkeu.export.pdf');


    // (WAJIB) TAMBAHKAN INI UNTUK KAJIAN
    Route::resource('kajian', \App\Http\Controllers\KajianController::class);
    Route::get('kajian-data', [\App\Http\Controllers\KajianController::class, 'data'])->name('kajian.data');
    
    // ... (Tambahkan rute admin lainnya di sini) ...

});


/*
|--------------------------------------------------------------------------
| Rute PUBLIK (Sudah Login)
|--------------------------------------------------------------------------
|
| Dilindungi oleh middleware 'auth' DAN 'role:publik'.
| Ini untuk fitur-fitur yang hanya bisa diakses oleh user 'publik'
| yang sudah login.
|
*/
Route::middleware(['auth', 'role:publik'])->name('public.')->group(function () {
    
    // URL: /qurban-saya
    Route::get('/qurban-saya', [QurbanController::class, 'index'])->name('qurban');
    
    // ... (Tambahkan rute 'publik' terotentikasi lainnya di sini) ...

});