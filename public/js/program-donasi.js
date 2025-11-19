// public/js/program-donasi.js
document.addEventListener('DOMContentLoaded', () => {

    // --- Element references ---
    const tokenMeta = document.querySelector('meta[name="csrf-token"]');
    const token = tokenMeta ? tokenMeta.content : '';
    const tbody = document.querySelector('#tabelDonasi tbody');
    const paginationContainer = document.getElementById('paginationLinks');
    const paginationInfo = document.getElementById('paginationInfo');

    const modalEl = document.getElementById('formModal');
    const modal = modalEl ? new bootstrap.Modal(modalEl) : null;
    const form = document.getElementById('donasiForm');
    const modalTitle = document.getElementById('formModalLabel');
    const submitButton = document.getElementById('submitBtn');
    const originalButtonText = submitButton ? submitButton.innerHTML : 'Simpan';

    let state = {
        currentPage: 1,
        perPage: 10,
        sortBy: 'created_at',
        sortDir: 'desc',
    };

    // --- helpers ---
    function setLoading(isLoading) {
        if (!submitButton) return;
        const cancelButton = form.querySelector('button[data-bs-dismiss="modal"]');
        if (isLoading) {
            submitButton.disabled = true;
            if (cancelButton) cancelButton.disabled = true;
            submitButton.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...`;
        } else {
            submitButton.disabled = false;
            if (cancelButton) cancelButton.disabled = false;
            submitButton.innerHTML = originalButtonText;
        }
    }

    function formatRupiah(angka) {
        if (angka === null || angka === undefined || isNaN(Number(angka))) return "Rp 0";
        return "Rp " + new Intl.NumberFormat('id-ID').format(Number(angka));
    }

    function escapeHtml(unsafe) {
        if (!unsafe) return '';
        return String(unsafe)
             .replace(/&/g, "&amp;")
             .replace(/</g, "&lt;")
             .replace(/>/g, "&gt;")
             .replace(/"/g, "&quot;")
             .replace(/'/g, "&#039;");
    }

    // --- load data ---
    async function loadData() {
        if (!tbody) return;
        const colCount = 7;
        tbody.innerHTML = `<tr><td colspan="${colCount}" class="text-center"><div class="spinner-border text-primary"></div></td></tr>`;

        const url = `/admin/program-donasi-data?page=${state.currentPage}&perPage=${state.perPage}&sortBy=${state.sortBy}&sortDir=${state.sortDir}`;
        try {
            const res = await fetch(url, { credentials: 'same-origin' });
            if (!res.ok) {
                let txt = await res.text();
                throw new Error('Gagal memuat data (Response: ' + res.status + '). ' + txt);
            }
            const response = await res.json();

            // Safely get data array
            let data = Array.isArray(response.data) ? response.data : [];
            const start = response.from || ((state.currentPage - 1) * state.perPage) + 1;

            renderTable(data, start);
            renderPagination(response);
        } catch (err) {
            console.error(err);
            tbody.innerHTML = `<tr><td colspan="${colCount}" class="text-center text-danger">${escapeHtml(err.message)}</td></tr>`;
            if (paginationInfo) paginationInfo.textContent = 'Gagal memuat data';
            if (paginationContainer) paginationContainer.innerHTML = '';
        }
    }

    function renderTable(data, startingNumber) {
        tbody.innerHTML = '';
        const colCount = 7;

        if (!Array.isArray(data) || data.length === 0) {
            tbody.innerHTML = `<tr><td colspan="${colCount}" class="text-center">Belum ada data program donasi.</td></tr>`;
            return;
        }

        data.forEach((item, i) => {
            const imgUrl = item && item.gambar_url ? item.gambar_url : 'https://via.placeholder.com/100';
            const judul = item.judul || '-';
            const target = item.target_dana || 0;
            const terkumpul = item.dana_terkumpul || 0;
            const persen = (item.persentase !== undefined && item.persentase !== null) ? item.persentase : 0;
            const id = item.id || '';

            const row = `
                <tr>
                    <td class="text-center">${startingNumber + i}</td>
                    <td class="text-center">
                        <img src="${imgUrl}" alt="Gambar" width="120" class="img-thumbnail" onerror="this.src='https://via.placeholder.com/100'">
                    </td>
                    <td>${escapeHtml(judul)}</td>
                    <td class="text-end">${formatRupiah(target)}</td>
                    <td class="text-end">${formatRupiah(terkumpul)}</td>
                    <td class="text-center">${escapeHtml(String(persen))}%</td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-warning me-1" onclick="window.editForm('${id}')"><i class="bi bi-pencil"></i> Edit</button>
                        <button class="btn btn-sm btn-danger" onclick="window.deleteData('${id}')"><i class="bi bi-trash"></i> Hapus</button>
                    </td>
                </tr>
            `;
            tbody.insertAdjacentHTML('beforeend', row);
        });
    }

    function renderPagination(response) {
        if (!paginationInfo || !paginationContainer) return;

        const from = response.from || 0;
        const to = response.to || 0;
        const total = response.total || 0;
        const links = Array.isArray(response.links) ? response.links : [];

        if (total === 0) {
            paginationInfo.textContent = 'Menampilkan 0 dari 0 data';
            paginationContainer.innerHTML = '';
            return;
        }

        paginationInfo.textContent = `Menampilkan ${from} - ${to} dari ${total} data`;

        let linksHtml = '<ul class="pagination justify-content-center mb-0">';
        links.forEach(link => {
            let label = String(link.label || '');
            if (label.includes('Previous')) label = '<';
            else if (label.includes('Next')) label = '>';
            const disabled = (!link.url) ? 'disabled' : '';
            const active = link.active ? 'active' : '';
            const href = link.url || '#';
            linksHtml += `
                <li class="page-item ${disabled} ${active}">
                    <a class="page-link" href="${href}">${label}</a>
                </li>
            `;
        });
        linksHtml += '</ul>';
        paginationContainer.innerHTML = linksHtml;
    }

    // --- events ---
    if (form) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            setLoading(true);

            const idVal = document.getElementById('id').value;
            const formData = new FormData(form);
            let url = '/admin/program-donasi';
            if (idVal) {
                url = `/admin/program-donasi/${idVal}`;
                formData.append('_method', 'PUT');
            }

            try {
                const res = await fetch(url, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
                    body: formData,
                    credentials: 'same-origin'
                });
                const data = await res.json();
                if (res.ok) {
                    Swal.fire('Berhasil!', data.message, 'success');
                    if (modal) modal.hide();
                    loadData();
                } else {
                    if (res.status === 422 && data.errors) {
                        const errMsg = Object.values(data.errors).map(v => v[0]).join('<br>');
                        throw new Error(errMsg);
                    }
                    throw new Error(data.message || 'Terjadi kesalahan');
                }
            } catch (err) {
                console.error(err);
                Swal.fire('Gagal', err.message, 'error');
            } finally {
                setLoading(false);
            }
        });
    }

    if (paginationContainer) {
        paginationContainer.addEventListener('click', e => {
            e.preventDefault();
            const target = e.target.closest('a.page-link');
            if (!target || target.parentElement.classList.contains('disabled') || target.parentElement.classList.contains('active')) return;
            try {
                const urlObj = new URL(target.href);
                const page = urlObj.searchParams.get('page');
                if (page) {
                    state.currentPage = parseInt(page);
                    loadData();
                }
            } catch (err) {
                // href might be '#', ignore
            }
        });
    }

    if (modalEl) {
        modalEl.addEventListener('hidden.bs.modal', function () {
            form.reset();
            document.getElementById('id').value = '';
            modalTitle.textContent = 'Form Program Donasi';
            const preview = document.getElementById('gambar-preview');
            if (preview) preview.innerHTML = '';
            setLoading(false);
        });
    }

    // --- global functions used by buttons ---
    window.addForm = function() {
        form.reset();
        document.getElementById('id').value = '';
        modalTitle.textContent = 'Tambah Program Donasi';
        const preview = document.getElementById('gambar-preview');
        if (preview) preview.innerHTML = '';
        if (modal) modal.show();
    }

    window.editForm = async function(id) {
        if (!id) return;
        form.reset();
        document.getElementById('id').value = id;
        modalTitle.textContent = 'Edit Program Donasi';
        const preview = document.getElementById('gambar-preview');
        if (preview) preview.innerHTML = '';

        try {
            const res = await fetch(`/admin/program-donasi/${id}`, { credentials: 'same-origin' });
            if (!res.ok) throw new Error('Data tidak ditemukan');
            const data = await res.json();

            document.getElementById('judul').value = data.judul || '';
            document.getElementById('deskripsi').value = data.deskripsi || '';
            document.getElementById('target_dana').value = data.target_dana != null ? data.target_dana : 0;
            document.getElementById('dana_terkumpul').value = data.dana_terkumpul != null ? data.dana_terkumpul : 0;
            document.getElementById('tanggal_selesai').value = data.tanggal_selesai || '';

            if (data.gambar_url) {
                if (preview) preview.innerHTML = `<img src="${data.gambar_url}" width="150" class="img-thumbnail" onerror="this.src='https://via.placeholder.com/150'">`;
            }

            if (modal) modal.show();
        } catch (err) {
            console.error(err);
            Swal.fire('Gagal', err.message, 'error');
        }
    }

    window.deleteData = async function(id) {
        if (!id) return;
        const confirm = await Swal.fire({
            title: 'Yakin ingin menghapus?',
            text: 'Data program donasi akan dihapus permanen!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonText: 'Batal',
            confirmButtonText: 'Ya, hapus'
        });

        if (!confirm.isConfirmed) return;

        try {
            const formData = new FormData();
            formData.append('_method', 'DELETE');

            const res = await fetch(`/admin/program-donasi/${id}`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
                body: formData,
                credentials: 'same-origin'
            });

            if (res.ok) {
                Swal.fire('Terhapus!', 'Data berhasil dihapus.', 'success');
                loadData();
            } else {
                const data = await res.json();
                throw new Error(data.message || 'Terjadi kesalahan');
            }
        } catch (err) {
            console.error(err);
            Swal.fire('Gagal', err.message, 'error');
        }
    }

    // initial load
    loadData();
});
