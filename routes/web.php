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
use App\Http\Controllers\BarangInventarisDetailController;
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
use App\Http\Controllers\DonasiPaymentController;
use App\Http\Controllers\HewanQurbanController;
use App\Http\Controllers\KajianController; 
use App\Http\Controllers\TabunganPaymentController;

/*
|--------------------------------------------------------------------------
| Rute Publik (Guest / Tamu)
|--------------------------------------------------------------------------
*/

Route::get('/', [PublicController::class, 'landingPage'])->name('public.landing');
Route::get('/jadwal-adzan', [PublicController::class, 'jadwalAdzan'])->name('public.jadwal-adzan');
Route::get('/api/jadwal-adzan', [PublicController::class, 'jadwalAdzanApi'])->name('public.jadwal-adzan.api');

Route::get('/artikel', [PublicController::class, 'artikel'])->name('public.artikel');
Route::get('/artikel/detail/{id}', [PublicController::class, 'getArtikelDetail'])->name('public.artikel.detail');

Route::get('/donasi', [PublicController::class, 'donasi'])->name('public.donasi');
Route::get('/donasi/detail/{id}', [PublicController::class, 'getDonasiDetail'])->name('public.donasi.detail');
Route::post('/donasi/checkout', [DonasiPaymentController::class, 'checkout'])->name('donasi.checkout');
Route::post('/tripay/callback', [DonasiPaymentController::class, 'callback']);

Route::get('/program', [PublicController::class, 'program'])->name('public.program');
Route::get('/program/detail/{id}', [PublicController::class, 'getProgramDetail'])->name('public.program.detail');

Route::get('/khutbah-jumat', [PublicController::class, 'jadwalKhotib'])->name('public.jadwal-khotib');
Route::get('/jadwal-kajian', [PublicController::class, 'jadwalKajian'])->name('public.jadwal-kajian');
Route::get('/tentang-kami', [App\Http\Controllers\PublicController::class, 'tentangKami'])->name('public.tentang-kami');

Route::get('/tabungan-qurban-saya', [QurbanController::class, 'index'])
    ->name('public.tabungan-qurban-saya');


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
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Google Auth
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google.redirect');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
Route::get('/verify-otp', [AuthController::class, 'showVerifyForm'])->name('auth.verify');
Route::post('/verify-otp', [AuthController::class, 'verifyProcess'])->name('auth.verify.process');
Route::post('/auth/otp/resend', [AuthController::class, 'resendOtp'])->name('auth.otp.resend');
Route::get('/complete-data', [AuthController::class, 'showCompleteDataForm'])->name('auth.complete-data');
Route::post('/complete-data', [AuthController::class, 'processCompleteData'])->name('auth.complete-data.process');


/*
|--------------------------------------------------------------------------
| Rute PENGURUS (Dashboard Admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:pengurus'])->prefix('pengurus')->name('pengurus.')->group(function () {

    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Keuangan & Inventaris
    Route::get('kategori-keuangan/data', [KategoriKeuanganController::class, 'data']);
    Route::resource('kategori-keuangan', KategoriKeuanganController::class);
    Route::get('pemasukan/data', [PemasukanController::class, 'data']);
    Route::resource('pemasukan', PemasukanController::class);
    Route::get('pengeluaran/data', [PengeluaranController::class, 'data']);
    Route::resource('pengeluaran', PengeluaranController::class);
    
    // Masjid Activities
    Route::resource('khotib-jumat', KhotibJumatController::class);
    Route::get('khotib-jumat-data', [KhotibJumatController::class, 'data'])->name('khotib-jumat.data');
    Route::resource('infaq-jumat', InfaqJumatController::class)->only(['index','store', 'update', 'destroy', 'show']);
    Route::get('infaq-jumat-data', [InfaqJumatController::class, 'data'])->name('infaq-jumat.data');
    
    //BarangInventaris
    Route::resource('inventaris', BarangInventarisController::class)->only([
        'index',    // GET /inventaris (Menampilkan view master)
        'store',    // POST /inventaris (API: Tambah barang master)
        'update',   // PUT/PATCH /inventaris/{id} (API: Edit barang master)
        'destroy',  // DELETE /inventaris/{id} (API: Hapus barang master)
        'show'      // GET /inventaris/{id} (API: Ambil data master tunggal)
    ]);

    Route::get('inventaris-data', [BarangInventarisController::class, 'data'])->name('inventaris.data');

    //BarangInventarisDetail
    Route::get('inventaris/{id_barang}/detail', [BarangInventarisDetailController::class, 'indexDetail'])->name('inventaris.detail.index');
    Route::prefix('barang-inventaris-detail')->group(function () {
        
        // API untuk mengambil data tabel unit detail berdasarkan ID Master
        // Contoh URL: /barang-inventaris-detail/uuid-barang-master-123/data
        Route::get('{id_barang}/data', [BarangInventarisDetailController::class, 'data'])->name('inventaris.detail.data');
        
        // API untuk Menambah Unit Detail Baru
        // Menggunakan POST ke root prefix karena id_barang dikirim via body request, bukan URL
        Route::post('/', [BarangInventarisDetailController::class, 'store'])->name('inventaris.detail.store');

        // API CRUD untuk satu unit detail (menggunakan ID Detail Unit)
        Route::get('{id_detail_barang}', [BarangInventarisDetailController::class, 'show'])->name('inventaris.detail.show');
        Route::put('{id_detail_barang}', [BarangInventarisDetailController::class, 'update'])->name('inventaris.detail.update');
        Route::delete('{id_detail_barang}', [BarangInventarisDetailController::class, 'destroy'])->name('inventaris.detail.destroy');
    });

    Route::resource('kajian', KajianController::class);
    Route::get('kajian-data', [KajianController::class, 'data'])->name('kajian.data');
    
    // Laporan & Grafik
    Route::get('/lapkeu', [LapKeuController::class, 'index'])->name('lapkeu.index');
    Route::get('/lapkeu/export-pdf', [LapKeuController::class, 'exportPdf'])->name('lapkeu.export.pdf');
    Route::get('grafik/data', [GrafikController::class, 'dataUntukGrafik'])->name('grafik.data');

    // Donasi
    Route::resource('transaksi-donasi', PemasukanDonasiController::class);
    Route::resource('donasi', DonasiController::class);
    Route::get('donasi-data', [DonasiController::class, 'data'])->name('donasi.data');
    Route::resource('pemasukan-donasi', PemasukanDonasiController::class)->only(['store', 'destroy']);

    // --- TABUNGAN QURBAN (ADMIN) ---
    Route::get('tabungan-qurban/cetak-pdf', [TabunganHewanQurbanController::class, 'cetakPdf'])->name('tabungan-qurban.cetakPdf');
    Route::get('tabungan-qurban-data', [TabunganHewanQurbanController::class, 'data'])->name('tabungan-qurban.data');
    Route::resource('tabungan-qurban', TabunganHewanQurbanController::class);
    Route::put('tabungan-qurban/{id}/status', [TabunganHewanQurbanController::class, 'updateStatus'])->name('tabungan-qurban.status');
    
    // Hewan & Pemasukan Qurban
    Route::resource('pemasukan-qurban', PemasukanTabunganQurbanController::class)->parameter('pemasukan-qurban', 'id');
    Route::resource('hewan-qurban', HewanQurbanController::class)->parameters(['hewan-qurban' => 'id']);

    // Content
    Route::get('artikel-data', [ArtikelController::class, 'artikelData'])->name('artikel.data');
    Route::resource('artikel', ArtikelController::class);
    Route::get('artikel-data/{id}', [ArtikelController::class, 'artikelData']);

    Route::resource('program', ProgramController::class);
    Route::get('program-data', [ProgramController::class, 'data'])->name('program.data');

    // Settings
    Route::get('/settings', [PengaturanController::class, 'edit'])->name('settings.edit');
    Route::post('/settings', [PengaturanController::class, 'update'])->name('settings.update');
});


/*
|--------------------------------------------------------------------------
| Rute JAMAAH (Publik yang Sudah Login)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:jamaah'])->name('jamaah.')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('/qurban-saya', [QurbanController::class, 'index'])->name('qurban');
    Route::post('/qurban-saya/store', [QurbanController::class, 'store'])->name('qurban.store'); 
    Route::get('/qurban-saya/{id}', [QurbanController::class, 'show'])->name('qurban.show');
    
    Route::post('/tabungan-qurban/checkout', [TabunganPaymentController::class, 'checkout'])->name('tabungan.checkout');
});