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
use App\Http\Controllers\KajianController; // Pastikan ini di-use

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
Route::get('/program', [PublicController::class, 'program'])->name('public.program');
Route::get('/jadwal-shalat-api', [PublicController::class, 'jadwalShalatApi'])->name('public.jadwal-shalat-api');
Route::get('/tabungan-qurban-saya', [PublicController::class, 'tabunganQurbanSaya'])->name('public.tabungan-qurban-saya');


/*
|--------------------------------------------------------------------------
| Rute Autentikasi
|--------------------------------------------------------------------------
*/
// Halaman Welcome
Route::get('/welcome', [AuthController::class, 'showWelcomeForm'])->name('auth.welcome');

// Halaman Sign In (Form Login)
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
// Proses Login (Email/Pass)
Route::post('/login', [AuthController::class, 'loginProcess']); // Sudah di-update di AuthController

// Halaman Sign Up (Form Registrasi)
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
// Proses Registrasi
Route::post('/register', [AuthController::class, 'registerProcess']); // Sudah di-update di AuthController

// Rute Logout
// Middleware 'auth' dihapus, controller akan menangani logout (jamaah atau pengurus)
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// RUTE BARU: GOOGLE AUTH
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google.redirect');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');


/*
|--------------------------------------------------------------------------
| Rute PENGURUS (Dashboard Admin)
|--------------------------------------------------------------------------
|
| Dilindungi oleh middleware 'auth:pengurus'.
| Prefix 'pengurus' dan nama 'pengurus.'
|
*/
// GANTI: middleware(['auth', 'role:admin']) -> middleware('auth:pengurus')
// GANTI: prefix('admin') -> prefix('pengurus')
// GANTI: name('admin.') -> name('pengurus.')
Route::middleware(['auth:pengurus'])->prefix('pengurus')->name('pengurus.')->group(function () {

    // Dashboard (URL: /pengurus/dashboard)
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');

    // Pemasukan (URL: /pengurus/pemasukan)
    Route::resource('pemasukan', PemasukanController::class);

    // Pengeluaran (URL: /pengurus/pengeluaran)
    Route::get('/pengeluaran', [PengeluaranController::class, 'index'])->name('pengeluaran');

    // Khotib Jumat (URL: /pengurus/khotib-jumat)
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
    Route::resource('kajian', KajianController::class); // Hapus namespace absolut jika sudah di-use di atas
    Route::get('kajian-data', [KajianController::class, 'data'])->name('kajian.data');

    // ... (Tambahkan rute pengurus lainnya di sini) ...

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
| Rute JAMAAH (Publik yang Sudah Login)
|--------------------------------------------------------------------------
|
| Dilindungi oleh middleware 'auth:jamaah'.
| GANTI: middleware(['auth', 'role:publik']) -> middleware('auth:jamaah')
| GANTI: name('public.') -> name('jamaah.')
|
*/
Route::middleware(['auth:jamaah'])->name('jamaah.')->group(function () {

    // URL: /qurban-saya (Name: jamaah.qurban)
    Route::get('/qurban-saya', [QurbanController::class, 'index'])->name('qurban');

    // ... (Tambahkan rute 'jamaah' terotentikasi lainnya di sini) ...

});
