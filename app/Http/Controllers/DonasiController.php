<?php
namespace App\Http\Controllers;
use App\Models\Donasi;
use App\Models\ProgramDonasi;
use Illuminate\Http\Request;

class DonasiController extends Controller
{
    public function detail($id)
    {
        $program = ProgramDonasi::findOrFail($id);
        return view('frontend.donasi.detail', compact('program'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'program_id' => 'required',
            'nama' => 'required',
            'nominal' => 'required|numeric|min:1000',
        ]);

        Donasi::create([
            'program_id' => $request->program_id,
            'nama' => $request->nama,
            'nominal' => $request->nominal,
            'metode' => $request->metode,
            'pesan' => $request->pesan,
        ]);

        return redirect()->route('donasi.sukses');
    }

    public function sukses()
    {
        return view('frontend.donasi.sukses');
    }
}
