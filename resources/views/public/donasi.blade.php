@extends('layouts.public')

@section('content')
<div class="container py-5">

    <h2 class="text-center mb-4 fw-bold">Program Donasi</h2>

    @if($programs->isEmpty())
        <div class="alert alert-info text-center">
            Belum ada program donasi tersedia.
        </div>
    @endif

    <div class="row g-4">
        @foreach($programs as $program)
            <div class="col-md-4">
                <div class="card shadow-sm rounded-4 h-100">

                    <!-- Gambar -->
                    <img src="{{ $program->gambar_url }}" 
                         class="card-img-top rounded-top-4"
                         style="height: 220px; object-fit: cover;">

                    <div class="card-body">

                        <h5 class="fw-bold">{{ $program->judul }}</h5>

                        <p class="text-muted small">
                            {{ Str::limit($program->deskripsi, 80) }}
                        </p>

                        <!-- Progress Bar -->
                        <div class="progress mb-2" style="height: 10px;">
                            <div class="progress-bar" 
                                 role="progressbar" 
                                 style="width: {{ $program->persentase }}%;"></div>
                        </div>

                        <div class="d-flex justify-content-between small text-muted mb-3">
                            <span>Terkumpul: Rp {{ number_format($program->dana_terkumpul) }}</span>
                            <span>{{ $program->persentase }}%</span>
                        </div>

                        <!-- Tanggal -->
                        <p class="small text-danger mb-2">
                            Sisa waktu: {{ $program->sisa_hari }} hari
                        </p>

                        <!-- Tombol -->
                        <a href="{{ route('donasi.detail', $program->id) }}" 
                           class="btn btn-success w-100 fw-bold rounded-pill">
                            Donasi Sekarang
                        </a>

                    </div>
                </div>
            </div>
        @endforeach
    </div>

</div>
@endsection
