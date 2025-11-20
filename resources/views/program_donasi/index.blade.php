@extends('layouts.app')

@section('title', 'Manajemen Program Donasi')

@section('content')
{{-- PENTING: Meta CSRF Token wajib ada di head, kita taruh sini buat jaga-jaga --}}
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container-fluid p-4">
    
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold">Manajemen Program Donasi</h4>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalTambahProgram">
            <i class="bi bi-plus-circle"></i> Tambah Program
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Tabel Daftar Program --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Gambar</th>
                            <th>Judul Program</th>
                            <th>Target Dana</th>
                            <th>Terkumpul</th>
                            <th>Persentase</th>
                            <th class="text-center" style="min-width: 180px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($programDonasi as $key => $p)
                        @php
                            $terkumpul = $p->donasi->sum('nominal');
                            $persen = $p->target_dana > 0 ? ($terkumpul / $p->target_dana) * 100 : 0;
                        @endphp
                        <tr>
                            <td>{{ $loop->iteration + $programDonasi->firstItem() - 1 }}</td>
                            <td>
                                @if($p->gambar)
                                    {{-- Menampilkan gambar --}}
                                    <img src="{{ asset('storage/'.$p->gambar) }}" alt="Img" class="rounded border" width="60" height="40" style="object-fit: cover;">
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="fw-bold">{{ $p->judul ?? $p->nama_program }}</td>
                            <td>Rp {{ number_format($p->target_dana, 0, ',', '.') }}</td>
                            <td class="text-success fw-bold">
                                Rp {{ number_format($terkumpul, 0, ',', '.') }}
                            </td>
                            <td>
                                <div class="progress" style="height: 5px; width: 50px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $persen }}%"></div>
                                </div>
                                <small class="text-muted">{{ round($persen) }}%</small>
                            </td>
                            <td class="text-center">
                                {{-- Tombol Input Donasi --}}
                                <button class="btn btn-sm btn-success me-1 btn-input-donasi" 
                                    data-id="{{ $p->id_program_donasi ?? $p->id }}" 
                                    data-nama="{{ $p->judul ?? $p->nama_program }}"
                                    title="Input Donasi Masuk">
                                    <i class="bi bi-wallet2"></i> + Donasi
                                </button>

                                {{-- Tombol Detail --}}
                                <button class="btn btn-sm btn-info text-white me-1 btn-detail-program" 
                                    data-id="{{ $p->id_program_donasi ?? $p->id }}" 
                                    title="Lihat Detail">
                                    <i class="bi bi-eye"></i>
                                </button>

                                {{-- Tombol Edit --}}
                                <a href="{{ route('admin.program-donasi.edit', $p->id_program_donasi ?? $p->id) }}" class="btn btn-sm btn-warning text-white me-1">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                
                                {{-- Tombol Hapus --}}
                                <form action="{{ route('admin.program-donasi.destroy', $p->id_program_donasi ?? $p->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus program ini?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Belum ada program donasi.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $programDonasi->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

</div>

{{-- ========================================== --}}
{{-- MODAL TAMBAH PROGRAM (FORM UPLOAD) --}}
{{-- ========================================== --}}
<div class="modal fade" id="modalTambahProgram" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Buat Program Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            {{-- Form ini akan di-handle oleh JavaScript (AJAX) --}}
            <form id="formTambahProgram" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Judul Program <span class="text-danger">*</span></label>
                        <input type="text" name="judul" class="form-control" placeholder="Contoh: Renovasi Masjid" required>
                    </div>
                    <div class="mb-3">
                        <label>Deskripsi <span class="text-danger">*</span></label>
                        <textarea name="deskripsi" class="form-control" rows="3" placeholder="Jelaskan programnya..." required></textarea>
                    </div>
                    <div class="mb-3">
                        <label>Target Dana (Rp) <span class="text-danger">*</span></label>
                        <input type="number" name="target_dana" class="form-control" placeholder="Contoh: 10000000" required>
                    </div>
                    {{-- Hidden field --}}
                    <input type="hidden" name="dana_terkumpul" value="0">
                    
                    <div class="mb-3">
                        <label>Batas Waktu (Tanggal Selesai) <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_selesai" class="form-control" required>
                    </div>
                     <div class="mb-3">
                        <label>Gambar Program (Opsional)</label>
                        <input type="file" name="gambar" class="form-control" accept="image/*">
                        <small class="text-muted">Format: jpg, png, jpeg. Max: 2MB</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Program</button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- MODAL INPUT DONASI --}}
<div class="modal fade" id="modalInputDonasi" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold"><i class="bi bi-wallet2"></i> Input Donasi Masuk</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formDonasiViaProgram">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-light border mb-3">
                        <small class="text-muted d-block">Donasi Untuk Program:</small>
                        <strong class="text-success fs-5" id="labelNamaProgram">-</strong>
                        <input type="hidden" name="id_program_donasi" id="inputProgramId">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Donatur</label>
                        <input type="text" name="nama_donatur" class="form-control" placeholder="Hamba Allah / Nama Jelas" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nominal (Rp)</label>
                        <input type="number" name="nominal" class="form-control" placeholder="Contoh: 50000" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal</label>
                        <input type="date" name="tanggal_donasi" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="2" placeholder="Doa atau catatan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Simpan Donasi</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL DETAIL --}}
<div class="modal fade" id="modalDetailProgram" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="modalTitleDetail">Detail Program</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-4 mt-2">
                    <div class="col-6">
                        <small class="text-muted fw-bold text-uppercase">Total Terkumpul</small>
                        <h3 class="fw-bold text-primary mt-1" id="detailTotalTerkumpul">Rp 0</h3>
                    </div>
                    <div class="col-6 border-start">
                        <small class="text-muted fw-bold text-uppercase">Sisa Target</small>
                        <h3 class="fw-bold text-danger mt-1" id="detailSisaTarget">Rp 0</h3>
                    </div>
                </div>
                <h6 class="fw-bold mb-3">Riwayat Donasi Masuk</h6>
                <div class="table-responsive border rounded">
                    <table class="table table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Tanggal</th>
                                <th>Nama Donatur</th>
                                <th class="text-end">Nominal</th>
                            </tr>
                        </thead>
                        <tbody id="tableBodyRiwayat">
                            <tr><td colspan="3" class="text-center">Memuat...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


{{-- SCRIPT JAVASCRIPT --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
{{-- Optional: Tambahkan SweetAlert2 untuk notifikasi cantik --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {

    // --- PENTING: SETUP CSRF TOKEN UNTUK SEMUA AJAX ---
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    const formatRupiah = (number) => {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(number);
    };

    // 1. SUBMIT FORM TAMBAH PROGRAM (FIX CSRF & FILE UPLOAD)
    $('#formTambahProgram').submit(function(e){
        e.preventDefault(); // Mencegah reload halaman standar
        
        let btn = $(this).find('button[type="submit"]');
        btn.text('Menyimpan...').prop('disabled', true);

        // Pakai FormData agar File Gambar terkirim
        let formData = new FormData(this);

        $.ajax({
            url: "{{ route('admin.program-donasi.store') }}",
            type: "POST",
            data: formData,
            contentType: false, // WAJIB untuk upload file
            processData: false, // WAJIB untuk upload file
            success: function(res){
                // Tutup Modal
                $('#modalTambahProgram').modal('hide');
                // Tampilkan Pesan Sukses (Pake Alert Biasa atau SweetAlert)
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Program donasi berhasil ditambahkan!',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    location.reload(); // Refresh halaman
                });
            },
            error: function(xhr){
                console.log(xhr.responseText);
                let errorMsg = 'Gagal menyimpan data.';
                
                // Cek kalau error validasi Laravel
                if(xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    errorMsg = Object.values(errors)[0][0]; // Ambil pesan error pertama
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: errorMsg
                });
                
                btn.text('Simpan Program').prop('disabled', false);
            }
        });
    });

    // 2. KLIK TOMBOL "+ Donasi"
    $('.btn-input-donasi').on('click', function() {
        let id = $(this).data('id');
        let nama = $(this).data('nama');

        $('#inputProgramId').val(id);
        $('#labelNamaProgram').text(nama);
        $('#formDonasiViaProgram')[0].reset();
        $('input[name="tanggal_donasi"]').val(new Date().toISOString().split('T')[0]);
        $('#modalInputDonasi').modal('show');
    });

    // 3. SUBMIT FORM DONASI VIA AJAX
    $('#formDonasiViaProgram').submit(function(e){
        e.preventDefault();
        
        let btn = $(this).find('button[type="submit"]');
        btn.text('Menyimpan...').prop('disabled', true);

        $.ajax({
            url: "{{ route('admin.transaksi-donasi.store') }}",
            type: "POST",
            data: $(this).serialize(), // Kalau cuma teks, serialize aman
            success: function(res){
                $('#modalInputDonasi').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Alhamdulillah',
                    text: res.message,
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            },
            error: function(xhr){
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Gagal menyimpan donasi. Periksa inputan.'
                });
                btn.text('Simpan Donasi').prop('disabled', false);
            }
        });
    });

    // 4. KLIK TOMBOL MATA (DETAIL)
    $('.btn-detail-program').on('click', function() {
        let id = $(this).data('id');
        let url = "{{ route('admin.program-donasi.index') }}/" + id;

        $('#modalTitleDetail').text('Memuat...');
        $('#detailTotalTerkumpul').text('...');
        $('#detailSisaTarget').text('...');
        $('#tableBodyRiwayat').html('<tr><td colspan="3" class="text-center">Loading...</td></tr>');
        $('#modalDetailProgram').modal('show');

        $.ajax({
            url: url,
            type: 'GET',
            success: function(response) {
                if(response.status == 'success') {
                    let d = response.data;
                    $('#modalTitleDetail').text('Detail: ' + d.nama_program);
                    $('#detailTotalTerkumpul').text(formatRupiah(d.total_terkumpul));
                    $('#detailSisaTarget').text(formatRupiah(d.sisa_target));

                    let rows = '';
                    if(d.riwayat.length > 0) {
                        d.riwayat.forEach(function(item) {
                            let date = new Date(item.created_at).toLocaleDateString('id-ID');
                            rows += `<tr><td>${date}</td><td>${item.nama_donatur}</td><td class="text-end fw-bold">${formatRupiah(item.nominal)}</td></tr>`;
                        });
                    } else {
                        rows = '<tr><td colspan="3" class="text-center text-muted">Belum ada donasi.</td></tr>';
                    }
                    $('#tableBodyRiwayat').html(rows);
                }
            }
        });
    });

});
</script>

@endsection