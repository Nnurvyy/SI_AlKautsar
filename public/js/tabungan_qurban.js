// Nama file baru: public/js/tabungan_qurban_refactored.js

$(document).ready(function() {

    // Ambil CSRF token dari tag meta
    const csrfToken = $('meta[name="csrf-token"]').attr('content');

    // ==========================================================
    // AMBIL SEMUA URL DARI ATRIBUT DATA DI TABEL
    // ==========================================================
    const $dataTable = $('#tabungan-datatable');
    const urls = {
        datatable: $dataTable.data('url-datatable'),
        show: $dataTable.data('url-show'),
        update: $dataTable.data('url-update'),
        destroy: $dataTable.data('url-destroy'),
        setoranDestroy: $dataTable.data('url-setoran-destroy')
    };

    /**
     * Fungsi helper untuk mengambil URL dan mengganti placeholder ID
     * @param {string} action - Kunci dari objek urls (cth: 'show', 'update')
     * @param {string|int} id - ID yang akan dimasukkan ke URL
     * @returns {string} URL yang sudah lengkap
     */
    function getUrl(action, id) {
        if (!urls[action]) {
            console.error('URL action not found:', action);
            return '';
        }
        return urls[action].replace('__ID__', id);
    }

    // Fungsi helper untuk format Rupiah
    function formatRupiah(angka) {
        if(isNaN(parseFloat(angka))) {
            return "Rp 0";
        }
        return "Rp " + new Intl.NumberFormat('id-ID').format(angka);
    }

    // ==========================================================
    // 1. INISIALISASI DATATABLES
    // ==========================================================

    // HAPUS: const dataTableUrl = '/admin/tabungan-qurban-data';

    var dataTable = $dataTable.DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: urls.datatable, // DIGANTI
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'nama_hewan', name: 'nama_hewan' },
            { data: 'nama_user', name: 'pengguna.nama' },
            { data: 'total_hewan', name: 'total_hewan' },
            { data: 'total_harga', name: 'total_harga_hewan_qurban' },
            { data: 'total_terkumpul', name: 'total_terkumpul', orderable: false, searchable: false },
            { data: 'sisa_target', name: 'sisa_target', orderable: false, searchable: false },
            { data: 'aksi', name: 'aksi', orderable: false, searchable: false },
        ]
    });

    // ... (Fungsi helper clearValidationErrors & showValidationErrors tetap sama) ...
    function clearValidationErrors() {
        $('.invalid-feedback').remove();
    }
    function showValidationErrors(errors, formElement) {
        clearValidationErrors();
        for (const field in errors) {
            const errorMsg = errors[field][0];
            const input = $(formElement).find(`[name="${field}"]`);
            input.after(`<div class="invalid-feedback">${errorMsg}</div>`);
        }
    }


    // ==========================================================
    // 2. SIMPAN TABUNGAN BARU (Create)
    // ==========================================================
    $('#formTambahTabungan').on('submit', function(e) {
        e.preventDefault();
        clearValidationErrors();

        // URL diambil dari atribut data-url di form (Ini sudah benar)
        let url = $(this).data('url');

        let formData = {
            id_pengguna: $('#tambah_id_pengguna').val(),
            nama_hewan: $('#tambah_nama_hewan').val(),
            total_hewan: $('#tambah_total_hewan').val(),
            total_harga_hewan_qurban: $('#tambah_total_harga_hewan_qurban').val(),
        };

        $.ajax({
            url: url, // -> /admin/tabungan-qurban (dari route 'admin.tabungan-qurban.store')
            method: 'POST',
            data: formData,
            headers: {'X-CSRF-TOKEN': csrfToken},
            success: function(response) {
                Swal.fire('Berhasil!', 'Data tabungan berhasil disimpan.', 'success');
                $('#modalTambahTabungan').modal('hide');
                dataTable.ajax.reload();
                $('#formTambahTabungan')[0].reset();
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    showValidationErrors(xhr.responseJSON.errors, '#formTambahTabungan');
                } else {
                    Swal.fire('Error!', 'Terjadi kesalahan saat menyimpan.', 'error');
                }
            }
        });
    });

    // ==========================================================
    // 3. EVENT TOMBOL EDIT (Show data)
    // ==========================================================
    $dataTable.on('click', '.btn-edit', function() {
        let id = $(this).data('id');
        let url = getUrl('show', id); // DIGANTI: /admin/tabungan-qurban/{id}

        $.get(url, function(data) {
            // Isi form modal update
            $('#update_id_tabungan').val(data.id_tabungan_hewan_qurban);
            $('#update_id_pengguna').val(data.id_pengguna);
            $('#update_nama_hewan').val(data.nama_hewan);
            $('#update_total_hewan').val(data.total_hewan);
            $('#update_total_harga_hewan_qurban').val(data.total_harga_hewan_qurban);

            // Set URL action untuk form update
            let updateUrl = getUrl('update', id); // DIGANTI: /admin/tabungan-qurban/{id}
            $('#formUpdateTabungan').attr('action', updateUrl);

            // Tampilkan modal
            $('#modalUpdateTabungan').modal('show');
        });
    });

    // ==========================================================
    // 4. SIMPAN UPDATE TABUNGAN (Update)
    // ==========================================================
    $('#formUpdateTabungan').on('submit', function(e) {
        e.preventDefault();
        clearValidationErrors();

        let url = $(this).attr('action'); // -> /admin/tabungan-qurban/{id} (sudah diset di atas)
        let formData = {
            _method: 'PUT',
            id_pengguna: $('#update_id_pengguna').val(),
            nama_hewan: $('#update_nama_hewan').val(),
            total_hewan: $('#update_total_hewan').val(),
            total_harga_hewan_qurban: $('#update_total_harga_hewan_qurban').val(),
        };

        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            headers: {'X-CSRF-TOKEN': csrfToken},
            success: function(response) {
                Swal.fire('Berhasil!', 'Data tabungan berhasil diupdate.', 'success');
                $('#modalUpdateTabungan').modal('hide');
                dataTable.ajax.reload();
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    showValidationErrors(xhr.responseJSON.errors, '#formUpdateTabungan');
                } else {
                    Swal.fire('Error!', 'Terjadi kesalahan saat update.', 'error');
                }
            }
        });
    });

    // ==========================================================
    // 5. EVENT TOMBOL DELETE (Delete)
    // ==========================================================
    $dataTable.on('click', '.btn-delete', function() {
        let id = $(this).data('id');
        let url = getUrl('destroy', id); // DIGANTI: /admin/tabungan-qurban/{id}

        Swal.fire({
            title: 'Anda yakin?',
            text: "Data tabungan ini akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    method: 'POST',
                    data: { _method: 'DELETE' },
                    headers: {'X-CSRF-TOKEN': csrfToken},
                    success: function() {
                        Swal.fire('Dihapus!', 'Data berhasil dihapus.', 'success');
                        dataTable.ajax.reload();
                    },
                    error: function() {
                        Swal.fire('Error!', 'Terjadi kesalahan saat menghapus.', 'error');
                    }
                });
            }
        });
    });

    // ==========================================================
    // 6. EVENT TOMBOL DETAIL (Modal Pemasukan)
    // ==========================================================
    let currentTabunganId = null;

    $dataTable.on('click', '.btn-detail', function() {
        let id = $(this).data('id');
        currentTabunganId = id;
        let url = getUrl('show', id); // DIGANTI: /admin/tabungan-qurban/{id}

        $.get(url, function(data) {
            // Isi judul modal
            $('#detailModalTitle').text(`Detail: ${data.nama_hewan} (${data.pengguna.nama})`);

            // ... (Isi stats) ...
            let totalTerkumpul = data.pemasukan_tabungan_qurban.reduce((acc, pemasukan) => acc + parseFloat(pemasukan.nominal), 0);
            let sisaTarget = parseFloat(data.total_harga_hewan_qurban) - totalTerkumpul;

            $('#detailTotalTabungan').text(formatRupiah(totalTerkumpul));
            $('#detailSisaTarget').text(formatRupiah(sisaTarget));
            $('#detailSisaTarget').toggleClass('text-success', sisaTarget <= 0).toggleClass('text-danger', sisaTarget > 0);


            // Isi ID tabungan di form tambah setoran
            $('#tambah_setoran_id_tabungan').val(id);

            // Isi tabel riwayat setoran
            $('#tabelRiwayatSetoran').html(buildSetoranRows(data.pemasukan_tabungan_qurban));

            // Tampilkan modal detail
            $('#modalDetailTabungan').modal('show');
        });
    });

    // ==========================================================
    // 7. SIMPAN SETORAN BARU (AJAX Pemasukan)
    // ==========================================================
    $('#formTambahSetoran').on('submit', function(e) {
        e.preventDefault();
        clearValidationErrors();

        let url = $(this).data('url'); // -> /api/pemasukan-qurban (dari route 'api.pemasukan-qurban.store')

        let formData = {
            id_tabungan_hewan_qurban: $('#tambah_setoran_id_tabungan').val(),
            tanggal: $(this).find('[name="tanggal"]').val(),
            nominal: $(this).find('[name="nominal"]').val(),
        };

        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            headers: {'X-CSRF-TOKEN': csrfToken},
            success: function(response) {
                Swal.fire('Berhasil!', 'Setoran berhasil disimpan.', 'success');
                $('#modalTambahSetoran').modal('hide');
                $('#formTambahSetoran')[0].reset();

                // Muat ulang data di modal detail dan tabel utama
                refreshDetailModal(currentTabunganId);
                dataTable.ajax.reload();
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    showValidationErrors(xhr.responseJSON.errors, '#formTambahSetoran');
                } else {
                    Swal.fire('Error!', 'Terjadi kesalahan.', 'error');
                }
            }
        });
    });

    // ==========================================================
    // 8. HAPUS SETORAN (AJAX Pemasukan)
    // ==========================================================
    $('#modalDetailTabungan').on('click', '.btn-hapus-setoran', function() {
        let idSetoran = $(this).data('id');
        let url = $(this).data('url'); // -> /api/pemasukan-qurban/{id} (ini sudah diambil dari HTML)

        Swal.fire({
            title: 'Anda yakin?',
            text: "Data setoran ini akan dihapus.",
            icon: 'warning',
            // ... (SweetAlert options) ...
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    method: 'POST',
                    data: { _method: 'DELETE' },
                    headers: {'X-CSRF-TOKEN': csrfToken},
                    success: function() {
                        Swal.fire('Dihapus!', 'Setoran berhasil dihapus.', 'success');
                        // Muat ulang data di modal detail dan tabel utama
                        refreshDetailModal(currentTabunganId);
                        dataTable.ajax.reload();
                    },
                    error: function() {
                        Swal.fire('Error!', 'Terjadi kesalahan.', 'error');
                    }
                });
            }
        });
    });

    /**
     * Helper untuk membangun baris HTML tabel setoran
     * (Digunakan di 2 tempat, jadi dibuat fungsi)
     */
    function buildSetoranRows(pemasukanList) {
        let setoranHtml = '';
        if(pemasukanList && pemasukanList.length > 0) {
            pemasukanList.forEach(pemasukan => {
                // AMBIL URL DELETE SETORAN DARI 'urls' OBJECT
                let deleteUrl = getUrl('setoranDestroy', pemasukan.id_pemasukan_tabungan_qurban);

                setoranHtml += `
                    <tr id="setoran-${pemasukan.id_pemasukan_tabungan_qurban}">
                        <td>${new Date(pemasukan.tanggal).toLocaleDateString('id-ID', {day: '2-digit', month: 'short', year: 'numeric'})}</td>
                        <td>${formatRupiah(pemasukan.nominal)}</td>
                        <td>
                            <button class="btn btn-danger btn-sm py-0 px-1 btn-hapus-setoran"
                                data-id="${pemasukan.id_pemasukan_tabungan_qurban}"
                                data-url="${deleteUrl}">
                                <i class="fas fa-trash fa-xs"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
        } else {
            setoranHtml = '<tr><td colspan="3" class="text-center">Belum ada riwayat setoran.</td></tr>';
        }
        return setoranHtml;
    }

    // Fungsi untuk me-refresh data di modal detail
    function refreshDetailModal(id) {
        if (!id) return;

        let url = getUrl('show', id); // DIGANTI
        $.get(url, function(data) {

            // ... (Isi stats) ...
            let totalTerkumpul = data.pemasukan_tabungan_qurban.reduce((acc, pemasukan) => acc + parseFloat(pemasukan.nominal), 0);
            let sisaTarget = parseFloat(data.total_harga_hewan_qurban) - totalTerkumpul;
            $('#detailTotalTabungan').text(formatRupiah(totalTerkumpul));
            $('#detailSisaTarget').text(formatRupiah(sisaTarget));
            $('#detailSisaTarget').toggleClass('text-success', sisaTarget <= 0).toggleClass('text-danger', sisaTarget > 0);

            // Isi tabel riwayat setoran (menggunakan fungsi helper baru)
            $('#tabelRiwayatSetoran').html(buildSetoranRows(data.pemasukan_tabungan_qurban));
        });
    }
});
