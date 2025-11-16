<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MasjidProfil;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException; // <-- Penting untuk AJAX

class PengaturanController extends Controller
{
    /**
     * Tampilkan halaman form pengaturan.
     */
    public function edit()
    {
        $settings = MasjidProfil::firstOrCreate([]); 
        
        // Pastikan view Anda ada di 'resources/views/admin/settings/edit.blade.php'
        // Jika file Anda 'settings.blade.php', ganti 'admin.settings.edit' menjadi 'settings'
        return view('settings', compact('settings')); 
    }

    /**
     * Update data pengaturan di database.
     */
    public function update(Request $request)
    {
        try {
            $request->validate([
                'nama_masjid' => 'required|string|max:255',
                'lokasi_nama' => 'required|string|max:255',
                'lokasi_id_api' => 'required|string|max:10',
                'foto_masjid' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', 
                'social_facebook' => 'nullable|url',
                'social_instagram' => 'nullable|url',
                'social_twitter' => 'nullable|url',
                'social_youtube' => 'nullable|url',
                'social_whatsapp' => 'nullable|string',
            ]);

            $settings = MasjidProfil::first();
            // Ambil semua data kecuali token, method, foto, dan hapus_foto
            $dataToUpdate = $request->except(['_token', '_method', 'foto_masjid', 'hapus_foto_masjid']);

            // 1. Jika ada FILE BARU di-upload
            if ($request->hasFile('foto_masjid')) {
                
                // Hapus foto lama (jika ada) dari disk 'public'
                if ($settings->foto_masjid && Storage::disk('public')->exists($settings->foto_masjid)) {
                    Storage::disk('public')->delete($settings->foto_masjid);
                }
                
                // Simpan foto baru ke 'storage/app/public/masjid'
                // Ini akan mengembalikan path 'masjid/namafile.jpg' (INI YANG BENAR)
                $path = $request->file('foto_masjid')->store('masjid', 'public');
                $dataToUpdate['foto_masjid'] = $path;

            // 2. Jika TIDAK ADA file baru, TAPI user klik "X" (Hapus Foto)
            } else if ($request->input('hapus_foto_masjid') === '1') {
                
                // Hapus foto lama (jika ada) dari disk 'public'
                if ($settings->foto_masjid && Storage::disk('public')->exists($settings->foto_masjid)) {
                    Storage::disk('public')->delete($settings->foto_masjid);
                }
                // Set 'null' di database
                $dataToUpdate['foto_masjid'] = null;
            }
            // 3. (Jika tidak ada file baru & tidak klik "X", biarkan foto_masjid apa adanya)

            // Update database
            $settings->update($dataToUpdate);
            $settings->refresh(); // Ambil data terbaru setelah update

            // Kirim respon JSON sukses
            return response()->json([
                'success' => true, 
                'message' => 'Pengaturan berhasil diperbarui!',
                'foto_url' => $settings->foto_masjid ? Storage::url($settings->foto_masjid) : null 
            ]);

        } catch (ValidationException $e) {
            // Kirim respon JSON jika validasi gagal
            return response()->json([
                'success' => false,
                'message' => 'Input tidak valid.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            // Kirim respon JSON jika ada error lain
            return response()->json([
                'success' => false, 
                'message' => 'Terjadi kesalahan server: ' . $e->getMessage()
            ], 500);
        }
    }
}