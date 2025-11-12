<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Artikel;

class ArtikelController extends Controller
{
    // Fungsi untuk menampilkan halaman Kategori Pengeluaran
    public function index()
    {
        // Langsung tampilkan view-nya
        return view('artikel');
    }
}
