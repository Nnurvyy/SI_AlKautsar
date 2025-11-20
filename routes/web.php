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
use App\Http\Controllers\TransaksiDonasiController;


/*
|--------------------------------------------------------------------------
| Rute Publik
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
| Rute ADMIN
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');

    Route::resource('pemasukan', PemasukanController::class);
    Route::resource('kategori-pemasukan', PemasukanKategoriController::class);

    Route::resource('pengeluaran', PengeluaranController::class);

    Route::resource('khotib-jumat', KhotibJumatController::class);
    Route::get('khotib-jumat-data', [KhotibJumatController::class, 'data'])->name('khotib-jumat.data');

    Route::resource('infaq-jumat', InfaqJumatController::class)->only([
        'index','store', 'update', 'destroy', 'show'
    ]);
    Route::get('infaq-jumat-data', [InfaqJumatController::class, 'data'])->name('infaq-jumat.data');

    Route::resource('inventaris', BarangInventarisController::class)->only([
        'index','store', 'update', 'destroy', 'show'
    ]);
    Route::get('inventaris-data', [BarangInventarisController::class, 'data'])->name('inventaris.data');

    Route::get('/lapkeu', [LapKeuController::class, 'index'])->name('lapkeu.index');
    Route::get('/lapkeu/export-pdf', [LapKeuController::class, 'exportPdf'])->name('lapkeu.export.pdf');

    /*
    |--------------------------------------------------------------------------
    | FIX BAGIAN KAJIAN — SUDAH COCOK DENGAN NAVBAR
    |--------------------------------------------------------------------------
    */
    Route::get('/kajian', [KajianController::class, 'index'])->name('kajian.index');
    Route::get('/kajian/data', [KajianController::class, 'data'])->name('kajian.data');
    Route::post('/kajian/store', [KajianController::class, 'store'])->name('kajian.store');
    Route::delete('/kajian/delete/{id}', [KajianController::class, 'delete'])->name('kajian.delete');


    Route::resource('program-donasi', ProgramDonasiController::class);
    Route::get('program-donasi-data', [ProgramDonasiController::class, 'data'])->name('program-donasi.data');

    Route::resource('transaksi-donasi', TransaksiDonasiController::class);

    Route::get('tabungan-qurban/cetak-pdf', [TabunganHewanQurbanController::class, 'cetakPdf'])
        ->name('tabungan-qurban.cetakPdf');
    Route::get('tabungan-qurban-data', [TabunganHewanQurbanController::class, 'data'])
        ->name('tabungan-qurban.data');
    Route::resource('tabungan-qurban', TabunganHewanQurbanController::class);

    Route::resource('pemasukan-qurban', PemasukanTabunganQurbanController::class)
        ->parameter('pemasukan-qurban', 'id');

});


/*
|--------------------------------------------------------------------------
| Rute Publik setelah login
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:publik'])->name('public.')->group(function () {
    Route::get('/qurban-saya', [QurbanController::class, 'index'])->name('qurban');
});
