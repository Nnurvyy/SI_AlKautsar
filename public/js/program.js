document.addEventListener('DOMContentLoaded', () => {

    // --- Definisi Elemen (Diubah ke Program) ---
    // Menggunakan ID yang disesuaikan untuk Program
    const form = document.getElementById('formProgram'); 
    const modalProgram = document.getElementById('modalProgram'); 
    const searchInput = document.getElementById('searchInput');
    const tbody = document.querySelector('#tabelKhotib tbody'); // ID tbody tabel Program
    const token = document.querySelector('meta[name="csrf-token"]').content;
    
    // Tombol Modal
    const submitButton = form ? form.querySelector('button[type="submit"]') : null;
    const cancelButton = form ? form.querySelector('button[data-bs-dismiss="modal"]') : null;
    const originalButtonText = submitButton ? submitButton.innerHTML : 'Simpan Data';

    // Elemen Input File Kustom
    const fotoInput = document.getElementById('foto_program'); // Input file
    const fotoLabel = document.getElementById('foto_program_label'); 
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
    
    // Elemen Status Program di Modal
    const statusProgramSelect = document.getElementById('status_program'); 

    // ID Header sorting
    const sortTanggal = document.getElementById('sortTanggal'); 
    const sortIcon = document.getElementById('sortIcon');

    if (sortTanggal) {
        sortTanggal.addEventListener('click', () => {
            state.sortDir = state.sortDir === 'asc' ? 'desc' : 'asc';
            sortIcon.className = state.sortDir === 'asc' ? 'bi bi-arrow-up' : 'bi bi-arrow-down';
            loadProgram(); 
        });
    }

    // State Management
    let state = {
        currentPage: 1,
        status: 'all', 
        search: '',
        perPage: 10,
        sortBy: 'tanggal_program', 
        sortDir: 'desc',        
        searchTimeout: null
    };


    // --- Event Listener Utama ---

    // 1. Submit form (tambah / edit)
    if (form) {
        form.addEventListener('submit', async e => {
            e.preventDefault();
            setLoading(true);

            // Ambil ID dari input hidden
            const id = document.getElementById('id_program').value; 
            const formData = new FormData(form);
            
            // Endpoint disesuaikan
            const url = id ? `/admin/program/${id}` : '/admin/program';
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
                    // Menggunakan ID modal yang benar
                    bootstrap.Modal.getInstance(modalProgram).hide(); 
                    loadProgram(); 
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

    // 2. Search Bar (Server-side)
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            clearTimeout(state.searchTimeout);

            state.searchTimeout = setTimeout(() => {
                state.search = searchInput.value;
                state.currentPage = 1; 
                loadProgram(); 
            }, 300); 
        });
    }

    // 3. Listener untuk Filter Status
    if (statusFilter) {
        statusFilter.addEventListener('change', () => {
            state.status = statusFilter.value;
            state.currentPage = 1; 
            loadProgram(); 
        });
    }

    // 4. Listener untuk Klik Pagination
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
                loadProgram(); 
            }
        });
    }

    // 5. Reset Modal saat ditutup
    if (modalProgram && form && fotoInput) { 
        modalProgram.addEventListener('hidden.bs.modal', function () {
            form.reset();
            // Memastikan logika reset visual foto dipicu
            fotoInput.dispatchEvent(new Event('change')); 
            document.getElementById('id_program').value = ''; 
            setLoading(false);
            
            // Set status default saat modal dibuka (untuk form Tambah)
            if (statusProgramSelect) {
                 statusProgramSelect.value = 'belum dilaksanakan';
            }
        });
    }

    // --- Fungsi Helper ---

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
    async function loadProgram() { 
        if (!tbody) return;
        
        // Tampilkan Loading
        let colCount = tbody.closest('table').querySelector('thead tr').cells.length;
        tbody.innerHTML = `<tr><td colspan="${colCount}" class="text-center"><div class="spinner-border text-primary"></div></td></tr>`;

        // URL endpoint diubah
        const url = `/admin/program-data?page=${state.currentPage}&status=${state.status}&search=${state.search}&perPage=${state.perPage}&sortBy=${state.sortBy}&sortDir=${state.sortDir}`;

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


    function formatTanggal(tanggalStr) {
        if (!tanggalStr) return '-';

        // Format tanggal program yang kini juga mengandung waktu (dateTime)
        const date = new Date(tanggalStr);
        return !isNaN(date)
            ? date.toLocaleDateString('id-ID', { 
                day: '2-digit', month: 'short', year: 'numeric',
                hour: '2-digit', minute: '2-digit' 
            })
            : 'Invalid Date';
    }

    /**
     * Fungsi untuk me-render isi tabel
     */
    function renderTable(data, startingNumber) {
        tbody.innerHTML = ''; 
        
        if (data.length === 0) {
            let colCount = tbody.closest('table').querySelector('thead tr').cells.length;
            tbody.innerHTML = `<tr><td colspan="${colCount}" class="text-center">Belum ada data.</td></tr>`;
            return;
        }

        data.forEach((item, i) => {
            // Fungsi untuk menampilkan badge status (SESUAIKAN DENGAN WARNA)
            const statusBadge = (status) => {
                let color;
                switch (status) {
                    case 'sudah dijalankan':
                        color = 'badge bg-secondary'; // Abu-abu: Selesai/Tidak Aktif
                        break;
                    case 'sedang berjalan':
                        color = 'badge bg-warning text-dark'; // Kuning: Perhatian/Berjalan
                        break;
                    case 'belum dilaksanakan':
                        color = 'badge bg-success'; // Hijau: Aktif/Siap
                        break;
                    default:
                        color = 'badge bg-info';
                }
                
                const statusText = status || 'Belum Ditentukan'; 
                return `<span class="${color}">${statusText}</span>`;
            };  
            
            const row = `
            <tr>
                <td class="text-center">${startingNumber + i}</td>

                <td class="text-center">${item.nama_program}</td>
                <td class="text-center">${formatTanggal(item.tanggal_program)}</td>
                <td class="text-center">${item.lokasi_program}</td>
                <td class="text-center">${statusBadge(item.status_program)}</td> <!-- Tampilkan Status dengan Badge -->
                <td class="text-center">
                    <button class="btn btn-info btn-sm text-white me-1" onclick="showDetailProgram('${item.id_program}')">
                        <i class="bi bi-eye"></i>
                    </button>
                    <button class="btn btn-warning btn-sm" onclick="editProgram('${item.id_program}')">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-danger btn-sm" onclick="hapusProgram('${item.id_program}')">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>`;
            tbody.insertAdjacentHTML('beforeend', row);
        });
    }

    /**
     * Fungsi untuk me-render link pagination (Tidak Berubah)
     */
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
    /**
 * Fungsi Global untuk Memuat Data Program ke dalam Modal Edit
 * Dipanggil dari tombol 'Edit' di tabel.
 */
    window.editProgram = async function(id_program) {
        try {
            // 1. Ambil data program dari API
            const res = await fetch(`/admin/program/${id_program}`);
            if (!res.ok) throw new Error('Data tidak ditemukan');

            const data = await res.json();

            // Variabel global (diasumsikan sudah didefinisikan di luar fungsi):
            const fotoInput = document.getElementById('foto_program');
            const previewContainer = document.getElementById('previewContainer');
            const previewFoto = document.getElementById('previewFoto');
            const clearFileBtn = document.getElementById('clearFile');
            const statusSelect = document.getElementById('status_program');
            
            // === 2. Isi field utama ===
            document.getElementById('id_program').value = data.id_program;
            document.getElementById('nama_program').value = data.nama_program;
            document.getElementById('penyelenggara_program').value = data.penyelenggara_program;
            document.getElementById('lokasi_program').value = data.lokasi_program;
            document.getElementById('deskripsi_program').value = data.deskripsi_program;

            // === 3. Status Program ===
            // Mengisi nilai Status (menggunakan variabel global statusSelect)
            if (statusSelect) {
                statusSelect.value = data.status_program || 'belum dilaksanakan';
            }

            // === 4. Tanggal (datetime-local) ===
            // Format harus YYYY-MM-DDTHH:MM
            if (data.tanggal_program) {
                document.getElementById('tanggal_program').value =
                    // Mengambil bagian tanggal dan waktu (0-15) lalu mengganti spasi menjadi 'T'
                    data.tanggal_program.substring(0, 16).replace(' ', 'T'); 
            } else {
                document.getElementById('tanggal_program').value = '';
            }

            // === 5. Foto Preview & Input File ===
            // Hapus nilai input file lama (penting agar user bisa pilih file baru)
            if (fotoInput) fotoInput.value = ""; 

            // Tampilkan/sembunyikan preview foto lama
            if (previewFoto && previewContainer) {
                if (data.foto_url) { // Menggunakan 'foto_url' dari Accessor Laravel
                    previewFoto.src = data.foto_url;
                    previewContainer.classList.remove('d-none');
                    if (clearFileBtn) clearFileBtn.classList.remove('d-none');
                } else {
                    previewFoto.src = "";
                    previewContainer.classList.add('d-none');
                    if (clearFileBtn) clearFileBtn.classList.add('d-none');
                }
            }
            
            // === 6. Tampilkan Modal ===
            // Menggunakan instance Bootstrap Modal
            const modalInstance = bootstrap.Modal.getInstance(modalProgram) || new bootstrap.Modal(modalProgram);
            modalInstance.show();

        } catch (err) {
            Swal.fire('Gagal', err.message, 'error');
        }
    };


    window.hapusProgram = async function(id_program) {
        const confirm = await Swal.fire({
            title: 'Yakin ingin menghapus?',
            text: 'Data program akan dihapus permanen!',
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

            const res = await fetch(`/admin/program/${id_program}`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
                body: formData
            });

            const data = await res.json();
            if (res.ok) {
                Swal.fire('Terhapus!', data.message, 'success');
                loadProgram(); // Muat ulang data
            } else {
                throw new Error(data.message || 'Terjadi kesalahan');
            }
        } catch (err) {
            Swal.fire('Gagal', err.message, 'error');
        }
    }

/**
 * Fungsi Global untuk menampilkan detail program lengkap di modal yang lebih besar.
 */
    window.showDetailProgram = async function(id_program) {
        try {
            // Ambil data detail (endpoint yang sama, tapi hasilnya digunakan untuk detail)
            const res = await fetch(`/admin/program/${id_program}`);
            if (!res.ok) throw new Error('Detail program tidak ditemukan');

            const data = await res.json();
            
            // --- Mempersiapkan data dan elemen ---
            
            // Asumsi fungsi formatTanggal dan statusBadge sudah ada
            const formattedDate = formatTanggal(data.tanggal_program); 
            
            // 1. Isi Judul Modal
            document.getElementById('detailNamaProgram').textContent = data.nama_program;

            // 2. Isi Detail Table
            document.getElementById('d_nama').textContent = data.nama_program;
            document.getElementById('d_penyelenggara').textContent = data.penyelenggara_program;
            document.getElementById('d_lokasi').textContent = data.lokasi_program;
            document.getElementById('d_tanggal').textContent = formattedDate;
            
            // Menggunakan statusBadge untuk status
            document.getElementById('d_status').innerHTML = statusBadge(data.status_program); 
            
            // Deskripsi (di div terpisah)
            document.getElementById('d_deskripsi').textContent = data.deskripsi_program;

            // 3. Isi Foto Program
            const detailFoto = document.getElementById('detailFotoProgram');
            
            // Menggunakan foto_url dari accessor Model. Jika null, tampilkan placeholder.
            detailFoto.src = data.foto_url || '/images/default_program.png'; 
            
            // 4. Tampilkan Modal Detail
            const modalDetail = new bootstrap.Modal(document.getElementById('modalDetailProgram'));
            modalDetail.show();

        } catch (err) {
            Swal.fire('Gagal', err.message, 'error');
        }
    };

    // Pastikan fungsi statusBadge di scope global jika Anda belum mendefinisikannya di luar renderTable
    const statusBadge = (status) => {
        let color;
        switch (status) {
            case 'sudah dijalankan': color = 'badge bg-secondary'; break;
            case 'sedang berjalan': color = 'badge bg-warning text-dark'; break;
            case 'belum dilaksanakan': color = 'badge bg-success'; break;
            default: color = 'badge bg-info';
        }
        const statusText = status || 'Belum Ditentukan'; 
        return `<span class="${color}">${statusText}</span>`;
    };

    // --- Logika Input File Kustom (Tidak berubah) ---
    if (fotoInput && fotoLabelSpan && clearFileBtn && previewContainer && preview) {
        fotoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                fotoLabelSpan.textContent = "Foto terpilih: " + file.name; 
                fotoLabelSpan.classList.remove('text-muted');
                clearFileBtn.classList.remove('d-none');
                const reader = new FileReader();
                reader.onload = function(event) {
                    preview.src = event.target.result;
                    previewContainer.classList.remove('d-none');
                }
                reader.readAsDataURL(file);
            } else {
                fotoLabelSpan.textContent = "Pilih file..."; 
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
    loadProgram(); // Muat data pertama kali
});