<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Artikel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ArtikelController extends Controller
{
    /**
     * Menampilkan halaman utama dashboard artikel.
     */
    public function index()
    {
        return view('artikel');
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
            return redirect()->route('admin.artikel.index')->with('success', 'Artikel berhasil ditambahkan!');
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

        // Foto baru?
        if ($request->hasFile('foto_artikel')) {

            // Hapus foto lama
            if ($artikel->foto_artikel) {
                Storage::disk('public')->delete($artikel->foto_artikel);
            }

            // Upload foto baru
            $path = $request->file('foto_artikel')->store('artikel_photos', 'public');
            $data['foto_artikel'] = $path;

        } else {
            // Jangan hapus foto_artikel jika user tidak upload baru
            unset($data['foto_artikel']);
        }

        try {
            $artikel->update($data);
            return redirect()->route('admin.artikel.index')->with('success', 'Artikel berhasil diperbarui!');
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
            \Log::info('artikelData terpanggil', ['id' => $id, 'query' => $request->all()]);
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
