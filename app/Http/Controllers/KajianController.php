<?php

namespace App\Http\Controllers;

// 1. Ganti Request dan Model
use App\Http\Requests\KajianRequest; // <-- Nanti kita buat file ini
use App\Models\Kajian; // <-- Gunakan model Kajian
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon; 

class KajianController extends Controller
{
    public function index()
    {
        // 2. Ganti view ke 'kajian'
        return view('kajian');
    }

    public function data(Request $request)
    {
        $query = Kajian::query();

        // --- Filter Status ---
        if ($request->status == 'aktif') {
            // Ambil yang (Tipe Rutin) ATAU (Tipe Event DAN Tanggal >= Hari Ini)
            $query->where(function($q) {
                $q->where('tipe', 'rutin')
                  ->orWhere(function($subQ) {
                      $subQ->where('tipe', 'event')
                           ->whereDate('tanggal_kajian', '>=', now());
                  });
            });
        } 
        elseif ($request->status == 'tidak_aktif') {
            // Ambil yang Tipe Event DAN Tanggal < Hari Ini
            // (Kajian Rutin tidak pernah masuk sini, kecuali kamu mau logic lain)
            $query->where('tipe', 'event')
                  ->whereDate('tanggal_kajian', '<', now());
        }

        // --- Filter Tipe (Tambahan yang kamu buat tadi) ---
        if ($request->has('tipe') && $request->tipe != '') {
            $query->where('tipe', $request->tipe);
        }

        // --- Filter Search ---
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_penceramah', 'LIKE', "%$search%")
                  ->orWhere('tema_kajian', 'LIKE', "%$search%");
            });
        }

        // ... sorting & pagination code ...
        // Contoh:
        $sortDir = $request->get('sortDir', 'desc');
        $sortBy = $request->get('sortBy', 'tanggal_kajian');
        
        // PENTING: Fix sorting untuk kajian rutin yg tanggalnya null
        // Agar kajian rutin tetap muncul rapi, kita bisa sort by created_at atau hari
        if($sortBy == 'tanggal_kajian') {
             $data = $query->orderByRaw("CASE WHEN tanggal_kajian IS NULL THEN 1 ELSE 0 END, tanggal_kajian " . $sortDir)->paginate($request->perPage);
        } else {
             $data = $query->orderBy($sortBy, $sortDir)->paginate($request->perPage);
        }

        return response()->json($data);
    }

    // 8. Ganti Request
    public function store(KajianRequest $request)
    {
        $data = $request->validated();

        // 9. Ganti nama file & folder
        if ($request->hasFile('foto_penceramah')) {
            $data['foto_penceramah'] = $request->file('foto_penceramah')->store('kajian', 'public');
        }

        // 10. Ganti Model
        Kajian::create($data);

        return response()->json(['success' => true, 'message' => 'Kajian berhasil ditambahkan.']);
    }

    // 11. Ganti Model
    public function show(Kajian $kajian)
    {
        return response()->json($kajian);
    }

    // 12. Ganti Request dan Model
    public function update(KajianRequest $request, Kajian $kajian)
    {
        $data = $request->validated();

        // 13. Ganti nama file & folder
        if ($request->hasFile('foto_penceramah')) {
            if ($kajian->foto_penceramah && Storage::disk('public')->exists($kajian->foto_penceramah)) {
                Storage::disk('public')->delete($kajian->foto_penceramah);
            }
            $data['foto_penceramah'] = $request->file('foto_penceramah')->store('kajian', 'public');
        }

        $kajian->update($data);

        return response()->json(['success' => true, 'message' => 'Kajian berhasil diperbarui.']);
    }

    // 14. Ganti Model
    public function destroy(Kajian $kajian)
    {
        // 15. Ganti nama file
        if ($kajian->foto_penceramah && Storage::disk('public')->exists($kajian->foto_penceramah)) {
            Storage::disk('public')->delete($kajian->foto_penceramah);
        }

        $kajian->delete();

        return response()->json(['success' => true, 'message' => 'Data kajian dihapus.']);
    }
}