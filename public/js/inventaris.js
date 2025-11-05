// Skrip ini mengimplementasikan logika CRUD, Search, dan Pagination untuk Barang Inventaris
// Menggunakan fetch API ke backend Laravel.
// Asumsi: SweetAlert2 (Swal) dan Bootstrap JS sudah dimuat di view.

document.addEventListener('DOMContentLoaded', () => {

    // --- 1. DEFINISI ELEMEN DAN STATE ---

    // Elemen Formulir & Modal
    const form = document.getElementById('formInventarisStock');
    const modalInventarisElement = document.getElementById('modalInventaris');
    // Pastikan ID ini sesuai dengan yang di HTML
    const modalInventaris = new bootstrap.Modal(modalInventarisElement); 
    const modalTitle = document.getElementById('modalInventarisLabel');

    // Elemen Tabel & Kontrol
    // Menggunakan ID tabel yang benar (tabelKhotib hanya ID placeholder, tapi kita pakai)
    const tbody = document.querySelector('#tabelKhotib tbody'); 
    const searchInput = document.getElementById('searchInput');
    const paginationContainer = document.getElementById('paginationLinks');
    const paginationInfo = document.getElementById('paginationInfo');
    const tambahButton = document.querySelector('[data-bs-target="#modalInventaris"]'); // Sesuaikan selector
    
    // Ambil CSRF token
    const token = document.querySelector('meta[name="csrf-token"]').content;
    
    // Tombol Modal
    const submitButton = form ? form.querySelector('button[type="submit"]') : null;
    const cancelButton = modalInventarisElement ? modalInventarisElement.querySelector('button[data-bs-dismiss="modal"]') : null;
    const originalButtonText = submitButton ? submitButton.innerHTML : 'Simpan Data';

    // State Management untuk request ke server
    let state = {
        currentPage: 1,
        search: '',
        perPage: 10,
        sortBy: 'nama_barang', // Diubah: Kolom sorting default
        sortDir: 'asc', 
        searchTimeout: null // Untuk debouncing search
    };

    // --- 2. HELPER FUNCTIONS ---

    /**
     * Fungsi untuk mengaktifkan/menonaktifkan loading tombol
     */
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

    /**
     * Mengubah Kondisi menjadi badge warna Bootstrap
     */
    function getKondisiBadge(kondisi) {
        let className = 'bg-success'; // Baik
        if (kondisi === 'Perlu Perbaikan') {
            className = 'bg-warning text-dark';
        } else if (kondisi === 'Rusak Berat') {
            className = 'bg-danger';
        }
        return `<span class="badge ${className}">${kondisi}</span>`;
    }

    // --- 3. OPERASI READ & RENDERING (SERVER-SIDE) ---

    /**
     * Memuat data Inventaris dari API Laravel.
     */
    async function loadInventaris() {
        if (!tbody) return;
        
        // 6 Kolom: No, Nama Barang, Satuan, Kondisi, Stock, Aksi
        let colCount = 6; 
        tbody.innerHTML = `<tr><td colspan="${colCount}" class="text-center"><div class="spinner-border text-primary"></div></td></tr>`;

        // Ganti endpoint API
        const url = `/inventaris-data?page=${state.currentPage}&search=${state.search}&perPage=${state.perPage}&sortBy=${state.sortBy}&sortDir=${state.sortDir}`;

        try {
            const res = await fetch(url);
            if (!res.ok) throw new Error('Gagal memuat data dari server');
            
            const response = await res.json(); // Data pagination Laravel
            
            // Render: response.data berisi data array, dan response.from adalah nomor awal
            renderTable(response.data, response.from || 1);
            renderPagination(response); 
            
        } catch (err) {
            tbody.innerHTML = `<tr><td colspan="${colCount}" class="text-center text-danger">${err.message}</td></tr>`;
            paginationInfo.textContent = 'Gagal memuat data';
            paginationContainer.innerHTML = '';
        }
    }
    
    /**
     * Fungsi untuk me-render isi tabel
     */
    function renderTable(data, startingNumber) {
        tbody.innerHTML = ''; // Kosongkan tabel
        let colCount = 6; // Sesuaikan jumlah kolom: No, Nama Barang, Satuan, Kondisi, Stock, Aksi
        
        if (data.length === 0) {
            tbody.innerHTML = `<tr><td colspan="${colCount}" class="text-center">Belum ada data barang inventaris.</td></tr>`;
            return;
        }

        data.forEach((item, i) => {
            const row = `
                <tr>
                    <td class="text-center">${startingNumber + i}</td>
                    <td>${item.nama_barang}</td>
                    <td class="text-center">${item.satuan}</td>
                    <td class="text-center">${getKondisiBadge(item.kondisi)}</td>
                    <td class="text-center fw-bold">${item.stock}</td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-info text-white me-2" onclick="editBarangInventaris('${item.id_barang}')">
                            <i class="bi bi-pencil-square"></i> Edit
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="hapusBarangInventaris('${item.id_barang}')">
                            <i class="bi bi-trash"></i> Hapus
                        </button>
                    </td>
                </tr>`;
            tbody.insertAdjacentHTML('beforeend', row);
        });
    }

    /**
     * Fungsi untuk me-render link pagination (mengikuti struktur Laravel)
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
            if (label.includes('Previous')) label = 'Sebelumnya';
            else if (label.includes('Next')) label = 'Selanjutnya';
            
            const disabled = !link.url ? 'disabled' : '';
            const active = link.active ? 'active' : '';

            linksHtml += `
                <li class="page-item ${disabled} ${active}">
                    <a class="page-link" href="${link.url || '#'}" data-page-url="${link.url}">${label}</a>
                </li>
            `;
        });

        linksHtml += '</ul>';
        paginationContainer.innerHTML = linksHtml;
    }


    // --- 4. EVENT LISTENERS UTAMA ---

    // 1. Submit form (CREATE / UPDATE)
    if (form) {
        form.addEventListener('submit', async e => {
            e.preventDefault();
            setLoading(true);

            // Ganti ID field
            const id = document.getElementById('id_barang').value; 
            const formData = new FormData(form);
            
            // Konfigurasi endpoint
            // Ganti Endpoint
            const url = id ? `/inventaris/${id}` : '/inventaris'; 
            // Tambahkan _method=PUT untuk UPDATE
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
                    modalInventaris.hide();
                    loadInventaris(); // Muat ulang data
                } else {
                    if (res.status === 422 && data.errors) {
                        let errorMessages = Object.values(data.errors).map(err => err[0]).join('<br>');
                        throw new Error(errorMessages);
                    }
                    throw new Error(data.message || 'Terjadi kesalahan pada server');
                }
            } catch (err) {
                Swal.fire('Gagal', err.message, 'error');
            } finally {
                setLoading(false);
            }
        });
    }

    // 2. Search Bar (DEBOUNCING)
    if (searchInput) {
        // PERUBAHAN: Mengubah placeholder untuk menginformasikan bahwa pencarian hanya berdasarkan Nama Barang
        searchInput.placeholder = 'Cari berdasarkan Nama Barang...';
        
        searchInput.addEventListener('input', function() {
            clearTimeout(state.searchTimeout);

            state.searchTimeout = setTimeout(() => {
                state.search = searchInput.value;
                state.currentPage = 1; // Reset ke halaman 1
                loadInventaris();
            }, 300); // Tunggu 300ms setelah user berhenti mengetik
        });
    }

    // 3. Listener untuk Klik Pagination
    if (paginationContainer) {
        paginationContainer.addEventListener('click', e => {
            e.preventDefault();
            const target = e.target.closest('a.page-link'); 
            
            if (!target || target.parentElement.classList.contains('disabled') || target.parentElement.classList.contains('active')) {
                return;
            }

            const url = target.getAttribute('data-page-url'); 
            if (url) {
                // Ekstrak nomor halaman dari URL
                const urlObj = new URL(url);
                const page = urlObj.searchParams.get('page'); 
                
                if (page) {
                    state.currentPage = parseInt(page);
                    loadInventaris();
                }
            }
        });
    }

    // 4. Reset Modal saat ditutup
    if (modalInventarisElement) {
        modalInventarisElement.addEventListener('hidden.bs.modal', function () {
            form.reset();
            // Ganti ID field
            document.getElementById('id_barang').value = ''; 
            modalTitle.textContent = 'Tambah/Ubah Data Inventaris';
            setLoading(false); 
        });
    }
    
    // 5. Listener untuk tombol "Tambah Barang Inventaris"
    if (tambahButton) {
        tambahButton.addEventListener('click', () => {
            form.reset();
            // Ganti ID field
            document.getElementById('id_barang').value = ''; 
            modalTitle.textContent = 'Tambah Barang Inventaris';
        });
    }

    // --- 5. FUNGSI GLOBAL (EDIT/HAPUS) ---
    
    // Fungsi untuk memuat data edit ke modal
    // Ganti nama fungsi
    window.editBarangInventaris = async function(id_barang) {
        try {
            // Ganti endpoint
            const res = await fetch(`/inventaris/${id_barang}`); 
            if (!res.ok) throw new Error('Data barang inventaris tidak ditemukan');
            const data = await res.json();

            // Isi form sesuai dengan kolom di Model BarangInventaris
            document.getElementById('id_barang').value = data.id_barang;
            document.getElementById('nama_barang').value = data.nama_barang;
            document.getElementById('satuan').value = data.satuan;
            document.getElementById('kondisi').value = data.kondisi;
            document.getElementById('stock').value = data.stock;
            
            modalTitle.textContent = 'Ubah Data Inventaris';
            modalInventaris.show();
        } catch (err) {
            Swal.fire('Gagal', err.message, 'error');
        }
    }

    // Fungsi untuk menghapus data
    // Ganti nama fungsi
    window.hapusBarangInventaris = async function(id_barang) {
        const confirmResult = await Swal.fire({
            title: 'Yakin ingin menghapus?',
            text: 'Data barang inventaris akan dihapus permanen!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal'
        });

        if (!confirmResult.isConfirmed) return;

        try {
            const formData = new FormData();
            formData.append('_method', 'DELETE');

            // Ganti endpoint
            const res = await fetch(`/inventaris/${id_barang}`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
                body: formData
            });

            const data = await res.json();
            if (res.ok) {
                Swal.fire('Terhapus!', data.message, 'success');
                loadInventaris(); // Muat ulang data
            } else {
                throw new Error(data.message || 'Terjadi kesalahan saat menghapus');
            }
        } catch (err) {
            Swal.fire('Gagal', err.message, 'error');
        }
    }

    // --- 6. INISIALISASI ---
    loadInventaris(); // Muat data pertama kali
});
