{{-- Menggunakan layout app.blade.php Anda --}}
@extends('layouts.app')

@section('title', 'Tabungan Hewan Qurban')

{{-- Import CSS DataTables, FontAwesome, dll --}}
@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <style>
        /* CSS Kustom untuk Statistik dan Error */
        .card-stat {
            background-color: #f8f9fa; border-radius: .5rem; padding: 1rem; text-align: center;
        }
        .card-stat h5 {
            font-size: 0.9rem; color: #6c757d; margin-bottom: .5rem;
        }
        .card-stat .amount {
            font-size: 1.5rem; font-weight: 700;
        }
        .card-stat .amount.text-success { color: #198754 !important; }
        .card-stat .amount.text-primary { color: #0d6efd !important; }
        .invalid-feedback {
            display: block; width: 100%; margin-top: .25rem; font-size: .875em; color: #dc3545;
        }
        /* Fix z-index modal bertumpuk */
        #modalTambahSetoran {
            z-index: 1060;
        }
    </style>
@endpush

{{-- Sesuaikan 'content' dengan @yield di layouts/app.blade.php --}}
@section('content')
    <div class="container-fluid pt-4">

        {{-- BARIS BARU UNTUK TOMBOL CETAK LAPORAN --}}
        <div class="d-flex justify-content-end mb-3">
            <div class="dropdown">
                <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownCetak" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-print fa-sm"></i> Cetak Laporan PDF
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownCetak">
                    <li>
                        <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('formCetakBulanIni').submit();">
                            Laporan Bulan Ini
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('formCetak30Hari').submit();">
                            Laporan 30 Hari Terakhir
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        {{-- Ini adalah baris judul dan tombol "Tambah Tabungan" Anda yang sudah ada --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">Tabungan Hewan Qurban</h1>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahTabungan">
                <i class="fas fa-plus fa-sm"></i> Tambah Tabungan
            </button>
        </div>

        {{-- Sisa dari file Anda (Card, Tabel, Modal, dll.) tetap sama --}}
        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="tabungan-datatable" width="100%" cellspacing="0"
                           data-url-datatable="{{ route('admin.tabungan-qurban.data') }}"
                           data-url-show="{{ route('admin.tabungan-qurban.show', ['tabungan_qurban' => '__ID__']) }}"
                           data-url-update="{{ route('admin.tabungan-qurban.update', ['tabungan_qurban' => '__ID__']) }}"
                           data-url-destroy="{{ route('admin.tabungan-qurban.destroy', ['tabungan_qurban' => '__ID__']) }}"
                           data-url-setoran-destroy="{{ route('admin.pemasukan-qurban.destroy', ['id' => '__ID__']) }}"
                    >
                        <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Hewan</th>
                            <th>Nama User</th>
                            <th>Total Hewan</th>
                            <th>Total Harga</th>
                            <th>Total Terkumpul</th>
                            <th>Sisa Target</th>
                            <th>Aksi</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- FORM TERSEMBUNYI UNTUK MENGIRIM PERMINTAAN CETAK --}}
    <form id="formCetakBulanIni" action="{{ route('admin.tabungan-qurban.cetak') }}" method="GET" target="_blank">
        <input type="hidden" name="type" value="bulan_ini">
    </form>
    <form id="formCetak30Hari" action="{{ route('admin.tabungan-qurban.cetak') }}" method="GET" target="_blank">
        <input type="hidden" name="type" value="30_hari">
    </form>

    <div class="modal fade" id="modalTambahTabungan" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="formTambahTabungan" data-url="{{ route('admin.tabungan-qurban.store') }}">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Tabungan Qurban Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nama User</label>
                            {{-- Variabel $penggunaList ini WAJIB dikirim dari controller --}}
                            <select id="tambah_id_pengguna" name="id_pengguna" class="form-select" required>
                                <option value="">-- Pilih User --</option>
                                @if(isset($penggunaList))
                                    @foreach ($penggunaList as $pengguna)
                                        <option value="{{ $pengguna->id_pengguna }}">{{ $pengguna->nama }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Hewan</label>
                            <select id="tambah_nama_hewan" name="nama_hewan" class="form-select" required>
                                <option value="kambing">Kambing</option>
                                <option value="domba">Domba</option>
                                <option value="sapi">Sapi</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Total Hewan</label>
                            <input type="number" id="tambah_total_hewan" name="total_hewan" class="form-control" required min="1" value="1">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Total Harga Hewan Qurban</label>
                            <input type="number" id="tambah_total_harga_hewan_qurban" name="total_harga_hewan_qurban" class="form-control" required min="0" value="0">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary" id="btnSimpanTabungan">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div class="modal fade" id="modalUpdateTabungan" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="formUpdateTabungan">
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Update Tabungan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="update_id_tabungan">
                        <div class="mb-3">
                            <label class="form-label">Nama User</label>
                            <select id="update_id_pengguna" name="id_pengguna" class="form-select" required>
                                <option value="">-- Pilih User --</option>
                                @if(isset($penggunaList))
                                    @foreach ($penggunaList as $pengguna)
                                        <option value="{{ $pengguna->id_pengguna }}">{{ $pengguna->nama }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Hewan</label>
                            <select id="update_nama_hewan" name="nama_hewan" class="form-select" required>
                                <option value="kambing">Kambing</option>
                                <option value="domba">Domba</option>
                                <option value="sapi">Sapi</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Total Hewan</label>
                            <input type="number" id="update_total_hewan" name="total_hewan" class="form-control" required min="1">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Total Harga Hewan Qurban</label>
                            <input type="number" id="update_total_harga_hewan_qurban" name="total_harga_hewan_qurban" class="form-control" required min="0">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary" id="btnUpdateTabungan">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalDetailTabungan" tabindex="-1" aria-hidden="true">
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
                            <i class="fas fa-plus"></i> Tambah Setoran
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
                    <button type="button" class="btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalTambahSetoran" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="formTambahSetoran" data-url="{{ route('admin.pemasukan-qurban.store') }}">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Setoran</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="tambah_setoran_id_tabungan" name="id_tabungan_hewan_qurban" value="">

                        <div class="mb-3">
                            <label class="form-label">Tanggal Setor</label>
                            <input type="date" name="tanggal" class="form-control" required value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nominal Setoran</label>
                            <input type="number" name="nominal" class="form-control" required min="0" placeholder="0">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="btnSimpanSetoran">Simpan Setoran</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- Muat JS DataTables, SweetAlert, dan JS kustom Anda --}}
    {{-- PENTING: Pastikan layout Anda memuat jQuery SEBELUM ini --}}
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{--
        Pastikan Anda memanggil file JS yang sudah di-refactor
        (yang membaca URL dari atribut data-* di tag <table>)
    --}}
    <script src="{{ asset('js/tabungan_qurban.js') }}"></script>
@endpush
