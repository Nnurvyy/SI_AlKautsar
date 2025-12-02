@extends('layouts.app')

@section('title', 'Donasi')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container-fluid p-4">

    {{-- HEADER & SEARCH --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div class="d-flex flex-wrap align-items-center gap-2">
            {{-- Filter Status --}}
            <select class="form-select rounded-pill ps-3" id="statusFilter" style="width: 160px; border-color: #e5e7eb;">
                <option value="aktif" selected>Status: Aktif</option>
                <option value="lewat">Status: Lewat</option>
                <option value="semua">Semua Status</option>
            </select>

            {{-- Search Bar --}}
            <div class="input-group" style="width: 300px;">
                <span class="input-group-text bg-white border-end-0 rounded-start-pill ps-3"><i class="bi bi-search text-muted"></i></span>
                <input type="text" id="searchInput" class="form-control border-start-0 rounded-end-pill" placeholder="Cari program donasi...">
            </div>
        </div>

        <button class="btn btn-gradient-green rounded-pill px-4 shadow-sm" id="btnTambahDonasi">
            <i class="bi bi-plus-lg me-2"></i> Program Baru
        </button>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold text-dark mb-0">Total Donasi Terkumpul</h5>
                {{-- Ubah teks keterangan agar sesuai --}}
                <small class="text-muted">Akumulasi seluruh pemasukan donasi</small>
            </div>
            
            {{-- Tampilkan variabel $totalDonasi dari Controller --}}
            <h3 class="fw-bold text-success mb-0">
                Rp <span id="totalDonasiHeader">{{ number_format($totalDonasi, 0, ',', '.') }}</span>
            </h3>
        </div>
    </div>

    {{-- TABEL UTAMA --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="tabelDonasi">
                    <thead>
                        <tr style="height: 50px;">
                            <th class="text-center ps-4 rounded-top-left">No</th>
                            <th class="text-center">Poster</th>
                            <th>Nama Program</th>
                            <th class="text-center cursor-pointer" id="sortMulai">Mulai <i class="bi bi-arrow-down-up small text-muted sort-icon"></i></th>
                            <th class="text-center cursor-pointer" id="sortSelesai">Selesai <i class="bi bi-arrow-down-up small text-muted sort-icon"></i></th>
                            <th class="text-end">Target</th>
                            <th class="text-end">Terkumpul</th>
                            <th class="text-center" style="width: 15%;">Progress</th>
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
{{-- MODAL 1: CREATE / EDIT PROGRAM DONASI (Style Public) --}}
{{-- ============================================================== --}}
<div class="modal fade" id="modalDonasi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg modal-rounded">
            
            {{-- Header Clean --}}
            <div class="modal-header border-0 pb-0 pt-4 px-4 bg-white">
                <div>
                    <h5 class="modal-title fw-bold text-dark" id="modalTitle">Program Donasi</h5>
                    <p class="text-muted small mb-0">Galang dana untuk kemaslahatan umat</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4 bg-white">
                <form id="formDonasi" enctype="multipart/form-data">
                    <input type="hidden" id="id_donasi" name="id_donasi">

                    {{-- WRAPPER HIJAU (Style Public) --}}
                    <div class="donation-card-wrapper">
                        
                        {{-- Upload Foto --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold text-success small text-uppercase ls-1">Poster / Gambar (Max 2 Mb)</label>
                            <input type="file" class="d-none" id="foto_donasi" name="foto_donasi" accept="image/*">
                            
                            {{-- 1. Tampilan Kosong --}}
                            <label for="foto_donasi" id="uploadPlaceholder" class="file-upload-box cursor-pointer">
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
                                <label for="foto_donasi" class="btn btn-light btn-sm position-absolute bottom-0 end-0 m-2 rounded-pill shadow-sm small fw-bold"><i class="bi bi-pencil me-1"></i> Ubah</label>
                            </div>
                        </div>

                        {{-- Nama Program --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold text-success small text-uppercase ls-1">Nama Program</label>
                            <input type="text" class="form-control rounded-pill-input" name="nama_donasi" id="nama_donasi" placeholder="Misal: Renovasi Masjid" required>
                        </div>

                        {{-- Target Dana --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold text-success small text-uppercase ls-1">Target Dana</label>
                            <div class="input-group rounded-pill-group">
                                <span class="input-group-text bg-white border-end-0 text-success fw-bold ps-3">Rp</span>
                                <input type="text" class="form-control border-start-0 ps-1 fw-bold text-dark" id="display_target_dana" placeholder="0" required>
                                <input type="hidden" name="target_dana" id="target_dana">
                            </div>
                        </div>

                        {{-- Tanggal --}}
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label fw-bold text-success small text-uppercase ls-1">Mulai</label>
                                <input type="date" class="form-control rounded-pill-input" name="tanggal_mulai" id="tanggal_mulai" required value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold text-success small text-uppercase ls-1">Selesai</label>
                                <input type="date" class="form-control rounded-pill-input" name="tanggal_selesai" id="tanggal_selesai">
                            </div>
                        </div>

                        {{-- Deskripsi --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold text-success small text-uppercase ls-1">Deskripsi</label>
                            <textarea class="form-control rounded-box-input" name="deskripsi" id="deskripsi" rows="2" placeholder="Tulis keterangan singkat..."></textarea>
                        </div>

                        {{-- Tombol --}}
                        <div class="d-grid">
                            <button type="submit" class="btn btn-gradient-green rounded-pill py-2 fw-bold shadow-sm">
                                <i class="bi bi-check-circle me-2"></i> Simpan Program
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================== --}}
{{-- MODAL 2: DETAIL DONASI & HISTORY (Clean Style) --}}
{{-- ============================================================== --}}
<div class="modal fade" id="modalDetail" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg modal-rounded">
            
            <div class="modal-header border-0 pb-0 pt-4 px-4 bg-white">
                <div>
                    <h5 class="modal-title fw-bold text-dark" id="detailTitle">Detail Donasi</h5>
                    <p class="text-muted small mb-0">Statistik dan riwayat transaksi</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-4">
                {{-- Statistik Cards --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="stat-card bg-light-green">
                            <small class="text-success fw-bold text-uppercase ls-1" style="font-size: 0.7rem;">Target</small>
                            <h5 class="fw-bold text-dark mt-1 mb-0" id="detTarget">Rp 0</h5>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card bg-gradient-green text-white shadow-sm">
                            <small class="text-white-50 fw-bold text-uppercase ls-1" style="font-size: 0.7rem;">Terkumpul</small>
                            <h5 class="fw-bold mt-1 mb-0" id="detTerkumpul">Rp 0</h5>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card bg-light-red">
                            <small class="text-danger fw-bold text-uppercase ls-1" style="font-size: 0.7rem;">Kekurangan</small>
                            <h5 class="fw-bold text-dark mt-1 mb-0" id="detSisa">Rp 0</h5>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3 px-1">
                    <h6 class="fw-bold m-0"><i class="bi bi-clock-history me-2 text-muted"></i>Riwayat Pemasukan</h6>
                    <button class="btn btn-sm btn-outline-success rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#modalInputPemasukan">
                        <i class="bi bi-plus-lg me-1"></i> Input Manual
                    </button>
                </div>

                <div class="table-responsive rounded-3 border">
                    <table class="table table-striped mb-0 align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-3 py-2 small text-muted text-uppercase">Tgl</th>
                                <th class="py-2 small text-muted text-uppercase">Donatur</th>
                                <th class="py-2 small text-muted text-uppercase text-center">Metode</th>
                                {{-- TAMBAHAN: Kolom Status --}}
                                <th class="py-2 small text-muted text-uppercase text-center">Status</th> 
                                <th class="py-2 small text-muted text-uppercase text-end">Nominal</th>
                                <th class="pe-3 py-2 small text-muted text-uppercase text-center">#</th>
                            </tr>
                        </thead>
                        <tbody id="tabelRiwayat" class="small"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================== --}}
{{-- MODAL 3: INPUT PEMASUKAN MANUAL (Style Public / Wrapper Hijau) --}}
{{-- ============================================================== --}}
<div class="modal fade" id="modalInputPemasukan" tabindex="-1" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered modal-sm"> <div class="modal-content border-0 shadow-lg modal-rounded">
            
            <div class="modal-header border-0 pb-0 pt-3 px-3 bg-white">
                <h6 class="modal-title fw-bold text-dark">Catat Donasi Masuk</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-3 bg-white">
                <form id="formPemasukan">
                    <input type="hidden" name="id_donasi" id="input_id_donasi">

                    {{-- WRAPPER HIJAU (Mini Version) --}}
                    <div class="donation-card-wrapper p-3">
                        
                        {{-- Nominal (Highlight) --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold text-success small">Nominal (Rp)</label>
                            
                            {{-- Input Visual (Untuk User liat titiknya) --}}
                            <input type="text" 
                                class="form-control rounded-pill-input text-center fw-bold text-success" 
                                style="font-size: 1.1rem;" 
                                id="display_nominal" 
                                placeholder="0" 
                                required>

                            {{-- Input Asli (Disembunyikan untuk dikirim ke Database) --}}
                            <input type="hidden" name="nominal" id="real_nominal">
                        </div>

                        {{-- Donatur --}}
                        <div class="mb-2">
                            <label class="form-label fw-bold text-success small">Nama Donatur</label>
                            <input type="text" class="form-control rounded-pill-input form-control-sm" name="nama_donatur" placeholder="Hamba Allah" required>
                        </div>

                        {{-- Metode --}}
                        <div class="mb-2">
                            <label class="form-label fw-bold text-success small">Metode</label>
                            <select class="form-select rounded-pill-input form-select-sm" name="metode_pembayaran" required>
                                <option value="tunai" selected>Tunai (Cash)</option>
                                <option value="whatsapp">Konfirmasi WA</option>
                                <option value="transfer">Transfer Bank</option>
                            </select>
                        </div>

                        {{-- Tanggal --}}
                        <div class="mb-2">
                            <label class="form-label fw-bold text-success small">Tanggal</label>
                            <input type="date" class="form-control rounded-pill-input form-control-sm" name="tanggal" value="{{ date('Y-m-d') }}" required>
                        </div>

                        {{-- Pesan --}}
                        <div class="mb-3">
                            <textarea class="form-control rounded-box-input form-control-sm" name="pesan" rows="2" placeholder="Pesan / Doa (Opsional)"></textarea>
                        </div>

                        <button type="submit" class="btn btn-gradient-green w-100 rounded-pill fw-bold shadow-sm py-2">
                            Simpan Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- CSS KHUSUS HALAMAN INI --}}
<style>
    /* Import Font Poppins */
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap');

    /* HAPUS 'body' global, ganti dengan class spesifik */
    /* Font Poppins hanya berlaku untuk Tabel, Card, dan Modal di halaman ini */
    #tabelDonasi, 
    .card, 
    .modal-content, 
    .donation-card-wrapper { 
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

    /* Input Group Rp */
    .rounded-pill-group .input-group-text {
        border-top-left-radius: 50px; border-bottom-left-radius: 50px;
        border: 1px solid #d1d5db; background: white;
    }
    .rounded-pill-group .form-control {
        border-top-right-radius: 50px; border-bottom-right-radius: 50px;
        border: 1px solid #d1d5db;
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

    /* --- STAT CARDS (MODAL DETAIL) --- */
    .stat-card {
        padding: 15px; border-radius: 16px; text-align: center;
    }
    .bg-light-green { background-color: #ecfdf5; color: #065f46; }
    .bg-light-red { background-color: #fef2f2; color: #991b1b; }
    .bg-gradient-green { background: linear-gradient(135deg, #10b981, #059669); }

    /* --- TABLE --- */
    .rounded-top-left { border-top-left-radius: 10px; }
    .rounded-top-right { border-top-right-radius: 10px; }
</style>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/donasi.js') }}"></script>
@endpush
@endsection