<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MasjidProfil;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PengaturanController extends Controller
{
    public function edit()
    {
        // 1. Ambil settings, atau buat baru dengan data default
        $settings = MasjidProfil::firstOrCreate([], [
            'nama_masjid' => 'Nama Masjid Anda',
            'lokasi_nama' => 'Alamat detail Anda...',
            'lokasi_id_api' => '1218', // Default (Tasikmalaya)
            'lokasi_nama_api' => 'KOTA TASIKMALAYA' // Default nama
        ]);

        // 2. Ambil ID yang tersimpan (dari old() atau db)
        $selectedLokasiId = old('lokasi_id_api', $settings->lokasi_id_api);

        // 3. Ambil NAMA yang tersimpan (dari old() atau db)
        $selectedLokasiText = old('lokasi_nama_api', $settings->lokasi_nama_api);

        // 4. (Pengecekan) Jika di DB ada ID tapi tidak ada NAMA (misal data lama)
        //    panggil API untuk mengambil namanya agar field tidak kosong.
        if ($selectedLokasiId && empty($selectedLokasiText)) {
            try {
                // Panggil API eksternal
                $response = Http::get("https://api.myquran.com/v2/sholat/kota/id/{$selectedLokasiId}");
                $data = $response->json();

                if ($data['status'] && isset($data['data'])) {
                    $selectedLokasiText = $data['data']['lokasi'];
                    // Langsung simpan ke DB agar tidak perlu dicari lagi nanti
                    $settings->update(['lokasi_nama_api' => $selectedLokasiText]);
                }
            } catch (\Exception $e) {
                // Jika API gagal, catat di log dan beri nama default
                Log::error('Gagal mengambil nama kota by ID: ' . $e->getMessage());
                $selectedLokasiText = "Gagal memuat nama kota"; // Teks sementara
            }
        }

        // 5. Kirim semua data ke view
        //    Sekarang view akan menerima $settings, $selectedLokasiId, dan $selectedLokasiText
        return view('settings', compact(
            'settings',
            'selectedLokasiId',
            'selectedLokasiText'
        ));
    }


    public function update(Request $request)
    {
        // 1. Validasi (Tambahkan lokasi_nama_api)
        $request->validate([
            'nama_masjid' => 'required|string|max:255',
            'lokasi_nama' => 'required|string|max:255',
            'lokasi_id_api' => 'required|string|max:10',
            'lokasi_nama_api' => 'required|string|max:255',
            'deskripsi_masjid' => 'nullable|string',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'foto_masjid' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'social_facebook' => 'nullable|url',
            'social_instagram' => 'nullable|url',
            'social_twitter' => 'nullable|url',
            'social_youtube' => 'nullable|url',
            'social_whatsapp' => 'nullable|string',
        ]);

        // 2. Ambil data
        $settings = MasjidProfil::first();
        // Ambil SEMUA data kecuali yg disebut di 'except'
        // 'lokasi_nama_api' akan otomatis ikut karena tidak ada di 'except'
        $dataToUpdate = $request->except(['_token', '_method', 'foto_masjid', 'hapus_foto_masjid']);

        // 3. Logika Upload Foto (Ini sudah benar)
        if ($request->hasFile('foto_masjid')) {

            if ($settings->foto_masjid && Storage::disk('public')->exists($settings->foto_masjid)) {
                Storage::disk('public')->delete($settings->foto_masjid);
            }

            $path = $request->file('foto_masjid')->store('masjid', 'public');
            $dataToUpdate['foto_masjid'] = $path;
        } else if ($request->input('hapus_foto_masjid') === '1') {

            if ($settings->foto_masjid && Storage::disk('public')->exists($settings->foto_masjid)) {
                Storage::disk('public')->delete($settings->foto_masjid);
            }
            $dataToUpdate['foto_masjid'] = null;
        }

        // 4. Update database
        //    Ini akan menyimpan 'lokasi_id_api' DAN 'lokasi_nama_api'
        $settings->update($dataToUpdate);
        $settings->refresh(); // Ambil data terbaru setelah update

        // 5. Kirim respon JSON sukses
        return response()->json([
            'success' => true,
            'message' => 'Pengaturan berhasil diperbarui!',
            'foto_url' => $settings->foto_masjid ? Storage::url($settings->foto_masjid) : null
        ]);
    }
}
