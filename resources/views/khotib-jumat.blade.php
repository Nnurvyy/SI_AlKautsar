@extends('layouts.app')

@section('title', 'Khutbah Jumat')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container-fluid p-4">

    {{-- Header: Filter, Search, Tombol --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        
        <div class="d-flex flex-wrap align-items-center">
            <div class="me-2 mb-2 mb-md-0">
                <select class="form-select" id="statusFilter" style="width: 150px;">
                    <option value="aktif" selected>Aktif</option>
                    <option value="tidak_aktif">Tidak Aktif</option>
                    <option value="semua">Semua</option>
                </select>
            </div>

            {{-- Search Bar --}}
            <div class="input-group search-bar me-2 mb-2 mb-md-0" style="width: 350px;">
                <span class="input-group-text bg-white border-end-0">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" id="searchInput" class="form-control border-start-0" placeholder="Cari khotib, imam, tema, atau tanggal...">
            </div>
        </div>
        
        {{-- Tombol Tambah Khotib --}}
        <div class="d-flex align-items-center mt-2 mt-md-0">
            <button class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#modalKhotib">
                <i class="bi bi-plus-circle me-2"></i>
                Tambah Khutbah
            </button>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center p-3 bg-white rounded shadow-sm mb-4">
        <h5 class="fw-bold mb-0">Data Khutbah Jumat</h5>
    </div>

    {{-- Wrapper Tabel --}}
    <div class="card transaction-table border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="tabelKhotib">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" style="width: 5%;" class="text-center">No</th>
                            <th scope="col" style="width: 10%;" class="text-center">Foto</th>
                            <th scope="col">Nama Khotib</th>
                            <th scope="col">Nama Imam</th>
                            <th scope="col">Tema Khutbah</th>
                            <th scope="col" class="text-center" id="sortTanggal" style="cursor:pointer;">
                                Tanggal <i id="sortIcon" class="bi bi-arrow-down"></i>
                            </th>
                            <th scope="col" style="width: 8%;" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Data dimuat lewat JS --}}
                    </tbody>
                </table>
            </div>
            
            <div id="paginationContainer" class="d-flex justify-content-between align-items-center mt-3">
                <span id="paginationInfo">Menampilkan 0 dari 0 data</span>
                <nav id="paginationLinks"></nav>
            </div>
            </div>
    </div>
</div>

<!-- Modal Form -->
<div class="modal fade" id="modalKhotib" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            
            <div class="modal-header">
                <div>
                    <h5 class="modal-title">Jadwal Khutbah</h5>
                    <p class="text-muted mb-0 small" style="line-height: 1;">Kelola data petugas jumat</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="formKhotib" enctype="multipart/form-data" style="display: flex; flex-direction: column; flex-grow: 1; overflow: hidden;">
                <input type="hidden" id="id_khutbah" name="id_khutbah">

                <div class="modal-body">
                    <div class="form-wrapper">
                        <div class="form-section-title">
                            Isi Data Lengkap
                        </div>

                        <div class="mb-3">
                            <label for="foto_khotib" class="form-label">Foto Khotib (Max 2 Mb)</label>
                            <input type="file" class="d-none" id="foto_khotib" name="foto_khotib" accept="image/*">
                            
                            <div class="position-relative custom-file-wrapper mb-2">
                                <label for="foto_khotib" id="foto_khotib_label" class="form-control d-block text-truncate border cursor-pointer m-0" style="cursor: pointer;">
                                    <span class="text-muted"><i class="bi bi-cloud-upload me-2"></i>Pilih foto...</span>
                                </label>
                                <button type="button" class="btn position-absolute d-none" id="clearFile" title="Hapus foto" 
                                        style="top: 50%; right: 0.5rem; transform: translateY(-50%); z-index: 5; color: #dc3545; background: transparent; border: 0;">
                                    <i class="bi bi-x-circle-fill fs-5"></i>
                                </button>
                            </div>
                            
                            <div id="previewContainer" class="position-relative d-none mt-3">
                                <img id="previewFoto" class="img-fluid" alt="Preview">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="nama_khotib" class="form-label">Nama Khotib <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama_khotib" name="nama_khotib" placeholder="Contoh: Ust. Fulan">
                        </div>

                        <div class="mb-3">
                            <label for="nama_imam" class="form-label">Nama Imam <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama_imam" name="nama_imam" placeholder="Nama Imam Sholat">
                        </div>

                        <div class="mb-3">
                            <label for="tema_khutbah" class="form-label">Tema Khutbah <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="tema_khutbah" name="tema_khutbah" placeholder="Judul/Tema materi">
                        </div>    

                        <div class="mb-3">
                            <label for="tanggal" class="form-label">Tanggal Pelaksanaan <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="tanggal" name="tanggal">
                        </div>

                        <div>
                            <button type="submit" class="btn-action-primary shadow-sm">
                                Simpan Data <i class="bi bi-arrow-right"></i>
                            </button>
                        </div>
                    </div> 
                    </div>
            </form>
        </div>
    </div>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap');

    #modalKhotib {
        font-family: 'Poppins', sans-serif;
    }

    /* Modal Content: Menggunakan Flex Column agar header tetap diam saat body discroll */
    #modalKhotib .modal-content {
        border-radius: 20px;
        border: none;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        height: 90vh; /* Tinggi fix agar scroll main di dalam */
        display: flex;
        flex-direction: column;
        overflow: hidden; /* Mencegah scroll ganda */
    }

    /* PERBAIKAN: Header dibuat Sticky & Tidak Boleh Mengecil */
    #modalKhotib .modal-header {
        border-bottom: 1px solid #f0f0f0;
        padding: 15px 25px;
        background: white;
        z-index: 10;
        flex-shrink: 0; /* PENTING: Mencegah header hilang/tergencet saat konten penuh */
    }

    #modalKhotib .modal-title {
        font-weight: 700;
        font-size: 1.3rem;
        color: #333;
    }

    /* PERBAIKAN: Body dibuat scrollable */
    #modalKhotib .modal-body {
        overflow-y: auto;
        padding: 20px 25px;
        flex-grow: 1; /* Mengisi sisa ruang */
    }

    /* Wrapper Hijau */
    .form-wrapper {
        background-color: #f0fdf4;
        border: 1px solid #dcfce7;
        border-radius: 15px;
        padding: 20px;
    }

    .form-section-title {
        color: #166534;
        font-weight: 700;
        margin-bottom: 15px;
        font-size: 1.1rem;
    }

    /* Form Inputs */
    #modalKhotib .form-label {
        font-weight: 600;
        font-size: 0.9rem;
        color: #374151;
        margin-bottom: 8px;
    }

    #modalKhotib .form-control {
        border-radius: 12px;
        padding: 12px 15px;
        border: 1px solid #e5e7eb;
        font-size: 0.95rem;
    }

    #modalKhotib .form-control:focus {
        border-color: #22c55e;
        box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.1);
    }

    /* Custom File Input */
    .custom-file-wrapper label {
        border-radius: 12px !important;
        padding: 12px 15px;
        background: white;
    }

    /* PERBAIKAN: Preview Image Lebih Besar */
    #previewFoto {
        width: 100%;          /* Lebar penuh mengikuti container */
        height: auto;         /* Tinggi menyesuaikan rasio */
        max-height: 350px;    /* Batas tinggi agar tidak kepanjangan */
        object-fit: cover;    /* Gambar di-crop rapi */
        border-radius: 12px;
        display: block;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    /* Tombol Simpan */
    .btn-action-primary {
        background-color: #198754;
        color: white;
        border: none;
        border-radius: 12px;
        padding: 14px;
        width: 100%;
        font-weight: 700;
        font-size: 1rem;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 8px;
        margin-top: 20px;
    }
    
    .btn-action-primary:hover {
        background-color: #157347;
        color: white;
    }

    /* Pagination (Tetap) */
    #paginationLinks .pagination { margin-bottom: 0; }
    #paginationLinks .page-item.disabled .page-link { background-color: #e9ecef; }
    #paginationLinks .page-item.active .page-link { background-color: #0d6efd; border-color: #0d6efd; }
    #paginationLinks .page-link { cursor: pointer; }
</style>


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/khotib.js') }}"></script>
@endsection
