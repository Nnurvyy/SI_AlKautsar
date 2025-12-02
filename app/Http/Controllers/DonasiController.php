<?php

namespace App\Http\Controllers;

use App\Models\Donasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DonasiController extends Controller
{
    public function index()
    {
        // PERBAIKAN: Tambahkan where('status', 'success')
        // Agar uang pending/gagal tidak ikut dijumlahkan di Header Halaman Admin
        $totalDonasi = DB::table('pemasukan_donasi')
                         ->where('status', 'success') 
                         ->sum('nominal');

        return view('donasi', compact('totalDonasi'));
    }

    // Data JSON untuk DataTables/Pagination
    public function data(Request $request)
    {
        $search  = $request->query('search', '');
        $status  = $request->query('status', 'aktif');
        $perPage = $request->query('perPage', 10);
        $sortBy  = $request->query('sortBy', 'created_at');
        $sortDir = $request->query('sortDir', 'desc');

        // PERBAIKAN: Subquery total terkumpul per program
        // Hanya hitung jika status = success
        $totalTerkumpulSubquery = DB::table('pemasukan_donasi')
            ->select(DB::raw('COALESCE(SUM(nominal), 0)'))
            ->whereColumn('id_donasi', 'donasi.id_donasi')
            ->where('status', 'success'); // <--- PENTING: Filter status success

        $query = Donasi::select('donasi.*')
            ->selectSub($totalTerkumpulSubquery, 'total_terkumpul');

        // 1. Filter Search
        if (!empty($search)) {
            $query->where('nama_donasi', 'ILIKE', "%{$search}%");
        }

        // 2. Filter Status
        $today = Carbon::today();
        if ($status === 'aktif') {
            $query->where(function($q) use ($today) {
                $q->whereDate('tanggal_selesai', '>=', $today)
                  ->orWhereNull('tanggal_selesai');
            });
        } elseif ($status === 'lewat') {
            $query->whereDate('tanggal_selesai', '<', $today);
        }

        // 3. Sorting
        $allowedSorts = ['nama_donasi', 'target_dana', 'tanggal_mulai', 'tanggal_selesai', 'created_at'];
        
        if ($sortBy === 'total_terkumpul') {
            $query->orderByRaw("($totalTerkumpulSubquery) $sortDir");
        } elseif (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDir);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $data = $query->paginate($perPage);

        return response()->json($data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_donasi' => 'required|string|max:255',
            'target_dana' => 'required|numeric|min:0',
            'tanggal_mulai' => 'required|date',
            'foto_donasi' => 'nullable|image|max:2048',
        ]);

        $data = $request->all();
        $data['id_donasi'] = (string) Str::uuid();

        if ($request->hasFile('foto_donasi')) {
            $data['foto_donasi'] = $request->file('foto_donasi')->store('donasi', 'public');
        }

        Donasi::create($data);

        return response()->json(['message' => 'Program donasi berhasil dibuat.']);
    }

    public function show($id)
    {
        // Load donasi beserta history pemasukan
        // Kita biarkan history memuat SEMUA status (pending/success) agar admin bisa melihat log transaksi
        $donasi = Donasi::with(['pemasukan' => function($q) {
            $q->orderBy('tanggal', 'desc');
        }])->findOrFail($id);

        // PERBAIKAN: Hitung total manual hanya yang success
        // Ini untuk angka besar di Modal Detail Admin
        $donasi->total_terkumpul = $donasi->pemasukan
                                          ->where('status', 'success')
                                          ->sum('nominal');

        $donasi->foto_url = $donasi->foto_donasi ? Storage::url($donasi->foto_donasi) : null;

        return response()->json($donasi);
    }

    // ... (Method update dan destroy sama seperti sebelumnya, tidak perlu diubah)
    public function update(Request $request, $id)
    {
        $donasi = Donasi::findOrFail($id);
        
        $request->validate([
            'nama_donasi' => 'required|string|max:255',
            'target_dana' => 'required|numeric|min:0',
            'tanggal_mulai' => 'required|date',
            'foto_donasi' => 'nullable|image|max:2048',
        ]);

        $data = $request->except(['foto_donasi']);

        if ($request->hasFile('foto_donasi')) {
            if ($donasi->foto_donasi && Storage::disk('public')->exists($donasi->foto_donasi)) {
                Storage::disk('public')->delete($donasi->foto_donasi);
            }
            $data['foto_donasi'] = $request->file('foto_donasi')->store('donasi', 'public');
        }

        $donasi->update($data);
        return response()->json(['message' => 'Program donasi berhasil diperbarui.']);
    }

    public function destroy($id)
    {
        $donasi = Donasi::findOrFail($id);
        if ($donasi->foto_donasi && Storage::disk('public')->exists($donasi->foto_donasi)) {
            Storage::disk('public')->delete($donasi->foto_donasi);
        }
        $donasi->delete();
        return response()->json(['message' => 'Program donasi dihapus.']);
    }
}