document.addEventListener('DOMContentLoaded', () => {

    // --- Definisi Elemen Utama ---
    const token = document.querySelector('meta[name="csrf-token"]').content;
    const tbody = document.querySelector('#tabelTabungan tbody');
    const paginationContainer = document.getElementById('paginationLinks');
    const paginationInfo = document.getElementById('paginationInfo');
    const $penggunaList = document.getElementById('penggunaListTemplate'); // Template <option>

    // --- Elemen Filter & Sort ---
    const statusFilter = document.getElementById('statusFilter');
    const sortButton = document.getElementById('sortTotalTerkumpul');
    const sortIcon = document.getElementById('sortIcon');

    // --- Elemen Modal Tabungan (Tambah/Edit) ---
    const modalTabunganEl = document.getElementById('modalTabungan');
    const modalTabungan = new bootstrap.Modal(modalTabunganEl);
    const formTabungan = document.getElementById('formTabungan');
    const modalTabunganTitle = document.getElementById('modalTabunganTitle');
    const submitButton = formTabungan.querySelector('button[type="submit"]');
    const originalButtonText = submitButton.innerHTML;

    // --- Elemen Modal Detail Setoran ---
    const modalDetailEl = document.getElementById('modalDetailTabungan');
    const modalDetail = new bootstrap.Modal(modalDetailEl);
    const modalDetailTitle = document.getElementById('detailModalTitle');
    const detailTotalTabungan = document.getElementById('detailTotalTabungan');
    const detailSisaTarget = document.getElementById('detailSisaTarget');
    const tabelRiwayatSetoran = document.getElementById('tabelRiwayatSetoran');

    // --- Elemen Modal Tambah Setoran ---
    const modalSetoranEl = document.getElementById('modalTambahSetoran');
    const modalSetoran = new bootstrap.Modal(modalSetoranEl);
    const formSetoran = document.getElementById('formTambahSetoran');
    const setoranSubmitButton = formSetoran.querySelector('button[type="submit"]');
    const originalSetoranButtonText = setoranSubmitButton.innerHTML;
    const inputIdTabunganSetoran = document.getElementById('tambah_setoran_id_tabungan');

    // --- Elemen Filter PDF (Pola LapKeu) ---
    const pdfPeriodeFilter = document.getElementById('filter-periode');
    const pdfFilterBulanan = document.getElementById('filter-bulanan');
    const pdfFilterTahunan = document.getElementById('filter-tahunan');
    const pdfFilterRentang = document.getElementById('filter-rentang');

    // --- State Management ---
    let state = {
        currentPage: 1,
        status: 'semua',
        sortBy: 'total_terkumpul',
        sortDir: 'desc',
    };
    let currentDetailTabunganId = null; // Untuk me-refresh modal detail

    // --- Fungsi Helper ---
    function formatRupiah(angka) {
        if(isNaN(parseFloat(angka))) return "Rp 0";
        return "Rp " + new Intl.NumberFormat('id-ID').format(angka);
    }

    function formatTanggal(tanggalStr) {
        if (!tanggalStr) return '-';
        const date = new Date(tanggalStr);
        return !isNaN(date)
            ? date.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })
            : '-';
    }

    // --- Fungsi Loading ---
    function setFormLoading(form, button, originalText, isLoading) {
        const cancelButton = form.querySelector('button[data-bs-dismiss="modal"]');
        if (isLoading) {
            button.disabled = true;
            if(cancelButton) cancelButton.disabled = true;
            button.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...`;
        } else {
            button.disabled = false;
            if(cancelButton) cancelButton.disabled = false;
            button.innerHTML = originalText;
        }
    }

    // --- Logika Filter PDF (Pola LapKeu) ---
    function togglePdfFilterVisibility() {
        const selectedValue = pdfPeriodeFilter.value;
        pdfFilterBulanan.style.display = 'none';
        pdfFilterTahunan.style.display = 'none';
        pdfFilterRentang.style.display = 'none';

        if (selectedValue === 'per_bulan') pdfFilterBulanan.style.display = 'block';
        else if (selectedValue === 'per_tahun') pdfFilterTahunan.style.display = 'block';
        else if (selectedValue === 'rentang_waktu') pdfFilterRentang.style.display = 'block';
    }
    pdfPeriodeFilter.addEventListener('change', togglePdfFilterVisibility);
    togglePdfFilterVisibility(); // Inisialisasi

    // --- Logika CRUD Utama (Pola Khotib) ---

    // 1. Muat Data Tabel Utama
    async function loadTabungan() {
        let colCount = tbody.closest('table').querySelector('thead tr').cells.length;
        tbody.innerHTML = `<tr><td colspan="${colCount}" class="text-center"><div class="spinner-border text-primary"></div></td></tr>`;

        const url = `/admin/tabungan-qurban-data?page=${state.currentPage}&status=${state.status}&sortBy=${state.sortBy}&sortDir=${state.sortDir}&perPage=10`;

        try {
            const res = await fetch(url);
            if (!res.ok) throw new Error('Gagal memuat data');
            const response = await res.json();

            renderTable(response.data, response.from || 1);
            renderPagination(response);
        } catch (err) {
            tbody.innerHTML = `<tr><td colspan="${colCount}" class="text-center text-danger">${err.message}</td></tr>`;
            paginationInfo.textContent = 'Gagal memuat data';
            paginationContainer.innerHTML = '';
        }
    }

    // 2. Render Tabel Utama
    function renderTable(data, startingNumber) {
        tbody.innerHTML = '';

        if (data.length === 0) {
            let colCount = tbody.closest('table').querySelector('thead tr').cells.length;
            tbody.innerHTML = `<tr><td colspan="${colCount}" class="text-center">Belum ada data tabungan.</td></tr>`;
            return;
        }

        data.forEach((item, i) => {
            const totalTerkumpul = parseFloat(item.total_terkumpul || 0);
            const totalHarga = parseFloat(item.total_harga_hewan_qurban);
            const sisaTarget = totalHarga - totalTerkumpul;

            let statusHtml;
            if (sisaTarget <= 0) {
                statusHtml = '<span class="badge bg-success">Lunas</span>';
            } else if (item.bayar_bulan_ini) {
                statusHtml = `<span class="badge bg-primary">Mencicil</span>`;
            } else {
                statusHtml = `<span class="badge bg-danger">Menunggak</span>`;
            }

            const row = `
            <tr>
                <td class="text-center">${startingNumber + i}</td>
                <td>
                    <div>${item.pengguna ? item.pengguna.nama : 'N/A'}</div>
                    <small class="text-muted">${Str.ucfirst(item.nama_hewan)} (${item.total_hewan} ekor)</small>
                </td>
                <td class="text-end">${formatRupiah(totalHarga)}</td>
                <td class="text-end">${formatRupiah(totalTerkumpul)}</td>
                <td class="text-end ${sisaTarget > 0 ? 'text-danger' : 'text-success'}">
                    ${sisaTarget <= 0 ? '-' : formatRupiah(sisaTarget)}
                </td>
                <td class="text-center">${statusHtml}</td>
                <td class="text-center">
                    <button class="btn btn-info btn-sm" title="Lihat Detail" onclick="window.showDetail('${item.id_tabungan_hewan_qurban}')">
                        <i class="bi bi-eye"></i>
                    </button>
                    <button class="btn btn-warning btn-sm" title="Edit" onclick="window.editTabungan('${item.id_tabungan_hewan_qurban}')">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-danger btn-sm" title="Hapus" onclick="window.hapusTabungan('${item.id_tabungan_hewan_qurban}')">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>`;
            tbody.insertAdjacentHTML('beforeend', row);
        });
    }

    // 3. Render Pagination
    function renderPagination(response) {
        const { from, to, total, links } = response;

        if (total === 0) {
            paginationInfo.textContent = 'Menampilkan 0 dari 0 data';
            paginationContainer.innerHTML = '';
            return;
        }

        paginationInfo.textContent = `Menampilkan ${from} - ${to} dari ${total} data`;

        let linksHtml = '<ul class="pagination justify-content-center mb-0">';
        links.forEach(link => {
            // --- PERUBAHAN DI SINI ---
            // Kita ganti logika replace() dengan includes() agar sama seperti khotib.js
            let label = link.label;
            if (label.includes('Previous')) label = '<';
            else if (label.includes('Next')) label = '>';
            // --- AKHIR PERUBAHAN ---

            const disabled = !link.url ? 'disabled' : '';
            const active = link.active ? 'active' : '';
            linksHtml += `
                <li class="page-item ${disabled} ${active}">
                    <a class="page-link" href="${link.url || '#'}">${label}</a>
                </li>`;
        });
        linksHtml += '</ul>';
        paginationContainer.innerHTML = linksHtml;
    }

    // 4. Event Listeners Filter & Sort
    statusFilter.addEventListener('change', () => {
        state.status = statusFilter.value;
        state.currentPage = 1;
        loadTabungan();
    });

    sortButton.addEventListener('click', () => {
        state.sortDir = state.sortDir === 'asc' ? 'desc' : 'asc';
        sortIcon.className = state.sortDir === 'asc' ? 'bi bi-arrow-up' : 'bi bi-arrow-down';
        loadTabungan();
    });

    paginationContainer.addEventListener('click', e => {
        e.preventDefault();
        const target = e.target.closest('a.page-link');
        if (!target || target.parentElement.classList.contains('disabled') || target.parentElement.classList.contains('active')) return;

        const url = new URL(target.href);
        const page = url.searchParams.get('page');
        if (page) {
            state.currentPage = parseInt(page);
            loadTabungan();
        }
    });

    // 5. Buka Modal Tambah
    document.getElementById('btnTambahTabungan').addEventListener('click', () => {
        formTabungan.reset();
        document.getElementById('id_tabungan_hewan_qurban').value = '';
        modalTabunganTitle.textContent = 'Tambah Tabungan Qurban Baru';
        // Isi dropdown pengguna dari template
        document.getElementById('id_pengguna').innerHTML = $penggunaList.innerHTML;
        modalTabungan.show();
    });

    // 6. Simpan/Update Tabungan (Form Utama)
    formTabungan.addEventListener('submit', async e => {
        e.preventDefault();
        setFormLoading(formTabungan, submitButton, originalButtonText, true);

        const id = document.getElementById('id_tabungan_hewan_qurban').value;
        const formData = new FormData(formTabungan);
        let url = '/admin/tabungan-qurban';
        if (id) {
            url = `/admin/tabungan-qurban/${id}`;
            formData.append('_method', 'PUT');
        }

        try {
            const res = await fetch(url, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
                body: formData
            });

            const data = await res.json();
            if (res.ok) {
                Swal.fire('Berhasil!', data.message, 'success');
                modalTabungan.hide();
                loadTabungan(); // Muat ulang data
            } else {
                if (res.status === 422 && data.errors) {
                    let errorMessages = Object.values(data.errors).map(err => err[0]).join('<br>');
                    throw new Error(errorMessages);
                }
                throw new Error(data.message || 'Terjadi kesalahan');
            }
        } catch (err) {
            Swal.fire('Gagal', err.message, 'error');
        } finally {
            setFormLoading(formTabungan, submitButton, originalButtonText, false);
        }
    });

    // 7. Reset Modal Tabungan saat ditutup
    modalTabunganEl.addEventListener('hidden.bs.modal', function () {
        formTabungan.reset();
        document.getElementById('id_tabungan_hewan_qurban').value = '';
        setFormLoading(formTabungan, submitButton, originalButtonText, false);
    });

    // 8. Edit Tabungan (Global function)
    window.editTabungan = async function(id) {
        try {
            const res = await fetch(`/admin/tabungan-qurban/${id}`);
            if (!res.ok) throw new Error('Data tidak ditemukan');
            const data = await res.json();

            formTabungan.reset();
            document.getElementById('id_tabungan_hewan_qurban').value = data.id_tabungan_hewan_qurban;
            modalTabunganTitle.textContent = 'Update Tabungan Qurban';

            // Isi dropdown pengguna dan pilih yang sesuai
            document.getElementById('id_pengguna').innerHTML = $penggunaList.innerHTML;
            document.getElementById('id_pengguna').value = data.id_pengguna;

            document.getElementById('nama_hewan').value = data.nama_hewan;
            document.getElementById('total_hewan').value = data.total_hewan;
            document.getElementById('total_harga_hewan_qurban').value = data.total_harga_hewan_qurban;

            modalTabungan.show();
        } catch (err) {
            Swal.fire('Gagal', err.message, 'error');
        }
    }

    // 9. Hapus Tabungan (Global function)
    window.hapusTabungan = async function(id) {
        const confirm = await Swal.fire({
            title: 'Yakin ingin menghapus?',
            text: 'Data tabungan dan semua riwayat setoran akan dihapus permanen!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonText: 'Batal',
            confirmButtonText: 'Ya, hapus'
        });

        if (!confirm.isConfirmed) return;

        try {
            const res = await fetch(`/admin/tabungan-qurban/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' }
            });

            const data = await res.json();
            if (res.ok) {
                Swal.fire('Terhapus!', data.message, 'success');
                loadTabungan();
            } else {
                throw new Error(data.message || 'Terjadi kesalahan');
            }
        } catch (err) {
            Swal.fire('Gagal', err.message, 'error');
        }
    }

    // --- Logika Modal Detail (Pola Lama) ---

    // 10. Tampilkan Detail (Global function)
    window.showDetail = async function(id) {
        currentDetailTabunganId = id; // Simpan ID untuk refresh
        try {
            const res = await fetch(`/admin/tabungan-qurban/${id}`);
            if (!res.ok) throw new Error('Data tidak ditemukan');
            const data = await res.json();

            modalDetailTitle.textContent = `Detail: ${Str.ucfirst(data.nama_hewan)} (${data.pengguna.nama})`;

            // Hitung stats
            const totalTerkumpul = data.pemasukan_tabungan_qurban.reduce((acc, p) => acc + parseFloat(p.nominal), 0);
            const sisaTarget = parseFloat(data.total_harga_hewan_qurban) - totalTerkumpul;

            detailTotalTabungan.textContent = formatRupiah(totalTerkumpul);
            detailSisaTarget.textContent = formatRupiah(sisaTarget);
            detailSisaTarget.classList.toggle('text-success', sisaTarget <= 0);
            detailSisaTarget.classList.toggle('text-danger', sisaTarget > 0);

            // Isi ID tabungan di form setoran
            inputIdTabunganSetoran.value = id;

            // Render tabel riwayat
            renderRiwayatSetoran(data.pemasukan_tabungan_qurban);

            modalDetail.show();
        } catch (err) {
            Swal.fire('Gagal', err.message, 'error');
        }
    }

    // 11. Render Riwayat Setoran
    function renderRiwayatSetoran(pemasukanList) {
        tabelRiwayatSetoran.innerHTML = '';
        if (!pemasukanList || pemasukanList.length === 0) {
            tabelRiwayatSetoran.innerHTML = '<tr><td colspan="3" class="text-center">Belum ada riwayat setoran.</td></tr>';
            return;
        }

        pemasukanList.forEach(p => {
            const row = `
                <tr>
                    <td>${formatTanggal(p.tanggal)}</td>
                    <td>${formatRupiah(p.nominal)}</td>
                    <td>
                        <button class="btn btn-danger btn-sm py-0 px-1"
                                title="Hapus setoran"
                                onclick="window.hapusSetoran('${p.id_pemasukan_tabungan_qurban}')">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>`;
            tabelRiwayatSetoran.insertAdjacentHTML('beforeend', row);
        });
    }

    // 12. Refresh Modal Detail (setelah tambah/hapus setoran)
    async function refreshDetailModal() {
        if (!currentDetailTabunganId) return;
        try {
            const res = await fetch(`/admin/tabungan-qurban/${currentDetailTabunganId}`);
            if (!res.ok) {
                modalDetail.hide();
                return;
            }
            const data = await res.json();

            // Hitung stats
            const totalTerkumpul = data.pemasukan_tabungan_qurban.reduce((acc, p) => acc + parseFloat(p.nominal), 0);
            const sisaTarget = parseFloat(data.total_harga_hewan_qurban) - totalTerkumpul;

            detailTotalTabungan.textContent = formatRupiah(totalTerkumpul);
            detailSisaTarget.textContent = formatRupiah(sisaTarget);
            detailSisaTarget.classList.toggle('text-success', sisaTarget <= 0);
            detailSisaTarget.classList.toggle('text-danger', sisaTarget > 0);

            // Render tabel riwayat
            renderRiwayatSetoran(data.pemasukan_tabungan_qurban);

        } catch (err) {
            console.error('Gagal refresh detail modal:', err);
        }
    }


    // 13. Simpan Setoran Baru
    formSetoran.addEventListener('submit', async e => {
        e.preventDefault();
        setFormLoading(formSetoran, setoranSubmitButton, originalSetoranButtonText, true);

        const formData = new FormData(formSetoran);

        try {
            const res = await fetch('/admin/pemasukan-qurban', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
                body: formData
            });

            const data = await res.json();
            if (res.ok) {
                Swal.fire('Berhasil!', data.message, 'success');
                modalSetoran.hide();
                formSetoran.reset();
                refreshDetailModal(); // Refresh modal detail
                loadTabungan();     // Refresh tabel utama
            } else {
                if (res.status === 422 && data.errors) {
                    let errorMessages = Object.values(data.errors).map(err => err[0]).join('<br>');
                    throw new Error(errorMessages);
                }
                throw new Error(data.message || 'Terjadi kesalahan');
            }
        } catch (err) {
            Swal.fire('Gagal', err.message, 'error');
        } finally {
            setFormLoading(formSetoran, setoranSubmitButton, originalSetoranButtonText, false);
        }
    });

    // 14. Hapus Setoran (Global function)
    window.hapusSetoran = async function(idSetoran) {
        const confirm = await Swal.fire({
            title: 'Yakin ingin menghapus setoran?',
            text: 'Data akan dihapus permanen!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonText: 'Batal',
            confirmButtonText: 'Ya, hapus'
        });

        if (!confirm.isConfirmed) return;

        try {
            const res = await fetch(`/admin/pemasukan-qurban/${idSetoran}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' }
            });

            const data = await res.json();
            if (res.ok) {
                Swal.fire('Terhapus!', data.message, 'success');
                refreshDetailModal(); // Refresh modal detail
                loadTabungan();     // Refresh tabel utama
            } else {
                throw new Error(data.message || 'Terjadi kesalahan');
            }
        } catch (err) {
            Swal.fire('Gagal', err.message, 'error');
        }
    }

    // 15. Utility String (jika tidak ada di global)
    // Sederhana, hanya untuk 'ucfirst' di judul modal detail
    const Str = {
        ucfirst: (s) => (s && s.length) ? s.charAt(0).toUpperCase() + s.slice(1) : ''
    };

    // --- Inisialisasi ---
    loadTabungan(); // Muat data tabel utama saat halaman dibuka
});
