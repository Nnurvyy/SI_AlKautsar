document.addEventListener('DOMContentLoaded', () => {
    // --- 1. SETUP VARIABEL GLOBAL & TOKEN ---
    const metaToken = document.querySelector('meta[name="csrf-token"]');
    const token = metaToken ? metaToken.content : '';
    const tbody = document.querySelector('#tabelKeuangan tbody');

    // --- 2. DETEKSI TOMBOL UTAMA ---
    const btnTambahPemasukan = document.getElementById('btnTambahPemasukan');
    const btnTambahPengeluaran = document.getElementById('btnTambahPengeluaran');
    const btnTambahTransaksi = btnTambahPemasukan || btnTambahPengeluaran;

    // --- 3. INISIALISASI MODAL ---
    let modalTransaksiBS = null;
    const modalTransaksiEl = document.getElementById('modalTransaksi');
    if (modalTransaksiEl) {
        modalTransaksiBS = new bootstrap.Modal(modalTransaksiEl);
    }

    let modalKategoriBS = null;
    const modalKategoriEl = document.getElementById('modalKategori');
    if (modalKategoriEl) {
        modalKategoriBS = new bootstrap.Modal(modalKategoriEl);
    }

    // Elemen Form
    const formTransaksi = document.getElementById('formTransaksi');
    const formKategori = document.getElementById('formKategori');
    const inputNominal = document.getElementById('inputNominal');

    // State Pagination
    let currentPage = 1;

    // --- 4. FORMAT RUPIAH OTOMATIS (SAAT KETIK) ---
    if (inputNominal) {
        inputNominal.addEventListener('keyup', function(e) {
            let value = this.value.replace(/[^,\d]/g, '').toString();
            let split = value.split(',');
            let sisa = split[0].length % 3;
            let rupiah = split[0].substr(0, sisa);
            let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                let separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
            this.value = rupiah;
        });
    }

    // --- 5. LOGIKA TOMBOL TAMBAH TRANSAKSI ---
    if (btnTambahTransaksi) {
        btnTambahTransaksi.addEventListener('click', function() {
            // 1. Reset Form
            if(formTransaksi) formTransaksi.reset();
            
            // 2. Reset ID Hidden
            const inputId = document.getElementById('id_keuangan');
            if (inputId) inputId.value = '';

            // 3. Set Judul & Warna Modal
            const modalTitle = document.getElementById('modalTransaksiTitle');
            
            // Cek Tipe Halaman dari tombol yang ada
            if (btnTambahPemasukan) {
                if(modalTitle) modalTitle.textContent = "Tambah Pemasukan";
                setupModalColor('pemasukan');
            } else {
                if(modalTitle) modalTitle.textContent = "Tambah Pengeluaran";
                setupModalColor('pengeluaran');
            }

            // 4. Tampilkan Modal
            if (modalTransaksiBS) modalTransaksiBS.show();

            // 5. Load Kategori (Filter otomatis via TIPE_HALAMAN)
            loadDropdownKategori();
        });
    }

    // --- FUNGSI GANTI WARNA (MERAH/HIJAU) ---
    function setupModalColor(tipe) {
        const btnSimpan = document.getElementById('btnSimpanTransaksi');
        const modalTitle = document.getElementById('modalTransaksiTitle');
        const labelNominal = document.getElementById('labelNominal'); // Target Label Nominal
        const spanRp = document.querySelector('#modalTransaksi .input-group-text');
        const btnSimpanKategori = document.getElementById('btnSimpanKategori');

        if (tipe === 'pengeluaran') {
            // MODE PENGELUARAN (MERAH)
            if(btnSimpan) { btnSimpan.classList.remove('btn-success'); btnSimpan.classList.add('btn-danger'); }
            if(modalTitle) modalTitle.classList.add('text-danger');
            
            // Ubah Label Nominal jadi Merah
            if(labelNominal) { 
                labelNominal.classList.remove('text-success'); 
                labelNominal.classList.add('text-danger'); 
            }

            if(spanRp) { 
                spanRp.classList.remove('text-success', 'bg-success', 'text-white'); 
                spanRp.classList.add('text-danger', 'fw-bold'); 
            }
            if(btnSimpanKategori) { btnSimpanKategori.classList.remove('btn-primary', 'btn-success'); btnSimpanKategori.classList.add('btn-danger'); }
        } else {
            // MODE PEMASUKAN (HIJAU)
            if(btnSimpan) { btnSimpan.classList.remove('btn-danger'); btnSimpan.classList.add('btn-success'); }
            if(modalTitle) modalTitle.classList.remove('text-danger');
            
            // Ubah Label Nominal jadi Hijau
            if(labelNominal) { 
                labelNominal.classList.remove('text-danger'); 
                labelNominal.classList.add('text-success'); 
            }

            if(spanRp) { spanRp.classList.remove('text-danger'); spanRp.classList.add('text-success', 'fw-bold'); }
            if(btnSimpanKategori) { btnSimpanKategori.classList.remove('btn-primary', 'btn-danger'); btnSimpanKategori.classList.add('btn-success'); }
        }
    }

    // --- 6. SUBMIT FORM TRANSAKSI ---
    if (formTransaksi) {
        formTransaksi.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const btnSimpan = document.getElementById('btnSimpanTransaksi');
            const originalText = btnSimpan.innerHTML;
            btnSimpan.disabled = true;
            btnSimpan.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Loading...';

            const inputId = document.getElementById('id_keuangan');
            const id = inputId ? inputId.value : '';
            const formData = new FormData(formTransaksi);

            // Bersihkan format Rupiah
            const inputNominal = document.getElementById('inputNominal');
            if(inputNominal) {
                const rawValue = inputNominal.value;
                const cleanValue = rawValue.replace(/\./g, '');
                formData.set('nominal', cleanValue);
                formData.delete('nominal_display');
            }

            let url = URL_API_TRANSAKSI;
            if (id) {
                url += `/${id}`;
                formData.append('_method', 'PUT');
            }

            try {
                const res = await fetch(url, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
                    body: formData
                });

                const data = await res.json();
                if (!res.ok) throw new Error(data.message || 'Gagal menyimpan');

                modalTransaksiBS.hide();
                Swal.fire({ title: 'Berhasil!', text: data.message, icon: 'success', timer: 1500, showConfirmButton: false });
                loadTransaksi();

            } catch (err) {
                Swal.fire('Error', err.message, 'error');
            } finally {
                btnSimpan.disabled = false;
                btnSimpan.innerHTML = originalText;
            }
        });
    }

    // --- 7. EDIT TRANSAKSI ---
    window.editTransaksi = async (id) => {
        try {
            const tipe = btnTambahPemasukan ? 'pemasukan' : 'pengeluaran';
            setupModalColor(tipe);
            
            if(formTransaksi) formTransaksi.reset();
            modalTransaksiBS.show();

            const res = await fetch(`${URL_API_TRANSAKSI}/${id}`);
            const data = await res.json();

            // Isi Data
            const inputId = document.getElementById('id_keuangan');
            if(inputId) inputId.value = data.id_keuangan;
            
            document.getElementById('inputTanggal').value = data.tanggal;
            document.getElementById('inputDeskripsi').value = data.deskripsi;
            
            if(inputNominal) {
                inputNominal.value = new Intl.NumberFormat('id-ID').format(data.nominal);
            }

            const modalTitle = document.getElementById('modalTransaksiTitle');
            if(modalTitle) modalTitle.textContent = tipe === 'pemasukan' ? "Edit Pemasukan" : "Edit Pengeluaran";

            await loadDropdownKategori();
            document.getElementById('selectKategori').value = data.id_kategori_keuangan;

        } catch (err) {
            Swal.fire('Error', 'Gagal memuat data edit', 'error');
            console.error(err);
        }
    };

    // --- 8. HAPUS TRANSAKSI ---
    window.hapusTransaksi = async (id) => {
        const c = await Swal.fire({ 
            title: 'Hapus Transaksi?', text: 'Data tidak bisa dikembalikan!', icon: 'warning', 
            showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: 'Ya, Hapus!'
        });
        
        if (c.isConfirmed) {
            try {
                const res = await fetch(`${URL_API_TRANSAKSI}/${id}`, { 
                    method: 'DELETE', headers: { 'X-CSRF-TOKEN': token } 
                });
                if(res.ok) {
                    Swal.fire('Terhapus!', 'Data berhasil dihapus.', 'success');
                    loadTransaksi();
                } else { throw new Error('Gagal menghapus'); }
            } catch(e) {
                Swal.fire('Gagal!', 'Terjadi kesalahan saat menghapus.', 'error');
            }
        }
    };

    // --- 9. FUNGSI LOAD KATEGORI (FILTER BERDASARKAN TIPE HALAMAN) ---
    async function loadDropdownKategori() {
        const select = document.getElementById('selectKategori');
        if(!select) return;
        try {
            // INI KUNCI FILTERNYA: Kirim parameter ?tipe=... ke Controller
            const res = await fetch(`${URL_API_KATEGORI}/data?tipe=${TIPE_HALAMAN}`);
            const data = await res.json();
            select.innerHTML = '<option value="">-- Pilih Kategori --</option>';
            data.forEach(cat => {
                select.innerHTML += `<option value="${cat.id_kategori_keuangan}">${cat.nama_kategori_keuangan}</option>`;
            });
        } catch (e) { console.log("Gagal load dropdown", e); }
    }

    // Kelola Kategori (Modal List)
    const btnKelolaKategori = document.getElementById('btnKelolaKategori');
    if(btnKelolaKategori) {
        btnKelolaKategori.addEventListener('click', () => {
            const tipe = btnTambahPemasukan ? 'pemasukan' : 'pengeluaran';
            setupModalColor(tipe);
            resetFormKategori();
            loadListKategori();
            modalKategoriBS.show();
        });
    }

    async function loadListKategori() {
        const listContainer = document.getElementById('listKategoriContainer');
        if(!listContainer) return;
        listContainer.innerHTML = '<div class="text-center py-3"><span class="spinner-border spinner-border-sm"></span></div>';
        try {
            // FILTER JUGA DI SINI
            const res = await fetch(`${URL_API_KATEGORI}/data?tipe=${TIPE_HALAMAN}`);
            const data = await res.json();
            renderListKategori(data);
        } catch (err) { listContainer.innerHTML = '<div class="text-danger text-center">Gagal memuat list kategori.</div>'; }
    }

    function renderListKategori(data) {
        const listContainer = document.getElementById('listKategoriContainer');
        listContainer.innerHTML = '';
        const badge = document.getElementById('totalKategoriBadge');
        if(badge) badge.textContent = `${data.length} Item`;

        if (data.length === 0) {
            listContainer.innerHTML = `<div class="text-center py-4"><i class="bi bi-inbox fs-1 text-muted"></i><p class="text-muted small mt-2">Belum ada kategori.</p></div>`;
            return;
        }

        data.forEach(cat => {
            const item = document.createElement('div');
            item.className = 'd-flex justify-content-between align-items-center p-3 mb-2 bg-white shadow-sm border rounded-3';
            item.innerHTML = `
                <span class="fw-bold text-dark">${cat.nama_kategori_keuangan}</span>
                <div>
                    <button class="btn btn-sm btn-light text-primary shadow-sm rounded-circle me-2" onclick="window.startEditKategori('${cat.id_kategori_keuangan}', '${cat.nama_kategori_keuangan}')"><i class="bi bi-pencil-fill"></i></button>
                    <button class="btn btn-sm btn-light text-danger shadow-sm rounded-circle" onclick="window.hapusKategori('${cat.id_kategori_keuangan}')"><i class="bi bi-trash-fill"></i></button>
                </div>`;
            listContainer.appendChild(item);
        });
    }

    if(formKategori) {
        formKategori.addEventListener('submit', async (e) => {
            e.preventDefault();
            const id = document.getElementById('id_kategori').value;
            const nama = document.getElementById('nama_kategori').value;
            const btnSimpan = document.getElementById('btnSimpanKategori');
            if(!nama) return;
            
            btnSimpan.disabled = true;
            btnSimpan.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
            
            let url = URL_API_KATEGORI;
            let method = 'POST';
            let body = { nama_kategori_keuangan: nama, tipe: TIPE_HALAMAN }; // Kirim Tipe saat save
            if (id) { url += `/${id}`; method = 'PUT'; }

            try {
                const res = await fetch(url, { method: method, headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token }, body: JSON.stringify(body) });
                if (res.ok) { resetFormKategori(); loadListKategori(); loadDropdownKategori(); } 
                else { alert('Gagal menyimpan'); }
            } catch (err) { console.error(err); } 
            finally { 
                btnSimpan.disabled = false; 
                btnSimpan.innerHTML = id ? '<i class="bi bi-check-lg"></i>' : '<i class="bi bi-plus-lg"></i>'; 
            }
        });
    }

    window.startEditKategori = (id, nama) => {
        document.getElementById('id_kategori').value = id;
        document.getElementById('nama_kategori').value = nama;
        document.getElementById('nama_kategori').focus();
        document.getElementById('btnSimpanKategori').innerHTML = '<i class="bi bi-check-lg"></i>';
        document.getElementById('btnBatalEditKategori').classList.remove('d-none');
    };

    const btnBatalKategori = document.getElementById('btnBatalEditKategori');
    if(btnBatalKategori) btnBatalKategori.addEventListener('click', resetFormKategori);

    function resetFormKategori() {
        document.getElementById('id_kategori').value = '';
        document.getElementById('nama_kategori').value = '';
        document.getElementById('btnSimpanKategori').innerHTML = '<i class="bi bi-plus-lg"></i>';
        document.getElementById('btnBatalEditKategori').classList.add('d-none');
    }

    window.hapusKategori = async (id) => {
        if(!confirm('Hapus?')) return;
        try { const res = await fetch(`${URL_API_KATEGORI}/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': token } }); if (res.ok) { loadListKategori(); loadDropdownKategori(); } } catch (err) { alert('Gagal'); }
    };

    // --- 10. LOAD DATA TABEL UTAMA ---
    async function loadTransaksi() {
        if(!tbody) return;
        const searchInput = document.getElementById('searchInput');
        const search = searchInput ? searchInput.value : '';

        // Tampilkan Loading
        tbody.innerHTML = `<tr><td colspan="5" class="text-center"><div class="spinner-border text-primary"></div></td></tr>`;

        try {
            // Fetch Data
            const res = await fetch(`${URL_API_TRANSAKSI}/data?page=${currentPage}&search=${search}`);
            const response = await res.json();

            // Render
            renderTable(response.data, response.from || 1);
            renderPagination(response);
        } catch (err) {
            console.error(err);
            tbody.innerHTML = `<tr><td colspan="5" class="text-center text-danger">Gagal memuat data</td></tr>`;
        }
    }

    function renderTable(data, startNum) {
        tbody.innerHTML = '';
        if (data.length === 0) {
            tbody.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-muted">Belum ada data transaksi.</td></tr>`;
            return;
        }

        data.forEach((item) => {
            const kategoriName = item.kategori ? item.kategori.nama_kategori_keuangan : '-';
            const nominalClass = TIPE_HALAMAN === 'pemasukan' ? 'text-success' : 'text-danger';

            // Helper format Rupiah khusus tabel
            const formatRupiahTabel = (angka) => "Rp " + new Intl.NumberFormat('id-ID').format(angka);
            const tanggalIndo = new Date(item.tanggal).toLocaleDateString('id-ID', {day: '2-digit', month: 'long', year: 'numeric'});

            const row = `
                <tr>
                    <td>${tanggalIndo}</td>
                    <td><span class="badge bg-light text-dark border">${kategoriName}</span></td>
                    <td>${item.deskripsi || '-'}</td>
                    <td class="text-end fw-bold ${nominalClass}">${formatRupiahTabel(item.nominal)}</td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-light text-primary me-1 rounded-circle border"
                            onclick="window.editTransaksi('${item.id_keuangan}')" title="Edit">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <button class="btn btn-sm btn-light text-danger rounded-circle border"
                            onclick="window.hapusTransaksi('${item.id_keuangan}')" title="Hapus">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
            tbody.insertAdjacentHTML('beforeend', row);
        });
    }

    function renderPagination(response) {
        const infoEl = document.getElementById('paginationInfo');
        if(infoEl) {
            infoEl.textContent = `Menampilkan ${response.from || 0} - ${response.to || 0} dari ${response.total} data`;
        }
    }

    // Search Listener
    const searchInput = document.getElementById('searchInput');
    if(searchInput) {
        searchInput.addEventListener('keyup', () => { currentPage = 1; loadTransaksi(); });
    }

    // Panggil fungsi saat load pertama kali
    loadTransaksi();

});