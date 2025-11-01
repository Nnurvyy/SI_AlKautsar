@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid p-4">

    <div class="row g-4 mb-4">

        <div class="col-md-6 col-xl-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div>
                        <p class="text-muted mb-1">Total Pemasukan</p>
                        <h5 class="fw-bold mb-0">Rp 2.000.000.000</h5>
                    </div>
                    <div class="stat-card-icon bg-custom-green-light text-custom-green">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div>
                        <p class="text-muted mb-1">Total Pengeluaran</p>
                        <h5 class="fw-bold mb-0">Rp 0</h5>
                    </div>
                    <div class="stat-card-icon bg-custom-red-light text-custom-red">
                        <i class="bi bi-graph-down-arrow"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div>
                        <p class="text-muted mb-1">Total Tabungan Qurban</p>
                        <h5 class="fw-bold mb-0">Rp 2.000.000</h5>
                    </div>
                    <div class="stat-card-icon bg-custom-yellow-light text-custom-yellow">
                        <i class="bi bi-person-check"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div>
                        <p class="text-muted mb-1">Saldo</p>
                        <h5 class="fw-bold mb-0">Rp 2.000.001</h5>
                    </div>
                    <div class="stat-card-icon bg-custom-blue-light text-custom-blue">
                        <i class="bi bi-wallet2"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card transaction-table dashboard-table border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold">Transaksi Terbaru</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">Tanggal</th>
                            <th scope="col">Tipe</th>
                            <th scope="col">Kategori</th>
                            <th scope="col">Deskripsi</th>
                            <th scope="col" class="text-end">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>22/10/2025</td>
                            <td><span class="badge bg-custom-green-light text-custom-green">Pemasukan</span></td>
                            <td>Donasi</td>
                            <td>Donasi</td>
                            <td class="text-end text-custom-green fw-bold">+ Rp 1.000.000</td>
                        </tr>
                        <tr>
                            <td>22/10/2025</td>
                            <td><span class="badge bg-custom-green-light text-custom-green">Pemasukan</span></td>
                            <td>SPP</td>
                            <td>SPP Santri</td>
                            <td class="text-end text-custom-green fw-bold">+ Rp 1.000.000</td>
                        </tr>
                        <tr>
                            <td>22/10/2025</td>
                            <td><span class="badge bg-custom-green-light text-custom-green">Pemasukan</span></td>
                            <td>Infaq</td>
                            <td>pemasukan infaq</td>
                            <td class="text-end text-custom-green fw-bold">+ Rp 1</td>
                        </tr>
                        <tr>
                            <td>21/10/2025</td>
                            <td><span class="badge bg-custom-red-light text-custom-red">Pengeluaran</span></td>
                            <td>Listrik</td>
                            <td>Bayar listrik bulan Oktober</td>
                            <td class="text-end text-custom-red fw-bold">- Rp 500.000</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection