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
use App\Http\Controllers\KajianController; 
use App\Http\Controllers\ProgramDonasiController;
use App\Http\Controllers\DonasiController;
use App\Http\Controllers\PemasukanKategoriController; 
use App\Http\Controllers\TransaksiDonasiController; // <--- WAJIB ADA: Import Controller Baru


/*
|--------------------------------------------------------------------------
| Rute Publik (Guest / Tamu)
|--------------------------------------------------------------------------
*/

Route::get('/', [PublicController::class, 'landingPage'])->name('public.landing');

Route::get('/jadwal-khotib', [PublicController::class, 'jadwalKhotib'])->name('public.jadwal-khotib');
Route::get('/jadwal-kajian', [PublicController::class, 'jadwalKajian'])->name('public.jadwal-kajian');
Route::get('/artikel', [PublicController::class, 'artikel'])->name('public.artikel');
Route::get('/donasi', [PublicController::class, 'donasi'])->name('public.donasi');
Route::get('/donasi/{id}', [DonasiController::class, 'detail'])->name('donasi.detail');
Route::post('/donasi/store', [DonasiController::class, 'store'])->name('donasi.store');
Route::get('/donasi-sukses', [DonasiController::class, 'sukses'])->name('donasi.sukses');
Route::get('/program', [PublicController::class, 'program'])->name('public.program');
Route::get('/jadwal-shalat-api', [PublicController::class, 'jadwalShalatApi'])->name('public.jadwal-shalat-api');
Route::get('/tabungan-qurban-saya', [PublicController::class, 'tabunganQurbanSaya'])->name('public.tabungan-qurban-saya');
Route::get('/jadwal-adzan', [PublicController::class, 'jadwalAdzan'])->name('public.jadwal-adzan');
Route::get('/api/jadwal-adzan', [PublicController::class, 'jadwalAdzanApi'])->name('public.jadwal-adzan.api');


/*
|--------------------------------------------------------------------------
| Rute Autentikasi
|--------------------------------------------------------------------------
*/
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

    // Dashboard
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');

    // Pemasukan
    Route::resource('pemasukan', PemasukanController::class);
    Route::resource('kategori-pemasukan', PemasukanKategoriController::class);

    // Pengeluaran
    Route::resource('pengeluaran', PengeluaranController::class);

    // Khotib Jumat
    Route::resource('khotib-jumat', KhotibJumatController::class);
    Route::get('khotib-jumat-data', [KhotibJumatController::class, 'data'])->name('khotib-jumat.data');

    // Infaq Jumat
    Route::resource('infaq-jumat', InfaqJumatController::class)->only([
        'index','store', 'update', 'destroy', 'show'
    ]);
    Route::get('infaq-jumat-data', [InfaqJumatController::class, 'data'])->name('infaq-jumat.data');

    // Inventaris
    Route::resource('inventaris', BarangInventarisController::class)->only([
        'index','store', 'update', 'destroy', 'show'
    ]);
    Route::get('inventaris-data', [BarangInventarisController::class, 'data'])->name('inventaris.data');

    // Laporan Keuangan
    Route::get('/lapkeu', [LapKeuController::class, 'index'])->name('lapkeu.index');
    Route::get('/lapkeu/export-pdf', [LapKeuController::class, 'exportPdf'])->name('lapkeu.export.pdf');

    // Kajian
    Route::resource('kajian', KajianController::class);
    Route::get('kajian-data', [KajianController::class, 'data'])->name('kajian.data');

    // Program Donasi (Master Data)
    Route::resource('program-donasi', ProgramDonasiController::class);
    Route::get('program-donasi-data', [ProgramDonasiController::class, 'data'])->name('program-donasi.data');

    // === TAMBAHAN BARU: TRANSAKSI DONASI (Input Donatur) ===
    Route::resource('transaksi-donasi', TransaksiDonasiController::class);

    // Tabungan Qurban
    Route::get('tabungan-qurban/cetak-pdf', [TabunganHewanQurbanController::class, 'cetakPdf'])
        ->name('tabungan-qurban.cetakPdf');
    Route::get('tabungan-qurban-data', [TabunganHewanQurbanController::class, 'data'])
        ->name('tabungan-qurban.data');
    Route::resource('tabungan-qurban', TabunganHewanQurbanController::class);

    // Pemasukan Qurban
    Route::resource('pemasukan-qurban', PemasukanTabunganQurbanController::class)
        ->parameter('pemasukan-qurban', 'id');

});


/*
|--------------------------------------------------------------------------
| Rute PUBLIK (Sudah Login)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:publik'])->name('public.')->group(function () {
    Route::get('/qurban-saya', [QurbanController::class, 'index'])->name('qurban');
});