<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BarangInventarisDetail;
use App\Models\BarangInventaris; // Import Model Master
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; // Import Log untuk error handling

class BarangInventarisDetailController extends Controller
{
    /**
     * Helper: Menghitung ulang dan memperbarui total_stock di tabel master.
     */
    protected function updateMasterStock($id_barang)
    {
        $totalStock = BarangInventarisDetail::where('id_barang', $id_barang)->count();
        $master = BarangInventaris::find($id_barang);
        
        if ($master) {
            $master->total_stock = $totalStock;
            $master->save(); 
        }
    }

    // Menampilkan data detail barang berdasarkan ID Barang Master (id_barang)
    // URL: /api/barang-inventaris/{id_barang}/details
    public function data($id_barang, Request $request)
    {
        // Validasi ID Barang Master
        if (!BarangInventaris::where('id_barang', $id_barang)->exists()) {
            return response()->json(['message' => 'Barang master tidak ditemukan.'], 404);
        }

        $search = $request->get('search');
        $kondisi = $request->get('kondisi');

        $sortBy = $request->get('sortBy', 'kode_barang'); // Menggunakan kode_barang
        $sortDir = $request->get('sortDir', 'asc');
        $perPage = $request->get('perPage', 10);

        $query = BarangInventarisDetail::where('id_barang', $id_barang)
            ->with('barangInventaris');

        if ($kondisi && $kondisi !== 'all') {
            $query->where('kondisi', $kondisi);
        }

        if ($search) {
            // Mencari berdasarkan kode_barang
            $query->whereRaw('LOWER(kode_barang) LIKE ?', ['%' . strtolower($search) . '%']);
        }

        $query->orderBy($sortBy, $sortDir);
        $data = $query->paginate($perPage);

        return response()->json($data);
    }

    // Menampilkan halaman View Detail Unit
    public function indexDetail($id_barang)
    {
        $barangMaster = BarangInventaris::findOrFail($id_barang);
        
        return view('barang-inventaris-detail', compact('barangMaster'));
    }

    /**
     * Menyimpan data detail barang baru, termasuk fitur kloning unit.
     */
    public function store(Request $request)
    {
        try {
            // 1. Validasi Input Dasar
            $validated = $request->validate([
                'id_barang' => 'required|uuid|exists:barang_inventaris,id_barang',
                'kondisi' => 'required|string|in:Baik,Perlu Perbaikan,Rusak Berat',
                'status' => 'required|string|in:Tersedia,Dipinjam,Perbaikan,Dihapus',
                'lokasi' => 'nullable|string|max:100',
                'deskripsi' => 'nullable|string',
                'tanggal_masuk' => 'nullable|date',
                'jumlah_kloning' => 'nullable|integer|min:0', // Validasi kloning
            ]);
            
            // 2. Ambil Jumlah Kloning dan Hitung Total Unit yang Dibuat
            $jumlahKloning = (int) $request->input('jumlah_kloning', 0);
            $totalUnitToCreate = $jumlahKloning + 1; // Unit asli + kloningan
            
            // 3. Ambil Data Master dan Hitung Count Awal
            $barangMaster = BarangInventaris::findOrFail($validated['id_barang']);
            $master_kode = $barangMaster->kode;
            
            $countAwal = BarangInventarisDetail::where('id_barang', $validated['id_barang'])->count();

            // 4. Siapkan data dasar untuk di-create
            $dataToCreate = $validated;
            unset($dataToCreate['jumlah_kloning']); // Hapus ini karena bukan kolom DB

            if (isset($dataToCreate['tanggal_masuk']) && empty($dataToCreate['tanggal_masuk'])) {
                unset($dataToCreate['tanggal_masuk']);
            }
            
            // 5. Eksekusi Loop untuk Membuat Semua Unit
            $createdUnits = 0;
            for ($i = 0; $i < $totalUnitToCreate; $i++) {
                
                $next_number = $countAwal + $i + 1;

                // Generate kode unik baru (Contoh: AC-01, AC-02, ...)
                $kode_unit_baru = $master_kode . '-' . str_pad($next_number, 2, '0', STR_PAD_LEFT);
                
                // Tambahkan Kode unit ke data (KOREKSI NAMA KOLOM)
                $dataToCreate['kode_barang'] = $kode_unit_baru; 
                
                // Simpan unit baru
                BarangInventarisDetail::create($dataToCreate);
                $createdUnits++;
            }
            
            // 6. Update Stok Master sekali setelah loop selesai
            $this->updateMasterStock($validated['id_barang']);

            $message = ($createdUnits > 1) 
                        ? "Berhasil menambahkan $createdUnits unit detail barang!" 
                        : "Detail barang inventaris berhasil ditambahkan!";

            return response()->json(['message' => $message], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error storing detail unit (Final): ' . $e->getMessage() . ' on line ' . $e->getLine());
            return response()->json(['message' => 'Terjadi kesalahan server saat menyimpan.'], 500);
        }
    }


    // Mengambil data unit detail tunggal (Read for Edit)
    public function show($id_detail_barang)
    {
        try {
            $detail = BarangInventarisDetail::with('barangInventaris')->findOrFail($id_detail_barang);
            return response()->json($detail);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Detail barang tidak ditemukan.'], 404);
        }
    }


    // Memperbarui data detail barang (Update)
    public function update(Request $request, $id_detail_barang)
    {
        try {
            $validated = $request->validate([
                'kondisi' => 'required|string|in:Baik,Perlu Perbaikan,Rusak Berat',
                'status' => 'required|string|in:Tersedia,Dipinjam,Perbaikan,Dihapus',
                'lokasi' => 'nullable|string|max:100',
                'deskripsi' => 'nullable|string',
                'tanggal_masuk' => 'nullable|date',
            ]);

            $detail = BarangInventarisDetail::findOrFail($id_detail_barang);
            $detail->update($validated);

            // --- MANUAL: UPDATE STOK MASTER ---
            $this->updateMasterStock($detail->id_barang); 

            return response()->json(['message' => 'Detail barang inventaris berhasil diubah!']);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error updating detail unit: ' . $e->getMessage());
            return response()->json(['message' => 'Terjadi kesalahan server saat memperbarui.'], 500);
        }
    }


    // Menghapus data detail barang (Delete)
    public function destroy($id_detail_barang)
    {
        try {
            $detail = BarangInventarisDetail::findOrFail($id_detail_barang);
            $id_barang_master = $detail->id_barang; // Simpan ID Master sebelum dihapus
            
            $deleted = $detail->delete(); 

            if ($deleted) {
                // --- MANUAL: UPDATE STOK MASTER ---
                $this->updateMasterStock($id_barang_master); 
                
                return response()->json(['message' => 'Detail barang inventaris berhasil dihapus!']);
            } else {
                return response()->json(['message' => 'Detail barang tidak ditemukan.'], 404);
            }
        } catch (\Exception $e) {
            Log::error('Error deleting detail unit: ' . $e->getMessage());
            return response()->json(['message' => 'Terjadi kesalahan saat menghapus.'], 500);
        }
    }
}