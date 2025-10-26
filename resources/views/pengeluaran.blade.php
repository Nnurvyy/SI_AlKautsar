@extends('layouts.app')

@section('title', 'Pengeluaran')

@section('content')

@php
    $transaksi = [
        (object)[
            'tanggal' => '23/10/2025',
            'kategori' => 'Pendidikan',
            'divisi' => 'Pendidikan',
            'vendor' => '-',
            'deskripsi' => 'Anggaran Pendidikan',
            'jumlah' => 1000000
        ],
    ];
    $totalPengeluaran = 1000000; // Sesuai gambar
@endphp

<div class="container-fluid p-4">

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        
        <div class="d-flex align-items-center flex-wrap">
            <div class="input-group search-bar me-2" style="width: 300px;">
                <span class="input-group-text bg-white border-end-0">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" class="form-control border-start-0" placeholder="Cari transaksi...">
            </div>

            <select class="form-select me-2" style="width: auto;">
                <option selected>Semua Kategori</option>
                <option value="1">Pendidikan</option>
                <option value="2">Listrik</option>
            </select>

            <select class="form-select" style="width: auto;">
                <option selected>Semua Divisi</option>
                <option value="1">Pendidikan</option>
            </select>
        </div>
        
        <div class="d-flex align-items-center mt-2 mt-md-0">
            <button type_button" class="btn btn-outline-secondary btn-custom-padding d-flex align-items-center me-2" data-bs-toggle="modal" data-bs-target="#modalKelolaKategori">
                <i class="bi bi-tags me-2"></i>
                Kelola Kategori
            </button>
            
            <a href="#" class="btn btn-danger btn-custom-padding d-flex align-items-center"> <i class="bi bi-plus-circle me-2"></i>
                Tambah Pengeluaran </a>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center p-3 bg-white rounded shadow-sm mb-4">
        <h5 class="fw-bold mb-0">Total Pengeluaran</h5>
        <h5 class="fw-bold mb-0 text-custom-red">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</h5>
    </div>


    <div class="card transaction-table border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    
                    <thead class="table-light">
                        <tr>
                            <th scope="col">Tanggal</th>
                            <th scope="col">Kategori</th>
                            <th scope="col">Divisi</th>
                            <th scope="col">Vendor</th>
                            <th scope="col">Deskripsi</th>
                            <th scope="col" class="text-end">Jumlah</th>
                            <th scope="col" class="text-center" style="width: 10%;">Aksi</th>
                        </tr>
                    </thead>
                    
                    <tbody>
                        @foreach ($transaksi as $item)
                        <tr>
                            <td>{{ $item->tanggal }}</td>
                            <td>{{ $item->kategori }}</td>
                            <td>{{ $item->divisi }}</td>
                            <td>{{ $item->vendor }}</td>
                            <td>{{ $item->deskripsi }}</td>
                            <td class="text-end text-custom-red fw-bold">- Rp {{ number_format($item->jumlah, 0, ',', '.') }}</td>
                            <td class="text-center">
                                <a href="#" class="btn btn-sm me-1" title="Edit">
                                    <i class="bi bi-pencil text-primary fs-6"></i> </a>
                                <a href="#" class="btn btn-sm" title="Hapus">
                                    <i class="bi bi-trash text-danger fs-6"></i> </a>
                            </td>
                        </tr>
                        @endforeach
                        
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection