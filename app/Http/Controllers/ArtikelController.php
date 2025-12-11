<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Artikel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ArtikelController extends Controller
{
    public function index()
    {

        $totalArtikel = Artikel::count();

        return view('artikel', compact('totalArtikel'));
    }

    public function create()
    {
        return view('artikel_form');
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'judul_artikel' => 'required|string|max:255',
            'isi_artikel' => 'required',
            'status_artikel' => 'required|in:draft,published',
            'penulis_artikel' => 'required|string|max:100',
            'tanggal_terbit_artikel' => 'required|date',
            'foto_artikel' => 'nullable|image|max:2048',
        ]);


        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal, periksa inputan anda.',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();


        if ($request->hasFile('foto_artikel')) {
            $path = $request->file('foto_artikel')->store('artikel_photos', 'public');
            $data['foto_artikel'] = $path;
        }


        try {
            Artikel::create($data);

            return response()->json([
                'message' => 'Artikel berhasil ditambahkan!',
                'redirect_url' => route('pengurus.artikel.index')
            ], 200);
        } catch (\Exception $e) {

            return response()->json([
                'message' => 'Gagal menyimpan artikel: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit(string $id)
    {
        $artikel = Artikel::findOrFail($id);
        return view('artikel_form', compact('artikel'));
    }

    public function update(Request $request, string $id)
    {
        $artikel = Artikel::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'judul_artikel' => 'required|string|max:255',
            'isi_artikel' => 'nullable',
            'status_artikel' => 'required|in:draft,published',
            'penulis_artikel' => 'required|string|max:100',
            'tanggal_terbit_artikel' => 'required|date',
            'foto_artikel' => 'nullable|image|max:2048',
        ]);


        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();

        if (empty($request->isi_artikel) || trim(strip_tags($request->isi_artikel)) == '') {
            return response()->json(['message' => 'Isi artikel tidak boleh kosong.'], 422);
        }


        if ($request->has('hapus_foto') && $request->hapus_foto == '1') {
            if ($artikel->foto_artikel) {
                Storage::disk('public')->delete($artikel->foto_artikel);
            }
            $data['foto_artikel'] = null;
        }


        if ($request->hasFile('foto_artikel')) {
            if ($artikel->foto_artikel && Storage::disk('public')->exists($artikel->foto_artikel)) {
                Storage::disk('public')->delete($artikel->foto_artikel);
            }
            $path = $request->file('foto_artikel')->store('artikel_photos', 'public');
            $data['foto_artikel'] = $path;
        } elseif (!array_key_exists('foto_artikel', $data)) {
            unset($data['foto_artikel']);
        }

        try {
            $artikel->update($data);

            return response()->json([
                'message' => 'Artikel berhasil diperbarui!',
                'redirect_url' => route('pengurus.artikel.index')
            ], 200);
        } catch (\Exception $e) {

            return response()->json([
                'message' => 'Gagal memperbarui artikel: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(string $id)
    {
        $artikel = Artikel::find($id);

        if (!$artikel) {
            return response()->json(['message' => 'Artikel tidak ditemukan.'], 404);
        }

        try {

            if ($artikel->foto_artikel) {
                Storage::disk('public')->delete($artikel->foto_artikel);
            }

            $artikel->delete();

            return response()->json(['message' => 'Artikel berhasil dihapus.'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal menghapus artikel: ' . $e->getMessage()], 500);
        }
    }

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


        if ($request->status && $request->status !== 'all') {
            $query->where('status_artikel', $request->status);
        }


        if ($request->search) {
            $query->where('judul_artikel', 'like', '%' . $request->search . '%')
                ->orWhere('penulis_artikel', 'like', '%' . $request->search . '%');
        }


        $query->orderBy($request->sortBy ?? 'tanggal_terbit_artikel', $request->sortDir ?? 'desc');


        $artikels = $query->paginate($request->perPage ?? 10);

        return response()->json($artikels);
    }
}
