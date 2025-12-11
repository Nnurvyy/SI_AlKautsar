@extends('layouts.app')

@section('title', 'Tabungan Qurban')

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Data Helper untuk JS (Hidden) --}}
    <textarea id="jamaahListJson" style="display:none;">@json($jamaahList)</textarea>
    <textarea id="hewanListJson" style="display:none;">@json($hewanList)</textarea>

    <div class="container-fluid p-4">

        <div class="row g-3 mb-4 align-items-center">

            {{-- KIRI: Search & Filter --}}
            <div class="col-12 col-xl-8">
                <div class="d-flex flex-wrap align-items-center gap-2">
                    {{-- Search Bar --}}
                    <div class="input-group" style="width: 280px;">
                        <span class="input-group-text bg-white border-end-0 rounded-start-pill ps-3"><i
                                class="bi bi-search text-muted"></i></span>
                        <input type="text" id="searchNama" class="form-control border-start-0 rounded-end-pill"
                            placeholder="Cari jamaah..." onkeyup="loadTableDelay()">
                    </div>

                    {{-- Filter Status --}}
                    <select class="form-select rounded-pill ps-3 shadow-sm border-0" id="filterStatusTabungan"
                        onchange="loadTable()" style="width: auto; min-width: 160px; background-color: #fff;">
                        <option value="semua">Status: Semua</option>
                        <option value="menunggu">⏳ Menunggu</option>
                        <option value="disetujui">✅ Disetujui</option>
                        <option value="ditolak">❌ Ditolak</option>
                    </select>

                    {{-- Filter Keuangan --}}
                    <select class="form-select rounded-pill ps-3 shadow-sm border-0" id="filterStatusSetoran"
                        onchange="loadTable()" style="width: auto; min-width: 190px; background-color: #fff;">
                        <option value="semua">Keuangan: Semua</option>
                        <option value="lunas">🟢 Lunas</option>
                        <option value="aktif">🔵 Aktif / Lancar</option>
                        <option value="menunggak">🔴 Menunggak</option>
                    </select>

                    {{-- Tombol Laporan --}}
                    <button class="btn btn-white border rounded-pill shadow-sm px-3 text-secondary bg-white" type="button"
                        data-bs-toggle="collapse" data-bs-target="#collapsePdf">
                        <i class="bi bi-file-earmark-pdf"></i> Laporan
                    </button>
                </div>
            </div>

            {{-- KANAN: Tombol Aksi (Harga & Tambah) --}}
            <div class="col-12 col-xl-4 text-xl-end text-start">
                <div class="d-flex gap-2 justify-content-xl-end justify-content-start">
                    <button class="btn btn-warning text-white rounded-pill px-4 shadow-sm fw-bold" data-bs-toggle="modal"
                        data-bs-target="#modalHargaHewan" onclick="loadListHarga()">
                        <i class="bi bi-tag-fill me-2"></i> Harga Hewan
                    </button>
                    <button class="btn btn-gradient-green rounded-pill px-4 shadow-sm" onclick="openModalCreate()">
                        <i class="bi bi-plus-lg me-2"></i> Buka Tabungan
                    </button>
                </div>
            </div>
        </div>

        <div class="collapse mb-4" id="collapsePdf">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-success mb-3"><i class="bi bi-printer me-2"></i>Cetak Laporan</h6>
                    <form action="{{ route('pengurus.tabungan-qurban.cetakPdf') }}" method="GET" target="_blank">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-muted">Periode</label>
                                <select id="filter-periode" name="periode" class="form-select rounded-pill-input"
                                    onchange="togglePdfFilter()">
                                    <option value="semua" selected>Semua Data</option>
                                    <option value="per_bulan">Per Bulan</option>
                                    <option value="per_tahun">Per Tahun</option>
                                    <option value="rentang_waktu">Rentang Waktu</option>
                                </select>
                            </div>

                            {{-- Filter Options (Hidden by default) --}}
                            <div class="col-md-4" id="filter-bulanan" style="display: none;">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold text-muted">Bulan</label>
                                        <select name="bulan" class="form-select rounded-pill-input">
                                            @for ($i = 1; $i <= 12; $i++)
                                                <option value="{{ $i }}" {{ $i == date('m') ? 'selected' : '' }}>
                                                    {{ \Carbon\Carbon::create(null, $i)->translatedFormat('F') }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold text-muted">Tahun</label>
                                        <select name="tahun_bulanan" class="form-select rounded-pill-input">
                                            @for ($i = date('Y'); $i >= date('Y') - 5; $i--)
                                                <option value="{{ $i }}">{{ $i }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3" id="filter-tahunan" style="display: none;">
                                <label class="form-label small fw-bold text-muted">Tahun</label>
                                <select name="tahun_tahunan" class="form-select rounded-pill-input">
                                    @for ($i = date('Y'); $i >= date('Y') - 5; $i--)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>

                            <div class="col-md-4" id="filter-rentang" style="display: none;">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold text-muted">Mulai</label>
                                        <input type="date" class="form-control rounded-pill-input" name="tanggal_mulai"
                                            value="{{ date('Y-m-01') }}">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold text-muted">Akhir</label>
                                        <input type="date" class="form-control rounded-pill-input"
                                            name="tanggal_akhir" value="{{ date('Y-m-d') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <button type="submit" class="btn btn-danger w-100 rounded-pill shadow-sm fw-bold">
                                    <i class="bi bi-file-pdf me-1"></i> Download PDF
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="tabelTabungan">
                        <thead class="bg-light">
                            <tr style="height: 50px;">
                                <th class="text-center ps-4 rounded-top-left" width="5%">No</th>
                                <th width="20%">Jamaah</th>
                                <th width="25%">Rincian Hewan</th>
                                <th class="text-end" width="15%">Target dan Terkumpul</th>
                                <th class="text-center" width="10%">Tipe</th>
                                <th class="text-center" width="10%">Approval</th>
                                <th class="text-center" width="10%">Status Bayar</th>
                                <th class="text-center pe-4 rounded-top-right" width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white" id="tableBody">
                            {{-- Data Loaded via JS --}}
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white border-0 py-3">
                <div id="paginationContainer" class="d-flex justify-content-between align-items-center">
                    <span id="paginationInfo" class="text-muted small ms-2"></span>
                    <nav id="paginationLinks" class="me-2"></nav>
                </div>
            </div>
        </div>

    </div>


    <div class="modal fade" id="modalTabungan" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg modal-rounded">

                <div class="modal-header border-0 pb-0 pt-4 px-4 bg-white">
                    <div>
                        <h5 class="modal-title fw-bold text-dark" id="modalTabunganTitle">Form Tabungan Qurban</h5>
                        <p class="text-muted small mb-0">Atur kesepakatan hewan dan metode pembayaran</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-4 bg-white">
                    <form id="formTabungan">
                        <input type="hidden" id="id_tabungan_hewan_qurban" name="id_tabungan_hewan_qurban">

                        {{-- WRAPPER HIJAU --}}
                        <div class="donation-card-wrapper">

                            {{-- Pilih Jamaah --}}
                            <div class="mb-3">
                                <label class="form-label fw-bold text-success small text-uppercase ls-1">Data
                                    Jamaah</label>
                                <select id="id_jamaah" name="id_jamaah" class="form-select rounded-pill-input" required>
                                    <option value="">-- Pilih Jamaah --</option>
                                    @foreach ($jamaahList as $j)
                                        <option value="{{ $j->id }}">{{ $j->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Rincian Hewan (Dynamic Rows) --}}
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="form-label fw-bold text-success small text-uppercase ls-1 mb-0">Rincian
                                        Hewan</label>
                                    <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3"
                                        onclick="addHewanRow()">
                                        <i class="bi bi-plus-lg me-1"></i> Tambah
                                    </button>
                                </div>
                                <div id="hewanContainer"></div>
                            </div>

                            {{-- Total Harga --}}
                            <div class="mb-4">
                                <label class="form-label fw-bold text-success small text-uppercase ls-1">Total Harga
                                    Kesepakatan (Deal)</label>
                                <div class="input-group rounded-pill-group">
                                    <span
                                        class="input-group-text bg-white border-end-0 text-success fw-bold ps-3">Rp</span>
                                    {{-- Input Visual --}}
                                    <input type="text" id="display_total_harga"
                                        class="form-control border-start-0 ps-1 fw-bold text-dark text-end pe-3"
                                        placeholder="0" required>
                                    {{-- Input Real --}}
                                    <input type="hidden" name="total_harga_hewan_qurban" id="total_harga_input">
                                </div>
                                <small class="text-muted text-end d-block mt-1 fst-italic"
                                    id="displayTotalTarget">Estimasi: Rp 0</small>
                            </div>

                            <hr class="border-success opacity-25">

                            {{-- Metode Tabungan --}}
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-success small text-uppercase ls-1">Metode</label>
                                    <select id="saving_type" name="saving_type" class="form-select rounded-pill-input"
                                        onchange="toggleDuration()">
                                        <option value="cicilan">Cicilan Rutin</option>
                                        <option value="bebas">Tabungan Bebas</option>
                                    </select>
                                </div>
                                <div class="col-md-6" id="divDuration">
                                    <label class="form-label fw-bold text-success small text-uppercase ls-1">Durasi
                                        (Bulan)</label>
                                    <input type="number" id="duration_months" name="duration_months"
                                        class="form-control rounded-pill-input" value="12" min="1">
                                    <small class="text-muted ms-2 mt-1 d-block" style="font-size:0.75rem;">Estimasi/bulan:
                                        <span id="estBulan" class="fw-bold text-success">-</span></small>
                                </div>
                            </div>

                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-gradient-green rounded-pill py-2 fw-bold shadow-sm">
                                    <i class="bi bi-save me-2"></i> Simpan Data
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="modalDetailTabungan" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg modal-rounded">

                <div class="modal-header border-0 pb-0 pt-4 px-4 bg-white">
                    <div>
                        <h5 class="modal-title fw-bold text-dark" id="detailModalTitle">Detail Tabungan</h5>
                        <div class="d-flex align-items-center gap-2">
                            <span id="detailSavingType"
                                class="badge bg-light text-secondary border rounded-pill text-capitalize">-</span>
                            <span id="detailStatusBadge">-</span>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-4">
                    {{-- Statistik Cards --}}
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="stat-card bg-light-green">
                                <small class="text-success fw-bold text-uppercase ls-1" style="font-size: 0.7rem;">Target
                                    Total</small>
                                <h5 class="fw-bold text-dark mt-1 mb-0" id="detailTotalHarga">Rp 0</h5>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stat-card bg-gradient-green text-white shadow-sm">
                                <small class="text-white-50 fw-bold text-uppercase ls-1"
                                    style="font-size: 0.7rem;">Terkumpul</small>
                                <h5 class="fw-bold mt-1 mb-0" id="detailTerkumpul">Rp 0</h5>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stat-card bg-light-red">
                                <small class="text-danger fw-bold text-uppercase ls-1"
                                    style="font-size: 0.7rem;">Kekurangan</small>
                                <h5 class="fw-bold text-dark mt-1 mb-0" id="detailSisa">Rp 0</h5>
                            </div>
                        </div>
                    </div>

                    {{-- List Hewan --}}
                    <h6 class="fw-bold text-success mb-2 small text-uppercase ls-1">Item Hewan Qurban</h6>
                    <ul id="detailListHewan" class="list-group mb-4 border-0"></ul>

                    {{-- Tabel Riwayat --}}
                    <div class="d-flex justify-content-between align-items-center mb-3 px-1">
                        <h6 class="fw-bold m-0"><i class="bi bi-clock-history me-2 text-muted"></i>Riwayat Setoran</h6>
                        <button class="btn btn-sm btn-outline-success rounded-pill px-3 fw-bold"
                            onclick="openModalSetor()">
                            <i class="bi bi-plus-lg me-1"></i> Input Manual
                        </button>
                    </div>

                    <div class="table-responsive rounded-3 border">
                        <table class="table table-striped mb-0 align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-3 py-2 small text-muted text-uppercase">Tanggal</th>
                                    <th class="py-2 small text-muted text-uppercase text-center">Metode</th>
                                    <th class="py-2 small text-muted text-uppercase text-center">Status</th>
                                    <th class="py-2 small text-muted text-uppercase text-end">Nominal</th>
                                    <th class="pe-3 py-2 small text-muted text-uppercase text-center">#</th>
                                </tr>
                            </thead>
                            <tbody id="tabelRiwayatSetoran" class="small"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="modalTambahSetoran" tabindex="-1" style="z-index: 1060;">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 shadow-lg modal-rounded">
                <div class="modal-header border-0 pb-0 pt-3 px-3 bg-white">
                    <h6 class="modal-title fw-bold text-dark">Catat Setoran Manual</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-3 bg-white">
                    <form id="formTambahSetoran">
                        <input type="hidden" id="setoran_id_tabungan" name="id_tabungan_hewan_qurban">

                        {{-- Hidden defaults --}}
                        <input type="hidden" name="metode_pembayaran" value="tunai">
                        <input type="hidden" name="status" value="success">

                        <div class="donation-card-wrapper p-3">
                            <div class="mb-3">
                                <label class="form-label fw-bold text-success small">Nominal (Rp)</label>
                                <input type="text"
                                    class="form-control rounded-pill-input text-center fw-bold text-success"
                                    style="font-size: 1.1rem;" id="display_nominal_setor" placeholder="0" required>
                                <input type="hidden" name="nominal" id="real_nominal_setor">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold text-success small">Tanggal</label>
                                <input type="date" name="tanggal"
                                    class="form-control rounded-pill-input form-control-sm" value="{{ date('Y-m-d') }}"
                                    required>
                            </div>
                            <button type="submit"
                                class="btn btn-gradient-green w-100 rounded-pill fw-bold shadow-sm py-2">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="modalHargaHewan" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg modal-rounded">
                <div class="modal-header bg-warning bg-opacity-10 border-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold text-dark"><i class="bi bi-tag-fill text-warning me-2"></i>Kelola Harga
                        Hewan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div id="alertHargaContainer"></div>
                    <form id="formHargaHewan" class="row g-2 mb-4 p-3 bg-light rounded-3 border">
                        <input type="hidden" name="id_hewan_qurban" id="input_id_hewan_qurban">
                        <div class="col-md-3">
                            <label class="small fw-bold text-muted">Jenis</label>
                            <select name="nama_hewan" class="form-select rounded-pill-input">
                                <option value="sapi">Sapi</option>
                                <option value="kambing">Kambing</option>
                                <option value="domba">Domba</option>
                                <option value="kerbau">Kerbau</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="small fw-bold text-muted">Kategori</label>
                            <select name="kategori_hewan" class="form-select rounded-pill-input">
                                <option value="premium">Premium</option>
                                <option value="reguler">Reguler</option>
                                <option value="basic">Basic</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="small fw-bold text-muted">Harga (Rp)</label>
                            {{-- Input Visual (Text dengan Format Rupiah) --}}
                            <input type="text" id="display_harga_hewan" class="form-control rounded-pill-input"
                                placeholder="0" required>

                            {{-- Input Asli (Hidden untuk dikirim ke Database) --}}
                            <input type="hidden" name="harga_hewan" id="real_harga_hewan">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-dark w-100 rounded-pill shadow-sm"
                                id="btnSimpanHarga">Simpan</button>
                        </div>
                    </form>

                    <h6 class="fw-bold mb-3">Daftar Harga</h6>
                    <div class="table-responsive rounded-3 border">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-3">Jenis</th>
                                    <th>Kategori</th>
                                    <th>Harga</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="listHargaBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="modalContactJamaah" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 shadow-lg modal-rounded overflow-hidden">

                {{-- Header Hijau (Dipertinggi pb-5 agar judul tidak ketabrak avatar) --}}
                <div
                    class="modal-header bg-gradient-green text-white border-0 flex-column align-items-center justify-content-center pt-3 pb-5 position-relative">
                    <h6 class="modal-title fw-bold fs-6">Kontak Jamaah</h6>
                    <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3"
                        data-bs-dismiss="modal"></button>
                </div>

                {{-- Body (Avatar ditarik ke atas negatif margin) --}}
                <div class="modal-body text-center pt-0 pb-4">
                    <div class="position-relative d-inline-block mb-3" style="margin-top: -50px;">
                        <img id="contactAvatar" src="" class="rounded-circle shadow border border-4 border-white"
                            style="width: 90px; height: 90px; object-fit: cover; background: #fff;">
                    </div>

                    <h6 id="contactName" class="fw-bold text-dark mb-1 fs-5">Nama Jamaah</h6>
                    <p class="text-muted small mb-3">Jamaah Terdaftar</p>

                    <div class="bg-light rounded-3 p-3 text-start mb-3 border">
                        <div class="mb-2">
                            <small class="d-block text-success fw-bold"
                                style="font-size:10px; letter-spacing:0.5px;">WHATSAPP</small>
                            <span id="contactPhone" class="fw-bold text-dark fs-6">-</span>
                        </div>
                        <div>
                            <small class="d-block text-success fw-bold"
                                style="font-size:10px; letter-spacing:0.5px;">EMAIL</small>
                            <span id="contactEmail" class="fw-bold text-dark text-break small">-</span>
                        </div>
                    </div>

                    <a id="btnChatWA" href="#" target="_blank"
                        class="btn btn-gradient-green w-100 rounded-pill fw-bold shadow-sm py-2">
                        <i class="bi bi-whatsapp me-2"></i> Chat WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- CSS KHUSUS HALAMAN INI --}}
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap');

        #tabelTabungan,
        .card,
        .modal-content,
        .donation-card-wrapper,
        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        .btn,
        .form-control,
        .form-select {
            font-family: 'Poppins', sans-serif;
        }

        .modal-rounded {
            border-radius: 20px !important;
            overflow: hidden;
        }

        .ls-1 {
            letter-spacing: 0.5px;
        }

        .btn-gradient-green {
            background: linear-gradient(135deg, #198754, #20c997);
            border: none;
            color: white;
            transition: all 0.3s;
        }

        .btn-gradient-green:hover {
            background: linear-gradient(135deg, #157347, #198754);
            transform: translateY(-1px);
            color: white;
        }

        .donation-card-wrapper {
            background-color: #f0fdf4;
            border: 1px solid #dcfce7;
            border-radius: 16px;
            padding: 20px;
            box-shadow: inset 0 0 15px rgba(34, 197, 94, 0.03);
        }

        .rounded-pill-input {
            border-radius: 50px !important;
            border: 1px solid #d1d5db;
            padding-left: 15px;
            font-size: 0.9rem;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #22c55e;
            box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.15);
        }

        .rounded-pill-group .input-group-text {
            border-top-left-radius: 50px;
            border-bottom-left-radius: 50px;
            border: 1px solid #d1d5db;
            background: white;
        }

        .rounded-pill-group .form-control {
            border-top-right-radius: 50px;
            border-bottom-right-radius: 50px;
            border: 1px solid #d1d5db;
        }

        .stat-card {
            padding: 15px;
            border-radius: 16px;
            text-align: center;
        }

        .bg-light-green {
            background-color: #ecfdf5;
            color: #065f46;
        }

        .bg-light-red {
            background-color: #fef2f2;
            color: #991b1b;
        }

        .bg-gradient-green {
            background: linear-gradient(135deg, #10b981, #059669);
        }

        .rounded-top-left {
            border-top-left-radius: 10px;
        }

        .rounded-top-right {
            border-top-right-radius: 10px;
        }

        .hewan-row {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 10px;
            margin-bottom: 8px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        div:where(.swal2-container) {
            z-index: 9999 !important;
        }
    </style>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/tabungan_qurban.js') }}"></script>
@endpush
