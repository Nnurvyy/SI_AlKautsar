@extends('layouts.app')

<!-- 1. Judul Halaman diubah -->
@section('title', 'Manajemen Data Santri')

@section('content')

<!-- 2. Data Dummy diubah menjadi data transaksi -->
@php
    $data = [
        (object)[
            'nis' => '132948291',
            'nama_santri' => 'papang rahmawan',
            'kelas' => '7A',
            'no_telp' => '081234567890',
            'wali_santri' => 'Budi',
            'status' => true
        ],
        (object)[
            'nis' => '302817245',
            'nama_santri' => 'kipli kurniawan',
            'kelas' => '8B',
            'no_telp' => '082233445566',
            'wali_santri' => 'Siti',
            'status' => false
        ],
        (object)[
            'nis' => '312132123',
            'nama_santri' => 'lathif abdul abdel',
            'kelas' => '9C',
            'no_telp' => '083344556677',
            'wali_santri' => 'Slamet',
            'status' => true
        ],
    ];
@endphp

<div class="container-fluid p-4">

    <!-- 3. Header Atas diubah (Search, Filter, Tombol) -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        
        <!-- Search Bar & Filter -->
        <div class="d-flex align-items-center flex-wrap">
            <!-- Search Bar -->
            <div class="input-group search-bar me-2" style="width: 300px;">
                <span class="input-group-text bg-white border-end-0">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" class="form-control border-start-0" placeholder="Cari Santri (nama atau nis)...">
            </div>
        </div>
        <!--Tombol Aksi -->
        <div class="d-flex align-items-center mt-2 mt-md-0">
            <!-- Tombol Tambah Pemasukan (BARU) -->
            <a href="#" class="btn btn-primary btn-custom-padding d-flex align-items-center"> <!-- Warna diubah ke btn-success -->
                <i class="bi bi-plus-circle me-2"></i> <!--Ikon diubah-->
                Tambah Santri <!-- Teks diubah -->
            </a>
        </div>
    </div>

    <!-- 5. Tabel Transaksi -->
    <div class="card transaction-table border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    
                    <!-- 6. Header Tabel diubah -->
                    <thead class="table-light">
                        <tr>
                            <th scope="col">NIS</th>
                            <th scope="col">Nama</th>
                            <th scope="col">Kelas</th>
                            <th scope="col">No.Telepon</th>
                            <th scope="col">Wali</th>
                            <th scope="col">Status</th>
                            <th scope="col">Aksi</th>
                        </tr>
                    </thead>
                    
                    <!-- 7. Isi Tabel diubah -->
                    <tbody>
                        @foreach ($data as $item)
                        <tr>
                            <td>{{ $item->nis}}</td>
                            <td>{{ $item->nama_santri}}</td>
                            <td>{{ $item->kelas}}</td>
                            <td>{{ $item->no_telp}}</td>
                            <td>{{ $item->wali_santri}}</td>
                            <td class="text-left">
                                @if ($item->status)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-danger">Nonaktif</span>
                                @endif
                            </td>
                            
                            <td class="text-left">
                                <a href="#" class="btn btn-sm me-1" title="Edit">
                                    <i class="bi bi-pencil text-primary fs-5"></i> <!-- fs-5 dari request sebelumnya -->
                                </a>
                                <a href="#" class="btn btn-sm" title="Hapus">
                                    <i class="bi bi-trash text-danger fs-5"></i> <!-- fs-5 dari request sebelumnya -->
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

<!-- 
    Modal untuk Kelola Kategori akan ditaruh di sini nanti.
    <div class="modal fade" id="modalKelolaKategori" ...>
    ...
    </div> 
-->

@endsection