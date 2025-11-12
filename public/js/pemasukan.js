$(document).ready(function() {
    // Setup CSRF Token untuk keamanan setiap Request AJAX
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // =========================================================================
    // 1. LOGIKA TAMBAH KATEGORI (AJAX)
    // =========================================================================
    $('#formKategoriAjax').on('submit', function(e) {
        e.preventDefault(); // Mencegah reload halaman
        
        let form = $(this);
        let url = form.attr('action');
        let data = form.serialize();

        $.ajax({
            type: "POST",
            url: url,
            data: data,
            success: function(response) {
                // 1. Tampilkan Alert Sukses
                showAlert('success', response.message);

                // 2. Reset Input Form
                form[0].reset();

                // 3. Tambah Baris Baru ke Tabel Kategori (Tanpa Reload)
                let newRow = `
                    <tr>
                        <td>${response.data.nama_kategori_pemasukan}</td>
                        <td class="text-center">
                            <button class="btn btn-xs btn-danger" disabled>
                                <i class="bi bi-trash"></i> (Reload utk hapus)
                            </button>
                        </td>
                    </tr>
                `;
                $('#tableBodyKategori').prepend(newRow);

                // 4. Tambah juga ke Dropdown di Modal Pemasukan (Real-time update)
                let newOption = new Option(response.data.nama_kategori_pemasukan, response.data.id_kategori_pemasukan);
                $('#selectKategoriPemasukan').append(newOption);
            },
            error: function(xhr) {
                let err = xhr.responseJSON;
                let errMsg = err && err.message ? err.message : 'Gagal menambahkan kategori.';
                showAlert('danger', errMsg);
            }
        });
    });

    // =========================================================================
    // 2. LOGIKA TAMBAH PEMASUKAN (AJAX)
    // =========================================================================
    $('#formPemasukanAjax').on('submit', function(e) {
        e.preventDefault();
        
        let form = $(this);
        let url = form.attr('action');
        let data = form.serialize();
        let btn = $('#btnSimpanPemasukan');

        // Ubah tombol jadi loading
        btn.prop('disabled', true).text('Menyimpan...');

        $.ajax({
            type: "POST",
            url: url,
            data: data,
            success: function(response) {
                // 1. Tutup Modal
                $('#modalTambahPemasukan').modal('hide');
                
                // 2. Reset Form
                form[0].reset();

                // 3. Tampilkan Alert Sukses
                showAlert('success', response.message);

                // 4. Format Rupiah & Tanggal untuk tampilan JS
                let rupiah = new Intl.NumberFormat('id-ID', { 
                    style: 'currency', 
                    currency: 'IDR', 
                    minimumFractionDigits: 0 
                }).format(response.data.nominal);
                
                let date = new Date(response.data.tanggal);
                let tglString = date.toLocaleDateString('id-ID', { 
                    day: 'numeric', month: 'long', year: 'numeric' 
                });

                // 5. Tambah Baris Baru ke Tabel Pemasukan (Paling Atas)
                let newRow = `
                    <tr class="table-success"> <td>Baru</td>
                        <td class="col-nowrap">${tglString}</td>
                        <td>
                            <span class="badge bg-info text-dark">
                                ${response.data.kategori_pemasukan.nama_kategori_pemasukan}
                            </span>
                        </td>
                        <td>${response.data.deskripsi ? response.data.deskripsi : '-'}</td>
                        <td class="text-end text-custom-green fw-bold col-nowrap">${rupiah}</td>
                        <td class="text-center">
                            <span class="badge bg-secondary">Reload utk aksi</span>
                        </td>
                    </tr>
                `;
                
                $('#tableBodyPemasukan').prepend(newRow);
            },
            error: function(xhr) {
                showAlert('danger', 'Gagal menyimpan data. Pastikan semua kolom terisi.');
                console.log(xhr.responseText);
            },
            complete: function() {
                // Kembalikan tombol seperti semula
                btn.prop('disabled', false).text('Simpan');
            }
        });
    });

    // =========================================================================
    // HELPER: Fungsi Menampilkan Alert
    // =========================================================================
    function showAlert(type, message) {
        let alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        $('#alert-area').html(alertHtml);
        
        // Hilang otomatis setelah 3 detik
        setTimeout(function() {
            $('.alert').alert('close');
        }, 3000);
    }
});