<?php

namespace App\Http\Controllers;

use App\Models\HewanQurban;
use Illuminate\Http\Request;

class HewanQurbanController extends Controller
{
    // Mengambil data JSON untuk ditampilkan di Modal/Tabel
    public function index() {
        $data = HewanQurban::where('is_active', true)->orderBy('nama_hewan')->get();
        return response()->json($data);
    }

    // Simpan Harga Baru / Edit
    public function store(Request $request) {
        $val = $request->validate([
            'nama_hewan' => 'required|in:kambing,domba,sapi,kerbau,unta',
            'kategori_hewan' => 'required|in:premium,reguler,basic',
            'harga_hewan' => 'required|numeric|min:0'
        ]);
        
        // Cek apakah kombinasi sudah ada, jika ada update harga saja
        $exist = HewanQurban::where('nama_hewan', $val['nama_hewan'])
                            ->where('kategori_hewan', $val['kategori_hewan'])
                            ->where('is_active', true)
                            ->first();

        if($exist) {
            $exist->update(['harga_hewan' => $val['harga_hewan']]);
            return response()->json(['message' => 'Harga hewan berhasil diperbarui']);
        } else {
            HewanQurban::create($val);
            return response()->json(['message' => 'Data hewan baru berhasil disimpan']);
        }
    }

    public function destroy($id) {
        $hewan = HewanQurban::findOrFail($id);
        $hewan->update(['is_active' => false]); 
        return response()->json(['message' => 'Hewan dinonaktifkan']);
    }

    public function update(Request $request, $id)
    {
        $hewan = HewanQurban::findOrFail($id);
        $hewan->update([
            'nama_hewan' => $request->nama_hewan,
            'kategori_hewan' => $request->kategori_hewan,
            'harga_hewan' => $request->harga_hewan,
        ]);

        return response()->json(['success' => true, 'message' => 'Harga berhasil diperbarui']);
    }
}