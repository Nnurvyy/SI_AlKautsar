<?php

namespace App\Http\Controllers;

use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ProgramController extends Controller
{
    public function index()
    {

        $totalProgram = Program::count();


        return view('program', compact('totalProgram'));
    }

    public function data(Request $request)
    {

        $status = $request->query('status', 'aktif');
        $search = $request->query('search', '');
        $perPage = $request->query('perPage', 10);
        $sortBy = $request->query('sortBy', 'tanggal_program');
        $sortDir = $request->query('sortDir', 'desc');

        $query = Program::query();


        if ($status === 'aktif') {
            $query->where('tanggal_program', '>=', Carbon::now());
        } elseif ($status === 'tidak_aktif') {
            $query->where('tanggal_program', '<', Carbon::now());
        }


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


        $allowedSorts = ['tanggal_program', 'nama_program', 'penyelenggara_program'];
        if (!in_array($sortBy, $allowedSorts)) $sortBy = 'tanggal_program';
        $sortDir = $sortDir === 'asc' ? 'asc' : 'desc';

        $data = $query->orderBy($sortBy, $sortDir)->paginate($perPage);

        return response()->json($data);
    }


    public function store(Request $request)
    {

        $rules = [
            'nama_program' => 'required|string|max:255',
            'penyelenggara_program' => 'required|string|max:150',
            'lokasi_program' => 'required|string|max:255',
            'tanggal_program' => 'required|date_format:Y-m-d\TH:i',
            'deskripsi_program' => 'required|string',
            'foto_program' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',

            'status_program' => 'nullable|string|max:50',
        ];


        $data = $request->validate($rules);







        if ($request->hasFile('foto_program')) {
            $data['foto_program'] = $request->file('foto_program')->store('program', 'public');
        }

        Program::create($data);

        return response()->json(['success' => true, 'message' => 'Program berhasil ditambahkan.']);
    }

    public function show(Program $program)
    {
        return response()->json($program);
    }

    public function update(Request $request, Program $program)
    {

        $rules = [
            'nama_program' => 'required|string|max:255',
            'penyelenggara_program' => 'required|string|max:150',
            'lokasi_program' => 'required|string|max:255',
            'tanggal_program' => 'required|date_format:Y-m-d\TH:i',
            'deskripsi_program' => 'required|string',
            'status_program' => 'nullable|string|max:50',

            'foto_program' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];


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

    public function destroy(Program $program)
    {
        if ($program->foto_program && Storage::disk('public')->exists($program->foto_program)) {
            Storage::disk('public')->delete($program->foto_program);
        }

        $program->delete();

        return response()->json(['success' => true, 'message' => 'Data program dihapus.']);
    }
}
