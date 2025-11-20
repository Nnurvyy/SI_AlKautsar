<?php

namespace App\Http\Controllers;

use App\Models\Program; // Menggunakan Model Program yang baru dibuat
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon; 

class ProgramController extends Controller
{
    /**
     * Tampilkan halaman view index.
     */
    public function index()
    {
        return view('program'); 
    }

    /**
     * Ambil data program untuk tampilan tabel (JSON API).
     */
    public function data(Request $request)
    {
        // Parameter filter dan sorting dari request
        $status = $request->query('status', 'aktif');
        $search = $request->query('search', '');
        $perPage = $request->query('perPage', 10);
        $sortBy = $request->query('sortBy', 'tanggal_program'); 
        $sortDir = $request->query('sortDir', 'desc'); 

        $query = Program::query();

        // 1. Filter status 
        if ($status === 'aktif') {
            $query->where('tanggal_program', '>=', Carbon::now()); 
        } elseif ($status === 'tidak_aktif') {
            $query->where('tanggal_program', '<', Carbon::now());
        }

        // 2. Filter search 
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $searchLower = strtolower($search);

                $q->whereRaw('LOWER(nama_program) LIKE ?', ["%{$searchLower}%"])
                ->orWhereRaw('LOWER(penyelenggara_program) LIKE ?', ["%{$searchLower}%"])
                ->orWhereRaw('LOWER(lokasi_program) LIKE ?', ["%{$searchLower}%"])
                ->orWhereRaw("LOWER(to_char(tanggal_program, 'DD Mon YYYY')) LIKE ?", ["%{$searchLower}%"])
                ->orWhereRaw("LOWER(to_char(tanggal_program, 'YYYY-MM-DD')) LIKE ?", ["%{$searchLower}%"]);
            }); 
        }

        // 3. Urutkan data
        $allowedSorts = ['tanggal_program', 'nama_program', 'penyelenggara_program']; 
        if (!in_array($sortBy, $allowedSorts)) $sortBy = 'tanggal_program';
        $sortDir = $sortDir === 'asc' ? 'asc' : 'desc';

        $data = $query->orderBy($sortBy, $sortDir)->paginate($perPage);

        return response()->json($data);
    }


    /**
     * Simpan data Program baru.
     * MENGGUNAKAN VALIDASI INLINE.
     */
    public function store(Request $request) 
    {
        // VALIDASI INLINE DISINI
        $rules = [
            'nama_program' => 'required|string|max:255',
            'penyelenggara_program' => 'required|string|max:150',
            'lokasi_program' => 'required|string|max:255',
            'tanggal_program' => 'required|date_format:Y-m-d\TH:i',
            'deskripsi_program' => 'required|string',
            'foto_program' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', 
            // Tambahkan kolom status, tetapi jadikan nullable
            'status_program' => 'nullable|string|max:50', // Agar tidak error jika kolom ada
        ];

        // Validasi dan simpan data yang sudah divalidasi
        $data = $request->validate($rules); 

        // Jika kolom status_program tidak dikirim, dan Anda TIDAK ingin default DB,
        // Anda bisa menyetel nilai awal di sini, tapi default DB lebih baik.
        // if (!isset($data['status_program'])) {
        //     $data['status_program'] = 'DRAFT'; 
        // }

        if ($request->hasFile('foto_program')) {
            $data['foto_program'] = $request->file('foto_program')->store('program', 'public');
        }

        Program::create($data);

        return response()->json(['success' => true, 'message' => 'Program berhasil ditambahkan.']);
    }

    /**
     * Tampilkan detail satu Program.
     */
    public function show(Program $program)
    {
        return response()->json($program);
    }

    /**
     * Perbarui data Program.
     * MENGGUNAKAN VALIDASI INLINE.
     */
    public function update(Request $request, Program $program) // PERUBAHAN: Dari ProgramRequest menjadi Request
    {
        // VALIDASI INLINE DISINI (dengan penyesuaian untuk PUT/UPDATE)
        $rules = [
            'nama_program' => 'required|string|max:255',
            'penyelenggara_program' => 'required|string|max:150',
            'lokasi_program' => 'required|string|max:255',
            'tanggal_program' => 'required|date_format:Y-m-d\TH:i',
            'deskripsi_program' => 'required|string',
            'status_program' => 'nullable|string|max:50',
            // Aturan File: Hanya validasi jika ada file yang diupload saat update
            'foto_program' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', 
        ];

        // Validasi dan simpan data yang sudah divalidasi
        $data = $request->validate($rules);

        if ($request->hasFile('foto_program')) {
            if ($program->foto_program && Storage::disk('public')->exists($program->foto_program)) {
                Storage::disk('public')->delete($program->foto_program);
            }
            $data['foto_program'] = $request->file('foto_program')->store('program', 'public');
        }

        $program->update($data);

        return response()->json(['success' => true, 'message' => 'Program berhasil diperbarui.']);
    }

    /**
     * Hapus data Program.
     */
    public function destroy(Program $program)
    {
        if ($program->foto_program && Storage::disk('public')->exists($program->foto_program)) {
            Storage::disk('public')->delete($program->foto_program);
        }

        $program->delete();

        return response()->json(['success' => true, 'message' => 'Data program dihapus.']);
    }
}