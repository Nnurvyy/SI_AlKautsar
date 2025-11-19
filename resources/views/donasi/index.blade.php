@extends('layouts.app')

@section('title', 'Transaksi Donasi')

@section('content')
<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold">Input Donasi Masuk</h4>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahDonasi">
            <i class="bi bi-plus-circle"></i> Terima Donasi Baru
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}</div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Nama Donatur</th>
                            <th>Program Tujuan</th>
                            <th class="text-end">Nominal</th>
                            <th>Keterangan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transaksi as $d)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($d->tanggal_donasi)->translatedFormat('d M Y') }}</td>
                            <td class="fw-bold">{{ $d->nama_donatur }}</td>
                            <td><span class="badge bg-info text-dark">{{ $d->program->judul ?? 'Umum' }}</span></td>
                            <td class="text-end fw-bold text-success">Rp {{ number_format($d->nominal, 0, ',', '.') }}</td>
                            <td>{{ $d->keterangan }}</td>
                            <td>
                                <form action="{{ route('admin.transaksi-donasi.destroy', $d->id_donasi) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center py-4">Belum ada data donasi.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $transaksi->links('pagination::bootstrap-5') }}</div>
        </div>
    </div>
</div>

<!-- Modal Tambah Donasi -->
<div class="modal fade" id="modalTambahDonasi" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Input Donasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formDonasi">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Program Donasi</label>
                        <select name="id_program_donasi" class="form-select" required>
                            <option value="">-- Pilih Program --</option>
                            @foreach($program as $p)
                                {{-- Sesuaikan id_program_donasi atau id --}}
                                <option value="{{ $p->id_program_donasi ?? $p->id }}">{{ $p->judul ?? $p->nama_program }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Nama Donatur</label>
                        <input type="text" name="nama_donatur" class="form-control" placeholder="Hamba Allah / Nama Jelas" required>
                    </div>
                    <div class="mb-3">
                        <label>Nominal (Rp)</label>
                        <input type="number" name="nominal" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Tanggal</label>
                        <input type="date" name="tanggal_donasi" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label>Keterangan / Doa</label>
                        <textarea name="keterangan" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$('#formDonasi').submit(function(e){
    e.preventDefault();
    $.ajax({
        url: "{{ route('admin.transaksi-donasi.store') }}",
        type: "POST",
        data: $(this).serialize(),
        success: function(res){
            alert(res.message);
            location.reload();
        },
        error: function(err){
            console.log(err);
            alert('Gagal menyimpan data. Cek Console untuk detail.');
        }
    });
});
</script>
@endsection