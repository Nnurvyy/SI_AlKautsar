@extends('layouts.public')

@section('title', 'Jadwal Kajian')

@push('styles')
<style>
    /* Style ini saya salin dari jadwal-khotib.blade.php */
    .kajian-list-card {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        border: 1px solid #eee;
    }
    .kajian-avatar {
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
    <span class="title">Jadwal Kajian</span>
</div>

<div class="p-3">
    {{-- Kita gunakan variabel $jadwalKajian dari controller --}}
    @forelse($jadwalKajian as $kajian)
        <div class="kajian-list-card d-flex gap-3 p-3 mb-3">
            {{-- Ganti foto_url jika nama accessor di Model Kajian berbeda --}}
            <img src="{{ $kajian->foto_url ?? asset('images/default.png') }}" alt="Foto {{ $kajian->nama_penceramah }}" class="kajian-avatar">
            <div style="flex: 1;">
                <h6 class="fw-bold mb-1">{{ $kajian->nama_penceramah }}</h6>
                <p class="text-muted mb-2" style="font-size: 0.9rem;">
                    Tema: "{{ $kajian->tema_kajian }}"
                </p>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="tanggal-badge">
                        {{-- Sesuaikan format tanggal --}}
                        {{ \Carbon\Carbon::parse($kajian->tanggal_kajian)->format('d M Y') }}
                        @if($kajian->waktu_kajian)
                            , {{ \Carbon\Carbon::parse($kajian->waktu_kajian)->format('H:i') }}
                        @endif
                    </span>
                </div>
            </div>
        </div>
    @empty
        <div class="alert alert-info text-center">
            Belum ada jadwal kajian yang tersedia.
        </div>
    @endforelse
</div>
@endsection