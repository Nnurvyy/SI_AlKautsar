@extends('layouts.public')

@section('title', 'Tabungan Qurban Saya')

@push('styles')
<style>
    /* Desain ini sudah mobile-first. */

    .donasi-title-heading {
        font-family: 'Poppins', sans-serif; 
        font-weight: 700;
        font-size: 1.8rem;
        color: #333;
        margin-bottom: 0.5rem;
    }
    /* Card Header Info User */
    .user-summary-card {
        background: linear-gradient(45deg, #0d6efd, #0b5ed7);
        border: none;
        border-radius: 12px;
        color: white;
    }
    .user-summary-card .total-label {
        font-size: 0.9rem;
        font-weight: 300;
        opacity: 0.8;
        margin-bottom: 0;
    }
    .user-summary-card .total-amount {
        font-size: 2.25rem;
        font-weight: 700;
        letter-spacing: -1px;
    }
    .user-summary-card .user-name {
        font-size: 1.1rem;
        font-weight: 600;
        opacity: 0.9;
    }

    /* Card Detail Qurban (Sekarang jadi 1 card saja) */
    .qurban-card-detail {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.06);
        border: 1px solid #eee;
        margin-bottom: 1.5rem;
    }
    
    .qurban-card-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        border-bottom: 1px solid #f0f0f0;
    }

    /* (BARU) Wrapper untuk ikon-ikon */
    .hewan-icon-wrapper {
        display: flex;
        gap: 0.5rem; /* Jarak antar ikon */
    }
    .hewan-icon {
        font-size: 2.0rem; /* Sedikit lebih kecil agar muat */
        padding: 0.6rem;
        border-radius: 12px;
    }
    .icon-kambing { color: #fd7e14; background-color: #fff4e6; }
    .icon-sapi { color: #0d6efd; background-color: #e7f0ff; }
    
    .qurban-card-header .hewan-title {
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 0;
    }
    
    .status-badge {
        font-size: 0.8rem;
        font-weight: 600;
        padding: 0.3em 0.7em;
        border-radius: 50px;
    }
    .status-menabung {
        color: #0d6efd;
        background-color: #e7f0ff;
    }
    .status-lunas {
        color: #198754;
        background-color: #e8f5e9;
    }

    /* Bagian body card (progress & stats) */
    .qurban-card-body {
        padding: 1rem;
    }
    
    .stats-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.5rem;
    }
    .stats-row .label {
        font-size: 0.9rem;
        color: #6c757d;
    }
    .stats-row .amount {
        font-size: 0.9rem;
        font-weight: 600;
        color: #212529;
    }

    .progress {
        height: 10px;
        border-radius: 10px;
    }
    .progress-bar {
        background-color: #198754; /* Warna hijau lunas */
    }

    /* (BARU) Box untuk Tenor & Sisa Bulan */
    .tenor-box {
        display: grid;
        grid-template-columns: 1fr 1fr; /* Bagi jadi 2 kolom */
        gap: 1rem;
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 1rem;
        text-align: center;
        margin-top: 1rem;
    }
    .tenor-box .tenor-item .label {
        font-size: 0.85rem;
        color: #6c757d;
        display: block;
        margin-bottom: 0.25rem;
    }
    .tenor-box .tenor-item .value {
        font-size: 1.1rem;
        font-weight: 700;
        color: #212529;
    }
    .tenor-box .tenor-item .value .bi {
        font-size: 1rem;
        vertical-align: -1px;
    }

    .kekurangan-box {
        background-color: #fff9e6; /* Kuning muda */
        border: 1px solid #ffeeba;
        border-radius: 8px;
        padding: 0.75rem;
        text-align: center;
        margin-top: 1rem;
    }
    .kekurangan-box .kekurangan-label {
        font-size: 0.9rem;
        color: #664d03;
        margin-bottom: 0;
    }
    .kekurangan-box .kekurangan-amount {
        font-size: 1.25rem;
        font-weight: 700;
        color: #d9534f; /* Merah */
    }

    /* Tabel Riwayat (Penting untuk mobile) */
    .table-responsive-qurban {
        max-height: 250px; 
        overflow-y: auto;
        border: 1px solid #eee;
        border-radius: 8px;
    }
    .table-qurban {
        margin-bottom: 0; 
    }
    .table-qurban th {
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        color: #6c757d;
        background-color: #f9f9f9;
        position: sticky; 
        top: 0; 
    }
    .table-qurban td {
        font-size: 0.9rem;
        vertical-align: middle;
    }
    .table-qurban .nominal {
        font-weight: 600;
        color: #198754; /* Hijau */
    }

</style>
@endpush

@section('content')

{{-- DATA DUMMY DIMULAI DARI SINI --}}
@php
    $namaUser = 'Muhammad Fulan';

    // Data dummy mentah (seperti yang Anda miliki)
    $hewanQurbanList = [
        (object)[
            'jenis' => 'kambing', 'status' => 'Menabung', 'target' => 3500000, 'terkumpul' => 3000000,
            'riwayatSetoran' => [
                (object)['tanggal' => '2025-01-10', 'nominal' => 1000000],
                (object)['tanggal' => '2025-02-15', 'nominal' => 1000000],
                (object)['tanggal' => '2025-03-05', 'nominal' => 1000000],
            ]
        ],
        (object)[
            'jenis' => 'sapi', 'status' => 'Menabung', 'target' => 3500000, 'terkumpul' => 500000,
            'riwayatSetoran' => [
                (object)['tanggal' => '2025-03-01', 'nominal' => 500000],
            ]
        ],
        (object)[
            'jenis' => 'kambing', 'status' => 'Lunas', 'target' => 3000000, 'terkumpul' => 3000000,
            'riwayatSetoran' => [
                (object)['tanggal' => '2024-10-10', 'nominal' => 1000000],
                (object)['tanggal' => '2024-11-10', 'nominal' => 1000000],
                (object)['tanggal' => '2024-12-10', 'nominal' => 1000000],
                (object)['tanggal' => '2025-01-10', 'nominal' => 500000],
                (object)['tanggal' => '2025-01-15', 'nominal' => 500000],
            ]
        ]
    ];

    // (BARU) Mengolah data dummy menjadi data ringkasan
    $totalTerkumpul = collect($hewanQurbanList)->sum('terkumpul');
    $totalTarget = collect($hewanQurbanList)->sum('target');
    $totalKekurangan = $totalTarget - $totalTerkumpul;
    $persentase = ($totalTarget > 0) ? ($totalTerkumpul / $totalTarget) * 100 : 0;
    
    $jumlahKambing = collect($hewanQurbanList)->where('jenis', 'kambing')->count();
    $jumlahSapi = collect($hewanQurbanList)->where('jenis', 'sapi')->count();
    
    // Membuat string "2 Kambing, 1 Sapi"
    $ringkasanHewan = [];
    if ($jumlahKambing > 0) $ringkasanHewan[] = "{$jumlahKambing} Kambing";
    if ($jumlahSapi > 0) $ringkasanHewan[] = "{$jumlahSapi} Sapi";
    $ringkasanHewanString = implode(', ', $ringkasanHewan); // "2 Kambing, 1 Sapi"
    
    // Data dummy tenor
    $tenorBulan = 10;
    $sisaBulan = 3; // (misal 10 bulan - 7 bulan sudah nabung)

    // Menggabungkan semua riwayat setoran dan mengurutkan
    $semuaRiwayatSetoran = collect($hewanQurbanList)
                                ->pluck('riwayatSetoran') // Ambil array riwayatSetoran
                                ->flatten(1) // Gabungkan jadi satu array
                                ->sortByDesc('tanggal'); // Urutkan dari terbaru
@endphp
{{-- DATA DUMMY BERAKHIR DI SINI --}}


<div class="p-3">

    <div class="container pt-4 pb-2">
        <h2 class="donasi-title-heading">
            Tabungan Qurban Saya
        </h2>
    </div>

    {{-- 1. KARTU INFO USER (Total terkumpul dari data olahan) --}}
    <div class="card user-summary-card p-3 mb-4">
        <span class="user-name mb-2">{{ $namaUser }}</span>
        <span class="total-label">Total Aset Qurban Anda</span>
        <span class="total-amount">Rp {{ number_format($totalTerkumpul, 0, ',', '.') }}</span>
    </div>

    <h6 class="fw-bold mb-3">Ringkasan Tabungan Anda</h6>
    
    {{-- 2. KARTU DETAIL (SEKARANG HANYA 1) --}}
    <div class="qurban-card-detail">
        {{-- HEADER KARTU: IKON, JENIS HEWAN, STATUS --}}
        <div class="qurban-card-header">
            
            {{-- (BARU) Menampilkan ikon berdasarkan jumlah --}}
            <div class="hewan-icon-wrapper">
                @if($jumlahKambing > 0)
                    <i class="bi bi-sheep hewan-icon icon-kambing"></i>
                @endif
                @if($jumlahSapi > 0)
                    <i class="bi bi-cow hewan-icon icon-sapi"></i>
                @endif
            </div>
            
            <div style="flex: 1;">
                {{-- (BARU) Menampilkan ringkasan string --}}
                <h5 class="hewan-title">{{ $ringkasanHewanString }}</h5>
                <small class="text-muted">Total Target: Rp {{ number_format($totalTarget, 0, ',', '.') }}</small>
            </div>
            
            <div class="text-end">
                {{-- (BARU) Status berdasarkan total kekurangan --}}
                <span class="status-badge {{ $totalKekurangan > 0 ? 'status-menabung' : 'status-lunas' }}">
                    {{ $totalKekurangan > 0 ? 'Menabung' : 'Lunas' }}
                </span>
            </div>
        </div>

        {{-- BODY KARTU: PROGRESS, STATS, KEKURANGAN --}}
        <div class="qurban-card-body">
            
            <div class="stats-row">
                <span class="label">Terkumpul</span>
                <span class="amount">Rp {{ number_format($totalTerkumpul, 0, ',', '.') }}</span>
            </div>

            <div class="progress mb-2" role="progressbar" aria-valuenow="{{ $persentase }}" aria-valuemin="0" aria-valuemax="100">
                <div class="progress-bar" style="width: {{ $persentase }}%"></div>
            </div>

            <div class="stats-row">
                <span class="label fst-italic">{{ number_format($persentase, 1) }}% tercapai</span>
                <span class="label">Target</span>
            </div>

            {{-- (BARU) BOX TENOR & SISA BULAN --}}
            <div class="tenor-box">
                <div class="tenor-item">
                    <span class="label">Tenor</span>
                    <span class="value">
                        <i class="bi bi-calendar-check me-1"></i>{{ $tenorBulan }} Bulan
                    </span>
                </div>
                <div class="tenor-item">
                    <span class="label">Sisa Waktu</span>
                    <span class="value">
                        <i class="bi bi-hourglass-split me-1"></i>{{ $sisaBulan }} Bulan
                    </span>
                </div>
            </div>

            {{-- Box kekurangan (berdasarkan total) --}}
            @if($totalKekurangan > 0)
            <div class="kekurangan-box">
                <span class="kekurangan-label">Total Kekurangan</span>
                <div class="kekurangan-amount">
                    Rp {{ number_format($totalKekurangan, 0, ',', '.') }}
                </div>
            </div>
            @endif

            {{-- (BARU) TABEL RIWAYAT (HANYA 1) --}}
            <h6 class="fw-bold mt-4 mb-2">Semua Riwayat Setoran</h6>
            <div class="table-responsive-qurban">
                <table class="table table-striped table-qurban">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Tanggal</th>
                            <th scope="col" class="text-end">Nominal</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- (BARU) Loop ke data gabungan --}}
                        @forelse($semuaRiwayatSetoran as $setoran)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ \Carbon\Carbon::parse($setoran->tanggal)->format('d M Y') }}</td>
                            <td class="text-end nominal">
                                + Rp {{ number_format($setoran->nominal, 0, ',', '.') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted p-3">
                                Belum ada riwayat setoran.
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