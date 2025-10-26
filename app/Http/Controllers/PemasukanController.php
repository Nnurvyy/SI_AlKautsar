<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PemasukanController extends Controller
{
    // Fungsi untuk menampilkan halaman Kategori Pemasukan
    public function indexPemasukan()
    {
        // Langsung tampilkan view-nya
        return view('pemasukan');
    }
}