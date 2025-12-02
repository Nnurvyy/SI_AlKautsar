document.addEventListener('DOMContentLoaded', () => {

    // --- 1. Definisi Elemen ---
    const form = document.getElementById('formKajian');
    const modalKajianEl = document.getElementById('modalKajian');
    const modalKajian = new bootstrap.Modal(modalKajianEl);
    const searchInput = document.getElementById('searchInput');
    const tbody = document.querySelector('#tabelKajian tbody');
    const token = document.querySelector('meta[name="csrf-token"]').content;
    
    const submitButton = form ? form.querySelector('button[type="submit"]') : null;
    const cancelButton = form ? form.querySelector('button[data-bs-dismiss="modal"]') : null;
    const originalButtonText = submitButton ? submitButton.innerHTML : 'Simpan';

    // Input File
    const fotoInput = document.getElementById('foto_penceramah');
    const fotoLabel = document.getElementById('foto_penceramah_label');
    const fotoLabelSpan = fotoLabel ? fotoLabel.querySelector('span') : null;
    const clearFileBtn = document.getElementById('clearFile');
    const preview = document.getElementById('previewFoto');
    const previewContainer = document.getElementById('previewContainer');

    // Filter & Table
    const statusFilter = document.getElementById('statusFilter');
    const tipeFilter = document.getElementById('tipeFilter'); 
    const paginationContainer = document.getElementById('paginationLinks');
    const paginationInfo = document.getElementById('paginationInfo');
    const sortTanggal = document.getElementById('sortTanggal');
    const sortIcon = document.getElementById('sortIcon');

    // --- Elemen Baru untuk Logic Hari/Tanggal ---
    const tipeInput = document.getElementById('tipe'); 
    const wrapperHari = document.getElementById('wrapperHari');
    const wrapperTanggal = document.getElementById('wrapperTanggal');
    const inputHari = document.getElementById('hari');
    const inputTanggal = document.getElementById('tanggal_kajian');

    let state = {
        currentPage: 1,
        status: 'aktif',
        tipe: '', 
        search: '',
        perPage: 10,
        sortBy: 'tanggal_kajian',
        sortDir: 'desc',       
        searchTimeout: null
    };

    // --- 2. Fungsi Logic Tampilan Input (BARU) ---
    function toggleInputType() {
        if (!tipeInput) return;

        if (tipeInput.value === 'rutin') {
            // Jika RUTIN: Tampilkan Hari, Sembunyikan Tanggal
            wrapperHari.classList.remove('d-none');
            wrapperTanggal.classList.add('d-none');
            
            // Set required pada Hari
            if(inputHari) inputHari.setAttribute('required', 'required');
            if(inputTanggal) {
                inputTanggal.removeAttribute('required');
                inputTanggal.value = ''; // Reset tanggal
            }
        } else {
            // Jika EVENT: Tampilkan Tanggal, Sembunyikan Hari
            wrapperHari.classList.add('d-none');
            wrapperTanggal.classList.remove('d-none');
            
            // Set required pada Tanggal
            if(inputTanggal) inputTanggal.setAttribute('required', 'required');
            if(inputHari) {
                inputHari.removeAttribute('required');
                inputHari.value = ''; // Reset hari
            }
        }
    }

    // --- 3. Event Listener ---

    // Listener Ganti Tipe di Modal (BARU)
    if (tipeInput) {
        tipeInput.addEventListener('change', toggleInputType);
        toggleInputType(); // Jalankan sekali saat load
    }

    // Listener Sorting
    if (sortTanggal) {
        sortTanggal.addEventListener('click', () => {
            state.sortDir = state.sortDir === 'asc' ? 'desc' : 'asc';
            sortIcon.className = state.sortDir === 'asc' ? 'bi bi-arrow-up' : 'bi bi-arrow-down';
            loadKajian();
        });
    }

    // Listener Filter Status
    if (statusFilter) {
        statusFilter.addEventListener('change', () => {
            state.status = statusFilter.value;
            state.currentPage = 1;
            loadKajian();
        });
    }

    // Listener Filter Tipe (Di Header)
    if (tipeFilter) {
        tipeFilter.addEventListener('change', () => {
            state.tipe = tipeFilter.value;
            state.currentPage = 1;
            loadKajian();
        });
    }

    // Listener Search
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            clearTimeout(state.searchTimeout);
            state.searchTimeout = setTimeout(() => {
                state.search = searchInput.value;
                state.currentPage = 1; 
                loadKajian();
            }, 300); 
        });
    }

    // Listener Pagination
    if (paginationContainer) {
        paginationContainer.addEventListener('click', e => {
            e.preventDefault();
            const target = e.target.closest('a.page-link');
            if (!target || target.parentElement.classList.contains('disabled') || target.parentElement.classList.contains('active')) return;
            
            const url = new URL(target.href);
            const page = url.searchParams.get('page'); 
            if (page) {
                state.currentPage = parseInt(page);
                loadKajian();
            }
        });
    }

    // --- 4. CRUD Operations (Submit Form) ---

    if (form) {
        form.addEventListener('submit', async e => {
            e.preventDefault();
            setLoading(true);

            const id = document.getElementById('id_kajian').value;
            const formData = new FormData(form);
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
                    modalKajian.hide();
                    loadKajian();
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

    // Reset Modal Saat Ditutup
    if (modalKajianEl) {
        modalKajianEl.addEventListener('hidden.bs.modal', function () {
            form.reset();
            if(fotoInput) fotoInput.dispatchEvent(new Event('change'));
            document.getElementById('id_kajian').value = '';
            
            // Reset tipe ke default (rutin) dan update tampilan
            if(tipeInput) {
                tipeInput.value = 'rutin'; 
                toggleInputType(); 
            }
            setLoading(false); 
        });
    }

    // --- 5. Functions Load Data & Render ---

    async function loadKajian() {
        if (!tbody) return;
        
        let colCount = tbody.closest('table').querySelector('thead tr').cells.length;
        tbody.innerHTML = `<tr><td colspan="${colCount}" class="text-center"><div class="spinner-border text-primary"></div></td></tr>`;

        const url = `/pengurus/kajian-data?page=${state.currentPage}&status=${state.status}&tipe=${state.tipe}&search=${state.search}&perPage=${state.perPage}&sortBy=${state.sortBy}&sortDir=${state.sortDir}`;

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

    function renderTable(data, startingNumber) {
        tbody.innerHTML = ''; 
        
        if (data.length === 0) {
            let colCount = tbody.closest('table').querySelector('thead tr').cells.length;
            tbody.innerHTML = `<tr><td colspan="${colCount}" class="text-center">Belum ada data.</td></tr>`;
            return;
        }

        data.forEach((item, i) => {
            // Badge Tipe
            let tipeBadge = '';
            if (item.tipe === 'rutin') {
                tipeBadge = '<span class="badge bg-info text-dark">Rutin</span>';
            } else if (item.tipe === 'event') {
                tipeBadge = '<span class="badge bg-warning text-dark">Event</span>';
            }

            // Logic Tampilan Tanggal vs Hari di Tabel
            let infoJadwal = '';
            if(item.tipe === 'rutin') {
                // Tampilkan Hari
                infoJadwal = `<span class="fw-bold">Setiap ${item.hari || '-'}</span>`;
            } else {
                // Tampilkan Tanggal
                infoJadwal = formatTanggal(item.tanggal_kajian);
            }

            const row = `
            <tr>
                <td class="text-center">${startingNumber + i}</td>
                <td class="text-center">
                    <img src="${item.foto_url}" class="rounded" style="width:50px;height:50px;object-fit:cover;" alt="Foto">
                </td>
                <td class="text-center">${tipeBadge}</td> <td>${item.nama_penceramah}</td>
                <td>${item.tema_kajian}</td>
                <td class="text-center">${formatWaktu(item.waktu_kajian)}</td>
                <td class="text-center">${infoJadwal}</td>
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

    // --- Helpers ---
    function formatTanggal(str) {
        if (!str) return '-';
        const date = new Date(str);
        return !isNaN(date) ? date.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }) : '-';
    }

    function formatWaktu(str) {
        return str ? str.substring(0, 5) : '-';
    }

    function setLoading(isLoading) {
        if (!submitButton || !cancelButton) return;
        if (isLoading) {
            submitButton.disabled = true;
            cancelButton.disabled = true;
            submitButton.innerHTML = `<span class="spinner-border spinner-border-sm"></span> Menyimpan...`;
        } else {
            submitButton.disabled = false;
            cancelButton.disabled = false;
            submitButton.innerHTML = originalButtonText;
        }
    }

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
            let label = link.label.replace('&laquo; Previous', '<').replace('Next &raquo;', '>');
            let activeClass = link.active ? 'active' : '';
            let disabledClass = !link.url ? 'disabled' : '';
            linksHtml += `<li class="page-item ${activeClass} ${disabledClass}"><a class="page-link" href="${link.url || '#'}">${label}</a></li>`;
        });
        linksHtml += '</ul>';
        paginationContainer.innerHTML = linksHtml;
    }

    // --- File Input Logic ---
    if (fotoInput) {
        fotoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                fotoLabelSpan.textContent = file.name;
                fotoLabelSpan.classList.remove('text-muted');
                clearFileBtn.classList.remove('d-none');
                const reader = new FileReader();
                reader.onload = (ev) => {
                    preview.src = ev.target.result;
                    previewContainer.classList.remove('d-none');
                }
                reader.readAsDataURL(file);
            } else {
                fotoLabelSpan.textContent = "Choose file...";
                fotoLabelSpan.classList.add('text-muted');
                clearFileBtn.classList.add('d-none');
                previewContainer.classList.add('d-none');
            }
        });
        
        clearFileBtn.addEventListener('click', function(e) {
            e.stopPropagation(); 
            fotoInput.value = ""; 
            fotoInput.dispatchEvent(new Event('change'));
        });
    }

    // --- 6. Global Functions (Window) ---
    
    // UPDATE: Fungsi Edit dengan Logic Hari/Tanggal
    window.editKajian = async function(id) {
        try {
            const res = await fetch(`/pengurus/kajian/${id}`);
            if (!res.ok) throw new Error('Gagal mengambil data');
            const data = await res.json();

            document.getElementById('id_kajian').value = data.id_kajian;
            
            // 1. Set Tipe dulu
            if(tipeInput) {
                tipeInput.value = data.tipe; 
                toggleInputType(); // Trigger agar input yang sesuai muncul
            }

            document.getElementById('nama_penceramah').value = data.nama_penceramah;
            document.getElementById('tema_kajian').value = data.tema_kajian;
            if (data.waktu_kajian) document.getElementById('waktu_kajian').value = data.waktu_kajian.substring(0, 5);

            // 2. Isi data Hari ATAU Tanggal sesuai tipe
            if (data.tipe === 'rutin') {
                if(inputHari) inputHari.value = data.hari;
            } else {
                if (data.tanggal_kajian && inputTanggal) {
                    inputTanggal.value = data.tanggal_kajian.split('T')[0];
                }
            }

            // Logic Foto
            if (data.foto_penceramah) {
                fotoLabelSpan.textContent = data.foto_penceramah.split('/').pop();
                fotoLabelSpan.classList.remove('text-muted');
                clearFileBtn.classList.remove('d-none');
                preview.src = data.foto_url;
                previewContainer.classList.remove('d-none');
            } else {
                fotoInput.dispatchEvent(new Event('change'));
            }

            modalKajian.show();
        } catch (err) {
            Swal.fire('Error', err.message, 'error');
        }
    }

    window.hapusKajian = async function(id) {
        const c = await Swal.fire({
            title: 'Hapus Kajian?', text: "Data tidak bisa dikembalikan!", icon: 'warning',
            showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: 'Ya, hapus'
        });
        if (c.isConfirmed) {
            try {
                const formData = new FormData();
                formData.append('_method', 'DELETE');
                const res = await fetch(`/pengurus/kajian/${id}`, {
                    method: 'POST', headers: { 'X-CSRF-TOKEN': token }, body: formData
                });
                if (res.ok) {
                    Swal.fire('Terhapus!', '', 'success');
                    loadKajian();
                } else {
                    throw new Error('Gagal menghapus');
                }
            } catch (err) {
                Swal.fire('Error', err.message, 'error');
            }
        }
    }

    // Init Load
    loadKajian();
});