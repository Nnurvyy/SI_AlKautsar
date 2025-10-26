<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DataSantriController extends Controller
{
    // Fungsi untuk menampilkan halaman Kategori Pengeluaran
    public function indexDataSantri()
    { 
        // $students = DataSantriController::all();
        // Langsung tampilkan view-nya
        return view('datasantri');
    }
}