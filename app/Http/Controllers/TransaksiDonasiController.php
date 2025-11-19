<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Donasi;
use App\Models\ProgramDonasi;
use App\Models\Pemasukan;
use App\Models\KategoriPemasukan;
use Illuminate\Support\Facades\DB;

class TransaksiDonasiController extends Controller
{
    public function index()
    {
        // Ambil data donasi beserta nama programnya
        $transaksi = Donasi::with('program')->latest('tanggal_donasi')->paginate(10);
        
        // Ambil list program untuk dropdown di modal tambah
        $program = ProgramDonasi::all();

        return view('donasi.index', compact('transaksi', 'program'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_program_donasi' => 'required|exists:program_donasi,id_program_donasi', // Sesuaikan nama PK program
            'nama_donatur' => 'required|string',
            'nominal' => 'required',
            'tanggal_donasi' => 'required|date',
        ]);

        // Bersihkan format Rupiah (hapus titik/koma)
        $nominalBersih = str_replace(['.', ','], '', $request->nominal);

        DB::beginTransaction(); // Pakai transaction biar aman (kalau satu gagal, semua batal)
        try {
            // 1. SIMPAN KE TABEL DONASI (Untuk Data Rincian)
            $donasi = Donasi::create([
                'id_program_donasi' => $request->id_program_donasi,
                'nama_donatur' => $request->nama_donatur,
                'nominal' => $nominalBersih,
                'tanggal_donasi' => $request->tanggal_donasi,
                'keterangan' => $request->keterangan,
                'metode_pembayaran' => 'Tunai' // Default atau ambil dari input
            ]);

            // 2. OTOMATIS SIMPAN KE TABEL PEMASUKAN (Biar muncul di menu Pemasukan)
            // Cari dulu ID Kategori untuk "Donasi"
            $kategoriDonasi = KategoriPemasukan::where('nama_kategori_pemasukan', 'ilike', '%Donasi%')->first();
            
            // Kalau gak ada kategori Donasi, buat baru atau pakai ID 1 (sesuaikan)
            $idKategori = $kategoriDonasi ? $kategoriDonasi->id_kategori_pemasukan : 1;

            // Ambil Nama Program buat deskripsi pemasukan
            $namaProgram = ProgramDonasi::find($request->id_program_donasi)->judul ?? 'Program Donasi';

            Pemasukan::create([
                'id_kategori_pemasukan' => $idKategori,
                'nominal' => $nominalBersih,
                'tanggal' => $request->tanggal_donasi,
                'deskripsi' => "Donasi dari {$request->nama_donatur} untuk {$namaProgram}",
            ]);

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Donasi berhasil diterima & masuk ke Pemasukan.']);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => 'Gagal menyimpan: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $donasi = Donasi::findOrFail($id);
        $donasi->delete();
        // Catatan: Idealnya data di Pemasukan juga dihapus, tapi untuk sekarang hapus donasinya saja dulu.
        return back()->with('success', 'Data donasi dihapus.');
    }
}