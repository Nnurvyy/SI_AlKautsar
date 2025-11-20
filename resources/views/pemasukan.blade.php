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
        <div class="modal-content">
            <form id="formTransaksi">
                <input type="hidden" id="id_keuangan" name="id_keuangan">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTransaksiTitle">Tambah Pemasukan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tanggal</label>
                        <input type="date" class="form-control" name="tanggal" id="inputTanggal" required value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kategori</label>
                        <select class="form-select" name="id_kategori_keuangan" id="selectKategori" required>
                            <option value="">-- Pilih Kategori --</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nominal (Rp)</label>
                        <input type="number" class="form-control" name="nominal" id="inputNominal" required min="1">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea class="form-control" name="deskripsi" id="inputDeskripsi" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL 2: Kelola Kategori (CRUD) --}}
<div class="modal fade" id="modalKategori" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kelola Kategori Pemasukan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                {{-- Form Tambah/Edit Kategori --}}
                <form id="formKategori" class="mb-4 p-3 bg-light rounded">
                    <input type="hidden" id="id_kategori" name="id_kategori">
                    <div class="input-group">
                        <input type="text" class="form-control" id="nama_kategori" name="nama_kategori_keuangan" placeholder="Nama Kategori Baru" required>
                        <button class="btn btn-primary" type="submit" id="btnSimpanKategori">
                            <i class="bi bi-plus-lg"></i> Tambah
                        </button>
                        <button type="button" class="btn btn-secondary d-none" id="btnBatalEditKategori">Batal</button>
                    </div>
                </form>

                {{-- List Kategori --}}
                <h6 class="fw-bold mb-2">Daftar Kategori</h6>
                <div class="list-group" id="listKategoriContainer" style="max-height: 300px; overflow-y: auto;">
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