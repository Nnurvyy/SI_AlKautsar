@extends('layouts.public')

@section('title', 'Detail Donasi')

@section('content')
<div class="container py-4">
    <h4 class="fw-bold mb-3">Detail Program Donasi</h4>

    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="fw-bold">{{ $program->nama_program }}</h5>
            <p>{{ $program->deskripsi }}</p>

            <p class="mt-3"><strong>Target:</strong> Rp {{ number_format($program->target, 0, ',', '.') }}</p>
            <p><strong>Dana Terkumpul:</strong> Rp {{ number_format($program->terkumpul, 0, ',', '.') }}</p>

            <a href="#" class="btn btn-success w-100 mt-3">Donasi Sekarang</a>
        </div>
    </div>
</div>
@endsection
