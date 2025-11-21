@extends('layouts.public')

@section('title', 'Tabungan Qurban Saya')

@push('styles')
<style>
    /* ... Style CSS Sama Persis seperti sebelumnya ... */
    .donasi-title-heading { font-family: 'Poppins', sans-serif; font-weight: 700; font-size: 1.8rem; color: #333; margin-bottom: 0.5rem; }
    .donasi-title-sub { font-size: 1rem; color: #6c757d; margin-bottom: 1rem; }
    .user-summary-card { background: linear-gradient(45deg, #0d6efd, #0b5ed7); border: none; border-radius: 12px; color: white; }
    .user-summary-card .total-label { font-size: 0.9rem; font-weight: 300; opacity: 0.8; margin-bottom: 0; }
    .user-summary-card .total-amount { font-size: 2.25rem; font-weight: 700; letter-spacing: -1px; }
    .user-summary-card .user-name { font-size: 1.1rem; font-weight: 600; opacity: 0.9; }
    .qurban-card-detail { background: #ffffff; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.06); border: 1px solid #eee; margin-bottom: 1.5rem; }
    .qurban-card-header { display: flex; align-items: center; gap: 1rem; padding: 1rem; border-bottom: 1px solid #f0f0f0; }
    .hewan-icon-wrapper { display: flex; gap: 0.5rem; }
    .hewan-icon { font-size: 2.0rem; padding: 0.6rem; border-radius: 12px; }
    .icon-kambing { color: #fd7e14; background-color: #fff4e6; }
    .icon-sapi { color: #0d6efd; background-color: #e7f0ff; }
    .qurban-card-header .hewan-title { font-size: 1.25rem; font-weight: 700; margin-bottom: 0; }
    .status-badge { font-size: 0.8rem; font-weight: 600; padding: 0.3em 0.7em; border-radius: 50px; }
    .status-menabung { color: #0d6efd; background-color: #e7f0ff; }
    .status-lunas { color: #198754; background-color: #e8f5e9; }
    .qurban-card-body { padding: 1rem; }
    .stats-row { display: flex; justify-content: space-between; margin-bottom: 0.5rem; }
    .stats-row .label { font-size: 0.9rem; color: #6c757d; }
    .stats-row .amount { font-size: 0.9rem; font-weight: 600; color: #212529; }
    .progress { height: 10px; border-radius: 10px; }
    .progress-bar { background-color: #198754; }
    .tenor-box { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; background-color: #f8f9fa; border-radius: 8px; padding: 1rem; text-align: center; margin-top: 1rem; }
    .tenor-box .tenor-item .label { font-size: 0.85rem; color: #6c757d; display: block; margin-bottom: 0.25rem; }
    .tenor-box .tenor-item .value { font-size: 1.1rem; font-weight: 700; color: #212529; }
    .kekurangan-box { background-color: #fff9e6; border: 1px solid #ffeeba; border-radius: 8px; padding: 0.75rem; text-align: center; margin-top: 1rem; }
    .kekurangan-box .kekurangan-label { font-size: 0.9rem; color: #664d03; margin-bottom: 0; }
    .kekurangan-box .kekurangan-amount { font-size: 1.25rem; font-weight: 700; color: #d9534f; }
    .table-responsive-qurban { max-height: 250px; overflow-y: auto; border: 1px solid #eee; border-radius: 8px; }
    .table-qurban { margin-bottom: 0; }
    .table-qurban th { font-size: 0.8rem; font-weight: 600; text-transform: uppercase; color: #6c757d; background-color: #f9f9f9; position: sticky; top: 0; }
    .table-qurban td { font-size: 0.9rem; vertical-align: middle; }
    .table-qurban .nominal { font-weight: 600; color: #198754; }
    @media (min-width: 992px) { .qurban-desktop-grid { display: grid; grid-template-columns: 1fr 400px; gap: 2rem; align-items: start; } }
</style>
@endpush

@section('content')

{{-- CEK LOGIN: Jika belum login, tampilkan Modal dan Overlay Blur --}}
@if(!Auth::guard('jamaah')->check())
    
    {{-- Tampilan Blur di belakang modal --}}
    <div style="filter: blur(5px); pointer-events: none; user-select: none;">
        <div class="container pt-4 pb-3">
            <h2 class="donasi-title-heading">Tabungan Qurban Saya</h2>
            <p class="donasi-title-sub">Fitur khusus untuk Jamaah terdaftar.</p>
        </div>
    </div>

    {{-- Modal Login --}}
    <div class="modal fade show" id="modalLoginRequired" tabindex="-1" aria-modal="true" role="dialog" style="display: block; background: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center p-4 border-0 shadow rounded-4">
                <div class="mb-3">
                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                        <i class="bi bi-lock-fill fs-1 text-primary"></i>
                    </div>
                </div>
                <h4 class="fw-bold mb-2">Akses Terbatas</h4>
                <p class="text-muted mb-4">
                    Fitur Tabungan Qurban hanya dapat diakses oleh Jamaah yang sudah masuk (Login).
                </p>
                <div class="d-grid gap-2">
                    <a href="{{ route('login') }}" class="btn btn-primary btn-lg fw-semibold">
                        Login Sekarang
                    </a>
                    <a href="{{ route('public.landing') }}" class="btn btn-outline-secondary btn-lg fw-semibold">
                        Kembali ke Beranda
                    </a>
                </div>
            </div>
        </div>
    </div>

@else
    {{-- JIKA SUDAH LOGIN: TAMPILKAN KONTEN --}}

    <div class="container pt-4 pb-3"> 
        <div class="row"> 
            <div class="col-12"> 
                <h2 class="donasi-title-heading">Tabungan Qurban Saya</h2> 
                <p class="donasi-title-sub">Pantau progress tabungan qurban Anda secara real-time.</p>
            </div> 
        </div>
    </div>

    <div class="container">
        <div class="qurban-desktop-grid">
            <div class="qurban-main-content">
                
                {{-- 1. KARTU INFO USER --}} 
                <div class="card user-summary-card p-3 mb-4"> 
                    <span class="user-name mb-2">{{ $user->name }}</span>
                    <span class="total-label">Total Aset Qurban Anda</span>
                    <span class="total-amount">Rp {{ number_format($totalTerkumpul, 0, ',', '.') }}</span>
                </div>

                @if($hewanQurbanList->isEmpty())
                    <div class="alert alert-info text-center py-4">
                        <i class="bi bi-info-circle fs-1 mb-3 d-block"></i>
                        <h5 class="fw-bold">Belum Ada Tabungan</h5>
                        <p class="mb-0">Anda belum terdaftar dalam program tabungan qurban. Silakan hubungi pengurus masjid untuk mendaftar.</p>
                    </div>
                @else
                    <h6 class="fw-bold mb-3">Detail Tabungan</h6>
                    
                    {{-- LOOPING DATA TABUNGAN DARI DATABASE --}}
                    @foreach($hewanQurbanList as $tabungan)
                        @php
                            $terkumpul = $tabungan->pemasukanTabunganQurban->sum('nominal');
                            $target = $tabungan->total_harga_hewan_qurban;
                            $kekurangan = $target - $terkumpul;
                            $persen = ($target > 0) ? ($terkumpul / $target) * 100 : 0;
                            
                            // Hitung sisa bulan
                            $sisaBulan = '-';
                            if($tabungan->saving_type == 'cicilan' && $tabungan->duration_months) {
                                $bulanBerjalan = \Carbon\Carbon::parse($tabungan->created_at)->diffInMonths(\Carbon\Carbon::now());
                                $sisa = $tabungan->duration_months - $bulanBerjalan;
                                $sisaBulan = $sisa > 0 ? $sisa . ' Bulan' : 'Selesai';
                            }
                        @endphp

                        <div class="qurban-card-detail">
                            {{-- HEADER KARTU --}}
                            <div class="qurban-card-header">
                                <div class="hewan-icon-wrapper">
                                    @if(in_array($tabungan->nama_hewan, ['kambing', 'domba']))
                                        <i class="bi bi-sheep hewan-icon icon-kambing"></i>
                                    @else
                                        <i class="bi bi-cow hewan-icon icon-sapi"></i>
                                    @endif
                                </div>
                                
                                <div style="flex: 1;">
                                    <h5 class="hewan-title">{{ ucfirst($tabungan->nama_hewan) }} ({{ $tabungan->total_hewan }} Ekor)</h5>
                                    <small class="text-muted">Target: Rp {{ number_format($target, 0, ',', '.') }}</small>
                                </div>
                                
                                <div class="text-end">
                                    <span class="status-badge {{ $kekurangan > 0 ? 'status-menabung' : 'status-lunas' }}">
                                        {{ $kekurangan > 0 ? 'Proses' : 'Lunas' }}
                                    </span>
                                </div>
                            </div>

                            {{-- BODY KARTU --}}
                            <div class="qurban-card-body">
                                <div class="stats-row">
                                    <span class="label">Terkumpul</span>
                                    <span class="amount">Rp {{ number_format($terkumpul, 0, ',', '.') }}</span>
                                </div>

                                <div class="progress mb-2" role="progressbar" aria-valuenow="{{ $persen }}" aria-valuemin="0" aria-valuemax="100">
                                    <div class="progress-bar" style="width: {{ $persen }}%"></div>
                                </div>

                                <div class="stats-row">
                                    <span class="label fst-italic">{{ number_format($persen, 1) }}%</span>
                                </div>

                                {{-- DETAIL TENOR --}}
                                <div class="tenor-box">
                                    <div class="tenor-item">
                                        <span class="label">Tipe</span>
                                        <span class="value text-capitalize">
                                            {{ $tabungan->saving_type }}
                                        </span>
                                    </div>
                                    <div class="tenor-item">
                                        <span class="label">Sisa Waktu</span>
                                        <span class="value">
                                            <i class="bi bi-hourglass-split me-1"></i>{{ $sisaBulan }}
                                        </span>
                                    </div>
                                </div>

                                @if($kekurangan > 0)
                                <div class="kekurangan-box">
                                    <span class="kekurangan-label">Kekurangan</span>
                                    <div class="kekurangan-amount">
                                        Rp {{ number_format($kekurangan, 0, ',', '.') }}
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        
            <div class="qurban-sidebar">
                <div class="card" style="border-radius: 12px; overflow: hidden;">
                    <div class="card-header fw-bold bg-light">
                        Riwayat Transaksi Terakhir
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive-qurban" style="max-height: 550px;">
                            <table class="table table-striped table-qurban mb-0">
                                <thead>
                                    <tr>
                                        <th scope="col">Tgl</th>
                                        <th scope="col">Hewan</th>
                                        <th scope="col" class="text-end">Nominal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($semuaRiwayatSetoran as $setoran)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($setoran->tanggal)->format('d/m/y') }}</td>
                                        <td class="text-capitalize">{{ $setoran->nama_hewan_info }}</td>
                                        <td class="text-end nominal">
                                            + {{ number_format($setoran->nominal, 0, ',', '.') }}
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
        </div>
    </div>
@endif

@endsection