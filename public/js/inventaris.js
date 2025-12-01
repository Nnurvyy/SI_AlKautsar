document.addEventListener('DOMContentLoaded', () => {

    // --- 1. DEFINISI ELEMEN ---
    const tableBody = document.querySelector('#tabelInventaris tbody');
    const form = document.getElementById('formInventarisStock');
    const modalElement = document.getElementById('modalInventaris');
    const modal = new bootstrap.Modal(modalElement);
    const modalTitle = document.getElementById('modalInventarisLabel');
    
    // Filter & Search
    const searchInput = document.getElementById('searchInput');
    const kondisiFilter = document.getElementById('kondisiFilter');
    const paginationContainer = document.getElementById('paginationLinks');
    const paginationInfo = document.getElementById('paginationInfo');

    // Token CSRF
    const token = document.querySelector('meta[name="csrf-token"]').content;

    // State
    let state = {
        page: 1,
        search: '',
        kondisi: 'all' // Untuk filter kondisi
    };

    // --- 2. LOAD DATA ---
    async function loadInventaris() {
        if (!tableBody) return;

        // Tampilkan Loading Spinner
        let colCount = 6;
        tableBody.innerHTML = `<tr><td colspan="${colCount}" class="text-center py-5"><div class="spinner-border text-success" role="status"></div></td></tr>`;

        // Ambil nilai dari inputan
        state.search = searchInput ? searchInput.value : '';
        state.kondisi = kondisiFilter ? kondisiFilter.value : 'all';

        // URL Endpoint
        const url = `/pengurus/inventaris-data?page=${state.page}&search=${state.search}&kondisi=${state.kondisi}`;

        try {
            const res = await fetch(url);
            if (!res.ok) throw new Error('Gagal mengambil data');
            const response = await res.json();
            
            renderTable(response);
        } catch (err) {
            console.error(err);
            tableBody.innerHTML = `<tr><td colspan="${colCount}" class="text-center text-danger py-4">Gagal memuat data.</td></tr>`;
        }
    }

    // --- 3. RENDER TABEL ---
    function renderTable(res) {
        tableBody.innerHTML = '';
        let no = res.from;

        if (res.data.length === 0) {
            tableBody.innerHTML = `<tr><td colspan="6" class="text-center py-5 text-muted">Belum ada data barang inventaris.</td></tr>`;
            paginationInfo.textContent = 'Menampilkan 0 data';
            paginationContainer.innerHTML = '';
            return;
        }

        res.data.forEach(item => {
            // Logic Warna Badge Kondisi
            let badgeClass = 'bg-secondary';
            if (item.kondisi === 'Baik') badgeClass = 'bg-success';
            if (item.kondisi === 'Perlu Perbaikan') badgeClass = 'bg-warning text-dark';
            if (item.kondisi === 'Rusak Berat') badgeClass = 'bg-danger';

            const row = `
                <tr>
                    <td class="text-center fw-bold text-muted">${no++}</td>
                    <td>
                        <div class="fw-bold text-dark">${item.nama_barang}</div>
                    </td>
                    <td class="text-center">${item.satuan}</td>
                    <td class="text-center">
                        <span class="badge rounded-pill ${badgeClass} px-3">${item.kondisi}</span>
                    </td>
                    <td class="text-center fw-bold text-dark">${item.stock}</td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-2">
                            
                            <button onclick="editBarang('${item.id_barang}')" class="btn btn-sm btn-warning text-white rounded-3 shadow-sm" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </button>

                            <button onclick="hapusBarang('${item.id_barang}')" class="btn btn-sm btn-danger rounded-3 shadow-sm" title="Hapus">
                                <i class="bi bi-trash"></i>
                            </button>

                        </div>
                    </td>
                </tr>
            `;
            tableBody.insertAdjacentHTML('beforeend', row);
        });

        // Info Pagination
        paginationInfo.textContent = `Menampilkan ${res.from} - ${res.to} dari ${res.total} data`;
        renderPagination(res);
    }

    // --- 4. RENDER PAGINATION ---
    function renderPagination(res) {
        // Kosongkan container
        paginationContainer.innerHTML = '';
        
        let html = '<ul class="pagination pagination-sm mb-0 justify-content-end">';

        res.links.forEach(link => {
            let activeClass = link.active ? 'active' : '';
            let disabledClass = link.url ? '' : 'disabled';
            
            // Rapikan label panah
            let label = link.label;
            label = label.replace('&laquo; Previous', '<i class="bi bi-chevron-left"></i>');
            label = label.replace('Next &raquo;', '<i class="bi bi-chevron-right"></i>');

            // LOGIC WARNA:
            // Jika aktif: Pakai 'bg-primary' (Biru Solid)
            // Jika tidak: Pakai 'text-primary' (Tulisan Biru)
            let colorClass = link.active ? 'bg-primary border-primary text-white' : 'text-primary';

            html += `
                <li class="page-item ${activeClass} ${disabledClass}">
                    <button class="page-link ${colorClass}" 
                            data-url="${link.url}" 
                            ${!link.url ? 'disabled' : ''}>
                        ${label}
                    </button>
                </li>
            `;
        });

        html += '</ul>';
        paginationContainer.innerHTML = html;

        // Event Listener (Tetap sama)
        paginationContainer.querySelectorAll('button').forEach(btn => {
            btn.addEventListener('click', function() {
                const url = this.dataset.url;
                if (!url || url === 'null') return;

                try {
                    const urlObj = new URL(url);
                    const pageNum = urlObj.searchParams.get('page');
                    if (pageNum) {
                        state.page = pageNum;
                        loadInventaris();
                    }
                } catch (e) {
                    const tempUrl = new URL(url, window.location.origin);
                    const pageNum = tempUrl.searchParams.get('page');
                    if (pageNum) {
                        state.page = pageNum;
                        loadInventaris();
                    }
                }
            });
        });
    }

    // --- 5. HANDLE SUBMIT FORM (CREATE / UPDATE) ---
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const idBarang = document.getElementById('id_barang').value;
        const formData = new FormData(form);
        
        // Tentukan URL & Method
        let url = '/pengurus/inventaris';
        let method = 'POST';

        if (idBarang) {
            url = `/pengurus/inventaris/${idBarang}`;
            formData.append('_method', 'PUT'); // Method spoofing Laravel
        }

        try {
            const res = await fetch(url, {
                method: method,
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: formData
            });

            const data = await res.json();

            if (res.ok) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: data.message,
                    timer: 1500,
                    showConfirmButton: false
                });
                modal.hide();
                loadInventaris(); // Reload tabel
            } else {
                // Handle Validasi Error Laravel
                if (res.status === 422) {
                    let errorMessages = Object.values(data.errors).flat().join('\n');
                    Swal.fire('Validasi Gagal', errorMessages, 'error');
                } else {
                    throw new Error(data.message || 'Terjadi kesalahan');
                }
            }
        } catch (err) {
            Swal.fire('Error', err.message, 'error');
        }
    });

    // --- 6. GLOBAL FUNCTIONS (Untuk onclick di HTML) ---
    
    // Edit Barang
    window.editBarang = async function(id) {
        try {
            const res = await fetch(`/pengurus/inventaris/${id}`);
            if (!res.ok) throw new Error('Gagal mengambil data');
            const data = await res.json();

            // Isi Form
            document.getElementById('id_barang').value = data.id_barang;
            document.getElementById('nama_barang').value = data.nama_barang;
            document.getElementById('satuan').value = data.satuan;
            document.getElementById('kondisi').value = data.kondisi;
            document.getElementById('stock').value = data.stock;

            // Ubah Judul Modal
            modalTitle.textContent = 'Ubah Barang Inventaris';
            modal.show();

        } catch (err) {
            Swal.fire('Error', err.message, 'error');
        }
    };

    // Hapus Barang
    window.hapusBarang = async function(id) {
        const confirm = await Swal.fire({
            title: 'Hapus Barang?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        });

        if (!confirm.isConfirmed) return;

        try {
            const formData = new FormData();
            formData.append('_method', 'DELETE');

            const res = await fetch(`/pengurus/inventaris/${id}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: formData
            });

            const data = await res.json();

            if (res.ok) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: data.message,
                    timer: 1500,
                    showConfirmButton: false
                });
                loadInventaris();
            } else {
                throw new Error(data.message);
            }
        } catch (err) {
            Swal.fire('Error', err.message, 'error');
        }
    };

    // --- 7. EVENT LISTENERS LAINNYA ---
    
    // Reset Modal saat ditutup
    modalElement.addEventListener('hidden.bs.modal', function () {
        form.reset();
        document.getElementById('id_barang').value = '';
        modalTitle.textContent = 'Barang Inventaris';
    });

    // Search & Filter
    let searchTimeout; // Variabel untuk menampung timer

    if(searchInput) {
        searchInput.addEventListener('keyup', () => {
            // Hapus timer sebelumnya jika user masih mengetik
            clearTimeout(searchTimeout);

            // Buat timer baru, tunggu 500ms (setengah detik) baru request
            searchTimeout = setTimeout(() => {
                state.page = 1; // Reset ke halaman 1
                loadInventaris();
            }, 500);
        });
    }
    
    if(kondisiFilter) {
        kondisiFilter.addEventListener('change', () => {
            state.page = 1;
            loadInventaris();
        });
    }
    // Load Pertama Kali
    loadInventaris();
});