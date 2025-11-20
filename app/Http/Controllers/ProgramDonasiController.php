<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProgramDonasi;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProgramDonasiController extends Controller
{
    public function index()
    {
        return view('program-donasi');
    }

    public function data(Request $request)
    {
        $perPage = $request->query('perPage', 10);
        $sortBy = $request->query('sortBy', 'created_at');
        $sortDir = $request->query('sortDir', 'desc');

        $allowedSorts = ['judul', 'target_dana', 'dana_terkumpul', 'created_at', 'tanggal_selesai']; 
        if (!in_array($sortBy, $allowedSorts)) $sortBy = 'created_at';
        $sortDir = $sortDir === 'asc' ? 'asc' : 'desc';

        $query = ProgramDonasi::query();
        $data = $query->orderBy($sortBy, $sortDir)->paginate($perPage);

        // Pastikan kita mengirim struktur array/paginasi yang konsisten ke JS
        return response()->json($data->toArray());
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'target_dana' => 'required|numeric|min:0',
            'dana_terkumpul' => 'required|numeric|min:0',
            'tanggal_selesai' => 'required|date',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->except('gambar');

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('donasi', 'public');
        }

        ProgramDonasi::create($data);
        return response()->json(['message' => 'Program donasi berhasil ditambahkan.'], 201);
    }

    public function show(string $id)
    {
        $program = ProgramDonasi::findOrFail($id);
        return response()->json($program);
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'target_dana' => 'required|numeric|min:0',
            'dana_terkumpul' => 'required|numeric|min:0',
            'tanggal_selesai' => 'required|date',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $program = ProgramDonasi::findOrFail($id);
        $data = $request->except('gambar');

        if ($request->hasFile('gambar')) {
            if ($program->gambar && Str::startsWith($program->gambar, 'donasi/')) {
                if (Storage::disk('public')->exists($program->gambar)) {
                    Storage::disk('public')->delete($program->gambar);
                }
            }
            $data['gambar'] = $request->file('gambar')->store('donasi', 'public');
        }

        $program->update($data);
        return response()->json(['message' => 'Program donasi berhasil diperbarui.']);
    }

    public function destroy(string $id)
    {
        $program = ProgramDonasi::findOrFail($id);
        
        if ($program->gambar && Str::startsWith($program->gambar, 'donasi/')) {
            if (Storage::disk('public')->exists($program->gambar)) {
                Storage::disk('public')->delete($program->gambar);
            }
        }
        
        $program->delete();
        return response()->json(['message' => 'Data berhasil dihapus.'], 200);
    }
    
}
