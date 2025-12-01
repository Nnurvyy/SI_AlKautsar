@extends('layouts.app')

@section('title', 'Stok & Inventori')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container-fluid p-4">

    {{-- HEADER & SEARCH --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div class="d-flex flex-wrap align-items-center gap-2">
            
            {{-- Filter Kondisi (Opsional, style disamakan) --}}
            <select class="form-select rounded-pill ps-3" id="kondisiFilter" style="width: 170px; border-color: #e5e7eb;">
                <option value="all" selected>Semua Kondisi</option>
                <option value="Baik">Baik</option>
                <option value="Perlu Perbaikan">Perlu Perbaikan</option>
                <option value="Rusak Berat">Rusak Berat</option>
            </select>

            {{-- Search Bar --}}
            <div class="input-group" style="width: 300px;">
                <span class="input-group-text bg-white border-end-0 rounded-start-pill ps-3"><i class="bi bi-search text-muted"></i></span>
                <input type="text" id="searchInput" class="form-control border-start-0 rounded-end-pill" placeholder="Cari nama barang...">
            </div>
        </div>

        {{-- Tombol Tambah --}}
        <button class="btn btn-gradient-green rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalInventaris" id="btnTambahBarang">
            <i class="bi bi-plus-lg me-2"></i> Tambah Barang
        </button>
    </div>

    {{-- CARD TOTAL BARANG (Div sebelum Table) --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold text-dark mb-0">Total Jenis Barang</h5>
                <small class="text-muted">Jumlah aset terdaftar di inventaris</small>
            </div>
            {{-- Angka dari Controller --}}
            <h3 class="fw-bold text-success mb-0">
                {{ number_format($totalBarang, 0, ',', '.') }} Item
            </h3>
        </div>
    </div>

    {{-- TABEL UTAMA --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="tabelInventaris">
                    <thead class="bg-light">
                        <tr style="height: 50px;"> 
                            <th class="text-center ps-4 rounded-top-left" style="width: 5%;">No</th>
                            <th style="width: 30%;">Nama Barang</th>
                            <th class="text-center" style="width: 10%;">Satuan</th>
                            <th class="text-center" style="width: 15%;">Kondisi</th>
                            <th class="text-center" style="width: 10%;">Stock</th>
                            <th class="text-center pe-4 rounded-top-right" style="width: 20%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        {{-- Data dimuat lewat JS --}}
                    </tbody>
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

{{-- MODAL TAMBAH / EDIT INVENTARIS (Style Green) --}}
<div class="modal fade" id="modalInventaris" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg modal-rounded">
            
            {{-- Header Clean --}}
            <div class="modal-header border-0 pb-0 pt-4 px-4 bg-white">
                <div>
                    <h5 class="modal-title fw-bold text-dark" id="modalInventarisLabel">Barang Inventaris</h5>
                    <p class="text-muted small mb-0">Kelola aset masjid & ketersediaan stok</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4 bg-white">
                <form id="formInventarisStock">
                    <input type="hidden" id="id_barang" name="id_barang">

                    {{-- WRAPPER HIJAU --}}
                    <div class="donation-card-wrapper">

                        {{-- Nama Barang --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold text-success small text-uppercase ls-1">Nama Barang</label>
                            <input type="text" class="form-control rounded-pill-input" id="nama_barang" name="nama_barang" placeholder="Contoh: Karpet Sajadah" required>
                        </div>

                        {{-- Satuan & Kondisi --}}
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label fw-bold text-success small text-uppercase ls-1">Satuan</label>
                                <select class="form-select rounded-pill-input" id="satuan" name="satuan" required>
                                    <option value="" disabled selected>Pilih...</option>
                                    <option value="Pcs">Pcs</option>
                                    <option value="Unit">Unit</option>
                                    <option value="Set">Set</option>
                                    <option value="Meter">Meter</option>
                                    <option value="Roll">Roll</option>
                                    <option value="Box">Box</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold text-success small text-uppercase ls-1">Kondisi</label>
                                <select class="form-select rounded-pill-input" id="kondisi" name="kondisi" required>
                                    <option value="" disabled selected>Pilih...</option>
                                    <option value="Baik">Baik</option>
                                    <option value="Perlu Perbaikan">Perlu Perbaikan</option>
                                    <option value="Rusak Berat">Rusak Berat</option>
                                </select>
                            </div>
                        </div>

                        {{-- Stock --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold text-success small text-uppercase ls-1">Jumlah Stock</label>
                            <input type="number" class="form-control rounded-pill-input" id="stock" name="stock" placeholder="0" min="0" required>
                        </div>

                        {{-- Tombol --}}
                        <div class="d-grid">
                            <button type="submit" class="btn btn-gradient-green rounded-pill py-2 fw-bold shadow-sm">
                                <i class="bi bi-box-seam me-2"></i> Simpan Barang
                            </button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- CSS KHUSUS HALAMAN INI (Sama dengan Donasi & Program) --}}
<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap');

    /* Scoping Font Poppins */
    #tabelInventaris, .card, .modal-content, .donation-card-wrapper { 
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
    .form-control:focus, .form-select:focus {
        border-color: #22c55e;
        box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.15);
    }

    /* --- TABLE --- */
    .rounded-top-left { border-top-left-radius: 10px; }
    .rounded-top-right { border-top-right-radius: 10px; }
    
    /* Pagination */
    #paginationLinks .pagination { margin-bottom: 0; }
    #paginationLinks .page-item.active .page-link { background-color: #198754; border-color: #198754; }
    #paginationLinks .page-link { cursor: pointer; color: #198754; }
</style>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
{{-- Pastikan nama file JS kamu benar (misal: public/js/inventaris.js) --}}
<script src="{{ asset('js/inventaris.js') }}"></script>
@endpush
@endsection