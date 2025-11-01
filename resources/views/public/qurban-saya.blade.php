@extends('layouts.public')

@section('title', 'Tabungan Qurban Saya')

@push('styles')
<style>
    .qurban-card {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        border: 1px solid #eee;
    }
    .hewan-icon {
        font-size: 2.5rem;
        padding: 0.75rem;
        border-radius: 12px;
    }
    .icon-kambing { color: #fd7e14; background-color: #fff4e6; }
    .icon-sapi { color: #0d6efd; background-color: #e7f0ff; }
</style>
@endpush

@section('content')
<div class="feature-header d-flex align-items-center p-3">
    <a href="{{ route('public.landing') }}" class="btn-back me-3">
        <i class="bi bi-arrow-left"></i>
    </a>
    <span class="title">Tabungan Qurban</span>
</div>

<div class="p-3">
    <div class="card text-center p-3 mb-4 shadow-sm" style="background-color: #e8f5e9; border: 0;">
        <h6 class="text-muted mb-0">Total Tabungan Anda</h6>
        <h2 class="fw-bold text-success">Rp {{ number_format($totalTabungan ?? 0, 0, ',', '.') }}</h2>
    </div>

    <h6 class="fw-bold mb-3">Hewan Qurban Anda</h6>
    
    @forelse($hewanQurban as $hewan)
        <div class="qurban-card d-flex align-items-center gap-3 p-3 mb-3">
            @if($hewan->jenis == 'kambing')
                <i class="bi bi-sheep hewan-icon icon-kambing"></i>
            @else
                <i class="bi bi-cow hewan-icon icon-sapi"></i> @endif

            <div style="flex: 1;">
                <h6 class="fw-bold mb-0 text-capitalize">{{ $hewan->jenis }}</h6>
                <p class="text-muted mb-0">
                    Status: <span class="fw-medium text-dark">{{ $hewan->status }}</span>
                </p>
            </div>
            
            <div class="text-end">
                <small class="text-muted d-block">Terkumpul</small>
                <span class="fw-bold">{{ number_format($hewan->terkumpul, 0, ',', '.') }}</span>
            </div>
        </div>
    @empty
        <div class="alert alert-info text-center">
            Anda belum memiliki tabungan hewan qurban.
        </div>
    @endforelse
</div>
@endsection