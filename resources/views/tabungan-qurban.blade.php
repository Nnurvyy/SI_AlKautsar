@extends('layouts.app')

@section('title', 'Tabungan Qurban')

@push('styles')
    {{-- CSS Kustom untuk status dan modal --}}
    <style>
        .badge.bg-danger { background-color: #dc3545 !important; }
        .badge.bg-success { background-color: #198754 !important; }
        .badge.bg-primary { background-color: #0d6efd !important; }

        /* CSS Modal Scroll */
        #modalTabungan .modal-dialog, #modalDetailTabungan .modal-dialog { max-height: 80vh; }
        #modalTabungan .modal-body, #modalDetailTabungan .modal-body { overflow-y: auto; max-height: 70vh; }

        /* Fix z-index modal bertumpuk */
        #modalTambahSetoran { z-index: 1060; }
        #modalDetailTabungan { z-index: 1050; }
        #modalTabungan { z-index: 1050; }

        /* CSS Kustom untuk Statistik */
        .card-stat { background-color: #f8f9fa; border-radius: .5rem; padding: 1rem; text-align: center; }
        .card-stat h5 { font-size: 0.9rem; color: #6c757d; margin-bottom: .5rem; }
        .card-stat .amount { font-size: 1.5rem; font-weight: 700; }

        /* CSS untuk Pagination */
        #paginationLinks .pagination { margin-bottom: 0; }
        #paginationLinks .page-item.disabled .page-link { background-color: #e9ecef; }
        #paginationLinks .page-item.active .page-link { z-index: 1; } /* fix z-index */
        #paginationLinks .page-link { cursor: pointer; }
    </style>
@endpush

@section('content')

    {{--
        Template untuk mengisi dropdown pengguna di JS.
        Ini tidak akan ditampilkan.
    --}}
    <template id="penggunaListTemplate">
        <option value="">-- Pilih User --</option>
        @if(isset($penggunaList))
            @foreach ($penggunaList as $pengguna)
                <option value="{{ $pengguna->id_pengguna }}">{{ $pengguna->nama }}</option>
            @endforeach
        @endif
    </template>


    <div class="container-fluid p-4">

        {{--
          BAGIAN 1: FILTER LAPORAN PDF (Pola LapKeu)
        --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-filter me-2"></i>Filter Laporan PDF
                </h5>
            </div>
            <div class="card-body">
                {{-- Form ini menargetkan rute PDF baru --}}
                <form action="{{ route('admin.tabungan-qurban.cetakPdf') }}" method="GET" target="_blank">
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

                        {{-- Filter dinamis (sama seperti lapkeu) --}}
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


        {{--
          BAGIAN 2: TABEL DATA (Pola KhotibJumat)
        --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <div class="d-flex flex-wrap align-items-center">
                {{-- Filter Status Baru --}}
                <div class="me-2 mb-2 mb-md-0">
                    <select class="form-select" id="statusFilter" style="width: 200px;">
                        <option value="semua" selected>Semua Status</option>
                        <option value="lunas">Lunas</option>
                        <option value="menunggak">Menunggak</option>
                        <option value="bayar_bulan_ini">Sudah Bayar Bulan Ini</option>
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
                    {{-- Tabel ini sekarang dikontrol oleh JS Vanilla --}}
                    <table class="table table-hover align-middle" id="tabelTabungan">
                        <thead class="table-light">
                        <tr>
                            <th scope="col" style="width: 5%;" class="text-center">No</th>
                            <th scope="col">Nama Penabung & Hewan</th>
                            <th scope="col" class="text-end">Total Target</th>
                            {{-- Tombol Sortir Baru --}}
                            <th scope="col" class="text-end" id="sortTotalTerkumpul" style="cursor:pointer;">
                                Total Terkumpul <i id="sortIcon" class="bi bi-arrow-down"></i>
                            </th>
                            <th scope="col" class="text-end">Sisa Target</th>
                            <th scope="col" class="text-center">Status</th>
                            <th scope="col" style="width: 12%;" class="text-center">Aksi</th>
                        </tr>
                        </thead>
                        <tbody>
                        {{-- Data dimuat lewat JS --}}
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


    {{--
      BAGIAN 3: MODAL-MODAL
    --}}

    <!-- Modal Form Tambah/Edit Tabungan (Pola Khotib) -->
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
                            <label for="id_pengguna" class="form-label">Nama User <span class="text-danger">*</span></label>
                            {{-- Dropdown ini akan diisi oleh JS dari template --}}
                            <select id="id_pengguna" name="id_pengguna" class="form-select" required>
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
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- Modal Detail & Riwayat Setoran (Pola Lama) -->
    <div class="modal fade" id="modalDetailTabungan" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailModalTitle">Detail: ...</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card-stat">
                                <h5>Total Tabungan</h5>
                                <span class="amount text-primary" id="detailTotalTabungan">Rp 0</span>
                            </div>
                        </div>
                        <div class="col-md-6">
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
                        {{-- Data dimuat oleh JS --}}
                        </tbody>
                    </table>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal Tambah Setoran (Pola Lama) -->
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
    {{-- Hapus jQuery & DataTables, ganti dengan JS Vanilla baru --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Arahkan ke nama file JS yang sudah benar -->
    <script src="{{ asset('js/tabungan_qurban.js') }}"></script>
@endpush
