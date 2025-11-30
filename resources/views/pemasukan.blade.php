@extends('layouts.app')

@section('title', 'Pemasukan')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container-fluid p-4">
    {{-- Header & Filter --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center flex-wrap">
            <div class="input-group search-bar me-2" style="width: 300px;">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                <input type="text" id="searchInput" class="form-control border-start-0" placeholder="Cari deskripsi...">
            </div>
        </div>
        
        <div class="d-flex align-items-center mt-2 mt-md-0">
            <button type="button" class="btn btn-outline-secondary me-2" id="btnKelolaKategori">
                <i class="bi bi-tags me-2"></i> Kelola Kategori
            </button>
            <button type="button" class="btn btn-success" id="btnTambahPemasukan">
                <i class="bi bi-plus-circle me-2"></i> Tambah Pemasukan
            </button>
        </div>
    </div>

    {{-- Info Total --}}
    <div class="d-flex justify-content-between align-items-center p-3 bg-white rounded shadow-sm mb-4">
        <h5 class="fw-bold mb-0">Total Pemasukan</h5>
        <h5 class="fw-bold mb-0 text-success" id="textTotalPemasukan">
            Rp {{ number_format($totalPemasukan, 0, ',', '.') }}
        </h5>
    </div>

    {{-- Tabel Transaksi --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="tabelKeuangan">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Kategori</th>
                            <th>Deskripsi</th>
                            <th class="text-end">Nominal</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Data dimuat via JS --}}
                    </tbody>
                </table>
            </div>
            {{-- Pagination --}}
            <div id="paginationContainer" class="d-flex justify-content-between align-items-center mt-3">
                <span id="paginationInfo"></span>
                <nav id="paginationLinks"></nav>
            </div>
        </div>
    </div>
</div>

{{-- MODAL 1: Form Transaksi (Tambah/Edit) --}}
<div class="modal fade" id="modalTransaksi" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px; overflow: hidden;">
            <form id="formTransaksi">
                <input type="hidden" id="id_keuangan" name="id_keuangan">
                
                {{-- Header Modern --}}
                <div class="modal-header border-0 pb-0 ps-4 pe-4 pt-4">
                    <h4 class="modal-title fw-bold" id="modalTransaksiTitle" style="color: #2c3e50;">
                        </h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-4">
                    {{-- Input Nominal (Fokus Utama) --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold text-success" id="labelNominal">Nominal Transaksi</label>
                        <div class="input-group input-group-lg shadow-sm">
                            <span class="input-group-text bg-white text-success fw-bold border-end-0" style="border-top-left-radius: 10px; border-bottom-left-radius: 10px;">Rp</span>
                            {{-- PENTING: type="text" agar bisa menerima titik, bukan number --}}
                            <input type="text" class="form-control border-start-0 fs-4 fw-bold text-dark" 
                                   name="nominal_display" id="inputNominal" required placeholder="0" autocomplete="off"
                                   style="border-top-right-radius: 10px; border-bottom-right-radius: 10px;">
                        </div>
                        <small class="text-muted fst-italic ms-1">Masukkan nominal tanpa titik.</small>
                    </div>

                    {{-- Grid untuk Tanggal & Kategori --}}
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Tanggal</label>
                            <input type="date" class="form-control py-2" name="tanggal" id="inputTanggal" required value="{{ date('Y-m-d') }}" style="border-radius: 8px;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Kategori</label>
                            <select class="form-select py-2" name="id_kategori_keuangan" id="selectKategori" required style="border-radius: 8px;">
                                <option value="">-- Pilih --</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-bold small text-muted">Deskripsi / Catatan</label>
                        <textarea class="form-control" name="deskripsi" id="inputDeskripsi" rows="3" placeholder="Contoh: Sedekah hamba Allah..." style="border-radius: 8px;"></textarea>
                    </div>
                </div>

                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-light fw-bold text-muted px-4" data-bs-dismiss="modal" style="border-radius: 8px;">Batal</button>
                    
                    {{-- Tombol menyesuaikan halaman (Merah/Hijau diatur via JS atau class bawaan) --}}
                    <button type="submit" class="btn btn-success fw-bold px-4 py-2 shadow-sm" id="btnSimpanTransaksi" style="border-radius: 8px; min-width: 120px;">
                        Simpan <i class="bi bi-arrow-right ms-1"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL 2: Kelola Kategori (CRUD) --}}
<div class="modal fade" id="modalKategori" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px; overflow: hidden;">
            {{-- Header --}}
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold" style="color: #2c3e50;">Kelola Kategori</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-4">
                {{-- Form Tambah/Edit Kategori --}}
                <form id="formKategori" class="mb-4">
                    <input type="hidden" id="id_kategori" name="id_kategori">
                    <label class="form-label fw-bold small text-muted">Nama Kategori</label>
                    <div class="input-group shadow-sm" style="border-radius: 10px; overflow: hidden;">
                        <input type="text" class="form-control border-0 py-2 bg-light" id="nama_kategori" name="nama_kategori_keuangan" placeholder="Tulis kategori baru..." required>
                        
                        {{-- Tombol Simpan (Warna akan handle by JS/Bootstrap default) --}}
                        <button class="btn btn-primary px-3 fw-bold" type="submit" id="btnSimpanKategori">
                            <i class="bi bi-plus-lg"></i>
                        </button>
                        
                        {{-- Tombol Batal Edit --}}
                        <button type="button" class="btn btn-secondary px-3 d-none" id="btnBatalEditKategori">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                </form>

                {{-- List Kategori --}}
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="fw-bold text-muted mb-0">Daftar Kategori</h6>
                    <span class="badge bg-light text-secondary rounded-pill" id="totalKategoriBadge">0 Item</span>
                </div>
                
                {{-- Container List dengan Scroll --}}
                <div id="listKategoriContainer" class="px-1 py-1" style="max-height: 300px; overflow-y: auto;">
                    {{-- List dimuat via JS --}}
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Variabel Global untuk membedakan halaman Pemasukan/Pengeluaran
    const TIPE_HALAMAN = 'pemasukan'; // Ubah jadi 'pengeluaran' di file pengeluaran.blade.php
    const URL_API_TRANSAKSI = '/pengurus/pemasukan';
    const URL_API_KATEGORI = '/pengurus/kategori-keuangan';
</script>
<script src="{{ asset('js/keuangan.js') }}"></script>
@endpush
