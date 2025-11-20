@extends('layouts.app')

@section('title', 'Tabungan Qurban')

@push('styles')
    <style>
        .badge.bg-danger { background-color: #dc3545 !important; }
        .badge.bg-success { background-color: #198754 !important; }
        .badge.bg-primary { background-color: #0d6efd !important; }
        .badge.bg-secondary { background-color: #6c757d !important; }
        #modalTabungan .modal-dialog, #modalDetailTabungan .modal-dialog { max-height: 80vh; }
        #modalTabungan .modal-body, #modalDetailTabungan .modal-body { overflow-y: auto; max-height: 70vh; }
        #modalTambahSetoran { z-index: 1060; }
        #modalDetailTabungan { z-index: 1050; }
        #modalTabungan { z-index: 1050; }
        .card-stat { background-color: #f8f9fa; border-radius: .5rem; padding: 1rem; text-align: center; }
        .card-stat h5 { font-size: 0.9rem; color: #6c757d; margin-bottom: .5rem; }
        .card-stat .amount { font-size: 1.5rem; font-weight: 700; }
        #paginationLinks .pagination { margin-bottom: 0; }
        #paginationLinks .page-item.disabled .page-link { background-color: #e9ecef; }
        #paginationLinks .page-item.active .page-link { z-index: 1; }
        #paginationLinks .page-link { cursor: pointer; }
    </style>
@endpush

@section('content')

    {{-- Template untuk dropdown JAMAAH --}}
    <template id="jamaahListTemplate">
        <option value="">-- Pilih Jamaah --</option>
        @if(isset($jamaahList))
            @foreach ($jamaahList as $jamaah)
                <option value="{{ $jamaah->id }}">{{ $jamaah->name }}</option>
            @endforeach
        @endif
    </template>


    <div class="container-fluid p-4">

        {{-- Filter PDF (Tidak ada perubahan) --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-filter me-2"></i>Filter Laporan PDF
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('pengurus.tabungan-qurban.cetakPdf') }}" method="GET" target="_blank">
                    <div class="row g-3 align-items-end mb-3">
                        <div class="col-md-3">
                            <label for="filter-periode" class="form-label">Periode Waktu</label>
                            <select id="filter-periode" name="periode" class="form-select">
                                <option value="semua" selected>Semua Setoran</option>
                                <option value="per_bulan">Per Bulan</option>
                                <option value="per_tahun">Per Tahun</option>
                                <option value="rentang_waktu">Rentang Waktu</option>
                            </select>
                        </div>

                        <div class="col-md-4" id="filter-bulanan" style="display: none;">
                            <div class="row g-2">
                                <div class="col-6">
                                    <label for="filter_bulan" class="form-label">Bulan</label>
                                    <select id="filter_bulan" name="bulan" class="form-select">
                                        @for ($i = 1; $i <= 12; $i++)
                                            <option value="{{ $i }}" {{ $i == date('m') ? 'selected' : '' }}>
                                                {{ \Carbon\Carbon::create(null, $i)->translatedFormat('F') }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label for="tahun_bulanan" class="form-label">Tahun</label>
                                    <select id="tahun_bulanan" name="tahun_bulanan" class="form-select">
                                        @for ($i = date('Y'); $i >= date('Y') - 5; $i--)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3" id="filter-tahunan" style="display: none;">
                            <label for="tahun_tahunan" class="form-label">Tahun</label>
                            <select id="tahun_tahunan" name="tahun_tahunan" class="form-select">
                                @for ($i = date('Y'); $i >= date('Y') - 5; $i--)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                        </div>

                        <div class="col-md-4" id="filter-rentang" style="display: none;">
                            <div class="row g-2">
                                <div class="col-6">
                                    <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
                                    <input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai" value="{{ date('Y-m-01') }}">
                                </div>
                                <div class="col-6">
                                    <label for="tanggal_akhir" class="form-label">Tanggal Akhir</label>
                                    <input type="date" class="form-control" id="tanggal_akhir" name="tanggal_akhir" value="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="bi bi-file-earmark-pdf me-1"></i>
                                Export PDF
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Tabel Data --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <div class="d-flex flex-wrap align-items-center">
                <div class="me-2 mb-2 mb-md-0">
                    <select class="form-select" id="statusFilter" style="width: 200px;">
                        <option value="semua" selected>Semua Status</option>
                        <option value="lunas">Lunas</option>
                        <option value="menunggak">Menunggak (Gagal Target Akumulasi)</option>
                        <option value="mencicil">Mencicil / Bebas</option>
                    </select>
                </div>
            </div>

            <div class="d-flex align-items-center mt-2 mt-md-0">
                <button class="btn btn-primary d-flex align-items-center" id="btnTambahTabungan">
                    <i class="bi bi-plus-circle me-2"></i>
                    Tambah Tabungan
                </button>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center p-3 bg-white rounded shadow-sm mb-4">
            <h5 class="fw-bold mb-0">Data Tabungan Qurban</h5>
        </div>

        <div class="card transaction-table border-0 shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="tabelTabungan">
                        <thead class="table-light">
                        <tr>
                            <th scope="col" style="width: 5%;" class="text-center">No</th>
                            <th scope="col">Nama Penabung & Hewan</th>
                            {{-- --- PERUBAHAN DI SINI: Pisahkan Target dan Tipe --- --}}
                            <th scope="col" class="text-end" style="width: 12%;">Total Target</th>
                            <th scope="col" class="text-center" style="width: 8%;">Tipe</th>
                            <th scope="col" class="text-end" style="width: 10%;">Cicilan Bulanan</th>
                            <th scope="col" class="text-end" id="sortTotalTerkumpul" style="cursor:pointer; width: 12%;">
                                Total Terkumpul <i id="sortIcon" class="bi bi-arrow-down"></i>
                            </th>
                            <th scope="col" class="text-end" style="width: 10%;">Sisa Target</th>
                            <th scope="col" style="width: 12%;" class="text-center">Status</th>
                            <th scope="col" style="width: 12%;" class="text-center">Aksi</th>
                        </tr>
                        </thead>
                        <tbody>
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

    {{-- Modal Form Tambah/Edit Tabungan --}}
    <div class="modal fade" id="modalTabungan" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="formTabungan" onsubmit="return false;">

                    <input type="hidden" id="id_tabungan_hewan_qurban" name="id_tabungan_hewan_qurban">

                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTabunganTitle">Tambah Tabungan Qurban Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="id_jamaah" class="form-label">Nama Jamaah <span class="text-danger">*</span></label>
                            <select id="id_jamaah" name="id_jamaah" class="form-select" required>
                                <option value="">-- Memuat... --</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="nama_hewan" class="form-label">Nama Hewan <span class="text-danger">*</span></label>
                            <select id="nama_hewan" name="nama_hewan" class="form-select" required>
                                <option value="kambing">Kambing</option>
                                <option value="domba">Domba</option>
                                <option value="sapi">Sapi</option>
                                <option value="kerbau">Kerbau</option>
                                <option value="unta">Unta</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="total_hewan" class="form-label">Total Hewan <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="total_hewan" name="total_hewan" required min="1" value="1">
                        </div>
                        <div class="mb-3">
                            <label for="total_harga_hewan_qurban" class="form-label">Total Harga (Target) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="total_harga_hewan_qurban" name="total_harga_hewan_qurban" required min="0" value="0">
                        </div>

                        {{-- --- INPUT BARU: Jenis Tabungan --- --}}
                        <div class="mb-3">
                            <label for="saving_type" class="form-label">Jenis Tabungan <span class="text-danger">*</span></label>
                            <select id="saving_type" name="saving_type" class="form-select" required>
                                <option value="cicilan">Cicilan Waktu (Target Harga/Bulan)</option>
                                <option value="bebas">Tabungan Bebas (Fleksibel)</option>
                            </select>
                        </div>

                        {{-- --- INPUT BARU: Durasi Cicilan (Hanya untuk tipe Cicilan) --- --}}
                        <div class="mb-3" id="duration_months_group">
                            <label for="duration_months" class="form-label">Durasi Cicilan (Bulan) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="duration_months" name="duration_months" min="1" placeholder="Contoh: 12">
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


    <!-- Modal Detail & Riwayat Setoran -->
    <div class="modal fade" id="modalDetailTabungan" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailModalTitle">Detail: ...</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-3">Tipe: <strong id="detailSavingType"></strong></p>
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card-stat">
                                <h5>Cicilan Bulanan</h5>
                                <span class="amount text-success" id="detailInstallmentAmount">Rp 0</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card-stat">
                                <h5>Total Tabungan</h5>
                                <span class="amount text-primary" id="detailTotalTabungan">Rp 0</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card-stat">
                                <h5>Sisa Target</h5>
                                <span class="amount text-danger" id="detailSisaTarget">Rp 0</span>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">Riwayat Setoran</h6>
                        <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahSetoran">
                            <i class="bi bi-plus"></i> Tambah Setoran
                        </button>
                    </div>

                    <table class="table table-sm table-striped">
                        <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Nominal</th>
                            <th>Aksi</th>
                        </tr>
                        </thead>
                        <tbody id="tabelRiwayatSetoran">
                        </tbody>
                    </table>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal Tambah Setoran -->
    <div class="modal fade" id="modalTambahSetoran" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="formTambahSetoran" onsubmit="return false;">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Setoran</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="tambah_setoran_id_tabungan" name="id_tabungan_hewan_qurban">
                        <div class="mb-3">
                            <label class="form-label">Tanggal Setor <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal" class="form-control" required value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nominal Setoran <span class="text-danger">*</span></label>
                            <input type="number" name="nominal" class="form-control" required min="1" placeholder="0">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Setoran</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/tabungan_qurban.js') }}"></script>
@endpush
