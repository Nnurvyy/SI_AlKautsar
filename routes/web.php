<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

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
    
    // Ini adalah halaman "dashboard" atau halaman utama setelah login
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    
    // Nanti kamu bisa tambahkan rute lain yang butuh login di sini
    // Route::get('/data-keuangan', [KeuanganController::class, 'index']);
});

// Arahkan halaman utama (/) ke halaman login
Route::get('/', function () {
    return redirect()->route('login');
});