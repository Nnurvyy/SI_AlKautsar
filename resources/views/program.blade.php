@extends('layouts.app')

@section('title', 'Program')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container-fluid p-4">

    {{-- HEADER & SEARCH --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div class="d-flex flex-wrap align-items-center gap-2">
            {{-- Filter Status --}}
            <select class="form-select rounded-pill ps-3" id="statusFilter" style="width: 170px; border-color: #e5e7eb;">
                <option value="all" selected>Semua Status</option>
                <option value="belum dilaksanakan">Belum Dilaksanakan</option>
                <option value="sedang berjalan">Sedang Berjalan</option>
                <option value="sudah dijalankan">Sudah Dijalankan</option>
            </select>

            {{-- Search Bar --}}
            <div class="input-group" style="width: 300px;">
                <span class="input-group-text bg-white border-end-0 rounded-start-pill ps-3"><i class="bi bi-search text-muted"></i></span>
                <input type="text" id="searchInput" class="form-control border-start-0 rounded-end-pill" placeholder="Cari nama program...">
            </div>
        </div>

        <button class="btn btn-gradient-green rounded-pill px-4 shadow-sm" id="btnTambahProgram">
            <i class="bi bi-plus-lg me-2"></i> Program Baru
        </button>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold text-dark mb-0">Total Program</h5>
                <small class="text-muted">Jumlah agenda kegiatan terdaftar</small>
            </div>
            {{-- ID ini (totalProgramHeader) untuk diisi via JS --}}
            <h3 class="fw-bold text-success mb-0" id="totalProgramHeader">
                {{-- Tampilkan jumlah program --}}
                {{ number_format($totalProgram, 0, ',', '.') }}
            </h3>
        </div>
    </div>

    {{-- TABEL UTAMA --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="tabelProgram">
                    <thead class="bg-light">
                        <tr style="height: 50px;">
                            <th class="text-center ps-4 rounded-top-left">No</th>
                            <th class="text-center">Foto</th>
                            <th>Nama Program</th>
                            <th class="text-center">Tanggal</th>
                            <th class="text-center">Lokasi</th>
                            <th class="text-center">Status</th>
                            <th class="text-center pe-4 rounded-top-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white"></tbody>
                </table>
            </div>
        </div>
        {{-- Pagination --}}
        <div class="card-footer bg-white border-0 py-3">
            <div id="paginationContainer" class="d-flex justify-content-between align-items-center">
                <span id="paginationInfo" class="text-muted small ms-2"></span>
                <nav id="paginationLinks" class="me-2"></nav>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================== --}}
{{-- MODAL 1: CREATE / EDIT PROGRAM (Style Public Donasi) --}}
{{-- ============================================================== --}}
<div class="modal fade" id="modalProgram" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg modal-rounded">
            
            {{-- Header Clean --}}
            <div class="modal-header border-0 pb-0 pt-4 px-4 bg-white">
                <div>
                    <h5 class="modal-title fw-bold text-dark" id="modalTitle">Program Kegiatan</h5>
                    <p class="text-muted small mb-0">Kelola agenda dan kegiatan masjid</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4 bg-white">
                <form id="formProgram" enctype="multipart/form-data">
                    <input type="hidden" id="id_program" name="id_program">

                    {{-- WRAPPER HIJAU (Style Public) --}}
                    <div class="donation-card-wrapper">
                        
                        {{-- Upload Foto --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold text-success small text-uppercase ls-1">Foto Kegiatan (Max 2 Mb)</label>
                            <input type="file" class="d-none" id="foto_program" name="foto_program" accept="image/*">
                            
                            {{-- 1. Tampilan Kosong --}}
                            <label for="foto_program" id="uploadPlaceholder" class="file-upload-box cursor-pointer">
                                <div class="text-center">
                                    <div class="icon-circle mb-2 mx-auto">
                                        <i class="bi bi-camera-fill text-success fs-5"></i>
                                    </div>
                                    <span class="text-muted small">Ketuk untuk upload</span>
                                </div>
                            </label>

                            {{-- 2. Tampilan Preview --}}
                            <div id="previewContainer" class="position-relative d-none">
                                <img id="previewFoto" class="img-fluid w-100 rounded-3 shadow-sm" style="max-height: 250px; object-fit: cover;">
                                <button type="button" id="btnHapusFoto" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2 rounded-circle shadow-sm" style="width: 30px; height: 30px; padding: 0;"><i class="bi bi-x"></i></button>
                                <label for="foto_program" class="btn btn-light btn-sm position-absolute bottom-0 end-0 m-2 rounded-pill shadow-sm small fw-bold"><i class="bi bi-pencil me-1"></i> Ubah</label>
                            </div>
                        </div>

                        {{-- Nama Program --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold text-success small text-uppercase ls-1">Nama Program</label>
                            <input type="text" class="form-control rounded-pill-input" name="nama_program" id="nama_program" placeholder="Contoh: Santunan Yatim" required>
                        </div>

                        {{-- Penyelenggara & Lokasi --}}
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label fw-bold text-success small text-uppercase ls-1">Penyelenggara</label>
                                <input type="text" class="form-control rounded-pill-input" name="penyelenggara_program" id="penyelenggara_program" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold text-success small text-uppercase ls-1">Lokasi</label>
                                <input type="text" class="form-control rounded-pill-input" name="lokasi_program" id="lokasi_program" required>
                            </div>
                        </div>

                        {{-- Tanggal & Status --}}
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label fw-bold text-success small text-uppercase ls-1">Waktu</label>
                                <input type="datetime-local" class="form-control rounded-pill-input" name="tanggal_program" id="tanggal_program" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold text-success small text-uppercase ls-1">Status</label>
                                <select class="form-select rounded-pill-input" id="status_program" name="status_program" required>
                                    <option value="belum dilaksanakan">Belum Dilaksanakan</option>
                                    <option value="sedang berjalan">Sedang Berjalan</option>
                                    <option value="sudah dijalankan">Sudah Dijalankan</option>
                                </select>
                            </div>
                        </div>

                        {{-- Deskripsi --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold text-success small text-uppercase ls-1">Deskripsi</label>
                            <textarea class="form-control rounded-box-input" name="deskripsi_program" id="deskripsi_program" rows="2" placeholder="Detail kegiatan..."></textarea>
                        </div>

                        {{-- Tombol --}}
                        <div class="d-grid">
                            <button type="submit" class="btn btn-gradient-green rounded-pill py-2 fw-bold shadow-sm">
                                <i class="bi bi-check-circle me-2"></i> Simpan Data
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================== --}}
{{-- MODAL 2: DETAIL PROGRAM (Clean Style) --}}
{{-- ============================================================== --}}
<div class="modal fade" id="modalDetailProgram" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg modal-rounded">
            
            <div class="modal-header border-0 pb-0 pt-4 px-4 bg-white">
                <div>
                    <h5 class="modal-title fw-bold text-dark">Detail Program</h5>
                    <p class="text-muted small mb-0">Informasi lengkap kegiatan</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-4">
                <div class="row g-4 align-items-start">
                    {{-- Foto --}}
                    <div class="col-md-5">
                        <img id="detailFotoProgram" src="" class="img-fluid rounded-4 shadow-sm w-100 object-fit-cover" style="min-height: 250px; max-height: 350px;">
                    </div>
                    
                    {{-- Info --}}
                    <div class="col-md-7">
                        <span id="d_status" class="badge rounded-pill px-3 py-2 mb-2"></span>
                        <h3 id="d_nama" class="fw-bold text-dark mb-3"></h3>
                        
                        <div class="d-flex flex-column gap-2 mb-4">
                            <div class="d-flex align-items-center text-muted">
                                <i class="bi bi-calendar-event me-2 text-success"></i>
                                <span id="d_tanggal" class="fw-medium"></span>
                            </div>
                            <div class="d-flex align-items-center text-muted">
                                <i class="bi bi-geo-alt me-2 text-danger"></i>
                                <span id="d_lokasi" class="fw-medium"></span>
                            </div>
                            <div class="d-flex align-items-center text-muted">
                                <i class="bi bi-person-badge me-2 text-primary"></i>
                                <span id="d_penyelenggara" class="fw-medium"></span>
                            </div>
                        </div>

                        <div class="p-3 bg-light rounded-3 border">
                            <small class="text-uppercase text-muted fw-bold ls-1 d-block mb-2">Deskripsi</small>
                            <p id="d_deskripsi" class="text-dark mb-0 small" style="line-height: 1.6;"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- CSS KHUSUS HALAMAN INI (Copy dari Donasi) --}}
<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap');

    /* Scoping Font Poppins */
    #tabelProgram, .card, .modal-content, .donation-card-wrapper { 
        font-family: 'Poppins', sans-serif; 
    }

    /* --- GLOBAL STYLES --- */
    .modal-rounded { border-radius: 20px !important; overflow: hidden; }
    .ls-1 { letter-spacing: 0.5px; }

    /* Button Gradient Green */
    .btn-gradient-green {
        background: linear-gradient(135deg, #198754, #20c997);
        border: none; color: white; transition: all 0.3s;
    }
    .btn-gradient-green:hover {
        background: linear-gradient(135deg, #157347, #198754);
        transform: translateY(-1px); color: white;
    }

    /* --- DONATION CARD WRAPPER (HIJAU MUDA) --- */
    .donation-card-wrapper {
        background-color: #f0fdf4;
        border: 1px solid #dcfce7;
        border-radius: 16px;
        padding: 20px;
        box-shadow: inset 0 0 15px rgba(34, 197, 94, 0.03);
    }

    /* --- INPUT STYLES --- */
    .rounded-pill-input {
        border-radius: 50px !important;
        border: 1px solid #d1d5db;
        padding-left: 15px; font-size: 0.9rem;
    }
    .rounded-box-input {
        border-radius: 12px !important;
        border: 1px solid #d1d5db; padding: 10px;
    }
    .form-control:focus, .form-select:focus {
        border-color: #22c55e;
        box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.15);
    }

    /* --- UPLOAD BOX --- */
    .file-upload-box {
        background: white; border: 2px dashed #cbd5e1;
        border-radius: 12px; height: 120px;
        display: flex; align-items: center; justify-content: center;
        transition: all 0.3s;
    }
    .file-upload-box:hover { border-color: #22c55e; background: #fafffc; }
    .icon-circle {
        width: 40px; height: 40px; background: #dcfce7;
        border-radius: 50%; display: flex; align-items: center; justify-content: center;
    }

    /* --- TABLE --- */
    .rounded-top-left { border-top-left-radius: 10px; }
    .rounded-top-right { border-top-right-radius: 10px; }
</style>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/program.js') }}"></script>
@endpush
@endsection