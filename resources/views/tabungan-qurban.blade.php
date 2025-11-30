@extends('layouts.app')

@section('title', 'Data Tabungan Qurban')

@push('styles')
    <style>
        /* Custom Badge Colors */
        .badge.bg-danger { background-color: #dc3545 !important; }
        .badge.bg-success { background-color: #198754 !important; }
        .badge.bg-warning { background-color: #ffc107 !important; color: #000 !important; }
        .badge.bg-primary { background-color: #0d6efd !important; }
        .badge.bg-secondary { background-color: #6c757d !important; }
        .badge.bg-info { background-color: #0dcaf0 !important; color: #000 !important; }
        
        /* Modal Scroll & Z-Index Fixes */
        #modalTabungan .modal-dialog, #modalDetailTabungan .modal-dialog { max-height: 90vh; }
        #modalTabungan .modal-body, #modalDetailTabungan .modal-body { overflow-y: auto; max-height: 75vh; }
        
        /* Ensure modals stack correctly */
        #modalTambahSetoran { z-index: 2060; }
        #modalDetailTabungan { z-index: 2050; }
        #modalTabungan { z-index: 2050; }
        #modalHargaHewan { z-index: 2050; }
        #modalContactJamaah { z-index: 2060; }

        /* --- PERBAIKAN DI SINI --- */
        /* Paksa SweetAlert agar selalu muncul di atas Modal Bootstrap yang z-index nya tinggi */
        div:where(.swal2-container) {
            z-index: 9999 !important;
        }

        /* Dashboard-like Cards inside Modal */
        .card-stat { background-color: #f8f9fa; border-radius: .5rem; padding: 1rem; text-align: center; border: 1px solid #dee2e6; }
        .card-stat h5 { font-size: 0.9rem; color: #6c757d; margin-bottom: .5rem; }
        .card-stat .amount { font-size: 1.3rem; font-weight: 700; }
        
        /* Styling for Dynamic Rows */
        .hewan-row { background: #fdfdfd; padding: 10px; border: 1px solid #eee; border-radius: 6px; margin-bottom: 8px; }
        .hewan-row:nth-child(even) { background: #f9f9f9; }
    </style>
@endpush

@section('content')

    {{-- Data Helper untuk JS (Hidden) --}}
    <textarea id="jamaahListJson" style="display:none;">@json($jamaahList)</textarea>
    <textarea id="hewanListJson" style="display:none;">@json($hewanList)</textarea>

    <div class="container-fluid p-4">

        {{-- ========================================== --}}
        {{-- SECTION 1: FILTER & EXPORT PDF --}}
        {{-- ========================================== --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-file-earmark-pdf me-2"></i>Laporan & Filter</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('pengurus.tabungan-qurban.cetakPdf') }}" method="GET" target="_blank">
                    <div class="row g-3 align-items-end mb-3">
                        <div class="col-md-3">
                            <label for="filter-periode" class="form-label small fw-bold">Periode Laporan</label>
                            <select id="filter-periode" name="periode" class="form-select" onchange="togglePdfFilter()">
                                <option value="semua" selected>Semua Data</option>
                                <option value="per_bulan">Per Bulan</option>
                                <option value="per_tahun">Per Tahun</option>
                                <option value="rentang_waktu">Rentang Waktu</option>
                            </select>
                        </div>

                        {{-- Filter Bulanan --}}
                        <div class="col-md-4" id="filter-bulanan" style="display: none;">
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label small">Bulan</label>
                                    <select name="bulan" class="form-select">
                                        @for ($i = 1; $i <= 12; $i++)
                                            <option value="{{ $i }}" {{ $i == date('m') ? 'selected' : '' }}>
                                                {{ \Carbon\Carbon::create(null, $i)->translatedFormat('F') }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label class="form-label small">Tahun</label>
                                    <select name="tahun_bulanan" class="form-select">
                                        @for ($i = date('Y'); $i >= date('Y') - 5; $i--)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- Filter Tahunan --}}
                        <div class="col-md-3" id="filter-tahunan" style="display: none;">
                            <label class="form-label small">Tahun</label>
                            <select name="tahun_tahunan" class="form-select">
                                @for ($i = date('Y'); $i >= date('Y') - 5; $i--)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                        </div>

                        {{-- Filter Rentang --}}
                        <div class="col-md-4" id="filter-rentang" style="display: none;">
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label small">Mulai</label>
                                    <input type="date" class="form-control" name="tanggal_mulai" value="{{ date('Y-m-01') }}">
                                </div>
                                <div class="col-6">
                                    <label class="form-label small">Akhir</label>
                                    <input type="date" class="form-control" name="tanggal_akhir" value="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <button type="submit" class="btn btn-danger w-100 fw-bold">
                                <i class="bi bi-download me-1"></i> Export PDF
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- ========================================== --}}
        {{-- SECTION 2: TOOLBAR & SEARCH --}}
        {{-- ========================================== --}}
        <div class="row g-3 mb-4">
            {{-- Search & Action Buttons --}}
            <div class="col-12">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                    
                    {{-- Search Input --}}
                    <div class="input-group shadow-sm" style="max-width: 400px; min-width: 250px; flex-grow: 1;">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" id="searchNama" class="form-control border-start-0 ps-0" placeholder="Cari nama jamaah..." onkeyup="loadTableDelay()">
                    </div>

                    {{-- Action Buttons --}}
                    <div class="d-flex gap-2">
                        <button class="btn btn-warning shadow-sm fw-bold text-dark" data-bs-toggle="modal" data-bs-target="#modalHargaHewan" onclick="loadListHarga()">
                            <i class="bi bi-tag-fill me-1"></i> Harga Hewan
                        </button>
                        <button class="btn btn-primary shadow-sm fw-bold" onclick="openModalCreate()">
                            <i class="bi bi-plus-circle me-1"></i> Buka Tabungan
                        </button>
                    </div>
                </div>
            </div>

            {{-- Filter Bar --}}
            <div class="col-12">
                <div class="card border-0 shadow-sm bg-light">
                    <div class="card-body p-2 d-flex flex-wrap align-items-center gap-2">
                        <span class="text-muted small fw-bold text-uppercase ms-1 me-2">
                            <i class="bi bi-funnel-fill me-1"></i>Filter Data:
                        </span>

                        {{-- Filter Status Tabungan --}}
                        <select class="form-select form-select-sm shadow-sm border-0" id="filterStatusTabungan" onchange="loadTable()" style="width: auto; cursor: pointer;">
                            <option value="semua">Status Tabungan (Semua)</option>
                            <option value="menunggu">⏳ Menunggu Approval</option>
                            <option value="disetujui">✅ Disetujui</option>
                            <option value="ditolak">❌ Ditolak</option>
                        </select>

                        {{-- Filter Status Setoran --}}
                        <select class="form-select form-select-sm shadow-sm border-0" id="filterStatusSetoran" onchange="loadTable()" style="width: auto; cursor: pointer;">
                            <option value="semua">Status Keuangan (Semua)</option>
                            <option value="lunas">🟢 Lunas</option>
                            <option value="aktif">🔵 Aktif / Lancar</option>
                            <option value="menunggak">🔴 Menunggak</option>
                        </select>

                        {{-- Filter Tipe Tabungan --}}
                        <select class="form-select form-select-sm shadow-sm border-0" id="filterTipeTabungan" onchange="loadTable()" style="width: auto; cursor: pointer;">
                            <option value="semua">Tipe (Semua)</option>
                            <option value="cicilan">📅 Cicilan Rutin</option>
                            <option value="bebas">💰 Tabungan Bebas</option>
                        </select>
                        
                        <button class="btn btn-sm btn-link text-decoration-none text-muted" onclick="location.reload()" title="Reset Semua Filter">
                            <i class="bi bi-arrow-counterclockwise"></i> Reset
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ========================================== --}}
        {{-- SECTION 3: TABEL UTAMA --}}
        {{-- ========================================== --}}
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="tabelTabungan">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" width="5%">No</th>
                                <th width="20%">Jamaah</th>
                                <th width="25%">Rincian Hewan</th>
                                <th class="text-end" width="15%">Keuangan</th>
                                <th class="text-center" width="10%">Tipe</th>
                                <th class="text-center" width="10%">Approval</th>
                                <th class="text-center" width="10%">Status Bayar</th>
                                <th class="text-center" width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            {{-- Data loaded via JS --}}
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-between align-items-center p-3 border-top">
                    <span id="paginationInfo" class="text-muted small"></span>
                    <nav id="paginationLinks"></nav>
                </div>
            </div>
        </div>
    </div>

    {{-- ========================================== --}}
    {{-- MODAL 1: KELOLA HARGA HEWAN --}}
    {{-- ========================================== --}}
    <div class="modal fade" id="modalHargaHewan" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title fw-bold" id="modalHargaTitle"><i class="bi bi-tag-fill"></i> Kelola Harga Hewan Qurban</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="alertHargaContainer"></div>
                    <form id="formHargaHewan" class="row g-2 mb-4 p-3 bg-light rounded border">
                        <input type="hidden" name="id_hewan_qurban" id="input_id_hewan_qurban">
                        
                        <div class="col-md-3">
                            <label class="small fw-bold">Jenis</label>
                            <select name="nama_hewan" class="form-select" required>
                                <option value="sapi">Sapi</option>
                                <option value="kambing">Kambing</option>
                                <option value="domba">Domba</option>
                                <option value="kerbau">Kerbau</option>
                                <option value="unta">Unta</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="small fw-bold">Kategori</label>
                            <select name="kategori_hewan" class="form-select" required>
                                <option value="premium">Premium</option>
                                <option value="reguler">Reguler</option>
                                <option value="basic">Basic</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="small fw-bold">Harga (Rp)</label>
                            <input type="number" name="harga_hewan" class="form-control" placeholder="0" required>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100" id="btnSimpanHarga">Simpan</button>
                        </div>
                    </form>
                    
                    <h6 class="fw-bold">Daftar Harga Saat Ini</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered table-striped">
                            <thead class="table-light"><tr><th>Jenis</th><th>Kategori</th><th>Harga</th><th class="text-center">Aksi</th></tr></thead>
                            <tbody id="listHargaBody">
                                {{-- Loaded via AJAX --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ========================================== --}}
    {{-- MODAL 2: TAMBAH / EDIT TABUNGAN --}}
    {{-- ========================================== --}}
    <div class="modal fade" id="modalTabungan" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="formTabungan">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold" id="modalTabunganTitle">Form Tabungan Qurban</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="id_tabungan_hewan_qurban" name="id_tabungan_hewan_qurban">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Jamaah <span class="text-danger">*</span></label>
                            <select id="id_jamaah" name="id_jamaah" class="form-select" required>
                                <option value="">-- Pilih Jamaah --</option>
                                @foreach ($jamaahList as $j)
                                    <option value="{{ $j->id }}">{{ $j->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label fw-bold mb-0">Rincian Hewan Qurban</label>
                            <button type="button" class="btn btn-sm btn-outline-success" onclick="addHewanRow()">
                                <i class="bi bi-plus-lg"></i> Tambah Hewan
                            </button>
                        </div>
                        <div id="hewanContainer" class="mb-3"></div>
                        <div class="row mb-3">
                            <div class="col-md-12 text-end">
                                <h6 class="fw-bold mb-1">Total Harga Kesepakatan (Deal):</h6>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="total_harga_hewan_qurban" id="total_harga_input" class="form-control text-end fw-bold" required>
                                </div>
                                <small class="text-muted" id="displayTotalTarget">Estimasi Sistem: Rp 0</small>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Metode Tabungan</label>
                                <select id="saving_type" name="saving_type" class="form-select" onchange="toggleDuration()">
                                    <option value="cicilan">Cicilan Rutin</option>
                                    <option value="bebas">Tabungan Bebas</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3" id="divDuration">
                                <label class="form-label fw-bold">Durasi (Bulan)</label>
                                <input type="number" id="duration_months" name="duration_months" class="form-control" value="12" min="1">
                                <small class="text-muted">Estimasi/bulan: <span id="estBulan" class="fw-bold">-</span></small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="btnSimpanTabungan">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ========================================== --}}
    {{-- MODAL 3: DETAIL & RIWAYAT (UPDATED) --}}
    {{-- ========================================== --}}
    <div class="modal fade" id="modalDetailTabungan" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title fw-bold" id="detailModalTitle">Detail Tabungan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    {{-- Info Header --}}
                    <div class="alert alert-light border d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted d-block">Tipe Tabungan</small>
                            <strong id="detailSavingType" class="fs-5 text-capitalize">-</strong>
                        </div>
                        <div class="text-end">
                            <small class="text-muted d-block">Status Approval</small>
                            <span id="detailStatusBadge" class="badge bg-secondary">-</span>
                        </div>
                    </div>

                    {{-- Card Stats --}}
                    <div class="row mb-4 g-3">
                        <div class="col-md-4">
                            <div class="card-stat">
                                <h5>Target Total</h5>
                                <span class="amount text-dark" id="detailTotalHarga">Rp 0</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card-stat">
                                <h5>Terkumpul (Success)</h5>
                                <span class="amount text-success" id="detailTerkumpul">Rp 0</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card-stat">
                                <h5>Sisa / Kekurangan</h5>
                                <span class="amount text-danger" id="detailSisa">Rp 0</span>
                            </div>
                        </div>
                    </div>

                    {{-- List Hewan --}}
                    <h6 class="fw-bold border-bottom pb-2">Item Qurban</h6>
                    <ul id="detailListHewan" class="list-group mb-4"></ul>

                    {{-- Tabel Riwayat Setoran --}}
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="fw-bold mb-0">Riwayat Setoran</h6>
                        <button class="btn btn-sm btn-success" onclick="openModalSetor()">
                            <i class="bi bi-plus-lg"></i> Input Manual
                        </button>
                    </div>
                    
                    {{-- UPDATE HEADER TABEL DI SINI --}}
                    <div class="table-responsive border rounded" style="max-height: 300px;">
                        <table class="table table-sm table-striped mb-0">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th>Tanggal / Ref</th>
                                    <th class="text-center">Metode</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-end">Nominal</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tabelRiwayatSetoran">
                                {{-- Loaded via JS --}}
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    {{-- ========================================== --}}
    {{-- MODAL 4: FORM SETORAN MANUAL --}}
    {{-- ========================================== --}}
    <div class="modal fade" id="modalTambahSetoran" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg">
                <form id="formTambahSetoran">
                    <div class="modal-header">
                        <h5 class="modal-title">Input Setoran Manual</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="setoran_id_tabungan" name="id_tabungan_hewan_qurban">
                        <div class="alert alert-info py-2 small">
                            <i class="bi bi-info-circle me-1"></i> Gunakan ini untuk input setoran Tunai/Transfer Manual.
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tanggal</label>
                            <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nominal (Rp)</label>
                            <input type="number" name="nominal" class="form-control" placeholder="Contoh: 500000" min="1" required>
                        </div>
                        {{-- Hidden Default Fields --}}
                        <input type="hidden" name="metode_pembayaran" value="tunai">
                        <input type="hidden" name="status" value="success"> 
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ========================================== --}}
    {{-- MODAL 5: INFO KONTAK JAMAAH --}}
    {{-- ========================================== --}}
    <div class="modal fade" id="modalContactJamaah" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 15px; overflow: hidden;">
                <div class="modal-header bg-success text-white border-0">
                    <h6 class="modal-title fw-bold"><i class="bi bi-person-lines-fill me-2"></i>Info Jamaah</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center pt-4 pb-4">
                    <div class="mb-3 position-relative d-inline-block">
                        <img id="contactAvatar" src="" alt="Foto Profil" 
                             class="rounded-circle shadow-sm border border-3 border-white" 
                             style="width: 100px; height: 100px; object-fit: cover; background-color: #f0f0f0;">
                    </div>

                    <h5 id="contactName" class="fw-bold text-dark mb-1">Nama Jamaah</h5>
                    <p class="text-muted small mb-3">Jamaah Terdaftar</p>

                    <div class="card bg-light border-0 p-3 text-start mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-envelope text-secondary fs-5 me-3"></i>
                            <div style="overflow: hidden; text-overflow: ellipsis;">
                                <small class="d-block text-muted" style="font-size: 10px;">EMAIL</small>
                                <span id="contactEmail" class="fw-bold text-dark" style="font-size: 13px;">-</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="bi bi-whatsapp text-success fs-5 me-3"></i>
                            <div>
                                <small class="d-block text-muted" style="font-size: 10px;">WHATSAPP / HP</small>
                                <span id="contactPhone" class="fw-bold text-dark" style="font-size: 13px;">-</span>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid">
                        <a id="btnChatWA" href="#" target="_blank" class="btn btn-success fw-bold">
                            <i class="bi bi-whatsapp me-2"></i> Hubungi via WhatsApp
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    {{-- Load SweetAlert --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    {{-- Pastikan path ini sesuai dengan tempat kamu menyimpan file JS tadi --}}
    <script src="{{ asset('js/tabungan_qurban.js') }}"></script>
@endpush