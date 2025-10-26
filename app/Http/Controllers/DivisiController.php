<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DivisiController extends Controller
{
    // Fungsi untuk menampilkan halaman Kategori Pengeluaran
    public function indexDivisi()
    {
        // Langsung tampilkan view-nya
        return view('divisi');
    }
}