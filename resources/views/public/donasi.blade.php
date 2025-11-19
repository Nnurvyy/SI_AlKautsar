@extends('layouts.public')

@section('content')

<style>
    .donasi-card {
        background: #fff;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 10px rgba(0,0,0,0.08);
        transition: 0.3s;
    }
    .donasi-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 6px 14px rgba(0,0,0,0.15);
    }
    .donasi-card img {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }
    .donasi-title {
        font-size: 20px;
        font-weight: 700;
    }
    .donasi-desc {
        font-size: 14px;
        color: #666;
        height: 60px;
        overflow: hidden;
    }
    .btn-donasi {
        background: #1e90ff;
        color: white;
        padding: 8px 0;
        border-radius: 8px;
        font-weight: 600;
    }
</style>

<div class="container py-5">

    <h2 class="fw-bold mb-4 text-center">Program Donasi</h2>

    <div class="row g-4">

        @forelse ($programs as $item)
            <div class="col-md-4">
                <div class="donasi-card p-3">

                    {{-- Gambar --}}
                    <img src="{{ $item->gambar_url }}" alt="gambar {{ $item->judul }}">

                    {{-- Konten --}}
                    <div class="mt-3">

                        <div class="donasi-title">{{ $item->judul }}</div>

                        <p class="donasi-desc">
                            {{ Str::limit($item->deskripsi, 120) }}
                        </p>

                        {{-- Jika kamu belum punya halaman detail, pakai "#" --}}
                        <a href="#"
                           class="btn btn-donasi w-100 mt-2">
                            Donasi Sekarang
                        </a>

                    </div>

                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <h5 class="text-muted">Belum ada program donasi.</h5>
            </div>
        @endforelse

    </div>

</div>

@endsection
