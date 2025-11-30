@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid p-4">

    {{-- Kartu Statistik --}}
    <div class="row g-4 mb-4">

        {{-- Total Pemasukan --}}
        <div class="col-md-6 col-xl-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div>
                        <p class="text-muted mb-1">Total Pemasukan</p>
                        <h5 class="fw-bold mb-0">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</h5>
                    </div>
                    <div class="stat-card-icon bg-success bg-opacity-10 text-success">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Pengeluaran --}}
        <div class="col-md-6 col-xl-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div>
                        <p class="text-muted mb-1">Total Pengeluaran</p>
                        <h5 class="fw-bold mb-0">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</h5>
                    </div>
                    <div class="stat-card-icon bg-danger bg-opacity-10 text-danger">
                        <i class="bi bi-graph-down-arrow"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Penabung Qurban --}}
        <div class="col-md-6 col-xl-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div>
                        <p class="text-muted mb-1">Total Tabungan Qurban</p>
                        <h5 class="fw-bold mb-0">{{ $totalPenabungQurban }}</h5>
                    </div>
                    <div class="stat-card-icon bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-person-check"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Saldo Akhir --}}
        <div class="col-md-6 col-xl-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div>
                        <p class="text-muted mb-1">Saldo Saat Ini</p>
                        <h5 class="fw-bold mb-0 {{ $saldo < 0 ? 'text-danger' : 'text-primary' }}">
                            Rp {{ number_format($saldo, 0, ',', '.') }}
                        </h5>
                    </div>
                    <div class="stat-card-icon bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-wallet2"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel Transaksi Terbaru --}}
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
                        @forelse($recentTransactions as $item)
                            @php
                                $isPemasukan = $item->tipe == 'pemasukan';
                                
                                // Logika Warna Badge (Label Tipe)
                                $badgeClass = $isPemasukan ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger';
                                
                                // Logika Warna Teks Nominal (Hijau/Merah)
                                $textClass = $isPemasukan ? 'text-success' : 'text-danger';
                                
                                $symbol = $isPemasukan ? '+' : '-';
                                $kategori = $item->kategori ? $item->kategori->nama_kategori_keuangan : '-';
                            @endphp
                            <tr>
                                <td>
                                    {{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d M Y') }}
                                </td>
                                <td>
                                    <span class="badge {{ $badgeClass }}">
                                        {{ ucfirst($item->tipe) }}
                                    </span>
                                </td>
                                <td>{{ $kategori }}</td>
                                <td>
                                    <span class="d-inline-block text-truncate" style="max-width: 250px;">
                                        {{ $item->deskripsi ?? '-' }}
                                    </span>
                                </td>
                                <td class="text-end {{ $textClass }} fw-bold">
                                    {{ $symbol }} Rp {{ number_format($item->nominal, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    Belum ada transaksi.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection