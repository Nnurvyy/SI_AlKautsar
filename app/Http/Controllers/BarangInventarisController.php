<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BarangInventarisController extends Controller
{
    public function index()
    {
        // Langsung tampilkan view-nya
        return view('barang-inventaris');
    }
}
