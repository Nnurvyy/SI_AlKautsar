document.addEventListener('DOMContentLoaded', () => {

    // --- Definisi Elemen (Ganti ID) ---
    const form = document.getElementById('formKajian');
    const modalKajian = document.getElementById('modalKajian');
    const searchInput = document.getElementById('searchInput');
    const tbody = document.querySelector('#tabelKajian tbody');
    const token = document.querySelector('meta[name="csrf-token"]').content;
    
    const submitButton = form ? form.querySelector('button[type="submit"]') : null;
    const cancelButton = form ? form.querySelector('button[data-bs-dismiss="modal"]') : null;
    const originalButtonText = submitButton ? submitButton.innerHTML : 'Simpan';

    // Elemen Input File Kustom (Ganti ID)
    const fotoInput = document.getElementById('foto_penceramah');
    const fotoLabel = document.getElementById('foto_penceramah_label');
    const fotoLabelSpan = fotoLabel ? fotoLabel.querySelector('span') : null;
    const clearFileBtn = document.getElementById('clearFile');
    
    const preview = document.getElementById('previewFoto');
    const previewContainer = document.getElementById('previewContainer');

    const statusFilter = document.getElementById('statusFilter');
    const paginationContainer = document.getElementById('paginationLinks');
    const paginationInfo = document.getElementById('paginationInfo');
    const tableContainer = document.getElementById('tabelKajian');

    const sortTanggal = document.getElementById('sortTanggal');
    const sortIcon = document.getElementById('sortIcon');

    if (sortTanggal) {
        sortTanggal.addEventListener('click', () => {
            state.sortDir = state.sortDir === 'asc' ? 'desc' : 'asc';
            sortIcon.className = state.sortDir === 'asc' ? 'bi bi-arrow-up' : 'bi bi-arrow-down';
            loadKajian(); // Ganti
        });
    }

    let state = {
        currentPage: 1,
        status: 'aktif',
        search: '',
        perPage: 10,
        sortBy: 'tanggal_kajian', // Ganti
        sortDir: 'desc',      
        searchTimeout: null
    };

    // --- Event Listener Utama ---

    if (form) {
        form.addEventListener('submit', async e => {
            e.preventDefault();
            setLoading(true);

            // Ganti ID
            const id = document.getElementById('id_kajian').value;
            const formData = new FormData(form);
            // Ganti URL
            const url = id ? `/pengurus/kajian/${id}` : '/pengurus/kajian';
            if (id) formData.append('_method', 'PUT');

            try {
                const res = await fetch(url, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
                    body: formData
                });

                const data = await res.json();
                if (res.ok) {
                    Swal.fire('Berhasil!', data.message, 'success');
                    bootstrap.Modal.getInstance(modalKajian).hide();
                    loadKajian(); // Ganti
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
                setLoading(false);
            }
        });
    }

    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            clearTimeout(state.searchTimeout);
            state.searchTimeout = setTimeout(() => {
                state.search = searchInput.value;
                state.currentPage = 1; 
                loadKajian(); // Ganti
            }, 300); 
        });
    }

    if (statusFilter) {
        statusFilter.addEventListener('change', () => {
            state.status = statusFilter.value;
            state.currentPage = 1; 
            loadKajian(); // Ganti
        });
    }

    if (paginationContainer) {
        paginationContainer.addEventListener('click', e => {
            e.preventDefault();
            const target = e.target.closest('a.page-link');
            
            if (!target || target.parentElement.classList.contains('disabled') || target.parentElement.classList.contains('active')) {
                return;
            }

            const url = new URL(target.href);
            const page = url.searchParams.get('page'); 
            
            if (page) {
                state.currentPage = parseInt(page);
                loadKajian(); // Ganti
            }
        });
    }

    if (modalKajian && form && fotoInput) {
        modalKajian.addEventListener('hidden.bs.modal', function () {
            form.reset();
            fotoInput.dispatchEvent(new Event('change'));
            document.getElementById('id_kajian').value = ''; // Ganti
            setLoading(false); 
        });
    }

    // --- Fungsi Helper (setLoading) --- (Tidak berubah)
    function setLoading(isLoading) {
        if (!submitButton || !cancelButton) return;
        if (isLoading) {
            submitButton.disabled = true;
            cancelButton.disabled = true;
            submitButton.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...`;
        } else {
            submitButton.disabled = false;
            cancelButton.disabled = false;
            submitButton.innerHTML = originalButtonText;
        }
    }

    // --- Fungsi Render ---
    async function loadKajian() { // Ganti
        if (!tbody) return;
        
        let colCount = tbody.closest('table').querySelector('thead tr').cells.length;
        tbody.innerHTML = `<tr><td colspan="${colCount}" class="text-center"><div class="spinner-border text-primary"></div></td></tr>`;

        // Ganti URL
        const url = `/pengurus/kajian-data?page=${state.currentPage}&status=${state.status}&search=${state.search}&perPage=${state.perPage}&sortBy=${state.sortBy}&sortDir=${state.sortDir}`;

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

    // Fungsi formatTanggal (Tidak berubah)
    function formatTanggal(tanggalStr) {
        if (!tanggalStr) return '-';
        const parts = tanggalStr.split(/[-/]/);
        if (parts.length === 3) {
            const [d, m, y] = parts;
            const date = new Date(`${y}-${m}-${d}T00:00:00`);
            if (!isNaN(date)) {
                return date.toLocaleDateString('id-ID', {
                    day: '2-digit', month: 'short', year: 'numeric'
                });
            }
        }
        const date = new Date(tanggalStr);
        return !isNaN(date)
            ? date.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })
            : 'Invalid Date';
    }

    function formatWaktu(waktuStr) {
        if (!waktuStr) return '-';
        // Asumsi format 'HH:MM:SS' atau 'HH:MM'
        const parts = waktuStr.split(':');
        if (parts.length >= 2) {
            return `${parts[0]}:${parts[1]}`;
        }
        return '-';
    }

    function renderTable(data, startingNumber) {
        tbody.innerHTML = ''; 
        
        if (data.length === 0) {
            let colCount = tbody.closest('table').querySelector('thead tr').cells.length;
            tbody.innerHTML = `<tr><td colspan="${colCount}" class="text-center">Belum ada data.</td></tr>`;
            return;
        }

        data.forEach((item, i) => {
            // Ganti isi tabel
            const row = `
            <tr>
                <td class="text-center">${startingNumber + i}</td>
                <td class="text-center"><img src="${item.foto_url}" class="rounded" style="width:60px;height:60px;object-fit:cover;" alt="Foto ${item.nama_penceramah}"></td>
                <td>${item.nama_penceramah}</td>
                <td>${item.tema_kajian}</td>
                <td class="text-center">${formatWaktu(item.waktu_kajian)}</td>
                <td class="text-center">${formatTanggal(item.tanggal_kajian)}</td>
                <td class="text-center">
                    <button class="btn btn-warning btn-sm" onclick="editKajian('${item.id_kajian}')"> 
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-danger btn-sm" onclick="hapusKajian('${item.id_kajian}')"> 
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>`;
            tbody.insertAdjacentHTML('beforeend', row);
        });
    }

    // Fungsi renderPagination (Tidak berubah)
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
            let label = link.label;
            if (label.includes('Previous')) label = '<';
            else if (label.includes('Next')) label = '>';
            const disabled = !link.url ? 'disabled' : '';
            const active = link.active ? 'active' : '';
            linksHtml += `<li class="page-item ${disabled} ${active}"><a class="page-link" href="${link.url || '#'}">${label}</a></li>`;
        });
        linksHtml += '</ul>';
        paginationContainer.innerHTML = linksHtml;
    }

    // --- Fungsi Global (Edit/Hapus) ---
    window.editKajian = async function(id_kajian) { // Ganti
        try {
            // Ganti URL
            const res = await fetch(`/pengurus/kajian/${id_kajian}`);
            if (!res.ok) throw new Error('Data tidak ditemukan');
            const data = await res.json();

            // Ganti ID
            document.getElementById('id_kajian').value = data.id_kajian;
            document.getElementById('nama_penceramah').value = data.nama_penceramah;
            document.getElementById('tema_kajian').value = data.tema_kajian;
            if (data.tanggal_kajian) {
                document.getElementById('tanggal_kajian').value = data.tanggal_kajian.split('T')[0];
            }
            if (data.waktu_kajian) {
                document.getElementById('waktu_kajian').value = data.waktu_kajian.substring(0, 5);
            }

            // Ganti
            if (data.foto_penceramah) {
                fotoLabelSpan.textContent = data.foto_penceramah.split('/').pop();
                fotoLabelSpan.classList.remove('text-muted');
                clearFileBtn.classList.remove('d-none');
                preview.src = data.foto_url;
                previewContainer.classList.remove('d-none');
            } else {
                fotoInput.dispatchEvent(new Event('change'));
            }

            new bootstrap.Modal(modalKajian).show();
        } catch (err) {
            Swal.fire('Gagal', err.message, 'error');
        }
    }

    window.hapusKajian = async function(id_kajian) { // Ganti
        const confirm = await Swal.fire({
            title: 'Yakin ingin menghapus?',
            text: 'Data akan dihapus permanen!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal'
        });

        if (!confirm.isConfirmed) return;

        try {
            const formData = new FormData();
            formData.append('_method', 'DELETE');

            // Ganti URL
            const res = await fetch(`/pengurus/kajian/${id_kajian}`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
                body: formData
            });

            const data = await res.json();
            if (res.ok) {
                Swal.fire('Terhapus!', data.message, 'success');
                loadKajian(); // Ganti
            } else {
                throw new Error(data.message || 'Terjadi kesalahan');
            }
        } catch (err) {
            Swal.fire('Gagal', err.message, 'error');
        }
    }

    // --- Logika Input File Kustom (Ganti ID) ---
    if (fotoInput && fotoLabelSpan && clearFileBtn && previewContainer && preview) {
        fotoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                fotoLabelSpan.textContent = file.name;
                fotoLabelSpan.classList.remove('text-muted');
                clearFileBtn.classList.remove('d-none');
                const reader = new FileReader();
                reader.onload = function(event) {
                    preview.src = event.target.result;
                    previewContainer.classList.remove('d-none');
                }
                reader.readAsDataURL(file);
            } else {
                fotoLabelSpan.textContent = "Choose file...";
                fotoLabelSpan.classList.add('text-muted');
                clearFileBtn.classList.add('d-none');
                preview.src = "";
                previewContainer.classList.add('d-none');
            }
        });
    }
    if (clearFileBtn && fotoInput) {
        clearFileBtn.addEventListener('click', function(e) {
            e.stopPropagation(); 
            fotoInput.value = ""; 
            fotoInput.dispatchEvent(new Event('change'));
        });
    }

    // --- Inisialisasi ---
    loadKajian(); // Ganti
});