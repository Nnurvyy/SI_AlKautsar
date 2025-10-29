<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KhotibJumatController extends Controller
{
    // Fungsi untuk menampilkan halaman Khotib Jumat
    public function index()
    {
        // Langsung tampilkan view-nya
        return view('khotib_jumat');
    }
}