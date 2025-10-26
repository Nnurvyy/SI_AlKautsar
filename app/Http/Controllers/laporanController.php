<?php

namespace App\Http\Controllers;

use App\Models\Pengeluaran;
use Illuminate\Http\Request;
use App\Models\Pemasukan;
use App\Models\PemasukanKategori;
class laporanController extends Controller{
    public function index(){

        $pemasukan = Pemasukan::with(['kategori', 'divisi'])->get()->map(function($item){
            $item->tipe = 'Pemasukan'; //beri label pemasukan
            $item->jumlah = $item->nominal;//pemasukan di set positif
            return $item;
        });



        $pengeluaran = Pengeluaran::with(['kategori', 'divisi'])->get()->map(function($item){
            $item->tipe = 'Pengeluaran'; //beri label pengeluaran
            $item->jumlah = - $item->nominal;//pengeluaran di set negatif
            return $item;
        });

        $totalPemasukan = $pemasukan->sum('nominal');
        $totalPengeluaran = $pengeluaran->sum('nominal');
        $totalSaldo = $totalPemasukan - $totalPengeluaran;

        $transaksi = $pemasukan->merge($pengeluaran)->sortByDesc('tanggal_transaksi');

        return view('laporan.index', [
            'totalPemasukan' => $totalPemasukan,
            'totalPengeluaran' => $totalPengeluaran,
            'totalSaldo' => $totalSaldo,
            'transaksi' => $transaksi
        ]);
    }

}


