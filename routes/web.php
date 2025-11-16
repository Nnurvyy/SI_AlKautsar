<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PemasukanController;
use App\Http\Controllers\PengeluaranController;
use App\Http\Controllers\KhotibJumatController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\TabunganHewanQurbanController;
use App\Http\Controllers\PemasukanTabunganQurbanController;
use App\Http\Controllers\InfaqJumatController;
use App\Http\Controllers\BarangInventarisController;
use App\Http\Controllers\LapKeuController;
use App\Http\Controllers\QurbanController;

// --- (Rute Publik & Auth biarkan saja, sudah benar) ---

// Halaman utama (/) sekarang adalah landing page publik
Route::get('/', [PublicController::class, 'landingPage'])->name('public.landing');
Route::get('/jadwal-khotib', [PublicController::class, 'jadwalKhotib'])->name('public.jadwal-khotib');
Route::get('/jadwal-kajian', [PublicController::class, 'jadwalKajian'])->name('public.jadwal-kajian');
Route::get('/artikel', [PublicController::class, 'artikel'])->name('public.artikel');
Route::get('/donasi', [PublicController::class, 'donasi'])->name('public.donasi');
Route::get('/program', [PublicController::class, 'program'])->name('public.program');
Route::get('/jadwal-shalat-api', [PublicController::class, 'jadwalShalatApi'])->name('public.jadwal-shalat-api');
Route::get('/tabungan-qurban-saya', [PublicController::class, 'tabunganQurbanSaya'])->name('public.tabungan-qurban-saya');

Route::get('/welcome', [AuthController::class, 'showWelcomeForm'])->name('auth.welcome');
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'loginProcess']);
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'registerProcess']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');


/*
|--------------------------------------------------------------------------
| Rute ADMIN (Berbasis Desktop)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard (URL: /admin/dashboard)
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');

    // Pemasukan (Sudah benar)
    Route::resource('pemasukan', PemasukanController::class);
    Route::resource('kategori-pemasukan', \App\Http\Controllers\PemasukanKategoriController::class);

    // --- PERBAIKAN DI SINI ---
    // 1. GANTI Route::get menjadi Route::resource untuk Pengeluaran
    Route::resource('pengeluaran', PengeluaranController::class);
    
    // 2. TAMBAHKAN rute untuk Kategori Pengeluaran
    Route::resource('kategori-pengeluaran', \App\Http\Controllers\PengeluaranKategoriController::class);
    // --- AKHIR PERBAIKAN ---


    // Khotib Jumat (URL: /admin/khotib-jumat)
    Route::resource('khotib-jumat', KhotibJumatController::class);
    Route::get('khotib-jumat-data', [KhotibJumatController::class, 'data'])->name('khotib-jumat.data');

    //infaq juamt
    Route::resource('infaq-jumat', InfaqJumatController::class)->only([
        'index','store', 'update', 'destroy', 'show'
    ]);
    Route::get('infaq-jumat-data', [InfaqJumatController::class, 'data'])->name('infaq-jumat.data');

    //inventaris dan stock
    Route::resource('inventaris', BarangInventarisController::class)->only([
        'index','store', 'update', 'destroy', 'show'
    ]);
    Route::get('inventaris-data', [BarangInventarisController::class, 'data'])->name('inventaris.data');
    
    // Laporan Keuangan
    Route::get('/lapkeu', [LapKeuController::class, 'index'])->name('lapkeu.index');
    Route::get('/lapkeu/export-pdf', [LapKeuController::class, 'exportPdf'])->name('lapkeu.export.pdf');


    // (WAJIB) TAMBAHKAN INI UNTUK KAJIAN
    Route::resource('kajian', \App\Http\Controllers\KajianController::class);
    Route::get('kajian-data', [\App\Http\Controllers\KajianController::class, 'data'])->name('kajian.data');

    // ... (Tambahkan rute admin lainnya di sini) ...

    // Rute PDF (pola lapkeu) - Rute spesifik di atas
    Route::get('tabungan-qurban/cetak-pdf', [TabunganHewanQurbanController::class, 'cetakPdf'])
        ->name('tabungan-qurban.cetakPdf');

    // Rute Data (pola khotib) - Rute spesifik di atas
    Route::get('tabungan-qurban-data', [TabunganHewanQurbanController::class, 'data'])
        ->name('tabungan-qurban.data');

    // Rute Resource (CRUD Utama) - Rute umum di bawah
    Route::resource('tabungan-qurban', TabunganHewanQurbanController::class);

    // Rute Resource (CRUD Setoran/Pemasukan)
    Route::resource('pemasukan-qurban', PemasukanTabunganQurbanController::class)
        ->parameter('pemasukan-qurban', 'id');

});


/*
|--------------------------------------------------------------------------
| Rute PUBLIK (Sudah Login)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:publik'])->name('public.')->group(function () {

    // URL: /qurban-saya
    Route::get('/qurban-saya', [QurbanController::class, 'index'])->name('qurban');

    // ... (Tambahkan rute 'publik' terotentikasi lainnya di sini) ...

});

Route::get('/jadwal-adzan', [PublicController::class, 'jadwalAdzan'])->name('public.jadwal-adzan');
Route::get('/api/jadwal-adzan', [PublicController::class, 'jadwalAdzanApi'])->name('public.jadwal-adzan.api');

