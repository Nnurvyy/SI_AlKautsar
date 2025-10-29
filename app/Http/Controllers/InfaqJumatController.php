<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InfaqJumatController extends Controller
{
    // Fungsi untuk menampilkan halaman Kategori Pengeluaran
    public function indexInfaqJumat()
    {
        // Langsung tampilkan view-nya
        return view('infaq_jumat');
    }
}