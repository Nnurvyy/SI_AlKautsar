<?php

namespace App\Http\Controllers;


use App\Http\Requests\KajianRequest;
use App\Models\Kajian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class KajianController extends Controller
{
    public function index()
    {

        return view('kajian');
    }

    public function data(Request $request)
    {
        $query = Kajian::query();


        if ($request->status == 'aktif') {

            $query->where(function ($q) {
                $q->where('tipe', 'rutin')
                    ->orWhere(function ($subQ) {
                        $subQ->where('tipe', 'event')
                            ->whereDate('tanggal_kajian', '>=', now());
                    });
            });
        } elseif ($request->status == 'tidak_aktif') {


            $query->where('tipe', 'event')
                ->whereDate('tanggal_kajian', '<', now());
        }


        if ($request->has('tipe') && $request->tipe != '') {
            $query->where('tipe', $request->tipe);
        }


        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_penceramah', 'LIKE', "%$search%")
                    ->orWhere('tema_kajian', 'LIKE', "%$search%");
            });
        }



        $sortDir = $request->get('sortDir', 'desc');
        $sortBy = $request->get('sortBy', 'tanggal_kajian');



        if ($sortBy == 'tanggal_kajian') {
            $data = $query->orderByRaw("CASE WHEN tanggal_kajian IS NULL THEN 1 ELSE 0 END, tanggal_kajian " . $sortDir)->paginate($request->perPage);
        } else {
            $data = $query->orderBy($sortBy, $sortDir)->paginate($request->perPage);
        }

        return response()->json($data);
    }


    public function store(KajianRequest $request)
    {
        $data = $request->validated();


        if ($request->hasFile('foto_penceramah')) {
            $data['foto_penceramah'] = $request->file('foto_penceramah')->store('kajian', 'public');
        }


        Kajian::create($data);

        return response()->json(['success' => true, 'message' => 'Kajian berhasil ditambahkan.']);
    }


    public function show(Kajian $kajian)
    {
        return response()->json($kajian);
    }


    public function update(KajianRequest $request, Kajian $kajian)
    {
        $data = $request->validated();


        if ($request->hasFile('foto_penceramah')) {
            if ($kajian->foto_penceramah && Storage::disk('public')->exists($kajian->foto_penceramah)) {
                Storage::disk('public')->delete($kajian->foto_penceramah);
            }
            $data['foto_penceramah'] = $request->file('foto_penceramah')->store('kajian', 'public');
        }

        $kajian->update($data);

        return response()->json(['success' => true, 'message' => 'Kajian berhasil diperbarui.']);
    }


    public function destroy(Kajian $kajian)
    {

        if ($kajian->foto_penceramah && Storage::disk('public')->exists($kajian->foto_penceramah)) {
            Storage::disk('public')->delete($kajian->foto_penceramah);
        }

        $kajian->delete();

        return response()->json(['success' => true, 'message' => 'Data kajian dihapus.']);
    }
}
