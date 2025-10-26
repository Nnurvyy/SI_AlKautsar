<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PengeluaranController extends Controller
{
    // Fungsi untuk menampilkan halaman Kategori Pengeluaran
    public function indexPengeluaran()
    {
        // Langsung tampilkan view-nya
        return view('pengeluaran');
    }
}