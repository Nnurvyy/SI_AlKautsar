<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\BarangInventaris;
use Illuminate\Validation\ValidationException;

class BarangInventarisController extends Controller
{


    public function index()
    {
        $totalBarang = BarangInventaris::count();
        return view('barang-inventaris', compact('totalBarang'));
    }


    public function data(Request $request)
    {
        $search = $request->get('search');
        $kondisi = $request->get('kondisi');

        $sortBy = $request->get('sortBy', 'nama_barang');
        $sortDir = $request->get('sortDir', 'asc');
        $perPage = $request->get('perPage', 10);

        $query = BarangInventaris::query();



        if ($kondisi && $kondisi !== 'all') {
            $query->where('kondisi', $kondisi);
        }


        if ($search) {
            $query->whereRaw('LOWER(nama_barang) LIKE ?', ['%' . strtolower($search) . '%']);
        }


        $query->orderBy($sortBy, $sortDir);


        $data = $query->paginate($perPage);

        return response()->json($data);
    }


    public function store(Request $request)
    {
        try {

            $validated = $request->validate([
                'nama_barang' => 'required|string|max:255',
                'satuan' => 'required|string|max:50',
                'kondisi' => 'required|string|in:Baik,Perlu Perbaikan,Rusak Berat',
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


    public function show($id_barang)
    {
        $barang = BarangInventaris::findOrFail($id_barang);
        return response()->json($barang);
    }


    public function update(Request $request, $id_barang)
    {
        try {

            $validated = $request->validate([
                'nama_barang' => 'required|string|max:255',
                'satuan' => 'required|string|max:50',
                'kondisi' => 'required|string|in:Baik,Perlu Perbaikan,Rusak Berat',
                'stock' => 'required|integer|min:0',
            ]);


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


    public function destroy($id_barang)
    {
        try {

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
