@extends('layouts.app')

@section('title', 'Infaq')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container-fluid p-4">

    {{-- HEADER & SEARCH --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div class="d-flex flex-wrap align-items-center gap-2">
            {{-- Search Bar --}}
            <div class="input-group" style="width: 300px;">
                <span class="input-group-text bg-white border-end-0 rounded-start-pill ps-3"><i class="bi bi-search text-muted"></i></span>
                <input type="text" id="searchInput" class="form-control border-start-0 rounded-end-pill" placeholder="Cari tanggal atau nominal...">
            </div>
        </div>

        <button class="btn btn-gradient-green rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#modaltambahinfaq" id="btnTambahInfaq">
            <i class="bi bi-plus-lg me-2"></i> Tambah Infaq
        </button>
    </div>

    {{-- CARD TOTAL INFAQ (Div sebelum Table) --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold text-dark mb-0">Total Infaq Jumat</h5>
                <small class="text-muted">Akumulasi seluruh pemasukan infaq</small>
            </div>
            <h3 class="fw-bold text-success mb-0">
                Rp <span id="totalInfaqHeader">{{ number_format($totalInfaq, 0, ',', '.') }}</span>
            </h3>
        </div>
    </div>

    {{-- TABEL UTAMA --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="tabelKhotib"> {{-- ID tetap tabelKhotib agar JS lama jalan, atau ganti di JS --}}
                    <thead class="bg-light">
                        <tr style="height: 50px;"> 
                            <th class="text-center ps-4 rounded-top-left" style="width: 10%;">No</th>
                            <th class="text-center" style="width: 40%;">Tanggal</th>
                            <th class="text-center" style="width: 30%;">Nominal</th>
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

{{-- MODAL TAMBAH / EDIT INFAQ (Style Donasi) --}}
<div class="modal fade" id="modaltambahinfaq" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg modal-rounded">
            
            {{-- Header Clean --}}
            <div class="modal-header border-0 pb-0 pt-4 px-4 bg-white">
                <div>
                    <h5 class="modal-title fw-bold text-dark" id="modalInfaqLabel">Infaq Jumat</h5>
                    <p class="text-muted small mb-0">Catat pemasukan kotak amal mingguan</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4 bg-white">
                <form id="formTambahInfaq">
                    <input type="hidden" id="id_infaq" name="id_infaq">

                    {{-- WRAPPER HIJAU --}}
                    <div class="donation-card-wrapper">

                        {{-- Input Tanggal --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold text-success small text-uppercase ls-1">Tanggal</label>
                            <input type="date" class="form-control rounded-pill-input" id="tanggal_infaq" name="tanggal_infaq" required>
                        </div>
                        
                        {{-- Input Nominal --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold text-success small text-uppercase ls-1">Nominal</label>
                            <div class="input-group rounded-pill-group">
                                <span class="input-group-text bg-white border-end-0 text-success fw-bold ps-3">Rp</span>
                                <input type="text" class="form-control border-start-0 ps-1 fw-bold text-dark" id="nominal_infaq" name="nominal_infaq" placeholder="0" required autocomplete="off">
                            </div>
                            <div class="form-text text-muted small ms-3 fst-italic">
                                Masukkan angka saja, format otomatis.
                            </div>
                        </div>

                        {{-- Tombol --}}
                        <div class="d-grid">
                            <button type="submit" class="btn btn-gradient-green rounded-pill py-2 fw-bold shadow-sm">
                                <i class="bi bi-wallet2 me-2"></i> Simpan Data
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
    #tabelKhotib, .card, .modal-content, .donation-card-wrapper { 
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
    .form-control:focus {
        border-color: #22c55e;
        box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.15);
    }

    /* Input Group Rp */
    .rounded-pill-group .input-group-text {
        border-top-left-radius: 50px; border-bottom-left-radius: 50px;
        border: 1px solid #d1d5db; background: white;
    }
    .rounded-pill-group .form-control {
        border-top-right-radius: 50px; border-bottom-right-radius: 50px;
        border: 1px solid #d1d5db;
    }

    /* --- TABLE --- */
    .rounded-top-left { border-top-left-radius: 10px; }
    .rounded-top-right { border-top-right-radius: 10px; }
</style>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
{{-- Pastikan nama file JS nya sesuai --}}
<script src="{{ asset('js/infaq.js') }}"></script>
@endpush
@endsection