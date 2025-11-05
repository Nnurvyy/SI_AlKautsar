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
        $status = $request->query('status', 'aktif');
        $search = $request->query('search', '');
        $perPage = $request->query('perPage', 10);
        // 3. Ganti kolom default
        $sortBy = $request->query('sortBy', 'tanggal_kajian'); 
        $sortDir = $request->query('sortDir', 'desc');  

        // 4. Ganti Model
        $query = Kajian::query();

        // 5. Ganti kolom tanggal
        if ($status === 'aktif') {
            $query->where('tanggal_kajian', '>=', Carbon::today());
        } elseif ($status === 'tidak_aktif') {
            $query->where('tanggal_kajian', '<', Carbon::today());
        }

        // 6. Ganti kolom pencarian
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $searchLower = strtolower($search);

                $q->whereRaw('LOWER(nama_penceramah) LIKE ?', ["%{$searchLower}%"])
                ->orWhereRaw('LOWER(tema_kajian) LIKE ?', ["%{$searchLower}%"])
                ->orWhereRaw("LOWER(to_char(tanggal_kajian, 'DD Mon YYYY')) LIKE ?", ["%{$searchLower}%"])
                ->orWhereRaw("LOWER(to_char(tanggal_kajian, 'DD Month YYYY')) LIKE ?", ["%{$searchLower}%"])
                ->orWhereRaw("LOWER(to_char(tanggal_kajian, 'YYYY-MM-DD')) LIKE ?", ["%{$searchLower}%"]);
            });
        }

        // 7. Ganti kolom sort
        $allowedSorts = ['tanggal_kajian', 'nama_penceramah', 'tema_kajian']; 
        if (!in_array($sortBy, $allowedSorts)) $sortBy = 'tanggal_kajian';
        $sortDir = $sortDir === 'asc' ? 'asc' : 'desc';

        $data = $query->orderBy($sortBy, $sortDir)->paginate($perPage);

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