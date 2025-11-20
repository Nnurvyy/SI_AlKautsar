@extends('layouts.app')

@section('title', 'Kelola Donasi')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container-fluid p-4">
    {{-- Header & Filter --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div class="input-group search-bar me-2" style="width: 350px;">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
            <input type="text" id="searchInput" class="form-control border-start-0" placeholder="Cari program donasi...">
        </div>
        <button class="btn btn-primary" id="btnTambahDonasi">
            <i class="bi bi-plus-circle me-2"></i> Program Baru
        </button>
    </div>

    {{-- Tabel Utama --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="tabelDonasi">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center">No</th>
                            <th class="text-center">Poster</th>
                            <th>Nama Program</th>
                            <th class="text-end">Target Dana</th>
                            <th class="text-end">Terkumpul</th>
                            <th class="text-center">Progress</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div id="paginationContainer" class="d-flex justify-content-between align-items-center mt-3">
                <span id="paginationInfo"></span>
                <nav id="paginationLinks"></nav>
            </div>
        </div>
    </div>
</div>

{{-- MODAL 1: Create/Edit Program Donasi (Ada upload foto) --}}
<div class="modal fade" id="modalDonasi" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formDonasi" enctype="multipart/form-data">
                <input type="hidden" id="id_donasi" name="id_donasi">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Program Donasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    {{-- Input Foto (Style Khotib) --}}
                    <div class="mb-3">
                        <label class="form-label">Poster Donasi</label>
                        <input type="file" class="d-none" id="foto_donasi" name="foto_donasi" accept="image/*">
                        <div class="position-relative">
                            <label for="foto_donasi" id="foto_label" class="form-control d-block text-truncate" style="cursor: pointer;">
                                <span class="text-muted">Pilih gambar...</span>
                            </label>
                        </div>
                        <img id="previewFoto" class="rounded mt-2 d-none mx-auto d-block" style="width: 100%; max-height: 200px; object-fit: contain;">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nama Donasi <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama_donasi" id="nama_donasi" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Target Dana (Rp) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="target_dana" id="target_dana" required min="0">
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="tanggal_mulai" id="tanggal_mulai" required value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Tanggal Selesai</label>
                            <input type="date" class="form-control" name="tanggal_selesai" id="tanggal_selesai">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea class="form-control" name="deskripsi" id="deskripsi" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL 2: Detail & Pemasukan (Style Tabungan Qurban) --}}
<div class="modal fade" id="modalDetail" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailTitle">Detail Donasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                {{-- Statistik Cards --}}
                <div class="row mb-4 text-center">
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted">Target Dana</small>
                            <h5 class="fw-bold mt-1" id="detTarget">Rp 0</h5>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted">Terkumpul</small>
                            <h5 class="fw-bold text-success mt-1" id="detTerkumpul">Rp 0</h5>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted">Kekurangan</small>
                            <h5 class="fw-bold text-danger mt-1" id="detSisa">Rp 0</h5>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold">Riwayat Pemasukan</h6>
                    {{-- Tombol untuk memicu modal input pemasukan --}}
                    <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalInputPemasukan">
                        <i class="bi bi-plus-lg"></i> Tambah Pemasukan
                    </button>
                </div>

                <table class="table table-sm table-striped">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Nama Donatur</th>
                            <th class="text-end">Nominal</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tabelRiwayat"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- MODAL 3: Form Input Pemasukan (Dipanggil dari dalam Modal Detail) --}}
<div class="modal fade" id="modalInputPemasukan" tabindex="-1" style="z-index: 1060;">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <form id="formPemasukan">
                <div class="modal-header bg-success text-white">
                    <h6 class="modal-title">Input Donasi Masuk</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    {{-- ID Donasi di-set otomatis via JS saat buka detail --}}
                    <input type="hidden" name="id_donasi" id="input_id_donasi">
                    
                    <div class="mb-2">
                        <label class="small">Nama Donatur / Hamba Allah</label>
                        <input type="text" class="form-control form-control-sm" name="nama_donatur" required>
                    </div>
                    <div class="mb-2">
                        <label class="small">Tanggal</label>
                        <input type="date" class="form-control form-control-sm" name="tanggal" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-2">
                        <label class="small">Nominal (Rp)</label>
                        <input type="number" class="form-control form-control-sm" name="nominal" required min="1">
                    </div>
                    <div class="mb-2">
                        <label class="small">Pesan / Doa (Opsional)</label>
                        <textarea class="form-control form-control-sm" name="pesan" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer p-1">
                    <button type="submit" class="btn btn-success btn-sm w-100">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/donasi.js') }}"></script>
@endpush
@endsection