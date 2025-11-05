// Script ini mengimplementasikan logika CRUD, Search, dan Pagination
// menggunakan fetch API ke backend Laravel.
// Asumsi: SweetAlert2 (Swal) dan Bootstrap JS sudah dimuat di view.

document.addEventListener('DOMContentLoaded', () => {

    // --- 1. DEFINISI ELEMEN DAN STATE ---

    // Elemen Formulir & Modal
    const form = document.getElementById('formTambahInfaq');
    const modalTambahInfaqElement = document.getElementById('modaltambahinfaq');
    const modalTambahInfaq = new bootstrap.Modal(modalTambahInfaqElement);
    const modalTitle = document.getElementById('modalInfaqLabel');

    // Elemen Tabel & Kontrol
    const tbody = document.querySelector('#tabelKhotib tbody');
    const searchInput = document.getElementById('searchInput');
    const paginationContainer = document.getElementById('paginationLinks');
    const paginationInfo = document.getElementById('paginationInfo');
    const tambahButton = document.querySelector('[data-bs-target="#modaltambahinfaq"]');
    
    // Asumsi: View sudah menyertakan meta tag CSRF token
    const token = document.querySelector('meta[name="csrf-token"]').content;
    
    // Tombol Modal
    const submitButton = form ? form.querySelector('button[type="submit"]') : null;
    const cancelButton = modalTambahInfaqElement ? modalTambahInfaqElement.querySelector('button[data-bs-dismiss="modal"]') : null;
    const originalButtonText = submitButton ? submitButton.innerHTML : 'Simpan';

    // State Management untuk request ke server
    let state = {
        currentPage: 1,
        search: '',
        perPage: 10,
        sortBy: 'tanggal_infaq', // Asumsi kolom untuk sorting di DB
        sortDir: 'desc',        
        searchTimeout: null     // Untuk debouncing search
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
     * Format angka menjadi Rupiah (Rp 1.000.000).
     */
    function formatRupiah(angka) {
        if (angka === undefined || angka === null) return 'Rp 0';
        return "Rp " + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    /**
     * Format string tanggal (YYYY-MM-DD) ke ID-ID (DD Bul MM TAHUN).
     * @param {string} tanggalStr - Tanggal dalam format string yang dapat diparse (e.g., ISO, YYYY-MM-DD).
     */
    function formatTanggal(tanggalStr) {
        if (!tanggalStr) return '-';
        
        // Coba parse tanggal. Tanggal dari Laravel biasanya format ISO atau YYYY-MM-DD
        const date = new Date(tanggalStr); 
        
        return !isNaN(date)
            ? date.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })
            : 'Invalid Date';
    }

    // --- 3. OPERASI READ & RENDERING (SERVER-SIDE) ---

    /**
     * Memuat data Infaq dari API Laravel.
     */
    async function loadInfaq() {
        if (!tbody) return;
        
        // Asumsi 4 kolom: No, Tanggal, Nominal, Aksi
        let colCount = 4;
        tbody.innerHTML = `<tr><td colspan="${colCount}" class="text-center"><div class="spinner-border text-primary"></div></td></tr>`;

        // Buat URL dengan query params untuk server
        const url = `/infaq-jumat-data?page=${state.currentPage}&search=${state.search}&perPage=${state.perPage}&sortBy=${state.sortBy}&sortDir=${state.sortDir}`;

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
        let colCount = 4; // Asumsi 4 kolom: No, Tanggal, Nominal, Aksi
        
        if (data.length === 0) {
            tbody.innerHTML = `<tr><td colspan="${colCount}" class="text-center">Belum ada data atau data tidak ditemukan.</td></tr>`;
            return;
        }

        data.forEach((item, i) => {
            const row = `
                <tr>
                    <td class="text-center">${startingNumber + i}</td>
                    <td class="text-center">${formatTanggal(item.tanggal_infaq)}</td>
                    <td class="text-center fw-bold text-success">${formatRupiah(item.nominal_infaq)}</td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-info text-white me-2" onclick="editInfaq('${item.id_infaq}')">
                            <i class="bi bi-pencil-square"></i> Edit
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="hapusInfaq('${item.id_infaq}')">
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

        // Update info: "Menampilkan 1 - 10 dari 100 data"
        paginationInfo.textContent = `Menampilkan ${from} - ${to} dari ${total} data`;

        // HTML pagination
        let linksHtml = '<ul class="pagination justify-content-center mb-0">';
        
        links.forEach(link => {
            let label = link.label;
            if (label.includes('Previous')) label = 'Sebelumnya';
            else if (label.includes('Next')) label = 'Selanjutnya';
            
            const disabled = !link.url ? 'disabled' : '';
            const active = link.active ? 'active' : '';

            // Penting: link.url sudah berisi query params termasuk nomor halaman
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

            const id = document.getElementById('id_infaq').value;
            const formData = new FormData(form);
            
            // Konfigurasi endpoint
            const url = id ? `/infaq-jumat/${id}` : '/infaq-jumat';
            // Tambahkan _method=PUT untuk UPDATE
            if (id) formData.append('_method', 'PUT');

            try {
                const res = await fetch(url, {
                    method: 'POST', // POST untuk CREATE dan PUT override
                    headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
                    body: formData
                });

                const data = await res.json();
                if (res.ok) {
                    Swal.fire('Berhasil!', data.message, 'success');
                    modalTambahInfaq.hide();
                    loadInfaq(); // Muat ulang data
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
        searchInput.addEventListener('input', function() {
            clearTimeout(state.searchTimeout);

            state.searchTimeout = setTimeout(() => {
                state.search = searchInput.value;
                state.currentPage = 1; // Reset ke halaman 1
                loadInfaq();
            }, 300); // Tunggu 300ms setelah user berhenti mengetik
        });
    }

    // 3. Listener untuk Klik Pagination (Mengambil halaman dari URL link)
    if (paginationContainer) {
        paginationContainer.addEventListener('click', e => {
            e.preventDefault();
            const target = e.target.closest('a.page-link'); 
            
            if (!target || target.parentElement.classList.contains('disabled') || target.parentElement.classList.contains('active')) {
                return;
            }

            const url = target.getAttribute('data-page-url'); // Ambil URL lengkap dari atribut kustom
            if (url) {
                // Ekstrak nomor halaman dari URL
                const urlObj = new URL(url);
                const page = urlObj.searchParams.get('page'); 
                
                if (page) {
                    state.currentPage = parseInt(page);
                    loadInfaq();
                }
            }
        });
    }

    // 4. Reset Modal saat ditutup
    if (modalTambahInfaqElement) {
        modalTambahInfaqElement.addEventListener('hidden.bs.modal', function () {
            form.reset();
            document.getElementById('id_infaq').value = '';
            modalTitle.textContent = 'Tambah Infaq Jumat';
            setLoading(false); 
        });
    }
    
    // 5. Listener untuk tombol "Tambah Pemasukan Infaq"
    if (tambahButton) {
        tambahButton.addEventListener('click', () => {
            form.reset();
            document.getElementById('id_infaq').value = ''; 
            modalTitle.textContent = 'Tambah Infaq Jumat';
        });
    }

    // --- 5. FUNGSI GLOBAL (EDIT/HAPUS) ---
    
    // Fungsi untuk memuat data edit ke modal
    window.editInfaq = async function(id_infaq) {
        try {
            const res = await fetch(`/infaq-jumat/${id_infaq}`); // Asumsi endpoint single data
            if (!res.ok) throw new Error('Data infaq tidak ditemukan');
            const data = await res.json();

            // Isi form
            document.getElementById('id_infaq').value = data.id_infaq;
            document.getElementById('tanggal_infaq').value = data.tanggal_infaq; // YYYY-MM-DD
            document.getElementById('nominal_infaq').value = data.nominal_infaq;
            
            modalTitle.textContent = 'Ubah Infaq Jumat';
            modalTambahInfaq.show();
        } catch (err) {
            Swal.fire('Gagal', err.message, 'error');
        }
    }

    // Fungsi untuk menghapus data
    window.hapusInfaq = async function(id_infaq) {
        const confirmResult = await Swal.fire({
            title: 'Yakin ingin menghapus?',
            text: 'Data infaq akan dihapus permanen!',
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

            const res = await fetch(`/infaq-jumat/${id_infaq}`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
                body: formData
            });

            const data = await res.json();
            if (res.ok) {
                Swal.fire('Terhapus!', data.message, 'success');
                loadInfaq(); // Muat ulang data
            } else {
                throw new Error(data.message || 'Terjadi kesalahan saat menghapus');
            }
        } catch (err) {
            Swal.fire('Gagal', err.message, 'error');
        }
    }

    // --- 6. INISIALISASI ---
    loadInfaq(); // Muat data pertama kali
});
