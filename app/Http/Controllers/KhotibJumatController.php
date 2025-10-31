<?php

namespace App\Http\Controllers;

use App\Http\Requests\KhotibJumatRequest;
use App\Models\KhotibJumat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class KhotibJumatController extends Controller
{
    public function index()
    {
        return view('khotib-jumat');
    }

    public function data()
    {
        return response()->json(KhotibJumat::orderBy('tanggal', 'desc')->get());
    }

    public function store(KhotibJumatRequest $request)
    {
        $data = $request->validated();
        $data['id_khutbah'] = Str::uuid();

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
