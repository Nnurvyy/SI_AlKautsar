<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MasjidProfil; // <-- Tambahkan ini
use Illuminate\Support\Facades\Storage; // <-- Tambahkan ini
use Illuminate\Support\Facades\Artisan;

class PengaturanController extends Controller
{
    /**
     * Tampilkan halaman form pengaturan.
     */
    public function edit()
    {
        // Ambil baris pertama, atau buat baru jika tabel masih kosong
        // Model Anda akan otomatis membuat UUID jika ini baris baru
        $settings = MasjidProfil::firstOrCreate([]); 
        
        return view('settings', compact('settings'));
    }

    /**
     * Update data pengaturan di database.
     */
    public function update(Request $request)
    {
        // Validasi input
        $request->validate([
            'nama_masjid' => 'required|string|max:255',
            'lokasi_nama' => 'required|string|max:255',
            'lokasi_id_api' => 'required|string|max:10',
            'foto_masjid' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', // 2MB Max
            'social_facebook' => 'nullable|url',
            'social_instagram' => 'nullable|url',
            'social_twitter' => 'nullable|url',
            'social_youtube' => 'nullable|url',
            'social_whatsapp' => 'nullable|string',
        ]);

        // Ambil data settings (yang seharusnya hanya ada 1 baris)
        $settings = MasjidProfil::first();

        // Handle Upload Foto
        if ($request->hasFile('foto_masjid')) {
            // Hapus foto lama jika ada
            if ($settings->foto_masjid) {
                Storage::delete($settings->foto_masjid);
            }
            
            // Simpan foto baru ke 'storage/app/public/masjid'
            // Nama file akan di-generate otomatis
            $path = $request->file('foto_masjid')->store('public/masjid');
            $settings->foto_masjid = $path;
        }

        // Update data lainnya
        $settings->nama_masjid = $request->nama_masjid;
        $settings->lokasi_nama = $request->lokasi_nama;
        $settings->lokasi_id_api = $request->lokasi_id_api;
        $settings->social_facebook = $request->social_facebook;
        $settings->social_instagram = $request->social_instagram;
        $settings->social_twitter = $request->social_twitter;
        $settings->social_youtube = $request->social_youtube;
        $settings->social_whatsapp = $request->social_whatsapp;

        $settings->save();

        // Redirect kembali ke halaman settings dengan pesan sukses
        return redirect()->route('admin.settings.edit')->with('success', 'Pengaturan berhasil diperbarui!');
    }
}