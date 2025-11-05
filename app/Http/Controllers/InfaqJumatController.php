<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\InfaqJumat; // Pastikan Model sudah di-import
use Illuminate\Validation\ValidationException;

class InfaqJumatController extends Controller
{
    // Fungsi untuk menampilkan halaman utama infaq (view blade)
    public function index()
    {
        // Ganti dengan nama view Blade Anda yang sebenarnya
        return view('infaq-jumat'); 
    }

    // Dipanggil oleh: GET /infaq-jumat-data (untuk tabel, search, sort, pagination)
    public function data(Request $request)
    {
        $search = $request->get('search');
        $sortBy = $request->get('sortBy', 'tanggal_infaq'); 
        $sortDir = $request->get('sortDir', 'desc');       
        $perPage = $request->get('perPage', 10);
        
        $query = InfaqJumat::query();

        // Logika Pencarian (Search)
        if ($search) {
            // Mencari berdasarkan tanggal infaq
            $query->where('tanggal_infaq', 'LIKE', "%{$search}%")
                  // Mencari berdasarkan nominal infaq
                  ->orWhere('nominal_infaq', 'LIKE', "%{$search}%");
        }

        // Logika Pengurutan (Sort)
        $query->orderBy($sortBy, $sortDir);

        // Lakukan Pagination dan kembalikan data dalam format JSON
        $data = $query->paginate($perPage);

        return response()->json($data);
    }

    // Dipanggil oleh: POST /infaq-jumat (CREATE)
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'tanggal_infaq' => 'required|date',
                'nominal_infaq' => 'required|integer|min:0',
            ]);

            InfaqJumat::create($validated);
            
            return response()->json(['message' => 'Data infaq berhasil ditambahkan!'], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
             return response()->json(['message' => 'Terjadi kesalahan server saat menyimpan.'], 500);
        }
    }

    // Dipanggil oleh: GET /infaq-jumat/{id} (READ untuk Edit)
    public function show($id_infaq) // Menggunakan $id_infaq agar lebih jelas
    {
        // Menggunakan findOrFail untuk mengambil data berdasarkan Primary Key
        // Model akan otomatis menggunakan $primaryKey yang Anda definisikan di Model
        $infaq = InfaqJumat::findOrFail($id_infaq);
        
        // Mengembalikan data dengan kunci 'id_infaq'
        return response()->json([
            'id_infaq' => $infaq->{$infaq->getKeyName()}, // Mengambil nilai PK secara dinamis (id_infaq_jumat)
            'tanggal_infaq' => $infaq->tanggal_infaq->format('Y-m-d'),
            'nominal_infaq' => $infaq->nominal_infaq,
        ]);
    }

    // Dipanggil oleh: PUT/PATCH /infaq-jumat/{id} (UPDATE)
    public function update(Request $request, $id_infaq) // Menggunakan $id_infaq
    {
        try {
            $validated = $request->validate([
                'tanggal_infaq' => 'required|date',
                'nominal_infaq' => 'required|integer|min:0',
            ]);

            // Menggunakan $id_infaq untuk mencari data
            $infaq = InfaqJumat::findOrFail($id_infaq);
            $infaq->update($validated);
            
            return response()->json(['message' => 'Data infaq berhasil diubah!']);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
             return response()->json(['message' => 'Terjadi kesalahan server saat memperbarui.'], 500);
        }
    }

    // Dipanggil oleh: DELETE /infaq-jumat/{id} (DELETE)
    public function destroy($id_infaq) // Menggunakan $id_infaq
    {
        try {
            // Menggunakan $id_infaq untuk menghapus data
            InfaqJumat::destroy($id_infaq);
            return response()->json(['message' => 'Data infaq berhasil dihapus!']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Data tidak ditemukan atau terjadi kesalahan saat menghapus.'], 500);
        }
    }
}
