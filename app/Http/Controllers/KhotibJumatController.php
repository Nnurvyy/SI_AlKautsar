<?php

namespace App\Http\Controllers;

use App\Http\Requests\KhotibJumatRequest;
use App\Models\KhotibJumat;
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon; 

class KhotibJumatController extends Controller
{
    public function index()
    {
        return view('khotib-jumat');
    }

    public function data(Request $request)
    {
        $status = $request->query('status', 'aktif');
        $search = $request->query('search', '');
        $perPage = $request->query('perPage', 10);
        $sortBy = $request->query('sortBy', 'tanggal'); // default urut tanggal
        $sortDir = $request->query('sortDir', 'desc');  // default terbaru dulu

        $query = KhotibJumat::query();

        // Filter status
        if ($status === 'aktif') {
            $query->where('tanggal', '>=', Carbon::today());
        } elseif ($status === 'tidak_aktif') {
            $query->where('tanggal', '<', Carbon::today());
        }

        // Filter search 
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $searchLower = strtolower($search);

                $q->whereRaw('LOWER(nama_khotib) LIKE ?', ["%{$searchLower}%"])
                ->orWhereRaw('LOWER(nama_imam) LIKE ?', ["%{$searchLower}%"])
                ->orWhereRaw('LOWER(tema_khutbah) LIKE ?', ["%{$searchLower}%"])
                ->orWhereRaw("LOWER(to_char(tanggal, 'DD Mon YYYY')) LIKE ?", ["%{$searchLower}%"])
                ->orWhereRaw("LOWER(to_char(tanggal, 'DD Month YYYY')) LIKE ?", ["%{$searchLower}%"])
                ->orWhereRaw("LOWER(to_char(tanggal, 'YYYY-MM-DD')) LIKE ?", ["%{$searchLower}%"]);
            });
        }

        // Urutkan data
        $allowedSorts = ['tanggal', 'nama_khotib', 'nama_imam']; 
        if (!in_array($sortBy, $allowedSorts)) $sortBy = 'tanggal';
        $sortDir = $sortDir === 'asc' ? 'asc' : 'desc';

        $data = $query->orderBy($sortBy, $sortDir)->paginate($perPage);

        return response()->json($data);
    }


    public function store(KhotibJumatRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('foto_khotib')) {
            $data['foto_khotib'] = $request->file('foto_khotib')->store('khotib_jumat', 'public');
        }

        KhotibJumat::create($data);

        return response()->json(['success' => true, 'message' => 'Khotib berhasil ditambahkan.']);
    }

    public function show(KhotibJumat $khotib_jumat)
    {
        return response()->json($khotib_jumat);
    }

    public function update(KhotibJumatRequest $request, KhotibJumat $khotib_jumat)
    {
        $data = $request->validated();

        if ($request->hasFile('foto_khotib')) {
            if ($khotib_jumat->foto_khotib && Storage::disk('public')->exists($khotib_jumat->foto_khotib)) {
                Storage::disk('public')->delete($khotib_jumat->foto_khotib);
            }
            $data['foto_khotib'] = $request->file('foto_khotib')->store('khotib_jumat', 'public');
        }

        $khotib_jumat->update($data);

        return response()->json(['success' => true, 'message' => 'Khotib berhasil diperbarui.']);
    }

    public function destroy(KhotibJumat $khotib_jumat)
    {
        if ($khotib_jumat->foto_khotib && Storage::disk('public')->exists($khotib_jumat->foto_khotib)) {
            Storage::disk('public')->delete($khotib_jumat->foto_khotib);
        }

        $khotib_jumat->delete();

        return response()->json(['success' => true, 'message' => 'Data khotib dihapus.']);
    }
}