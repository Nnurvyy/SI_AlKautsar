<?php

namespace App\Http\Controllers;

use App\Models\KategoriKeuangan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KategoriKeuanganController extends Controller
{

    public function data(Request $request)
    {
        $query = KategoriKeuangan::query();


        if ($request->has('tipe')) {
            $query->where('tipe', $request->query('tipe'));
        }

        $data = $query->orderBy('nama_kategori_keuangan', 'asc')->get();
        return response()->json($data);
    }


    public function store(Request $request)
    {
        $request->validate([
            'tipe' => 'required|in:pemasukan,pengeluaran',
            'nama_kategori_keuangan' => [
                'required',
                'string',
                'max:100',

                Rule::unique('kategori_keuangan')->where(function ($query) use ($request) {
                    return $query->where('tipe', $request->tipe);
                }),
            ],
        ]);

        $kategori = KategoriKeuangan::create($request->all());

        return response()->json(['message' => 'Kategori berhasil ditambah.', 'data' => $kategori]);
    }


    public function update(Request $request, $id)
    {
        $kategori = KategoriKeuangan::findOrFail($id);

        $request->validate([
            'nama_kategori_keuangan' => [
                'required',
                'string',
                'max:100',

                Rule::unique('kategori_keuangan')->where(function ($query) use ($kategori) {
                    return $query->where('tipe', $kategori->tipe);
                })->ignore($kategori->id_kategori_keuangan, 'id_kategori_keuangan'),
            ],
        ]);

        $kategori->update(['nama_kategori_keuangan' => $request->nama_kategori_keuangan]);

        return response()->json(['message' => 'Kategori berhasil diperbarui.']);
    }


    public function destroy($id)
    {
        $kategori = KategoriKeuangan::findOrFail($id);
        $kategori->delete();
        return response()->json(['message' => 'Kategori berhasil dihapus.']);
    }
}
