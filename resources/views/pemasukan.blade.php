@extends('layouts.app')

@section('title', 'Pemasukan')

@section('content')

@php
    // Data dummy transaksi pemasukan
    $transaksi = [
        (object)[
            'tanggal' => '22/10/2025',
            'kategori' => 'Donasi',
            'divisi' => 'Administrasi',
            'santri' => '-',
            'deskripsi' => 'Donasi Umum dari Donatur',
            'metode_pembayaran' => 'Tunai',
            'jumlah' => 1000000
        ],
        (object)[
            'tanggal' => '22/10/2025',
            'kategori' => 'SPP',
            'divisi' => 'Putra',
            'santri' => 'Panjei (241511019)',
            'deskripsi' => 'SPP Santri Bulan Oktober',
            'metode_pembayaran' => 'Transfer',
            'jumlah' => 1000000
        ],
        (object)[
            'tanggal' => '23/10/2025',
            'kategori' => 'Infaq',
            'divisi' => 'Administrasi',
            'santri' => '-',
            'deskripsi' => 'Pemasukan Infaq Jumat',
            'metode_pembayaran' => 'Tunai',
            'jumlah' => 100000
        ],
    ];

    $totalPemasukan = collect($transaksi)->sum('jumlah');
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
                <option value="1">Donasi</option>
                <option value="2">SPP</option>
                <option value="3">Infaq</option>
            </select>

            <select class="form-select" style="width: auto;">
                <option selected>Semua Divisi</option>
                <option value="1">Administrasi</option>
                <option value="2">Putra</option>
            </select>
        </div>
        
        <div class="d-flex align-items-center mt-2 mt-md-0">
            <button type="button" class="btn btn-outline-secondary btn-custom-padding d-flex align-items-center me-2" data-bs-toggle="modal" data-bs-target="#modalKelolaKategori">
                <i class="bi bi-tags me-2"></i>
                Kelola Kategori
            </button>
            
            <a href="#" class="btn btn-success btn-custom-padding d-flex align-items-center">
                <i class="bi bi-plus-circle me-2"></i>
                Tambah Pemasukan
            </a>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center p-3 bg-white rounded shadow-sm mb-4">
        <h5 class="fw-bold mb-0">Total Pemasukan</h5>
        <h5 class="fw-bold mb-0 text-custom-green">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</h5>
    </div>

    <div class="card transaction-table border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    
                    <thead class="table-light">
                        <tr>
                            <th scope="col" style="width: 10%;">Tanggal</th>
                            <th scope="col" style="width: 12%;">Kategori</th>
                            <th scope="col" style="width: 12%;">Divisi</th>
                            <th scope="col" style="width: 15%;">Santri</th>
                            <th scope="col">Deskripsi</th> 
                            <th scope="col" style="width: 13%;">Metode</th>
                            <th scope="col" style="width: 13%;" class="text-end">Jumlah</th>
                            <th scope="col" style="width: 8%;" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    
                    <tbody>
                        @foreach ($transaksi as $item)
                        <tr>
                            {{-- DIUBAH: ditambahkan class col-nowrap --}}
                            <td class="col-nowrap">{{ $item->tanggal }}</td>
                            <td>{{ $item->kategori }}</td>
                            <td>{{ $item->divisi }}</td>
                            <td>{{ $item->santri }}</td>
                            <td>{{ $item->deskripsi }}</td>
                            <td>{{ $item->metode_pembayaran }}</td>
                            
                            {{-- DIUBAH: ditambahkan class col-nowrap --}}
                            <td class="text-end text-custom-green fw-bold col-nowrap">
                                Rp {{ number_format($item->jumlah, 0, ',', '.') }}
                            </td>

                            {{-- DIUBAH: ditambahkan class col-nowrap --}}
                            <td class="text-center col-nowrap">
                                <a href="#" class="btn btn-sm me-1" title="Edit">
                                    {{-- DIUBAH: fs-5 menjadi fs-6 --}}
                                    <i class="bi bi-pencil text-primary fs-6"></i>
                                </a>
                                <a href="#" class="btn btn-sm" title="Hapus">
                                    {{-- DIUBAH: fs-5 menjadi fs-6 --}}
                                    <i class="bi bi-trash text-danger fs-6"></i>
                                </a>
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