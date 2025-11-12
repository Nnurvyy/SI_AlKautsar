$(document).ready(function() {
    // CSRF Token
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    // --- 1. AJAX TAMBAH KATEGORI ---
    $('#formKategoriAjax').on('submit', function(e) {
        e.preventDefault();
        let form = $(this);
        let csrfToken = $('meta[name="csrf-token"]').attr('content'); // Ambil token
        
        $.ajax({
            type: "POST",
            url: form.attr('action'),
            data: form.serialize(),
            success: function(response) {
                showAlert('success', response.message);
                form[0].reset();

                // PERBAIKAN: Buat form Hapus dinamis
                let newKatId = response.data.id_kategori_pengeluaran;
                let katDeleteUrl = `/admin/kategori-pengeluaran/${newKatId}`;

                let newRow = `
                    <tr>
                        <td class="ps-3">${response.data.nama_kategori_pengeluaran}</td>
                        <td class="text-center">
                            <form action="${katDeleteUrl}" method="POST" onsubmit="return confirm('Hapus kategori ini?');">
                                <input type="hidden" name="_token" value="${csrfToken}">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="btn btn-xs btn-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>`;
                $('#tableBodyKategori').prepend(newRow);

                // Tambah ke Dropdown
                $('#selectKategoriPengeluaran').append(new Option(response.data.nama_kategori_pengeluaran, response.data.id_kategori_pengeluaran));
            },
            error: function(xhr) {
                showAlert('danger', 'Gagal menambah kategori.');
            }
        });
    });

    // --- 2. AJAX TAMBAH PENGELUARAN ---
    $('#formPengeluaranAjax').on('submit', function(e) {
        e.preventDefault();
        let form = $(this);
        let btn = $('#btnSimpanPengeluaran');
        let csrfToken = $('meta[name="csrf-token"]').attr('content'); // Ambil token
        
        btn.prop('disabled', true).text('Menyimpan...');

        $.ajax({
            type: "POST",
            url: form.attr('action'),
            data: form.serialize(),
            success: function(response) {
                $('#modalTambahPengeluaran').modal('hide');
                form[0].reset();
                showAlert('success', response.message);

                // Format Rupiah
                let rupiah = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(response.data.nominal);
                let date = new Date(response.data.tanggal);
                let tglString = date.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });

                // PERBAIKAN: Buat tombol Aksi dinamis
                let newId = response.data.id_pengeluaran;
                let editUrl = `/admin/pengeluaran/${newId}/edit`;
                let deleteUrl = `/admin/pengeluaran/${newId}`;

                let actionHtml = `
                    <td class="text-center col-nowrap">
                        <a href="${editUrl}" class="btn btn-sm me-1" title="Edit">
                            <i class="bi bi-pencil text-primary fs-6"></i>
                        </a>
                        <form action="${deleteUrl}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                            <input type="hidden" name="_token" value="${csrfToken}">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="btn btn-sm" title="Hapus">
                                <i class="bi bi-trash text-danger fs-6"></i>
                            </button>
                        </form>
                    </td>
                `;

                let newRow = `
                    <tr class="table-danger"> <!-- Highlight Merah -->
                        <td>Baru</td>
                        <td class="col-nowrap">${tglString}</td>
                        <td><span class="badge bg-warning text-dark">${response.data.kategori_pengeluaran.nama_kategori_pengeluaran}</span></td>
                        <td>${response.data.deskripsi || '-'}</td>
                        <td class="text-end text-danger fw-bold col-nowrap">${rupiah}</td>
                        ${actionHtml}
                    </tr>`;
                $('#tableBodyPengeluaran').prepend(newRow);
            },
            error: function(xhr) {
                showAlert('danger', 'Gagal menyimpan. Cek inputan.');
            },
            complete: function() {
                btn.prop('disabled', false).text('Simpan');
            }
        });
    });

    function showAlert(type, message) {
        let html = `<div class="alert alert-${type} alert-dismissible fade show">${message}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>`;
        $('#alert-area').html(html);
        setTimeout(() => $('.alert').alert('close'), 3000);
    }
});