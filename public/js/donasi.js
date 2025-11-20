document.addEventListener('DOMContentLoaded', () => {
    const token = document.querySelector('meta[name="csrf-token"]').content;
    const tbody = document.querySelector('#tabelDonasi tbody');
    
    // --- Elements Modal Program Donasi ---
    const modalDonasiEl = document.getElementById('modalDonasi');
    const modalDonasi = new bootstrap.Modal(modalDonasiEl);
    const formDonasi = document.getElementById('formDonasi');
    const fotoInput = document.getElementById('foto_donasi');
    const previewFoto = document.getElementById('previewFoto');
    const fotoLabel = document.getElementById('foto_label').querySelector('span');

    // --- Elements Modal Detail & Pemasukan ---
    const modalDetailEl = document.getElementById('modalDetail');
    const modalDetail = new bootstrap.Modal(modalDetailEl);
    const modalInputPemasukanEl = document.getElementById('modalInputPemasukan');
    const modalInputPemasukan = new bootstrap.Modal(modalInputPemasukanEl);
    const formPemasukan = document.getElementById('formPemasukan');

    // State
    let currentPage = 1;
    let currentDonasiId = null; // Untuk track detail yang sedang dibuka

    // --- Format Rupiah ---
    const formatRupiah = (angka) => "Rp " + new Intl.NumberFormat('id-ID').format(angka);
    const formatTanggal = (str) => {
        if (!str) return '-';
        return new Date(str).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
    };

    // =========================================
    // 1. LOAD TABLE UTAMA
    // =========================================
    async function loadDonasi() {
        const search = document.getElementById('searchInput').value;
        tbody.innerHTML = `<tr><td colspan="7" class="text-center"><div class="spinner-border text-primary"></div></td></tr>`;

        try {
            const res = await fetch(`/pengurus/donasi-data?page=${currentPage}&search=${search}`);
            const response = await res.json();
            renderTable(response.data, response.from || 1);
            renderPagination(response);
        } catch (err) {
            tbody.innerHTML = `<tr><td colspan="7" class="text-center text-danger">Gagal memuat data</td></tr>`;
        }
    }

    function renderTable(data, startNum) {
        tbody.innerHTML = '';
        if (data.length === 0) {
            tbody.innerHTML = `<tr><td colspan="7" class="text-center">Belum ada program donasi.</td></tr>`;
            return;
        }

        data.forEach((item, i) => {
            const target = parseFloat(item.target_dana);
            const terkumpul = parseFloat(item.total_terkumpul);
            const persen = target > 0 ? Math.min(100, (terkumpul / target) * 100).toFixed(1) : 0;
            const fotoUrl = item.foto_donasi ? `/storage/${item.foto_donasi}` : 'https://via.placeholder.com/60?text=No+Img';

            const row = `
                <tr>
                    <td class="text-center">${startNum + i}</td>
                    <td class="text-center">
                        <img src="${fotoUrl}" class="rounded" style="width:50px; height:50px; object-fit:cover;">
                    </td>
                    <td>
                        <div class="fw-bold">${item.nama_donasi}</div>
                        <small class="text-muted">${formatTanggal(item.tanggal_mulai)}</small>
                    </td>
                    <td class="text-end">${formatRupiah(target)}</td>
                    <td class="text-end text-success fw-bold">${formatRupiah(terkumpul)}</td>
                    <td class="text-center">
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-success" style="width: ${persen}%"></div>
                        </div>
                        <small>${persen}%</small>
                    </td>
                    <td class="text-center">
                        <button class="btn btn-info btn-sm text-white" onclick="window.bukaDetail('${item.id_donasi}')" title="Lihat Pemasukan">
                            <i class="bi bi-eye"></i>
                        </button>
                        <button class="btn btn-warning btn-sm" onclick="window.editDonasi('${item.id_donasi}')">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="window.hapusDonasi('${item.id_donasi}')">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
            tbody.insertAdjacentHTML('beforeend', row);
        });
    }

    function renderPagination(response) {
        // Simplifikasi pagination (bisa dicopy dari kode Khotib/Tabungan)
        document.getElementById('paginationInfo').textContent = `Menampilkan ${response.from || 0} - ${response.to || 0} dari ${response.total} data`;
        // Logic button prev/next...
    }

    document.getElementById('searchInput').addEventListener('keyup', () => { currentPage = 1; loadDonasi(); });

    // =========================================
    // 2. CRUD PROGRAM DONASI (Parent)
    // =========================================
    document.getElementById('btnTambahDonasi').addEventListener('click', () => {
        formDonasi.reset();
        document.getElementById('id_donasi').value = '';
        previewFoto.classList.add('d-none');
        fotoLabel.textContent = "Pilih gambar...";
        document.getElementById('modalTitle').textContent = "Program Donasi Baru";
        modalDonasi.show();
    });

    // Preview Image Handler
    fotoInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            fotoLabel.textContent = file.name;
            const reader = new FileReader();
            reader.onload = (e) => {
                previewFoto.src = e.target.result;
                previewFoto.classList.remove('d-none');
            }
            reader.readAsDataURL(file);
        }
    });

    formDonasi.addEventListener('submit', async (e) => {
        e.preventDefault();
        const id = document.getElementById('id_donasi').value;
        const formData = new FormData(formDonasi);
        let url = '/pengurus/donasi';
        
        if (id) {
            url += `/${id}`;
            formData.append('_method', 'PUT');
        }

        try {
            const res = await fetch(url, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': token },
                body: formData
            });
            const data = await res.json();
            if (res.ok) {
                modalDonasi.hide();
                Swal.fire('Sukses', data.message, 'success');
                loadDonasi();
            } else {
                throw new Error(data.message || 'Error validasi');
            }
        } catch (err) {
            Swal.fire('Error', err.message, 'error');
        }
    });

    window.editDonasi = async (id) => {
        const res = await fetch(`/pengurus/donasi/${id}`);
        const data = await res.json();
        
        document.getElementById('id_donasi').value = data.id_donasi;
        document.getElementById('nama_donasi').value = data.nama_donasi;
        document.getElementById('target_dana').value = data.target_dana;
        document.getElementById('tanggal_mulai').value = data.tanggal_mulai.split('T')[0];
        if(data.tanggal_selesai) document.getElementById('tanggal_selesai').value = data.tanggal_selesai.split('T')[0];
        document.getElementById('deskripsi').value = data.deskripsi || '';

        if (data.foto_donasi) {
            previewFoto.src = data.foto_url;
            previewFoto.classList.remove('d-none');
            fotoLabel.textContent = "Ganti gambar...";
        } else {
            previewFoto.classList.add('d-none');
            fotoLabel.textContent = "Pilih gambar...";
        }
        
        document.getElementById('modalTitle').textContent = "Edit Program Donasi";
        modalDonasi.show();
    };

    window.hapusDonasi = async (id) => {
        const c = await Swal.fire({ title: 'Hapus?', text: 'Data donasi & pemasukannya akan hilang!', icon: 'warning', showCancelButton: true });
        if (c.isConfirmed) {
            await fetch(`/pengurus/donasi/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': token } });
            loadDonasi();
            Swal.fire('Terhapus', '', 'success');
        }
    };

    // =========================================
    // 3. MODAL DETAIL & PEMASUKAN (Child)
    // =========================================
    
    // Fungsi membuka detail (Klik icon mata)
    window.bukaDetail = async (id) => {
        currentDonasiId = id;
        await refreshModalDetail();
        modalDetail.show();
    };

    // Refresh isi modal detail (dipanggil saat buka atau setelah input uang)
    async function refreshModalDetail() {
        if(!currentDonasiId) return;

        const res = await fetch(`/pengurus/donasi/${currentDonasiId}`);
        const data = await res.json();

        // Update Header & Stats
        document.getElementById('detailTitle').textContent = data.nama_donasi;
        document.getElementById('input_id_donasi').value = data.id_donasi; // Set ID ke form input

        const target = parseFloat(data.target_dana);
        const terkumpul = parseFloat(data.total_terkumpul);
        const sisa = target - terkumpul;

        document.getElementById('detTarget').textContent = formatRupiah(target);
        document.getElementById('detTerkumpul').textContent = formatRupiah(terkumpul);
        document.getElementById('detSisa').textContent = formatRupiah(sisa > 0 ? sisa : 0);

        // Render Tabel Riwayat
        const tbodyRiwayat = document.getElementById('tabelRiwayat');
        tbodyRiwayat.innerHTML = '';

        if (data.pemasukan.length === 0) {
            tbodyRiwayat.innerHTML = `<tr><td colspan="4" class="text-center text-muted">Belum ada pemasukan.</td></tr>`;
        } else {
            data.pemasukan.forEach(p => {
                const row = `
                    <tr>
                        <td>${formatTanggal(p.tanggal)}</td>
                        <td>
                            ${p.nama_donatur}
                            ${p.pesan ? `<br><small class='text-muted fst-italic'>Msg: "${p.pesan}"</small>` : ''}
                        </td>
                        <td class="text-end">${formatRupiah(p.nominal)}</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-outline-danger py-0 px-1" onclick="hapusPemasukan('${p.id_pemasukan_donasi}')">
                                &times;
                            </button>
                        </td>
                    </tr>
                `;
                tbodyRiwayat.insertAdjacentHTML('beforeend', row);
            });
        }
    }

    // Submit Form Pemasukan (Uang Masuk)
    formPemasukan.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(formPemasukan);

        try {
            const res = await fetch('/pengurus/pemasukan-donasi', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
                body: formData
            });
            const data = await res.json();

            if (res.ok) {
                modalInputPemasukan.hide();
                formPemasukan.reset();
                Swal.fire({ icon: 'success', title: 'Masuk!', text: data.message, timer: 1500, showConfirmButton: false });
                
                // Refresh modal detail agar angka update realtime
                await refreshModalDetail();
                // Refresh tabel utama agar progress bar update
                loadDonasi();
            } else {
                throw new Error(data.message);
            }
        } catch (err) {
            Swal.fire('Gagal', err.message, 'error');
        }
    });

    // Hapus Pemasukan
    window.hapusPemasukan = async (idPemasukan) => {
        const c = await Swal.fire({ 
            title: 'Batalkan Donasi?', 
            text: 'Data akan dihapus dari riwayat.', 
            icon: 'warning', 
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
            confirmButtonColor: '#d33'
        });

        if (c.isConfirmed) {
            await fetch(`/pengurus/pemasukan-donasi/${idPemasukan}`, { 
                method: 'DELETE', 
                headers: { 'X-CSRF-TOKEN': token } 
            });
            
            await refreshModalDetail();
            loadDonasi();
            Swal.fire('Dihapus', '', 'success');
        }
    };

    // Init
    loadDonasi();
});