@extends('layouts.app')

@section('title', 'Barang Inventaris ')

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="container-fluid p-4">
        
        {{-- Tombol Kembali ke Dashboard Inventaris Master --}}
        <div class="mb-3">
            <a href="{{ route('pengurus.inventaris.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Barang Master
            </a>
        </div>

        {{-- HEADER & SEARCH --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <h4 class="fw-bold text-dark">Detail Unit: {{ $barangMaster->nama_barang ?? 'Loading...' }}
                <span class="text-muted small">({{ $barangMaster->kode ?? '' }})</span>
            </h4>
            <div class="d-flex flex-wrap align-items-center gap-2">

                {{-- Search Bar --}}
                <div class="input-group" style="width: 300px;">
                    <span class="input-group-text bg-white border-end-0 rounded-start-pill ps-3"><i
                            class="bi bi-search text-muted"></i></span>
                    <input type="text" id="searchInput" class="form-control border-start-0 rounded-end-pill"
                        placeholder="Cari kode unit (AD-01)...">
                </div>
                
                {{-- Filter Kondisi (BARU) --}}
                <select id="filterKondisi" class="form-select rounded-pill-input" style="width: 150px;">
                    <option value="all" selected>Semua Kondisi</option>
                    <option value="Baik">Baik</option>
                    <option value="Perlu Perbaikan">Perlu Perbaikan</option>
                    <option value="Rusak Berat">Rusak Berat</option>
                </select>
            </div>

            {{-- Tombol Tambah --}}
            <button class="btn btn-gradient-green rounded-pill px-4 shadow-sm" data-bs-toggle="modal"
                data-bs-target="#modalInventarisDetail" id="btnTambahUnit">
                <i class="bi bi-plus-lg me-2"></i> Tambah Unit
            </button>
        </div>

        {{-- CARD TOTAL UNIT (Disarankan) --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-bold text-dark mb-0">Total Unit Aset</h5>
                    <small class="text-muted">Jumlah unit fisik {{ $barangMaster->nama_barang ?? '' }} yang terdaftar</small><br>
                    <small class="text-muted">Lihat deskripsi barang pada fitur Edit</small>
                </div>
                {{-- Angka Total Stock dari Barang Master --}}
                <h3 class="fw-bold text-success mb-0">
                    {{ number_format($barangMaster->total_stock ?? 0, 0, ',', '.') }} Unit
                </h3>
            </div>
        </div>

        {{-- TABEL UTAMA (Data Detail) --}}
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="tabelInventarisDetail">
                        <thead class="bg-light">
                            <tr style="height: 50px;">
                                <th class="text-center ps-4 rounded-top-left" style="width: 5%;">No</th>
                                <th style="width: 15%;">Kode Inventaris</th>
                                <th style="width: 15%;">Lokasi</th>
                                <th class="text-center" style="width: 15%;">Tanggal Masuk</th>
                                <th class="text-center" style="width: 15%;">Kondisi</th>
                                <th class="text-center" style="width: 15%;">Status</th>
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

    {{-- MODAL TAMBAH / EDIT DETAIL UNIT --}}
    <div class="modal fade" id="modalInventarisDetail" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg modal-rounded">

                {{-- Header Clean --}}
                <div class="modal-header border-0 pb-0 pt-4 px-4 bg-white">
                    <div>
                        <h5 class="modal-title fw-bold text-dark" id="modalInventarisDetailLabel">Unit Inventaris</h5>
                        <p class="text-muted small mb-0">Kelola detail unit aset: <span class="fw-bold">{{ $barangMaster->nama_barang ?? '' }}</span></p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4 bg-white">
                    <form id="formInventarisDetail">
                        {{-- ID Barang Master, Wajib di-Pass --}}
                        <input type="hidden" id="id_barang_master" name="id_barang" value="{{ $barangMaster->id_barang ?? '' }}">
                        {{-- ID Detail untuk Edit --}}
                        <input type="hidden" id="id_detail_barang" name="id_detail_barang">

                        {{-- WRAPPER HIJAU --}}
                        <div class="donation-card-wrapper">
                            
                            {{-- Lokasi --}}
                            <div class="mb-3">
                                <label class="form-label fw-bold text-success small text-uppercase ls-1">Lokasi Unit</label>
                                <input type="text" class="form-control rounded-pill-input" id="lokasi"
                                    name="lokasi" placeholder="Contoh: Ruang Rapat A" required>
                            </div>

                            {{-- Kondisi & Status --}}
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <label class="form-label fw-bold text-success small text-uppercase ls-1">Kondisi</label>
                                    <select class="form-select rounded-pill-input" id="kondisi" name="kondisi" required>
                                        <option value="" disabled selected>Pilih...</option>
                                        <option value="Baik">Baik</option>
                                        <option value="Perlu Perbaikan">Perlu Perbaikan</option>
                                        <option value="Rusak Berat">Rusak Berat</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-bold text-success small text-uppercase ls-1">Status</label>
                                    <select class="form-select rounded-pill-input" id="status" name="status" required>
                                        <option value="" disabled selected>Pilih...</option>
                                        <option value="Tersedia">Tersedia</option>
                                        <option value="Dipinjam">Dipinjam</option>
                                        <option value="Perbaikan">Perbaikan</option>
                                        <option value="Dihapus">Dihapus</option>
                                    </select>
                                </div>
                            </div>

                            {{-- Tanggal Masuk (Opsional, jika ingin diisi manual/diubah) --}}
                            <div class="mb-3">
                                <label class="form-label fw-bold text-success small text-uppercase ls-1">Tanggal Masuk (Optional)</label>
                                <input type="date" class="form-control rounded-pill-input" id="tanggal_masuk"
                                    name="tanggal_masuk">
                                <small class="text-muted ms-3">Kosongkan jika ingin tanggal hari ini.</small>
                            </div>

                             {{-- Deskripsi/Catatan Kerusakan --}}
                            <div class="mb-4">
                                <label class="form-label fw-bold text-success small text-uppercase ls-1">Deskripsi / Catatan</label>
                                <textarea class="form-control rounded-pill-input" id="deskripsi" name="deskripsi" 
                                    placeholder="Catatan kerusakan, kondisi spesifik, atau riwayat perbaikan." rows="3"></textarea>
                            </div>

                            <div id="kloningSection" class="mb-3">
                                <label for="jumlah_kloning" class="form-label fw-bold">Gandakan Unit (Otomatis)</label>
                                <p class="form-text text-muted">Unit akan dikloning dengan lokasi, kondisi, dan deskripsi yang sama.</p>
                                <input type="number" class="form-control" id="jumlah_kloning" name="jumlah_kloning" min="0" value="0" placeholder="Masukkan jumlah unit kloning (0 = hanya 1 unit)">
                            </div>

                            {{-- Tombol --}}
                            <div class="d-grid">
                                <button type="submit" class="btn btn-gradient-green rounded-pill py-2 fw-bold shadow-sm">
                                    <i class="bi bi-box-seam me-2"></i> Simpan Unit
                                </button>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- CSS KHUSUS HALAMAN INI (Tidak Berubah) --}}
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap');

        #tabelInventarisDetail,
        .card,
        .modal-content,
        .donation-card-wrapper {
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

        .rounded-top-left {
            border-top-left-radius: 10px;
        }

        .rounded-top-right {
            border-top-right-radius: 10px;
        }

        #paginationLinks .pagination {
            margin-bottom: 0;
        }

        #paginationLinks .page-item.active .page-link {
            background-color: #198754;
            border-color: #198754;
        }

        #paginationLinks .page-link {
            cursor: pointer;
            color: #198754;
        }
    </style>

    <script>
        const BASE_API_URL = "{{ url('pengurus/barang-inventaris-detail') }}";
        console.log('BLADE DEBUG: BASE_API_URL successfully set to:', BASE_API_URL);
    </script>

    @push('scripts')

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        const BASE_API_URL = "{{ url('pengurus/barang-inventaris-detail') }}";
        <script src="{{ asset('js/inventaris-detail.js') }}"></script>
        
    @endpush
@endsection