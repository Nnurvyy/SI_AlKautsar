<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\BarangInventaris; // Import Model BarangInventaris
use Illuminate\Validation\ValidationException;

class BarangInventarisController extends Controller
{
    // Endpoint untuk menampilkan halaman utama inventaris (view blade)
    // Dipanggil oleh: GET /inventaris
    public function index()
    {
        // Menghitung jumlah jenis barang yang terdaftar
        $totalBarang = BarangInventaris::count();
        
        // Jika ingin menghitung total stok fisik, gunakan: 
        // $totalStok = BarangInventaris::sum('stock');

        return view('barang-inventaris', compact('totalBarang'));
    }

    // Dipanggil oleh: GET /inventaris-data (untuk tabel, search, sort, pagination)
    public function data(Request $request)
    {
        $search = $request->get('search');
        $kondisi = $request->get('kondisi'); // <--- 1. Tangkap parameter kondisi
        
        $sortBy = $request->get('sortBy', 'nama_barang'); 
        $sortDir = $request->get('sortDir', 'asc'); 
        $perPage = $request->get('perPage', 10);
        
        $query = BarangInventaris::query();

        // --- A. Logika Filter Kondisi (INI YANG SEBELUMNYA HILANG) ---
        // Jika ada filter kondisi DAN nilainya bukan 'all'
        if ($kondisi && $kondisi !== 'all') {
            $query->where('kondisi', $kondisi);
        }

        // --- B. Logika Pencarian (Search) ---
        if ($search) {
            // Menggunakan whereRaw dengan LOWER() agar 'AC' == 'ac'
            // Kita ubah nama_barang jadi huruf kecil, dan input search juga jadi huruf kecil
            $query->whereRaw('LOWER(nama_barang) LIKE ?', ['%' . strtolower($search) . '%']);
        }

        // --- C. Logika Pengurutan (Sort) ---
        $query->orderBy($sortBy, $sortDir);

        // --- D. Pagination ---
        $data = $query->paginate($perPage);

        return response()->json($data);
    }

    // Dipanggil oleh: POST /inventaris (CREATE)
    public function store(Request $request)
    {
        try {
            // Aturan validasi disesuaikan dengan kolom Model BarangInventaris
            $validated = $request->validate([
                'nama_barang' => 'required|string|max:255',
                'satuan' => 'required|string|max:50',
                'kondisi' => 'required|string|in:Baik,Perlu Perbaikan,Rusak Berat', // Pastikan nilai sesuai
                'stock' => 'required|integer|min:1',
            ]);

            BarangInventaris::create($validated);
            
            return response()->json(['message' => 'Data barang inventaris berhasil ditambahkan!'], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
             return response()->json(['message' => 'Terjadi kesalahan server saat menyimpan.'], 500);
        }
    }

    // Dipanggil oleh: GET /inventaris/{id} (READ untuk Edit)
    public function show($id_barang) 
    {
        // Menggunakan $id_barang untuk mencari data berdasarkan Primary Key
        $barang = BarangInventaris::findOrFail($id_barang);
        
        // Mengembalikan seluruh data untuk mengisi form
        return response()->json($barang); 
    }

    // Dipanggil oleh: PUT/PATCH /inventaris/{id} (UPDATE)
    public function update(Request $request, $id_barang) 
    {
        try {
            // Aturan validasi sama dengan Store
            $validated = $request->validate([
                'nama_barang' => 'required|string|max:255',
                'satuan' => 'required|string|max:50',
                'kondisi' => 'required|string|in:Baik,Perlu Perbaikan,Rusak Berat',
                'stock' => 'required|integer|min:0',
            ]);

            // Menggunakan $id_barang (UUID) untuk mencari data
            $barang = BarangInventaris::findOrFail($id_barang);
            $barang->update($validated);
            
            return response()->json(['message' => 'Data barang inventaris berhasil diubah!']);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
             return response()->json(['message' => 'Terjadi kesalahan server saat memperbarui.'], 500);
        }
    }

    // Dipanggil oleh: DELETE /inventaris/{id} (DELETE)
    public function destroy($id_barang) 
    {
        try {
            // Menggunakan $id_barang (UUID) untuk menghapus data
            $deleted = BarangInventaris::destroy($id_barang);
            
            if ($deleted) {
                return response()->json(['message' => 'Data barang inventaris berhasil dihapus!']);
            } else {
                 return response()->json(['message' => 'Data barang tidak ditemukan.'], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'Terjadi kesalahan saat menghapus.'], 500);
        }
    }
}
