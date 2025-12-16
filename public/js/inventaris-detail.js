document.addEventListener('DOMContentLoaded', () => {

    // PASTIKAN BASE_API_URL SUDAH DIDEFINISIKAN DI VIEW BLADE!

    // --- SETUP ELEMENT ---
    const tableBody = document.querySelector('#tabelInventarisDetail tbody');
    const form = document.getElementById('formInventarisDetail');
    const modalElement = document.getElementById('modalInventarisDetail');
    const modal = new bootstrap.Modal(modalElement);
    const modalTitle = document.getElementById('modalInventarisDetailLabel');
    
    // Elemen untuk Kloning
    const kloningSection = document.getElementById('kloningSection'); 
    const jumlahKloningInput = document.getElementById('jumlah_kloning'); 
    
    // Ambil ID Barang Master dari hidden input di View
    const idBarangMaster = document.getElementById('id_barang_master').value;

    const searchInput = document.getElementById('searchInput');
    const kondisiFilter = document.getElementById('filterKondisi');
    const paginationContainer = document.getElementById('paginationLinks');
    const paginationInfo = document.getElementById('paginationInfo');

    const token = document.querySelector('meta[name="csrf-token"]').content;

    // --- STATE ---
    let state = {
        page: 1,
        search: '',
        kondisi: 'all' 
    };

    // --- UTILITIES ---
    function formatDate(dateString) {
        if (!dateString) return '-';
        const options = { year: 'numeric', month: 'long', day: 'numeric' };
        return new Date(dateString + 'T00:00:00').toLocaleDateString('id-ID', options);
    }

    // --- LOAD DATA ---
    async function loadDetailInventaris() {
        if (!tableBody || !idBarangMaster || typeof BASE_API_URL === 'undefined') {
            console.error("BASE_API_URL atau idBarangMaster belum terdefinisi.");
            tableBody.innerHTML = `<tr><td colspan="7" class="text-center text-danger py-4">Konfigurasi API atau ID Master hilang.</td></tr>`;
            return;
        }

        const colCount = 7; 
        tableBody.innerHTML = `<tr><td colspan="${colCount}" class="text-center py-5"><div class="spinner-border text-success" role="status"></div></td></tr>`;

        state.search = searchInput ? searchInput.value : '';
        state.kondisi = kondisiFilter ? kondisiFilter.value : 'all';

        // Menggunakan BASE_API_URL
        const url = `${BASE_API_URL}/${idBarangMaster}/data?page=${state.page}&search=${state.search}&kondisi=${state.kondisi}`;

        let res; 
        
        try {
            res = await fetch(url);
            
            if (!res.ok) {
                let statusText = res.statusText || 'Unknown Error';
                let errorMessage = `Gagal mengambil data. Status: ${res.status} (${statusText}).`;
                
                if (res.status === 404) {
                    errorMessage += ` Cek kembali URL: ${url} dan rute PHP Anda.`;
                } else if (res.status >= 500) {
                    errorMessage += ` Cek log error server (Laravel log).`;
                }
                throw new Error(errorMessage);
            }
            
            const response = await res.json();
            
            renderDetailTable(response);
        } catch (err) {
            console.error("Kesalahan Load Data Detail:", err);
            const colCount = 7;
            tableBody.innerHTML = `<tr><td colspan="${colCount}" class="text-center text-danger py-4">
                Gagal memuat data detail. Error: ${err.message}.
            </td></tr>`;
        }
    }

    // --- RENDER TABLE ---
    function renderDetailTable(res) {
        tableBody.innerHTML = '';
        let no = res.from;

        const colCount = 7;
        
        if (res.data.length === 0) {
            tableBody.innerHTML = `<tr><td colspan="${colCount}" class="text-center py-5 text-muted">Belum ada unit aset terdaftar. Klik "Tambah Unit" untuk mendaftarkan unit pertama.</td></tr>`;
            paginationInfo.textContent = 'Menampilkan 0 data';
            paginationContainer.innerHTML = '';
            return;
        }

        res.data.forEach(item => {
            // Logika Badge Kondisi
            let kondisiBadgeClass = 'bg-secondary';
            if (item.kondisi === 'Baik') kondisiBadgeClass = 'bg-success';
            if (item.kondisi === 'Perlu Perbaikan') kondisiBadgeClass = 'bg-warning text-dark';
            if (item.kondisi === 'Rusak Berat') kondisiBadgeClass = 'bg-danger';

            // Logika Badge Status
            let statusBadgeClass = 'bg-secondary';
            if (item.status === 'Tersedia') statusBadgeClass = 'bg-info text-white';
            if (item.status === 'Dipinjam') statusBadgeClass = 'bg-warning text-dark';
            if (item.status === 'Perbaikan') statusBadgeClass = 'bg-danger';
            if (item.status === 'Dihapus') statusBadgeClass = 'bg-dark';

            const row = `
                <tr>
                    <td class="text-center fw-bold text-muted">${no++}</td>
                    <td><div class="fw-bold text-dark">${item.kode_barang}</div></td>
                    <td>${item.lokasi || '-'}</td>
                    <td class="text-center">${formatDate(item.tanggal_masuk)}</td>
                    <td class="text-center">
                        <span class="badge rounded-pill ${kondisiBadgeClass} px-3">${item.kondisi}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge rounded-pill ${statusBadgeClass} px-3">${item.status}</span>
                    </td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-2">
                            
                            <button onclick="editDetailUnit('${item.id_detail_barang}')" class="btn btn-sm btn-warning text-white rounded-3 shadow-sm" title="Edit Unit">
                                <i class="bi bi-pencil"></i>
                            </button>

                            <button onclick="hapusDetailUnit('${item.id_detail_barang}', '${item.kode_barang}')" class="btn btn-sm btn-danger rounded-3 shadow-sm" title="Hapus Unit">
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

    // --- RENDER PAGINATION (tetap sama) ---
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
                        loadDetailInventaris();
                    }
                } catch (e) {
                    const tempUrl = new URL(url, window.location.origin);
                    const pageNum = tempUrl.searchParams.get('page');
                    if (pageNum) {
                        state.page = pageNum;
                        loadDetailInventaris();
                    }
                }
            });
        });
    }

    // --- FORM SUBMIT (STORE / UPDATE DETAIL UNIT) ---
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const idDetailBarang = document.getElementById('id_detail_barang').value;
        const formData = new FormData(form);
        
        if (formData.get('tanggal_masuk') === '') {
            formData.delete('tanggal_masuk');
        }
        
        let url = BASE_API_URL; 
        let method = 'POST';

        // Logika untuk mengirim jumlah_kloning=0 jika mode Edit
        if (idDetailBarang) {
            url = `${BASE_API_URL}/${idDetailBarang}`; 
            method = 'POST'; 
            formData.append('_method', 'PUT'); 
            
            // Pastikan kloning tidak terkirim saat update
            formData.set('jumlah_kloning', 0);
        } else {
            // Jika Tambah Baru, pastikan jumlah_kloning terisi (default 0)
            if (!formData.get('jumlah_kloning')) {
                formData.append('jumlah_kloning', 0);
            }
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

            let data;
            try {
                data = await res.json();
            } catch (jsonError) {
                if (!res.ok) {
                    const text = await res.text();
                    throw new Error(`Kesalahan Server: Status ${res.status}. Respons non-JSON. Detail: ${text.substring(0, 100)}...`);
                }
                data = { message: 'Operasi berhasil!' };
            }

            if (res.ok) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: data.message || 'Operasi berhasil!',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    // Refresh halaman untuk memperbarui total_stock di card total unit
                    window.location.reload(); 
                });
                modal.hide();
                
            } else {
                
                if (res.status === 422 && data && data.errors) {
                    let errorMessages = Object.values(data.errors).flat().join('\n');
                    Swal.fire('Validasi Gagal', errorMessages, 'error');
                } else {
                    throw new Error(data.message || `Terjadi kesalahan saat menyimpan (${res.status}).`);
                }
            }
        } catch (err) {
            Swal.fire('Error', err.message, 'error');
            console.error("Kesalahan Fetch/Server:", err);
        }
    });

    
    // --- CRUD ACTIONS ---

    // Edit Unit Detail
    window.editDetailUnit = async function(id) {
        try {
            // 🚨 Sembunyikan Kloning Section saat Edit
            if (kloningSection) {
                kloningSection.style.display = 'none';
            }
            
            const res = await fetch(`${BASE_API_URL}/${id}`);
            if (!res.ok) throw new Error('Gagal mengambil data unit detail');
            const data = await res.json();

            // Populate form fields
            document.getElementById('id_detail_barang').value = data.id_detail_barang;
            document.getElementById('lokasi').value = data.lokasi;
            document.getElementById('kondisi').value = data.kondisi;
            document.getElementById('status').value = data.status;
            document.getElementById('deskripsi').value = data.deskripsi || '';
            
            if (data.tanggal_masuk) {
                document.getElementById('tanggal_masuk').value = data.tanggal_masuk;
            } else {
                 document.getElementById('tanggal_masuk').value = '';
            }
            
            // KOREKSI: Menggunakan data.kode_barang untuk judul modal
            modalTitle.textContent = `Ubah Unit ${data.kode_barang}`; 
            
            modal.show();

        } catch (err) {
            Swal.fire('Error', err.message, 'error');
        }
    };

    // Hapus Unit Detail (Menggunakan kode yang sudah di-pass dari renderDetailTable)
    window.hapusDetailUnit = async function(id, kode) {
        const confirm = await Swal.fire({
            title: `Hapus Unit ${kode}?`,
            text: "Unit ini akan dihapus dari inventaris dan Total Stock akan berkurang.",
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

            const res = await fetch(`${BASE_API_URL}/${id}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: formData
            });

            let data;
            try {
                data = await res.json();
            } catch (jsonError) {
                if (!res.ok) {
                    const text = await res.text();
                    throw new Error(`Kesalahan Server: Status ${res.status}. Respons non-JSON. Detail: ${text.substring(0, 100)}...`);
                }
                data = { message: 'Unit berhasil dihapus!' };
            }

            if (res.ok) {
                 Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: data.message || 'Unit berhasil dihapus!',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    window.location.reload(); 
                });
            } else {
                throw new Error(data.message || `Gagal menghapus unit (${res.status}).`);
            }
        } catch (err) {
            Swal.fire('Error', err.message, 'error');
            console.error("Kesalahan Fetch/Server:", err);
        }
    };

    
    // --- EVENT LISTENERS ---
    
    // Reset modal saat ditutup
    modalElement.addEventListener('hidden.bs.modal', function () {
        form.reset();
        document.getElementById('id_detail_barang').value = '';
        document.getElementById('tanggal_masuk').value = ''; 
        document.getElementById('id_barang_master').value = idBarangMaster;
        modalTitle.textContent = 'Unit Inventaris';
        
        // 🚨 Tampilkan kembali Kloning Section saat modal ditutup (Mode Tambah Baru)
        if (kloningSection) {
            kloningSection.style.display = 'block';
            // Reset input kloning ke 0
            if (jumlahKloningInput) {
                jumlahKloningInput.value = 0;
            }
        }
    });

    
    let searchTimeout; 

    if(searchInput) {
        searchInput.addEventListener('keyup', () => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                state.page = 1; 
                loadDetailInventaris();
            }, 500);
        });
    }
    
    if(kondisiFilter) {
        kondisiFilter.addEventListener('change', () => {
            state.page = 1;
            loadDetailInventaris();
        });
    }
    
    // Initial Load
    if (idBarangMaster) {
        loadDetailInventaris();
    } else {
         tableBody.innerHTML = `<tr><td colspan="7" class="text-center text-danger py-4">ID Barang Master tidak ditemukan.</td></tr>`;
    }
});