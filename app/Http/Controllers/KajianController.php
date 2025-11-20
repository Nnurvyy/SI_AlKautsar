<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kajian;

class KajianController extends Controller
{
    public function index()
    {
        return view('kajian');
    }

    public function data(Request $request)
    {
        $query = Kajian::orderBy('created_at', 'DESC');

        if ($request->jenis && $request->jenis !== 'semua') {
            $query->where('jenis_kajian', $request->jenis);
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'foto_penceramah' => 'nullable|mimes:jpg,jpeg,png|max:2048',
            'jenis_kajian' => 'required',
            'nama_penceramah' => 'required',
            'tema_kajian' => 'required',
            'tanggal_kajian' => 'required',
            'waktu_kajian' => 'required',
        ]);

        // Upload foto
        if ($request->hasFile('foto_penceramah')) {
            $foto = time().'.'.$request->foto_penceramah->extension();
            $request->foto_penceramah->move(public_path('uploads/kajian'), $foto);
        } else {
            $foto = $request->old_foto ?? null;
        }

        Kajian::updateOrCreate(
            ['id_kajian' => $request->id_kajian],
            [
                'foto' => $foto,
                'jenis_kajian' => $request->jenis_kajian,
                'nama_penceramah' => $request->nama_penceramah,
                'tema_kajian' => $request->tema_kajian,
                'tanggal_kajian' => $request->tanggal_kajian,
                'waktu_kajian' => $request->waktu_kajian,
            ]
        );

        return response()->json(['status' => true]);
    }

    public function delete($id)
    {
        Kajian::where('id_kajian', $id)->delete();
        return response()->json(['status' => true]);
    }
}
