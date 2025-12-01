<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Artikel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ArtikelController extends Controller
{
    /**
     * Menampilkan halaman utama dashboard artikel.
     */
    public function index()
    {
        // Hitung total artikel
        $totalArtikel = Artikel::count();
        
        return view('artikel', compact('totalArtikel'));
    }

    /**
     * Menampilkan form untuk membuat artikel baru.
     */
    public function create()
    {
        return view('artikel_form');
    }

    /**
     * Menyimpan artikel baru ke database (Tambah).
     */
    public function store(Request $request)
    {
        // 1. Validasi Input
        $validator = Validator::make($request->all(), [
            'judul_artikel' => 'required|string|max:255',
            'isi_artikel' => 'required',
            'status_artikel' => 'required|in:draft,published',
            'penulis_artikel' => 'required|string|max:100',
            'tanggal_terbit_artikel' => 'required|date',
            'foto_artikel' => 'nullable|image|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();
        $fotoPath = null;

        // 2. Upload Foto Jika Ada
        if ($request->hasFile('foto_artikel')) {
            $path = $request->file('foto_artikel')->store('artikel_photos', 'public');
            $data['foto_artikel'] = $path;
        }

        // 3. Simpan ke Database
        try {
            Artikel::create($data);
            return redirect()->route('pengurus.artikel.index')->with('success', 'Artikel berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyimpan artikel: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Menampilkan form edit.
     */
    public function edit(string $id)
    {
        $artikel = Artikel::findOrFail($id);
        return view('artikel_form', compact('artikel'));
    }

    /**
     * Update artikel
     */
    public function update(Request $request, string $id)
    {
        $artikel = Artikel::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'judul_artikel' => 'required|string|max:255',
            // Hapus validasi 'required' untuk isi_artikel di sini jika Anda mengandalkan validasi JS
            // atau pastikan input hidden terisi.
            'isi_artikel' => 'nullable', 
            'status_artikel' => 'required|in:draft,published',
            'penulis_artikel' => 'required|string|max:100',
            'tanggal_terbit_artikel' => 'required|date',
            'foto_artikel' => 'nullable|image|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();
        
        // Pastikan isi artikel tidak kosong jika validator di atas 'nullable'
        if (empty($request->isi_artikel) || trim(strip_tags($request->isi_artikel)) == '') {
             return redirect()->back()->with('error', 'Isi artikel tidak boleh kosong.')->withInput();
        }

        // --- LOGIKA BARU: Cek apakah ada permintaan hapus foto ---
        if ($request->has('hapus_foto') && $request->hapus_foto == '1') {
            // Hapus file lama jika ada
            if ($artikel->foto_artikel) {
                Storage::disk('public')->delete($artikel->foto_artikel);
            }
            // Set kolom di DB menjadi null
            $data['foto_artikel'] = null;
        }

        // --- Logika upload foto baru (jika ada file diupload) ---
        if ($request->hasFile('foto_artikel')) {
            // Hapus foto lama (jika belum dihapus oleh logika 'hapus_foto' di atas)
            if ($artikel->foto_artikel && Storage::disk('public')->exists($artikel->foto_artikel)) {
                Storage::disk('public')->delete($artikel->foto_artikel);
            }
            // Upload baru
            $path = $request->file('foto_artikel')->store('artikel_photos', 'public');
            $data['foto_artikel'] = $path;
        } 
        // Jika tidak ada file baru DAN tidak ada request hapus foto, 
        // maka jangan update kolom foto_artikel (hapus dari array $data)
        elseif (!array_key_exists('foto_artikel', $data)) {
             unset($data['foto_artikel']);
        }

        try {
            $artikel->update($data);
            return redirect()->route('pengurus.artikel.index')->with('success', 'Artikel berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui artikel: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Delete artikel
     */
    public function destroy(string $id)
    {
        $artikel = Artikel::find($id);

        if (!$artikel) {
            return response()->json(['message' => 'Artikel tidak ditemukan.'], 404);
        }

        try {
            // Hapus foto
            if ($artikel->foto_artikel) {
                Storage::disk('public')->delete($artikel->foto_artikel);
            }

            $artikel->delete();

            return response()->json(['message' => 'Artikel berhasil dihapus.'], 200);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal menghapus artikel: ' . $e->getMessage()], 500);
        }
    }

    /**
     * API untuk data artikel (list & single)
     */
    public function artikelData(Request $request, string $id = null)
    {
            Log::info('artikelData terpanggil', ['id' => $id, 'query' => $request->all()]);
        if ($id) {
            $artikel = Artikel::select(
                'id_artikel',
                'judul_artikel',
                'isi_artikel',
                'status_artikel',
                'penulis_artikel',
                'tanggal_terbit_artikel',
                'foto_artikel'
            )->findOrFail($id);

            return response()->json($artikel);
        }

        $query = Artikel::query();

        // Filter Status
        if ($request->status && $request->status !== 'all') {
            $query->where('status_artikel', $request->status);
        }

        // Searching
        if ($request->search) {
            $query->where('judul_artikel', 'like', '%' . $request->search . '%')
                ->orWhere('penulis_artikel', 'like', '%' . $request->search . '%');
        }

        // Sorting
        $query->orderBy($request->sortBy ?? 'tanggal_terbit_artikel', $request->sortDir ?? 'desc');

        // Pagination
        $artikels = $query->paginate($request->perPage ?? 10);

        return response()->json($artikels);
    }
}
