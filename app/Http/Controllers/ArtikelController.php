<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Artikel;

class ArtikelController extends Controller
{
    // Fungsi untuk menampilkan halaman Kategori Pengeluaran
    public function index()
    {
        return view('artikel');
    }

    // Di ArtikelController.php

// ...

    public function create()
    {
        // Menggunakan file artikel_form.blade.php di folder public/
        return view('artikel_form'); 
    }

    public function edit(string $id)
    {
        $artikel = Artikel::findOrFail($id); 
        
        // Menggunakan file artikel_form.blade.php di folder public/, 
        // mengirim data $artikel agar form terisi
        return view('artikel_form', compact('artikel')); 
    }

    // ... (fungsi store, update, destroy tetap sama)
}
