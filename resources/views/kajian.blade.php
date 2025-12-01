@extends('layouts.app')

@section('title', 'Kajian')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container-fluid p-4">

    {{-- Header: Filter, Search, Tombol --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        
        <div class="d-flex flex-wrap align-items-center">
            {{-- Filter Status --}}
            <div class="me-2 mb-2 mb-md-0">
                <select class="form-select" id="statusFilter" style="width: 150px;">
                    <option value="aktif" selected>Status: Aktif</option>
                    <option value="tidak_aktif">Status: Lewat</option>
                    <option value="semua">Semua Status</option>
                </select>
            </div>

            {{-- 1. FILTER TIPE BARU --}}
            <div class="me-2 mb-2 mb-md-0">
                <select class="form-select" id="tipeFilter" style="width: 150px;">
                    <option value="" selected>Semua Tipe</option>
                    <option value="rutin">Kajian Rutin</option>
                    <option value="event">Event Besar</option>
                </select>
            </div>

            {{-- Search Bar --}}
            <div class="input-group search-bar me-2 mb-2 mb-md-0" style="width: 300px;">
                <span class="input-group-text bg-white border-end-0">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" id="searchInput" class="form-control border-start-0" placeholder="Cari penceramah / tema...">
            </div>
        </div>
        
        {{-- Tombol Tambah --}}
        <div class="d-flex align-items-center mt-2 mt-md-0">
            <button class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#modalKajian">
                <i class="bi bi-plus-circle me-2"></i>
                Tambah Kajian
            </button>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center p-3 bg-white rounded shadow-sm mb-4">
        <h5 class="fw-bold mb-0">Data Kajian</h5>
    </div>

    {{-- Wrapper Tabel --}}
    <div class="card transaction-table border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="tabelKajian">
                    <thead class="table-light">
                        <tr> 
                            <th scope="col" style="width: 5%;" class="text-center">No</th>
                            <th scope="col" style="width: 8%;" class="text-center">Foto</th>
                            {{-- 2. KOLOM TIPE BARU --}}
                            <th scope="col" style="width: 10%;" class="text-center">Tipe</th>
                            <th scope="col">Nama Penceramah</th>
                            <th scope="col">Tema Kajian</th>
                            <th scope="col" class="text-center">Waktu</th>
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

<div class="modal fade" id="modalKajian" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            
            <div class="modal-header">
                <div>
                    <h5 class="modal-title">Jadwal Kajian</h5>
                    <p class="text-muted mb-0 small" style="line-height: 1;">Kelola agenda kajian & event besar</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="formKajian" enctype="multipart/form-data" style="display: flex; flex-direction: column; flex-grow: 1; overflow: hidden;">
                
                <input type="hidden" id="id_kajian" name="id_kajian">

                <div class="modal-body">
                    <div class="form-wrapper">
                        <div class="form-section-title">
                            Detail Acara
                        </div>

                        <div class="mb-3">
                            <label for="foto_penceramah" class="form-label">Flyer / Foto Penceramah (Max 2 Mb)</label>
                            <input type="file" class="d-none" id="foto_penceramah" name="foto_penceramah" accept="image/*">
                            
                            <div class="position-relative custom-file-wrapper mb-2">
                                <label for="foto_penceramah" id="foto_penceramah_label" class="form-control d-block text-truncate border cursor-pointer m-0" style="cursor: pointer;">
                                    <span class="text-muted"><i class="bi bi-cloud-upload me-2"></i>Pilih gambar...</span>
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
                            <label for="tipe" class="form-label">Tipe Kajian <span class="text-danger">*</span></label>
                            <select class="form-select" id="tipe" name="tipe" required>
                                <option value="rutin">Kajian Rutin</option>
                                <option value="event">Event Besar</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="nama_penceramah" class="form-label">Nama Penceramah <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama_penceramah" name="nama_penceramah" placeholder="Contoh: Ust. Adi Hidayat" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="tema_kajian" class="form-label">Tema Kajian <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="tema_kajian" name="tema_kajian" placeholder="Judul materi kajian" required>
                        </div>

                        <div class="row g-3">
                            <div class="col-7">
                                <label for="tanggal_kajian" class="form-label">Tanggal <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="tanggal_kajian" name="tanggal_kajian" required>
                            </div>
                            <div class="col-5">
                                <label for="waktu_kajian" class="form-label">Pukul (WIB)</label>
                                <input type="time" class="form-control" id="waktu_kajian" name="waktu_kajian">
                            </div>
                        </div>

                        <div>
                            <button type="submit" class="btn-action-primary shadow-sm">
                                Simpan Agenda <i class="bi bi-calendar-check"></i>
                            </button>
                        </div>

                    </div> </div>
            </form>
        </div>
    </div>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap');

    #modalKajian {
        font-family: 'Poppins', sans-serif;
    }

    /* Modal Content: Flex Column agar header diam */
    #modalKajian .modal-content {
        border-radius: 20px;
        border: none;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        height: 90vh; /* Tinggi fix */
        display: flex;
        flex-direction: column;
        overflow: hidden; 
    }

    /* Header Sticky & Tidak Mengecil */
    #modalKajian .modal-header {
        border-bottom: 1px solid #f0f0f0;
        padding: 15px 25px;
        background: white;
        z-index: 10;
        flex-shrink: 0; 
    }

    #modalKajian .modal-title {
        font-weight: 700;
        font-size: 1.3rem;
        color: #333;
    }

    /* Body Scrollable */
    #modalKajian .modal-body {
        overflow-y: auto;
        padding: 20px 25px;
        flex-grow: 1;
    }

    /* Wrapper Hijau (Style Donasi) */
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

    /* Inputs */
    #modalKajian .form-label {
        font-weight: 600;
        font-size: 0.9rem;
        color: #374151;
        margin-bottom: 8px;
    }

    #modalKajian .form-control, #modalKajian .form-select {
        border-radius: 12px;
        padding: 12px 15px;
        border: 1px solid #e5e7eb;
        font-size: 0.95rem;
    }

    #modalKajian .form-control:focus, #modalKajian .form-select:focus {
        border-color: #22c55e;
        box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.1);
    }

    /* Custom File Input */
    .custom-file-wrapper label {
        border-radius: 12px !important;
        padding: 12px 15px;
        background: white;
    }

    /* Preview Image Besar */
    #previewFoto {
        width: 100%;
        height: auto;
        max-height: 350px;
        object-fit: cover;
        border-radius: 12px;
        display: block;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    /* Tombol Simpan Full Width */
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
    
    /* Pagination (Bawaan) */
    #paginationLinks .pagination { margin-bottom: 0; }
    #paginationLinks .page-item.disabled .page-link { background-color: #e9ecef; }
    #paginationLinks .page-item.active .page-link { background-color: #0d6efd; border-color: #0d6efd; }
    #paginationLinks .page-link { cursor: pointer; }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/kajian.js') }}"></script>
@endsection