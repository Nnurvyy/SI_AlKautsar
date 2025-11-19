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
use App\Http\Controllers\ProgramDonasiController; // <--- TAMBAHAN BARIS INI

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
Route::get('/artikel', [PublicController::class, 'artikel'])->name('public.artikel');
Route::get('/donasi', [PublicController::class, 'donasi'])->name('public.donasi'); // <-- INI SUDAH BENAR
Route::get('/donasi/{id}', [ProgramDonasiController::class, 'detail'])->name('donasi.detail');
Route::post('/donasi/store', [ProgramDonasiController::class, 'store'])->name('donasi.store');
Route::get('/donasi-sukses', [ProgramDonasiController::class, 'sukses'])->name('donasi.sukses');
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
// Rute untuk menampilkan halaman login
Route::get('/welcome', [AuthController::class, 'showWelcomeForm'])->name('auth.welcome');

// 2. Halaman Sign In (Form Login)
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');

// 3. Proses Login
// (Dinamai 'login' agar cocok dengan form action="{{ route('login') }}")
Route::post('/login', [AuthController::class, 'loginProcess']);

// 4. Halaman Sign Up (Form Registrasi)
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');

// 5. Proses Registrasi
Route::post('/register', [AuthController::class, 'registerProcess']);

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
    Route::resource('kajian', KajianController::class); // <-- Saya perbaiki panggilannya
    Route::get('kajian-data', [KajianController::class, 'data'])->name('kajian.data'); // <-- Saya perbaiki panggilannya

    // --- TAMBAHAN UNTUK DONASI ADMIN ---
    Route::resource('program-donasi', ProgramDonasiController::class);
    Route::get('program-donasi-data', [ProgramDonasiController::class, 'data'])->name('program-donasi.data');
    // ------------------------------------

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