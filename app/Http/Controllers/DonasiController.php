<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProgramDonasi;
use App\Models\Donasi;

class DonasiController extends Controller
{
    // HALAMAN DETAIL PROGRAM
    public function detail($id)
    {
        $program = ProgramDonasi::findOrFail($id);
        return view('public.donasi_detail', compact('program'));
    }

    // PROSES INPUT DONASI
    public function store(Request $request)
    {
        $request->validate([
            'program_id' => 'required|uuid|exists:program_donasi,id',
            'nama_donatur' => 'required',
            'jumlah' => 'required|numeric|min:1000',
        ]);

        Donasi::create([
            'program_id' => $request->program_id,
            'nama_donatur' => $request->nama_donatur,
            'jumlah' => $request->jumlah,
        ]);

        // update dana terkumpul
        $program = ProgramDonasi::find($request->program_id);
        $program->dana_terkumpul += $request->jumlah;
        $program->save();

        return redirect()->route('donasi.sukses');
    }

    // HALAMAN SUKSES
    public function sukses()
    {
        return view('public.donasi_sukses');
    }
}
