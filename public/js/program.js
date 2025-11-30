document.addEventListener('DOMContentLoaded', () => {

    // --- Definisi Elemen ---
    const form = document.getElementById('formProgram'); 
    const modalProgramEl = document.getElementById('modalProgram');
    // Inisialisasi Bootstrap Modal
    const modalProgram = new bootstrap.Modal(modalProgramEl); 
    
    const searchInput = document.getElementById('searchInput');
    const tbody = document.querySelector('#tabelProgram tbody'); // Selector ID Tabel diperbaiki
    const token = document.querySelector('meta[name="csrf-token"]').content;
    
    // Tombol Modal
    const submitButton = form ? form.querySelector('button[type="submit"]') : null;
    const cancelButton = form ? form.querySelector('button[data-bs-dismiss="modal"]') : null;
    const originalButtonText = submitButton ? submitButton.innerHTML : 'Simpan';

    // Elemen Input File Kustom (Sesuai Kajian)
    const fotoInput = document.getElementById('foto_program'); 
    const fotoLabel = document.getElementById('foto_program_label'); 
    const fotoLabelSpan = fotoLabel ? fotoLabel.querySelector('span') : null;
    const clearFileBtn = document.getElementById('clearFile');
    
    // Elemen Preview
    const preview = document.getElementById('previewFoto');
    const previewContainer = document.getElementById('previewContainer');

    // Filter
    const statusFilter = document.getElementById('statusFilter');
    const paginationContainer = document.getElementById('paginationLinks');
    const paginationInfo = document.getElementById('paginationInfo');
    const statusProgramSelect = document.getElementById('status_program'); 

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

    // --- Event Listener ---

    // 1. Submit form (tambah / edit)
    if (form) {
        form.addEventListener('submit', async e => {
            e.preventDefault();
            setLoading(true);

            const id = document.getElementById('id_program').value; 
            const formData = new FormData(form);
            
            const url = id ? `/pengurus/program/${id}` : '/pengurus/program';
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
                    modalProgram.hide(); 
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

    // 2. Search
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

    // 3. Filter Status
    if (statusFilter) {
        statusFilter.addEventListener('change', () => {
            state.status = statusFilter.value;
            state.currentPage = 1; 
            loadProgram(); 
        });
    }

    // 4. Pagination
    if (paginationContainer) {
        paginationContainer.addEventListener('click', e => {
            e.preventDefault();
            const target = e.target.closest('a.page-link'); 
            if (!target || target.parentElement.classList.contains('disabled') || target.parentElement.classList.contains('active')) return;

            const url = new URL(target.href);
            const page = url.searchParams.get('page'); 
            if (page) {
                state.currentPage = parseInt(page);
                loadProgram(); 
            }
        });
    }

    // 5. Reset Modal saat ditutup & Logika Reset Foto
    if (modalProgramEl) { 
        modalProgramEl.addEventListener('hidden.bs.modal', function () {
            form.reset();
            document.getElementById('id_program').value = ''; 
            
            // Reset Tampilan File Input
            if (fotoInput) {
                fotoInput.value = "";
                // Dispatch event manual agar UI terupdate
                fotoInput.dispatchEvent(new Event('change')); 
            }
            
            setLoading(false);
            
            // Default select status
            if (statusProgramSelect) {
                 statusProgramSelect.value = 'belum dilaksanakan';
            }
        });
    }

    // --- LOGIKA FILE INPUT (Mirip Kajian) ---
    if (fotoInput) {
        fotoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Ada file dipilih
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
                // Tidak ada file (atau di-reset)
                // Cek apakah ini mode Edit dan sebelumnya ada foto? 
                // Jika reset manual (value kosong), kembalikan ke default
                if (!fotoInput.dataset.hasExisting) {
                    fotoLabelSpan.textContent = "Pilih foto..."; 
                    fotoLabelSpan.classList.add('text-muted');
                    clearFileBtn.classList.add('d-none');
                    preview.src = "";
                    previewContainer.classList.add('d-none');
                }
            }
        });

        // Tombol Hapus (X)
        clearFileBtn.addEventListener('click', function(e) {
            e.stopPropagation(); 
            fotoInput.value = ""; 
            // Hapus flag existing saat user manual menghapus
            delete fotoInput.dataset.hasExisting; 
            fotoInput.dispatchEvent(new Event('change'));
        });
    }

    // --- Fungsi Helper ---

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

    function formatTanggal(tanggalStr) {
        if (!tanggalStr) return '-';
        const date = new Date(tanggalStr);
        return !isNaN(date)
            ? date.toLocaleDateString('id-ID', { 
                day: '2-digit', month: 'short', year: 'numeric',
                hour: '2-digit', minute: '2-digit' 
            })
            : '-';
    }

    // --- Render Table ---
    async function loadProgram() { 
        if (!tbody) return;
        
        let colCount = tbody.closest('table').querySelector('thead tr').cells.length;
        tbody.innerHTML = `<tr><td colspan="${colCount}" class="text-center"><div class="spinner-border text-primary"></div></td></tr>`;

        const url = `/pengurus/program-data?page=${state.currentPage}&status=${state.status}&search=${state.search}&perPage=${state.perPage}&sortBy=${state.sortBy}&sortDir=${state.sortDir}`;

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
            const statusBadge = (status) => {
                let color;
                switch (status) {
                    case 'sudah dijalankan': color = 'badge bg-secondary'; break;
                    case 'sedang berjalan': color = 'badge bg-warning text-dark'; break;
                    case 'belum dilaksanakan': color = 'badge bg-success'; break;
                    default: color = 'badge bg-info';
                }
                return `<span class="${color}">${status || 'Belum Ditentukan'}</span>`;
            };  
            
            const row = `
                <tr>
                    <td class="text-center">${startingNumber + i}</td>
                    <td class="text-center">
                        <img src="${item.foto_url}" class="rounded shadow-sm" style="width:50px; height:50px; object-fit: cover;" alt="Foto">
                    </td>
                    <td class="text-center">${item.nama_program}</td>
                    <td class="text-center">${formatTanggal(item.tanggal_program)}</td>
                    <td class="text-center">${item.lokasi_program}</td>
                    <td class="text-center">${statusBadge(item.status_program)}</td>
                    
                    <td class="text-center text-nowrap aksi-col">
                        <button class="btn btn-info btn-sm text-white me-1" onclick="showDetailProgram('${item.id_program}')">
                            <i class="bi bi-eye"></i>
                        </button>
                        <button class="btn btn-warning btn-sm me-1" onclick="editProgram('${item.id_program}')">
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
            const disabled = !link.url ? 'disabled' : '';
            const active = link.active ? 'active' : '';
            linksHtml += `<li class="page-item ${disabled} ${active}"><a class="page-link" href="${link.url || '#'}">${label}</a></li>`;
        });
        linksHtml += '</ul>';
        paginationContainer.innerHTML = linksHtml;
    }

    // --- Global Functions ---

    window.editProgram = async function(id_program) {
        try {
            const res = await fetch(`/pengurus/program/${id_program}`);
            if (!res.ok) throw new Error('Data tidak ditemukan');

            const data = await res.json();

            // Isi Form
            document.getElementById('id_program').value = data.id_program;
            document.getElementById('nama_program').value = data.nama_program;
            document.getElementById('penyelenggara_program').value = data.penyelenggara_program;
            document.getElementById('lokasi_program').value = data.lokasi_program;
            document.getElementById('deskripsi_program').value = data.deskripsi_program;
            if (statusProgramSelect) statusProgramSelect.value = data.status_program;

            if (data.tanggal_program) {
                // Convert to datetime-local format
                document.getElementById('tanggal_program').value = data.tanggal_program.substring(0, 16).replace(' ', 'T'); 
            }

            // --- Logic Preview Foto saat Edit ---
            fotoInput.value = ""; // Reset input file
            
            if (data.foto_program) {
                // Set text label ke nama file (atau placeholder 'Foto tersimpan')
                fotoLabelSpan.textContent = data.foto_program.split('/').pop();
                fotoLabelSpan.classList.remove('text-muted');
                
                // Tampilkan tombol hapus dan preview
                clearFileBtn.classList.remove('d-none');
                preview.src = data.foto_url; 
                previewContainer.classList.remove('d-none');
                
                // Set flag bahwa ini gambar dari DB
                fotoInput.dataset.hasExisting = "true";
            } else {
                // Reset UI jika tidak ada foto
                fotoLabelSpan.textContent = "Pilih foto...";
                fotoLabelSpan.classList.add('text-muted');
                clearFileBtn.classList.add('d-none');
                previewContainer.classList.add('d-none');
                delete fotoInput.dataset.hasExisting;
            }

            modalProgram.show();
        } catch (err) {
            Swal.fire('Gagal', err.message, 'error');
        }
    };

    window.hapusProgram = async function(id_program) {
        const confirm = await Swal.fire({
            title: 'Yakin ingin menghapus?',
            text: 'Data akan dihapus permanen!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal'
        });

        if (!confirm.isConfirmed) return;

        try {
            const formData = new FormData();
            formData.append('_method', 'DELETE');

            const res = await fetch(`/pengurus/program/${id_program}`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
                body: formData
            });

            const data = await res.json();
            if (res.ok) {
                Swal.fire('Terhapus!', data.message, 'success');
                loadProgram();
            } else {
                throw new Error(data.message || 'Terjadi kesalahan');
            }
        } catch (err) {
            Swal.fire('Gagal', err.message, 'error');
        }
    }

    window.showDetailProgram = async function(id_program) {
        try {
            const res = await fetch(`/pengurus/program/${id_program}`);
            if (!res.ok) throw new Error('Detail tidak ditemukan');
            const data = await res.json();
            
            // Isi detail
            document.getElementById('d_nama').textContent = data.nama_program;
            document.getElementById('d_penyelenggara').textContent = data.penyelenggara_program;
            document.getElementById('d_lokasi').textContent = data.lokasi_program;
            document.getElementById('d_tanggal').textContent = formatTanggal(data.tanggal_program);
            document.getElementById('d_status').textContent = data.status_program;
            document.getElementById('d_deskripsi').textContent = data.deskripsi_program;
            
            // Foto Detail
            const detailFoto = document.getElementById('detailFotoProgram');
            detailFoto.src = data.foto_url || '/images/default_program.png';
            
            const modalDetail = new bootstrap.Modal(document.getElementById('modalDetailProgram'));
            modalDetail.show();

        } catch (err) {
            Swal.fire('Gagal', err.message, 'error');
        }
    };

    // Init
    loadProgram();
});