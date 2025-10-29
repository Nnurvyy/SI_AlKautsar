<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PemasukanController;
use App\Http\Controllers\PengeluaranController;
use App\Http\Controllers\KhotibJumatController;
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
    
    // Dashboard
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    
    Route::resource('pemasukan', PemasukanController::class);
    
    // Pengeluaran
    Route::get('/pengeluaran', [PengeluaranController::class, 'index'])
         ->name('pengeluaran');

    // Khotib Jumat
    Route::get('/khotib_jumat', [KhotibJumatController::class, 'index'])
         ->name('khotib_jumat');
});

// Halaman utama diarahkan ke login
Route::get('/', function () {
    return redirect()->route('login');
});
