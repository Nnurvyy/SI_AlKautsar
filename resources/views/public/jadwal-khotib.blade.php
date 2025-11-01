@extends('layouts.public')

@section('title', 'Jadwal Khutbah Jumat')

@push('styles')
<style>
    .khotib-list-card {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        border: 1px solid #eee;
    }
    .khotib-avatar {
        width: 80px;
        height: 80px;
        border-radius: 12px;
        object-fit: cover;
    }
    .tanggal-badge {
        font-size: 0.8rem;
        font-weight: 600;
        padding: 0.5em 0.75em;
        background-color: #e7f0ff;
        color: #0d6efd;
        border-radius: 8px;
    }
</style>
@endpush

@section('content')
<div class="feature-header d-flex align-items-center p-3">
    <a href="{{ route('public.landing') }}" class="btn-back me-3">
        <i class="bi bi-arrow-left"></i>
    </a>
    <span class="title">Jadwal Khutbah Jumat</span>
</div>

<div class="p-3">
    @forelse($jadwalKhotib as $khotib)
        <div class="khotib-list-card d-flex gap-3 p-3 mb-3">
            <img src="{{ $khotib->foto_url }}" alt="Foto {{ $khotib->nama_khotib }}" class="khotib-avatar">
            <div style="flex: 1;">
                <h6 class="fw-bold mb-1">{{ $khotib->nama_khotib }}</h6>
                <p class="text-muted mb-2" style="font-size: 0.9rem;">
                    Tema: "{{ $khotib->tema_khutbah }}"
                </p>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="tanggal-badge">
                        {{ \Carbon\Carbon::parse($khotib->tanggal)->format('d M Y') }}
                    </span>
                    <span class="text-muted" style="font-size: 0.8rem;">Imam: {{ $khotib->nama_imam }}</span>
                </div>
            </div>
        </div>
    @empty
        <div class="alert alert-info text-center">
            Belum ada jadwal khutbah yang tersedia.
        </div>
    @endforelse
</div>
@endsection