@extends('layouts.app')

<!-- 1. Judul Halaman diubah -->
@section('title', 'Manajemen Divisi')

@section('content')

<!-- 2. Data Dummy diubah menjadi data transaksi -->
@php
    $division = [
        (object)[
            'nama_divisi' => 'Kepesantrenan',
            'deskripsi_divisi' => 'ya pokoknya ngurus pesantren si dia teh',
            'is_aktif' => true
        ],
        (object)[
            'nama_divisi' => 'Putra',
            'deskripsi_divisi' => 'ngurus yang putra',
            'is_aktif' => false
        ],
        (object)[
            'nama_divisi' => 'Putri',
            'deskripsi_divisi' => 'yang ini ngurus yang putri',
            'is_aktif' => true
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
                <input type="text" class="form-control border-start-0" placeholder="Cari Divisi...">
            </div>
        </div>
        <!--Tombol Aksi -->
        <div class="d-flex align-items-center mt-2 mt-md-0">
            <a href="#" 
            class="btn btn-primary btn-custom-padding d-flex align-items-center"
            data-bs-toggle="modal"
            data-bs-target="#modalTambahDivisi">
                <i class="bi bi-plus-circle me-2"></i>
                Tambah Divisi
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
                            <th scope="col" style="width: 25%">Divisi</th>
                            <th scope="col" style="width: 45%">Deskripsi</th>
                            <th scope="col" style="width: 15%">Status</th>
                            <th scope="col" style="width: 15%">Aksi</th>
                        </tr>
                    </thead>
                    
                    <!-- 7. Isi Tabel diubah -->
                    <tbody>
                        @foreach ($division as $item)
                        <tr>
                            <td>{{ $item->nama_divisi }}</td>
                            <td>{{ $item->deskripsi_divisi }}</td>
                            <td class="text-left">
                                @if ($item->is_aktif)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-danger">Nonaktif</span>
                                @endif
                            </td>
                            
                            <td class="text-center col-nowrap">
                                <!-- Tombol Edit -->
                                <a href="#" 
                                class="btn btn-sm me-1" 
                                title="Edit"
                                data-bs-toggle="modal" 
                                data-bs-target="#modalEditDivisi">
                                    <i class="bi bi-pencil text-primary fs-6"></i>
                                </a>

                                <!-- Tombol Hapus -->
                                <a href="#" 
                                class="btn btn-sm" 
                                title="Hapus"
                                data-bs-toggle="modal" 
                                data-bs-target="#modalHapusDivisi">
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


<!-- modal untuk tambah divisi -->
<div class="modal fade" id="modalTambahDivisi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Divisi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Isi form di sini...
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="submit" class="btn btn-primary">Tambah</button>
            </div>
        </div>
    </div>
</div>

<!-- modal untuk edit divisi -->
<div class="modal fade" id="modalEditDivisi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Divisi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Isi form di sini...
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
</div>

<!-- modal untuk hapus divisi -->
<div class="modal fade" id="modalHapusDivisi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Hapus Divisi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Isi form di sini...
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-danger">Hapus</button>
            </div>
        </div>
    </div>
</div>

@endsection