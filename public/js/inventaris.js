document.addEventListener('DOMContentLoaded', () => {

    // SETUP ELEMENT
    const tableBody = document.querySelector('#tabelInventaris tbody');
    const form = document.getElementById('formInventarisStock');
    const modalElement = document.getElementById('modalInventaris');
    const modal = new bootstrap.Modal(modalElement);
    const modalTitle = document.getElementById('modalInventarisLabel');
    
    // Hapus referensi ke kondisiFilter, karena sudah dihapus di View Master
    const searchInput = document.getElementById('searchInput');
    const paginationContainer = document.getElementById('paginationLinks');
    const paginationInfo = document.getElementById('paginationInfo');

    // Token CSRF
    const token = document.querySelector('meta[name="csrf-token"]').content;

    // State Aplikasi
    let state = {
        page: 1,
        search: '',
        // kondisi DIHAPUS
    };

    // Fungsi untuk memuat data inventaris
    async function loadInventaris() {
        if (!tableBody) return;

        // Total 6 kolom di view master: No, Nama, Kode, Satuan, Total Stock, Aksi
        const colCount = 6; 
        tableBody.innerHTML = `<tr><td colspan="${colCount}" class="text-center py-5"><div class="spinner-border text-success" role="status"></div></td></tr>`;

        // Update state
        state.search = searchInput ? searchInput.value : '';
        // state.kondisi DIHAPUS

        // URL API: Hapus parameter kondisi
        const url = `/pengurus/inventaris-data?page=${state.page}&search=${state.search}`;

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

    // Fungsi untuk merender tabel
    function renderTable(res) {
        tableBody.innerHTML = '';
        let no = res.from;

        const colCount = 6;

        if (res.data.length === 0) {
            tableBody.innerHTML = `<tr><td colspan="${colCount}" class="text-center py-5 text-muted">Belum ada data barang inventaris.</td></tr>`;
            paginationInfo.textContent = 'Menampilkan 0 data';
            paginationContainer.innerHTML = '';
            return;
        }

        res.data.forEach(item => {
            
            // Logika badge kondisi DIHAPUS
            
            const row = `
                <tr>
                    <td class="text-center fw-bold text-muted">${no++}</td>
                    <td>
                        <div class="fw-bold text-dark">${item.nama_barang}</div>
                    </td>
                    <td class="text-center fw-bold text-dark">${item.kode || '-'}</td>
                    <td class="text-center">${item.satuan}</td>
                    <td class="text-center fw-bold text-dark">${item.total_stock || 0}</td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-2">
                            
                            <a href="/pengurus/inventaris/${item.id_barang}/detail" class="btn btn-sm btn-gradient-green rounded-pill shadow-sm" title="Lihat Detail">
                                <i class="bi bi-list-columns-reverse me-1"></i> Detail Unit
                            </a>
                            
                            <button onclick="editBarang('${item.id_barang}')" class="btn btn-sm btn-warning text-white rounded-3 shadow-sm" title="Edit Master">
                                <i class="bi bi-pencil"></i>
                            </button>

                            <button onclick="hapusBarang('${item.id_barang}')" class="btn btn-sm btn-danger rounded-3 shadow-sm" title="Hapus Master">
                                <i class="bi bi-trash"></i>
                            </button>

                        </div>
                    </td>
                </tr>
            `;
            tableBody.insertAdjacentHTML('beforeend', row);
        });

        
        paginationInfo.textContent = `Menampilkan ${res.from} - ${res.to} dari ${res.total} data`;
        renderPagination(res);
    }

    // Fungsi merender pagination (Logika tetap sama)
    function renderPagination(res) {
        
        paginationContainer.innerHTML = '';
        
        let html = '<ul class="pagination pagination-sm mb-0 justify-content-end">';

        res.links.forEach(link => {
            let activeClass = link.active ? 'active' : '';
            let disabledClass = link.url ? '' : 'disabled';
            
            
            let label = link.label;
            label = label.replace('&laquo; Previous', '<i class="bi bi-chevron-left"></i>');
            label = label.replace('Next &raquo;', '<i class="bi bi-chevron-right"></i>');

            
            
            
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

    // Form Submit (Store/Update Master)
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const idBarang = document.getElementById('id_barang').value;
        const formData = new FormData(form);
        
        
        let url = '/pengurus/inventaris';
        let method = 'POST';

        if (idBarang) {
            url = `/pengurus/inventaris/${idBarang}`;
            formData.append('_method', 'PUT'); 
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
                loadInventaris(); 
            } else {
                
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

    
    // Fungsi Edit Barang Master
    // ... (Kode sebelumnya)

// Fungsi Edit Barang Master
    window.editBarang = async function(id) {
        try {
            const res = await fetch(`/pengurus/inventaris/${id}`);
            if (!res.ok) throw new Error('Gagal mengambil data');
            const data = await res.json();

            // Ambil elemen Kode
            const kodeInput = document.getElementById('kode');
            
            // 1. Isi data form
            document.getElementById('id_barang').value = data.id_barang;
            document.getElementById('nama_barang').value = data.nama_barang;
            kodeInput.value = data.kode;
            document.getElementById('satuan').value = data.satuan;
            
            // 2. Terapkan Read-Only pada Kode saat Edit
            kodeInput.setAttribute('readonly', true);
            kodeInput.classList.add('bg-light'); // Tambahkan visual indikator read-only

            modalTitle.textContent = 'Ubah Barang Inventaris';
            modal.show();

        } catch (err) {
            Swal.fire('Error', err.message, 'error');
        }
    };

    // ... (Kode hapusBarang)

    // Reset modal saat ditutup
    modalElement.addEventListener('hidden.bs.modal', function () {
        const kodeInput = document.getElementById('kode');

        form.reset();
        document.getElementById('id_barang').value = '';
        modalTitle.textContent = 'Barang Inventaris';
        
        if (kodeInput) {
            kodeInput.removeAttribute('readonly');
            kodeInput.classList.remove('bg-light');
        }
    });

    // ... (Kode selanjutnya)

    // Fungsi Hapus Barang Master (Logika tetap sama)
    window.hapusBarang = async function(id) {
        const confirm = await Swal.fire({
            title: 'Hapus Barang Master?',
            text: "Menghapus master akan menghapus SEMUA unit detail terkait!",
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

    
    
    // Reset modal saat ditutup
    modalElement.addEventListener('hidden.bs.modal', function () {
        form.reset();
        document.getElementById('id_barang').value = '';
        modalTitle.textContent = 'Barang Inventaris';
    });

    
    let searchTimeout; 

    if(searchInput) {
        searchInput.addEventListener('keyup', () => {
            
            clearTimeout(searchTimeout);

            
            searchTimeout = setTimeout(() => {
                state.page = 1; 
                loadInventaris();
            }, 500);
        });
    }
    
    // Hapus event listener untuk kondisiFilter
    
    loadInventaris();
});