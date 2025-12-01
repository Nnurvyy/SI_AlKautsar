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
            {{-- Form Filter ID ganti jadi formFilterLaporan --}}
            <form id="formFilterLaporan"> 
                <div class="row g-3 align-items-end mb-3">
                    {{-- Select Tipe --}}
                    <div class="col-md-3">
                        <label class="form-label">Tipe Transaksi</label>
                        <select id="tipe_transaksi" name="tipe_transaksi" class="form-select">
                            <option value="semua">Pemasukan & Pengeluaran</option>
                            <option value="pemasukan">Pemasukan Saja</option>
                            <option value="pengeluaran">Pengeluaran Saja</option>
                        </select>
                    </div>

                    {{-- Select Periode (Logika hide/show elemen tetap pakai JS lama atau gabung) --}}
                    <div class="col-md-3">
                        <label class="form-label">Periode Waktu</label>
                        <select id="filter-periode" name="periode" class="form-select">
                            <option value="semua">Semua</option>
                            <option value="per_bulan">Per Bulan</option>
                            <option value="per_tahun">Per Tahun</option>
                            <option value="rentang_waktu">Rentang Waktu</option>
                        </select>
                    </div>
                    
                    {{-- Filter Dinamis (Hidden/Show handled by JS) --}}
                    <div class="col-md-4" id="filter-bulanan" style="display: none;">
                        <div class="row g-2">
                            <div class="col-6">
                                <select name="bulan" class="form-select">
                                    @foreach(range(1, 12) as $m)
                                    <option value="{{ $m }}">{{ \Carbon\Carbon::create()->month($m)->isoFormat('MMMM') }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6">
                                <select name="tahun_bulanan" class="form-select">
                                    <option value="2025">2025</option>
                                    <option value="2024">2024</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    {{-- ... Tambahkan div filter-tahunan dan filter-rentang sesuai file lama ... --}}
                </div> 

                <div class="row">
                    <div class="col-12">
                        {{-- Button Type Button agar tidak reload --}}
                        <button type="button" id="btnTerapkanFilter" class="btn btn-primary me-2">
                            <i class="bi bi-search me-1"></i> Terapkan Filter
                        </button>
                        {{-- Export PDF tetap Submit Form biasa (buka tab baru) --}}
                        <button type="submit" formaction="{{ route('pengurus.lapkeu.export.pdf') }}" formtarget="_blank" class="btn btn-danger">
                            <i class="bi bi-file-earmark-pdf me-1"></i> Export PDF
                        </button>
                    </div>
                </div> 
            </form>
        </div>
    </div>

    {{-- CARD STATISTIK (Bisa statis dari load awal, atau mau di update via JS juga opsional) --}}
    {{-- Untuk simpelnya, biarkan statistik diload server-side saat pertama buka, tabelnya yang ajax --}}
    
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
                        {{-- KOSONGKAN INI, AKAN DIISI JS --}}
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