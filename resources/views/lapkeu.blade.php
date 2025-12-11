@extends('layouts.app')

@section('title', 'Laporan Keuangan')

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="container-fluid p-4">

        {{-- CARD FILTER --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold"><i class="bi bi-filter me-2"></i>Filter Laporan</h5>
            </div>
            <div class="card-body">
                {{-- Form Filter ID --}}
                <form id="formFilterLaporan">
                    <div class="row g-3 align-items-end mb-3">
                        {{-- 1. Select Tipe Transaksi --}}
                        <div class="col-md-3">
                            <label class="form-label fw-bold small">Tipe Transaksi</label>
                            <select id="tipe_transaksi" name="tipe_transaksi" class="form-select">
                                <option value="semua">Pemasukan & Pengeluaran</option>
                                <option value="pemasukan">Pemasukan Saja</option>
                                <option value="pengeluaran">Pengeluaran Saja</option>
                            </select>
                        </div>

                        {{-- 2. Select Periode --}}
                        <div class="col-md-3">
                            <label class="form-label fw-bold small">Periode Waktu</label>
                            <select id="filter-periode" name="periode" class="form-select">
                                <option value="semua">Semua Waktu</option>
                                <option value="per_bulan">Per Bulan</option>
                                <option value="per_tahun">Per Tahun</option>
                                <option value="rentang_waktu">Rentang Waktu</option>
                            </select>
                        </div>

                        {{-- A. Filter Bulan --}}
                        <div class="col-md-4 filter-option" id="filter-bulanan" style="display: none;">
                            <label class="form-label fw-bold small">Pilih Bulan & Tahun</label>
                            <div class="input-group">
                                <select name="bulan" class="form-select">
                                    @foreach (range(1, 12) as $m)
                                        <option value="{{ $m }}" {{ date('n') == $m ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::create()->month($m)->isoFormat('MMMM') }}
                                        </option>
                                    @endforeach
                                </select>
                                <select name="tahun_bulanan" class="form-select">
                                    @foreach (range(date('Y'), 2020) as $y)
                                        <option value="{{ $y }}">{{ $y }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- B. Filter Tahun --}}
                        <div class="col-md-3 filter-option" id="filter-tahunan" style="display: none;">
                            <label class="form-label fw-bold small">Pilih Tahun</label>
                            <select name="tahun_tahunan" class="form-select">
                                @foreach (range(date('Y'), 2020) as $y)
                                    <option value="{{ $y }}">{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- C. Filter Rentang Waktu --}}
                        <div class="col-md-5 filter-option" id="filter-rentang" style="display: none;">
                            <label class="form-label fw-bold small">Rentang Tanggal</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">Dari</span>
                                <input type="date" name="tanggal_mulai" class="form-control"
                                    value="{{ date('Y-m-01') }}">
                                <span class="input-group-text bg-light">S/d</span>
                                <input type="date" name="tanggal_akhir" class="form-control" value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            {{-- Tombol Aksi --}}
                            <button type="button" id="btnTerapkanFilter" class="btn btn-primary me-2">
                                <i class="bi bi-search me-1"></i> Terapkan Filter
                            </button>
                            <button type="submit" formaction="{{ route('pengurus.lapkeu.export.pdf') }}"
                                formtarget="_blank" class="btn btn-danger">
                                <i class="bi bi-file-earmark-pdf me-1"></i> Export PDF
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>


        {{-- TABEL --}}
        <div class="card transaction-table border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">Data Transaksi</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="tabelLaporan">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center">Tanggal</th>
                                <th class="text-center">Tipe</th>
                                <th class="text-center">Kategori</th>
                                <th>Deskripsi</th>
                                <th class="text-end">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>

                {{-- PAGINATION CONTAINER --}}
                <div id="paginationContainer" class="d-flex justify-content-between align-items-center mt-3">
                    <span id="paginationInfo"></span>
                    <nav id="paginationLinks"></nav>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- Load Script Baru --}}
    <script src="{{ asset('js/laporan.js') }}"></script>
@endpush
