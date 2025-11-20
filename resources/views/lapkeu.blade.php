@extends('layouts.app')

@section('title', 'Laporan Keuangan')

@section('content')

<div class="container-fluid p-4">

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold">
                <i class="bi bi-filter me-2"></i>Filter Laporan
            </h5>
        </div>
        <div class="card-body">
            
            <form action="{{ route('pengurus.lapkeu.export.pdf') }}" method="GET" target="_blank">
                
                <div class="row g-3 align-items-end mb-3">
                    
                    <div class="col-md-3">
                        <label for="tipe_transaksi" class="form-label">Tipe Transaksi</label>
                        <select id="tipe_transaksi" name="tipe_transaksi" class="form-select">
                            <option value="semua" selected>Pemasukan & Pengeluaran</option>
                            <option value="pemasukan">Pemasukan Saja</option>
                            <option value="pengeluaran">Pengeluaran Saja</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="filter-periode" class="form-label">Periode Waktu</label>
                        <select id="filter-periode" name="periode" class="form-select">
                            <option value="semua" selected>Semua</option>
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
                                    <option value="1">Januari</option>
                                    <option value="2">Februari</option>
                                    <option value="3">Maret</option>
                                    <option value="4">April</option>
                                    <option value="5">Mei</option>
                                    <option value="6">Juni</option>
                                    <option value="7">Juli</option>
                                    <option value="8">Agustus</option>
                                    <option value="9">September</option>
                                    <option value="10">Oktober</option>
                                    <option value="11">November</option>
                                    <option value="12">Desember</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label for="tahun_bulanan" class="form-label">Tahun</label>
                                <select id="tahun_bulanan" name="tahun_bulanan" class="form-select">
                                    <option value="2025">2025</option>
                                    <option value="2024">2024</option>
                                    <option value="2023">2023</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3" id="filter-tahunan" style="display: none;">
                        <label for="tahun_tahunan" class="form-label">Tahun</label>
                        <select id="tahun_tahunan" name="tahun_tahunan" class="form-select">
                            <option value="2025">2025</option>
                            <option value="2024">2024</option>
                            <option value="2023">2023</option>
                        </select>
                    </div>

                    <div class="col-md-4" id="filter-rentang" style="display: none;">
                        <div class="row g-2">
                            <div class="col-6">
                                <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
                                <input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai">
                            </div>
                            <div class="col-6">
                                <label for="tanggal_akhir" class="form-label">Tanggal Akhir</label>
                                <input type="date" class="form-control" id="tanggal_akhir" name="tanggal_akhir">
                            </div>
                        </div>
                    </div>

                </div> <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-danger me-2">
                            <i class="bi bi-file-earmark-pdf me-1"></i>
                            Export PDF
                        </button>
                    </div>
                </div> </form>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card stat-card">
                <div class="card-body">
                    <div>
                        <p class="text-muted mb-1">Total Pemasukan</p>
                        <h4 class="fw-bold mb-0 text-custom-green">Rp 0</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card">
                <div class="card-body">
                    <div>
                        <p class="text-muted mb-1">Total Pengeluaran</p>
                        <h4 class="fw-bold mb-0 text-custom-red">Rp 0</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card">
                <div class="card-body">
                    <div>
                        <p class="text-muted mb-1">Saldo</p>
                        <h4 class="fw-bold mb-0 text-custom-blue">Rp 0</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card transaction-table border-0 shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold">Data Transaksi - Semua Periode</h5>
            <small class="text-muted">0 transaksi</small> 
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" style="width: 10%;">Tanggal</th>
                            <th scope="col" style="width: 10%;">Tipe</th>
                            <th scope="col" style="width: 15%;">Kategori</th>
                            <th scope="col" style="width: 15%;">Divisi</th>
                            <th scope="col">Deskripsi</th>
                            <th scope="col" style="width: 15%;" class="text-end">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="6" class="text-center text-muted p-4">
                                Tidak ada data transaksi untuk periode ini.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        const periodeFilter = document.getElementById('filter-periode');
        const filterBulanan = document.getElementById('filter-bulanan');
        const filterTahunan = document.getElementById('filter-tahunan');
        const filterRentang = document.getElementById('filter-rentang'); 

        function toggleFilterVisibility() {
            const selectedValue = periodeFilter.value;

            // Sembunyikan semua dulu
            filterBulanan.style.display = 'none';
            filterTahunan.style.display = 'none';
            filterRentang.style.display = 'none'; 

            // Tampilkan yang sesuai
            if (selectedValue === 'per_bulan') {
                filterBulanan.style.display = 'block';
            } else if (selectedValue === 'per_tahun') {
                filterTahunan.style.display = 'block';
            } else if (selectedValue === 'rentang_waktu') { 
                filterRentang.style.display = 'block';
            }
        }

        periodeFilter.addEventListener('change', toggleFilterVisibility);
        toggleFilterVisibility();
    });
</script>
@endpush