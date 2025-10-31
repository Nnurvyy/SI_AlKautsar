document.addEventListener('DOMContentLoaded', () => {

    // --- Definisi Elemen ---
    const form = document.getElementById('formKhotib');
    const modalKhotib = document.getElementById('modalKhotib');
    const searchInput = document.getElementById('searchInput');
    const tbody = document.querySelector('#tabelKhotib tbody');
    const token = document.querySelector('meta[name="csrf-token"]').content;
    
    // Tombol Modal
    const submitButton = form ? form.querySelector('button[type="submit"]') : null;
    const cancelButton = form ? form.querySelector('button[data-bs-dismiss="modal"]') : null;
    const originalButtonText = submitButton ? submitButton.innerHTML : 'Simpan';

    // Elemen Input File Kustom
    const fotoInput = document.getElementById('foto_khotib');
    const fotoLabel = document.getElementById('foto_khotib_label');
    const fotoLabelSpan = fotoLabel ? fotoLabel.querySelector('span') : null;
    const clearFileBtn = document.getElementById('clearFile');
    
    // Elemen Preview
    const preview = document.getElementById('previewFoto');
    const previewContainer = document.getElementById('previewContainer');

    // Elemen Filter & Pagination
    const statusFilter = document.getElementById('statusFilter');
    const paginationContainer = document.getElementById('paginationLinks');
    const paginationInfo = document.getElementById('paginationInfo');
    const tableContainer = document.getElementById('tabelKhotib');

    const sortTanggal = document.getElementById('sortTanggal');
    const sortIcon = document.getElementById('sortIcon');

    if (sortTanggal) {
        sortTanggal.addEventListener('click', () => {
            // Toggle arah sort
            state.sortDir = state.sortDir === 'asc' ? 'desc' : 'asc';
            sortIcon.className = state.sortDir === 'asc' ? 'bi bi-arrow-up' : 'bi bi-arrow-down';
            loadKhotib();
        });
    }


    // State Management
    let state = {
        currentPage: 1,
        status: 'aktif',
        search: '',
        perPage: 10,
        sortBy: 'tanggal',    
        sortDir: 'desc',      
        searchTimeout: null
    };


    // --- Event Listener Utama ---

    // 1. Submit form (tambah / edit)
    if (form) {
        form.addEventListener('submit', async e => {
            e.preventDefault();
            setLoading(true);

            const id = document.getElementById('id_khutbah').value;
            const formData = new FormData(form);
            const url = id ? `/khotib-jumat/${id}` : '/khotib-jumat';
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
                    bootstrap.Modal.getInstance(modalKhotib).hide();
                    loadKhotib(); // Muat ulang data
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
                // Pastikan loading di-stop, bahkan jika error
                setLoading(false);
            }
        });
    }

    // 2. Search Bar (Server-side)
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            // Hentikan timeout sebelumnya
            clearTimeout(state.searchTimeout);

            // Set timeout baru (300ms) sebelum memicu search
            state.searchTimeout = setTimeout(() => {
                state.search = searchInput.value;
                state.currentPage = 1; // Reset ke halaman 1
                loadKhotib();
            }, 300); 
        });
    }

    // 3. Listener untuk Filter Status
    if (statusFilter) {
        statusFilter.addEventListener('change', () => {
            state.status = statusFilter.value;
            state.currentPage = 1; // Reset ke halaman 1
            loadKhotib();
        });
    }

    // 4. Listener untuk Klik Pagination
    if (paginationContainer) {
        paginationContainer.addEventListener('click', e => {
            e.preventDefault();
            const target = e.target.closest('a.page-link'); // Cari link yang diklik
            
            if (!target || target.parentElement.classList.contains('disabled') || target.parentElement.classList.contains('active')) {
                return; // Abaikan jika link non-aktif atau halaman saat ini
            }

            const url = new URL(target.href);
            const page = url.searchParams.get('page'); // Ambil nomor halaman dari URL
            
            if (page) {
                state.currentPage = parseInt(page);
                loadKhotib();
            }
        });
    }

    // 5. Reset Modal saat ditutup
    if (modalKhotib && form && fotoInput) {
        modalKhotib.addEventListener('hidden.bs.modal', function () {
            form.reset();
            fotoInput.dispatchEvent(new Event('change'));
            document.getElementById('id_khutbah').value = '';
            setLoading(false); // Pastikan tombol simpan kembali normal
        });
    }

    // --- Fungsi Helper ---

    // Fungsi untuk mengaktifkan/menonaktifkan loading tombol
    function setLoading(isLoading) {
        if (!submitButton || !cancelButton) return;

        if (isLoading) {
            submitButton.disabled = true;
            cancelButton.disabled = true;
            submitButton.innerHTML = `
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                Menyimpan...`;
        } else {
            submitButton.disabled = false;
            cancelButton.disabled = false;
            submitButton.innerHTML = originalButtonText;
        }
    }

    // --- Fungsi Render ---
    async function loadKhotib() {
        if (!tbody) return;
        
        // Tampilkan loading di tabel
        let colCount = tbody.closest('table').querySelector('thead tr').cells.length;
        tbody.innerHTML = `<tr><td colspan="${colCount}" class="text-center"><div class="spinner-border text-primary"></div></td></tr>`;

        // Buat URL dengan query params
        const url = `/khotib-jumat-data?page=${state.currentPage}&status=${state.status}&search=${state.search}&perPage=${state.perPage}&sortBy=${state.sortBy}&sortDir=${state.sortDir}`;

        try {
            const res = await fetch(url);
            if (!res.ok) throw new Error('Gagal memuat data');
            
            const response = await res.json(); // Ini adalah response pagination Laravel default
            
            renderTable(response.data, response.from || 1);
            renderPagination(response); // Kirim seluruh response
            
        } catch (err) {
            tbody.innerHTML = `<tr><td colspan="${colCount}" class="text-center text-danger">${err.message}</td></tr>`;
            paginationInfo.textContent = 'Gagal memuat data';
            paginationContainer.innerHTML = '';
        }
    }


    function formatTanggal(tanggalStr) {
        if (!tanggalStr) return '-';

        // Jika formatnya dd/mm/yyyy atau dd-mm-yyyy
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

        // Coba parse default jika format sudah ISO
        const date = new Date(tanggalStr);
        return !isNaN(date)
            ? date.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })
            : 'Invalid Date';
    }

    /**
     * Fungsi untuk me-render isi tabel
     */
    function renderTable(data, startingNumber) {
        tbody.innerHTML = ''; // Kosongkan tabel
        
        if (data.length === 0) {
            let colCount = tbody.closest('table').querySelector('thead tr').cells.length;
            tbody.innerHTML = `<tr><td colspan="${colCount}" class="text-center">Belum ada data.</td></tr>`;
            return;
        }

        data.forEach((item, i) => {
            const row = `
            <tr>
                <td class="text-center">${startingNumber + i}</td>
                <td class="text-center"><img src="${item.foto_url}" class="rounded" style="width:60px;height:60px;object-fit:cover;" alt="Foto ${item.nama_khotib}"></td>
                <td>${item.nama_khotib}</td>
                <td>${item.nama_imam}</td>
                <td>${item.tema_khutbah}</td> 
                <td class="text-center">${formatTanggal(item.tanggal)}</td>
                <td class="text-center">
                    <button class="btn btn-warning btn-sm" onclick="editKhotib('${item.id_khutbah}')">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-danger btn-sm" onclick="hapusKhotib('${item.id_khutbah}')">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>`;
            tbody.insertAdjacentHTML('beforeend', row);
        });
    }

    /**
     * Fungsi untuk me-render link pagination
     */
    function renderPagination(response) {
        const { from, to, total, links } = response;

        if (total === 0) {
            paginationInfo.textContent = 'Menampilkan 0 dari 0 data';
            paginationContainer.innerHTML = '';
            return;
        }

        // Update info: "Menampilkan 1 - 10 dari 100 data"
        paginationInfo.textContent = `Menampilkan ${from} - ${to} dari ${total} data`;

        // HTML pagination
        let linksHtml = '<ul class="pagination justify-content-center mb-0">';
        
        links.forEach(link => {
            let label = link.label;
            if (label.includes('Previous')) label = '<';
            else if (label.includes('Next')) label = '>';
            
            const disabled = !link.url ? 'disabled' : '';
            const active = link.active ? 'active' : '';

            linksHtml += `
                <li class="page-item ${disabled} ${active}">
                    <a class="page-link" href="${link.url || '#'}">${label}</a>
                </li>
            `;
        });

        linksHtml += '</ul>';
        paginationContainer.innerHTML = linksHtml;
    }



    

    
    

    // --- Fungsi Global (Edit/Hapus) ---
    window.editKhotib = async function(id_khutbah) {
        try {
            const res = await fetch(`/khotib-jumat/${id_khutbah}`);
            if (!res.ok) throw new Error('Data tidak ditemukan');
            const data = await res.json();

            document.getElementById('id_khutbah').value = data.id_khutbah;
            document.getElementById('nama_khotib').value = data.nama_khotib;
            document.getElementById('nama_imam').value = data.nama_imam;
            document.getElementById('tema_khutbah').value = data.tema_khutbah;
            if (data.tanggal) {
                document.getElementById('tanggal').value = data.tanggal.split('T')[0];
            }

            if (data.foto_khotib) {
                fotoLabelSpan.textContent = data.foto_khotib.split('/').pop();
                fotoLabelSpan.classList.remove('text-muted');
                clearFileBtn.classList.remove('d-none');
                preview.src = data.foto_url;
                previewContainer.classList.remove('d-none');
            } else {
                fotoInput.dispatchEvent(new Event('change'));
            }

            new bootstrap.Modal(modalKhotib).show();
        } catch (err) {
            Swal.fire('Gagal', err.message, 'error');
        }
    }

    window.hapusKhotib = async function(id_khutbah) {
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

            const res = await fetch(`/khotib-jumat/${id_khutbah}`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
                body: formData
            });

            const data = await res.json();
            if (res.ok) {
                Swal.fire('Terhapus!', data.message, 'success');
                loadKhotib(); // Muat ulang data
            } else {
                throw new Error(data.message || 'Terjadi kesalahan');
            }
        } catch (err) {
            Swal.fire('Gagal', err.message, 'error');
        }
    }

    // --- Logika Input File Kustom (Tidak berubah) ---
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
    loadKhotib(); // Muat data pertama kali
});