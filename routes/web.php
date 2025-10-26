<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PemasukanController;
use App\Http\Controllers\PengeluaranController;
use App\Http\Controllers\DataSantriController;
use App\Http\Controllers\DivisiController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Rute untuk menampilkan halaman login
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');

// Rute untuk memproses data login
Route::post('/login', [AuthController::class, 'loginProcess'])->name('login.process');

// Rute untuk logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// Grup Rute yang Dilindungi (Hanya bisa diakses setelah login)
Route::middleware(['auth'])->group(function () {
    
    // Rute Dashboard
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    
    // 2. Rute Kategori Pemasukan diubah
    Route::get('/pemasukan', [PemasukanController::class, 'indexPemasukan'])
         ->name('pemasukan');
         
    // 3. Rute Kategori Pengeluaran diubah
    Route::get('/pengeluaran', [PengeluaranController::class, 'indexPengeluaran'])
         ->name('pengeluaran');

    //4. Route untuk kategori data santri
    Route::get('/datasantri', [DataSantriController::class, 'indexDataSantri'])
         ->name('datasantri');

    //5. Route untuk kategori divisi
    Route::get('/divisi', [DivisiController::class, 'indexDivisi'])
         ->name('divisi');
         
});

// Arahkan halaman utama (/) ke halaman login
Route::get('/', function () {
    return redirect()->route('login');
});