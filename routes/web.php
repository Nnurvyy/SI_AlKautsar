<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PemasukanController;
use App\Http\Controllers\PengeluaranController;
use App\Http\Controllers\KhotibJumatController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\TabunganHewanQurbanController;
use App\Http\Controllers\PemasukanTabunganQurbanController;
use App\Http\Controllers\InfaqJumatController;
use App\Http\Controllers\BarangInventarisController;
use App\Http\Controllers\GrafikController;
use App\Http\Controllers\LapKeuController;
use App\Http\Controllers\QurbanController;
use App\Http\Controllers\PengaturanController;
use App\Http\Controllers\DonasiController;
use App\Http\Controllers\PemasukanDonasiController;
use App\Http\Controllers\KategoriKeuanganController;
use App\Http\Controllers\ArtikelController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\ProfileController;


use App\Http\Controllers\KajianController; 
use App\Http\Controllers\ProgramDonasiController;


/*
|--------------------------------------------------------------------------
| Rute Publik (Guest / Tamu)
|--------------------------------------------------------------------------
*/

Route::get('/', [PublicController::class, 'landingPage'])->name('public.landing');
Route::get('/jadwal-adzan', [PublicController::class, 'jadwalAdzan'])->name('public.jadwal-adzan');
Route::get('/artikel', [PublicController::class, 'artikel'])->name('public.artikel');
Route::get('/artikel/detail/{id}', [PublicController::class, 'getArtikelDetail'])->name('public.artikel.detail');
Route::get('/donasi', [PublicController::class, 'donasi'])->name('public.donasi');
Route::get('/program', [PublicController::class, 'program'])->name('public.program');

Route::get('/khutbah-jumat', [PublicController::class, 'jadwalKhotib'])->name('public.jadwal-khotib');
Route::get('/jadwal-kajian', [PublicController::class, 'jadwalKajian'])->name('public.jadwal-kajian');
Route::get('/api/jadwal-adzan', [PublicController::class, 'jadwalAdzanApi'])->name('public.jadwal-adzan.api');

Route::get('/tabungan-qurban-saya', function() {
    if (Auth::guard('jamaah')->check()) {
        // Jika sudah login, forward ke controller logic
        return app(App\Http\Controllers\QurbanController::class)->index();
    }
    // Jika belum login, tetap load view tapi view-nya akan menampilkan modal (karena kita handle di blade)
    return view('public.tabungan-qurban-saya'); 
})->name('public.tabungan-qurban-saya');


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

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('kategori-keuangan/data', [KategoriKeuanganController::class, 'data']);
    Route::resource('kategori-keuangan', KategoriKeuanganController::class);

    // Route Pemasukan
    Route::get('pemasukan/data', [PemasukanController::class, 'data']);
    Route::resource('pemasukan', PemasukanController::class);

    // Route Pengeluaran
    Route::get('pengeluaran/data', [PengeluaranController::class, 'data']);
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


    // ... (Tambahkan rute pengurus lainnya di sini) ...
    // Program Donasi (Master Data)
    Route::resource('program-donasi', ProgramDonasiController::class);
    Route::get('program-donasi-data', [ProgramDonasiController::class, 'data'])->name('program-donasi.data');

    // === TAMBAHAN BARU: TRANSAKSI DONASI (Input Donatur) ===
    Route::resource('transaksi-donasi', PemasukanDonasiController::class);

    // Tabungan Qurban
    Route::get('tabungan-qurban/cetak-pdf', [TabunganHewanQurbanController::class, 'cetakPdf'])
        ->name('tabungan-qurban.cetakPdf');
    Route::get('tabungan-qurban-data', [TabunganHewanQurbanController::class, 'data'])
        ->name('tabungan-qurban.data');
    Route::resource('tabungan-qurban', TabunganHewanQurbanController::class);

    // Pemasukan Qurban
    Route::resource('pemasukan-qurban', PemasukanTabunganQurbanController::class)
        ->parameter('pemasukan-qurban', 'id');

    // Donasi (Parent)
    Route::resource('donasi', DonasiController::class);
    Route::get('donasi-data', [DonasiController::class, 'data'])->name('donasi.data');

    // Pemasukan Donasi (Child - Transaksi)
    Route::resource('pemasukan-donasi', PemasukanDonasiController::class)
        ->only(['store', 'destroy']);
    // Artikel
    Route::get('artikel-data', [ArtikelController::class, 'artikelData'])->name('artikel.data');
    Route::get('artikel-data/{id}', [ArtikelController::class, 'artikelData'])->name('artikel.detail');
    Route::resource('artikel', ArtikelController::class)->names([
        'store' => 'artikel.store', 
        'index' => 'artikel.index', 
        'update' => 'artikel.update',
        'destroy' => 'artikel.destroy', // Tambahkan ini juga
        // ...
    ]);
        

    // Event
    Route::resource('program', ProgramController::class);
    Route::get('program-data', [ProgramController::class, 'data'])->name('program.data');

    // Halaman grafik
    Route::get('grafik', [GrafikController::class, 'index'])
        ->name('grafik.index');

    // API grafik dipindah ke prefix /api atau /pengurus/api
    Route::get('grafik/data', [GrafikController::class, 'dataUntukGrafik'])
        ->name('grafik.data');



    Route::get('/settings', [PengaturanController::class, 'edit'])->name('settings.edit');
    Route::post('/settings', [PengaturanController::class, 'update'])->name('settings.update');

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

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // URL: /qurban-saya (Name: jamaah.qurban)
    Route::get('/qurban-saya', [QurbanController::class, 'index'])->name('qurban');
});



