document.addEventListener('DOMContentLoaded', () => {

    // ==========================================
    // 1. SETUP VARIABEL & ELEMENT GLOBAL
    // ==========================================
    const tokenMeta = document.querySelector('meta[name="csrf-token"]');
    const token = tokenMeta ? tokenMeta.content : '';

    // Definisi Elemen Filter & Search [DIPERBARUI]
    const filterTabunganEl = document.getElementById('filterStatusTabungan');
    const filterSetoranEl = document.getElementById('filterStatusSetoran');
    const filterTipeTabunganEl = document.getElementById('filterTipeTabungan'); // [BARU]
    const searchNamaEl = document.getElementById('searchNama'); // [BARU]
    
    // Modal & Form Tabungan
    const modalTabunganEl = document.getElementById('modalTabungan');
    const modalTabungan = modalTabunganEl ? new bootstrap.Modal(modalTabunganEl) : null;
    const formTabungan = document.getElementById('formTabungan');
    const hewanContainer = document.getElementById('hewanContainer');
    
    // Modal & Form Detail
    const modalDetailEl = document.getElementById('modalDetailTabungan');
    const modalDetail = modalDetailEl ? new bootstrap.Modal(modalDetailEl) : null;
    
    // Modal & Form Setoran
    const modalSetorEl = document.getElementById('modalTambahSetoran');
    const modalSetor = modalSetorEl ? new bootstrap.Modal(modalSetorEl) : null;
    const formSetoran = document.getElementById('formTambahSetoran');

    // Modal & Form Harga
    const modalHargaEl = document.getElementById('modalHargaHewan');
    const modalHarga = modalHargaEl ? new bootstrap.Modal(modalHargaEl) : null;
    const formHarga = document.getElementById('formHargaHewan');

    // Modal Kontak Jamaah
    const modalContactEl = document.getElementById('modalContactJamaah');
    const modalContact = modalContactEl ? new bootstrap.Modal(modalContactEl) : null;

    // Data Master Hewan
    let hewanListMaster = [];
    try {
        const jsonEl = document.getElementById('hewanListJson');
        if (jsonEl && jsonEl.value) {
            hewanListMaster = JSON.parse(jsonEl.value);
        }
    } catch (e) {
        console.error("Gagal parsing data master hewan:", e);
    }

    // State Variables [DIPERBARUI]
    let state = {
        currentPage: 1,
        statusTabungan: 'semua',
        statusSetoran: 'semua',
        tipeTabungan: 'semua', // [BARU]
        searchNama: '',        // [BARU]
        sortBy: 'created_at',
        sortDir: 'desc'
    };
    let currentDetailId = null;
    let searchTimeout = null; // Untuk delay pencarian

    // --- Helper Formatter ---
    const fmtMoney = (n) => 'Rp ' + new Intl.NumberFormat('id-ID').format(n);
    const formatDate = (str) => {
        if (!str) return '-';
        const date = new Date(str);
        return date.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
    };

    // ==========================================
    // 2. FUNGSI UTAMA: LOAD TABLE
    // ==========================================
    
    // Fungsi Delay Pencarian (Agar tidak request setiap ketik) [BARU]
    window.loadTableDelay = function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            loadTable(1);
        }, 500); // Tunggu 500ms setelah berhenti mengetik
    };

    window.loadTable = function(page = 1) {
        state.currentPage = page;
        const tbody = document.getElementById('tableBody');
        if (!tbody) return;

        // Ambil Value Filter [DIPERBARUI]
        if (filterTabunganEl) state.statusTabungan = filterTabunganEl.value;
        if (filterSetoranEl) state.statusSetoran = filterSetoranEl.value;
        if (filterTipeTabunganEl) state.tipeTabungan = filterTipeTabunganEl.value;
        if (searchNamaEl) state.searchNama = searchNamaEl.value;

        tbody.innerHTML = `<tr><td colspan="8" class="text-center py-5"><div class="spinner-border text-primary"></div></td></tr>`;

        // Susun URL dengan Parameter Baru [DIPERBARUI]
        const params = new URLSearchParams({
            page: page,
            status_tabungan: state.statusTabungan,
            status_setoran: state.statusSetoran,
            tipe_tabungan: state.tipeTabungan,
            search_nama: state.searchNama,
            perPage: 10,
            sortBy: state.sortBy,
            sortDir: state.sortDir
        });

        fetch(`/pengurus/tabungan-qurban-data?${params.toString()}`)
            .then(res => {
                if (!res.ok) throw new Error("Gagal mengambil data");
                return res.json();
            })
            .then(response => {
                const data = response.data;
                tbody.innerHTML = '';

                if (data.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="8" class="text-center py-4 text-muted">Data tidak ditemukan.</td></tr>`;
                    renderPagination(response);
                    return;
                }

                data.forEach((item, index) => {
                    const no = (response.from || 1) + index;

                    // --- LOGIKA NAMA JAMAAH KLIK ---
                    let jamaahHtml = '<span class="text-muted">Unknown</span>';
                    if (item.jamaah) {
                        const jamaahJson = encodeURIComponent(JSON.stringify(item.jamaah));
                        // Highlight teks pencarian (Opsional, agar user tau yg mana match)
                        let nama = item.jamaah.name;
                        
                        jamaahHtml = `
                            <div class="d-flex align-items-center">
                                <a href="javascript:void(0)" 
                                   onclick="openContactModal('${jamaahJson}')" 
                                   class="fw-bold text-primary text-decoration-none" 
                                   title="Lihat Kontak">
                                   ${nama}
                                </a>
                                <i class="bi bi-info-circle-fill text-muted ms-1" style="font-size: 0.75rem; cursor:pointer;" onclick="openContactModal('${jamaahJson}')"></i>
                            </div>
                        `;
                    }

                    // Render List Hewan
                    let hewanHtml = '<ul class="mb-0 ps-3 small text-muted">';
                    if (item.details && item.details.length > 0) {
                        item.details.forEach(d => {
                            const nama = d.hewan ? d.hewan.nama_hewan : 'Unknown';
                            const kat = d.hewan ? d.hewan.kategori_hewan : '';
                            hewanHtml += `<li>${d.jumlah_hewan} ekor ${nama} (${kat})</li>`;
                        });
                    } else {
                        hewanHtml += '<li>-</li>';
                    }
                    hewanHtml += '</ul>';

                    // Badges
                    let badgeApproval = '';
                    let actionBtns = '';

                    if (item.status === 'menunggu') {
                        badgeApproval = '<span class="badge bg-warning text-dark">Menunggu Acc</span>';
                        actionBtns = `
                            <button class="btn btn-sm btn-success mb-1" title="Setujui" onclick="approveTabungan('${item.id_tabungan_hewan_qurban}', 'disetujui')"><i class="bi bi-check-lg"></i></button>
                            <button class="btn btn-sm btn-danger mb-1" title="Tolak" onclick="approveTabungan('${item.id_tabungan_hewan_qurban}', 'ditolak')"><i class="bi bi-x-lg"></i></button>
                            <button class="btn btn-sm btn-secondary mb-1" title="Hapus" onclick="deleteTabungan('${item.id_tabungan_hewan_qurban}')"><i class="bi bi-trash"></i></button>
                        `;
                    } else if (item.status === 'disetujui') {
                        badgeApproval = '<span class="badge bg-success">Disetujui</span>';
                        actionBtns = `
                            <button class="btn btn-sm btn-info text-white mb-1" title="Detail & Setoran" onclick="openDetail('${item.id_tabungan_hewan_qurban}')"><i class="bi bi-eye"></i></button>
                            <button class="btn btn-sm btn-warning mb-1" title="Edit" onclick="editTabungan('${item.id_tabungan_hewan_qurban}')"><i class="bi bi-pencil"></i></button>
                        `;
                    } else {
                        badgeApproval = '<span class="badge bg-secondary">Ditolak</span>';
                        actionBtns = `<button class="btn btn-sm btn-danger" onclick="deleteTabungan('${item.id_tabungan_hewan_qurban}')"><i class="bi bi-trash"></i></button>`;
                    }

                    let badgeFinance = '-';
                    if (item.status === 'disetujui') {
                        if (item.finance_status === 'lunas') {
                            badgeFinance = '<span class="badge bg-success">Lunas</span>';
                        } else if (item.finance_status === 'menunggak') {
                            badgeFinance = '<span class="badge bg-danger">Menunggak</span>';
                        } else {
                            badgeFinance = `<span class="badge bg-primary">${item.finance_label || 'Aktif'}</span>`;
                        }
                    } else {
                        badgeFinance = '<span class="text-muted small">-</span>';
                    }

                    // Render Row
                    const row = `
                        <tr>
                            <td class="text-center">${no}</td>
                            <td>${jamaahHtml}</td>
                            <td>${hewanHtml}</td>
                            <td class="text-end">
                                <small class="d-block text-muted">Target: ${fmtMoney(item.total_harga_hewan_qurban)}</small>
                                <strong class="text-success">${fmtMoney(item.terkumpul)}</strong>
                            </td>
                            <td class="text-center text-capitalize small">
                                ${item.saving_type} <br> 
                                ${item.saving_type == 'cicilan' ? '(' + (item.duration_months || '-') + ' bln)' : ''}
                            </td>
                            <td class="text-center">${badgeApproval}</td>
                            <td class="text-center">${badgeFinance}</td>
                            <td class="text-center">${actionBtns}</td>
                        </tr>
                    `;
                    tbody.insertAdjacentHTML('beforeend', row);
                });

                renderPagination(response);
            })
            .catch(err => {
                console.error(err);
                tbody.innerHTML = `<tr><td colspan="8" class="text-center py-4 text-danger">Gagal memuat data.</td></tr>`;
            });
    };

    function renderPagination(response) {
        const container = document.getElementById('paginationLinks');
        const info = document.getElementById('paginationInfo');
        if (!container || !info) return;

        if (response.total === 0) {
            info.innerText = 'Menampilkan 0 data';
            container.innerHTML = '';
            return;
        }
        info.innerText = `Menampilkan ${response.from} - ${response.to} dari ${response.total} data`;

        let nav = '<ul class="pagination pagination-sm m-0">';
        response.links.forEach(link => {
            const active = link.active ? 'active' : '';
            const disabled = link.url ? '' : 'disabled';
            const label = link.label.replace('&laquo;', '').replace('&raquo;', '');
            
            let pageNum = 1;
            if (link.url) {
                const urlObj = new URL(link.url);
                pageNum = urlObj.searchParams.get('page');
            }
            nav += `<li class="page-item ${active} ${disabled}"><button class="page-link" onclick="loadTable(${pageNum})">${label}</button></li>`;
        });
        nav += '</ul>';
        container.innerHTML = nav;
    }

    // --- Event Listeners Filter [DIPERBARUI] ---
    if (filterTabunganEl) filterTabunganEl.addEventListener('change', () => loadTable(1));
    if (filterSetoranEl) filterSetoranEl.addEventListener('change', () => loadTable(1));
    if (filterTipeTabunganEl) filterTipeTabunganEl.addEventListener('change', () => loadTable(1));
    // Listener untuk search sudah dihandle via onkeyup="loadTableDelay()" di HTML

    // ==========================================
    // 3. MODAL CREATE / EDIT TABUNGAN
    // ==========================================
    window.openModalCreate = function() {
        if (!formTabungan) return;
        formTabungan.reset();
        document.getElementById('id_tabungan_hewan_qurban').value = '';
        document.getElementById('modalTabunganTitle').innerText = 'Tambah Tabungan Baru';
        hewanContainer.innerHTML = '';
        addHewanRow();
        updateTotalDisplay();
        modalTabungan.show();
    };

    window.addHewanRow = function(selectedId = null, selectedQty = 1) {
        let options = '<option value="" data-harga="0">-- Pilih Hewan --</option>';
        hewanListMaster.forEach(h => {
            const sel = (selectedId && h.id_hewan_qurban == selectedId) ? 'selected' : '';
            options += `<option value="${h.id_hewan_qurban}" data-harga="${h.harga_hewan}" ${sel}>${h.nama_hewan} (${h.kategori_hewan}) - ${fmtMoney(h.harga_hewan)}</option>`;
        });

        const rowDiv = document.createElement('div');
        rowDiv.classList.add('hewan-row', 'row', 'g-2', 'align-items-center');
        rowDiv.innerHTML = `
            <div class="col-7">
                <select name="hewan_items[][id_hewan]" class="form-select select-hewan" onchange="window.updateTotalDisplay()" required>${options}</select>
            </div>
            <div class="col-3">
                <input type="number" name="hewan_items[][jumlah]" class="form-control input-qty" value="${selectedQty}" min="1" oninput="window.updateTotalDisplay()" required placeholder="Qty">
            </div>
            <div class="col-2">
                <button type="button" class="btn btn-danger w-100" onclick="window.removeRow(this)"><i class="bi bi-trash"></i></button>
            </div>
        `;
        hewanContainer.appendChild(rowDiv);
        updateTotalDisplay();
    };

    window.removeRow = function(btn) {
        if (document.querySelectorAll('.hewan-row').length > 1) {
            btn.closest('.hewan-row').remove();
            updateTotalDisplay();
        } else {
            Swal.fire('Info', 'Minimal harus ada 1 hewan.', 'info');
        }
    };

    window.updateTotalDisplay = function() {
        let total = 0;
        document.querySelectorAll('.hewan-row').forEach(row => {
            const select = row.querySelector('.select-hewan');
            const qty = row.querySelector('.input-qty').value;
            const selectedOption = select.options[select.selectedIndex];
            const harga = selectedOption ? selectedOption.getAttribute('data-harga') : 0;
            total += (parseInt(harga || 0) * parseInt(qty || 0));
        });

        const displayEl = document.getElementById('displayTotalTarget');
        if (displayEl) displayEl.innerText = fmtMoney(total) + " (Estimasi Sistem)";

        const inputEl = document.getElementById('total_harga_input');
        if (inputEl) inputEl.value = total; 

        const durationInput = document.getElementById('duration_months');
        const estEl = document.getElementById('estBulan');
        if (durationInput && estEl) {
            const duration = durationInput.value;
            const totalDeal = inputEl ? inputEl.value : total;
            if (duration > 0) estEl.innerText = fmtMoney(Math.round(totalDeal / duration));
            else estEl.innerText = '-';
        }
    };

    const manualInputPrice = document.getElementById('total_harga_input');
    if(manualInputPrice) {
        manualInputPrice.addEventListener('input', function() {
             const durationInput = document.getElementById('duration_months');
             const estEl = document.getElementById('estBulan');
             if (durationInput && estEl && durationInput.value > 0) {
                 estEl.innerText = fmtMoney(Math.round(this.value / durationInput.value));
             }
        });
    }

    window.toggleDuration = function() {
        const type = document.getElementById('saving_type').value;
        const div = document.getElementById('divDuration');
        const input = document.getElementById('duration_months');
        if (type == 'bebas') {
            div.style.display = 'none';
            input.removeAttribute('required');
        } else {
            div.style.display = 'block';
            input.setAttribute('required', 'required');
        }
        updateTotalDisplay();
    };

    if (formTabungan) {
        formTabungan.addEventListener('submit', function(e) {
            e.preventDefault();
            const id = document.getElementById('id_tabungan_hewan_qurban').value;
            const formData = new FormData(this);
            formData.delete('hewan_items[][id_hewan]');
            formData.delete('hewan_items[][jumlah]');
            document.querySelectorAll('.hewan-row').forEach((row, index) => {
                const idH = row.querySelector('.select-hewan').value;
                const qty = row.querySelector('.input-qty').value;
                formData.append(`hewan_items[${index}][id_hewan]`, idH);
                formData.append(`hewan_items[${index}][jumlah]`, qty);
            });

            let url = "/pengurus/tabungan-qurban"; 
            if (id) {
                url = `/pengurus/tabungan-qurban/${id}`;
                formData.append('_method', 'PUT');
            }

            fetch(url, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
                body: formData
            })
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    Swal.fire('Berhasil', res.message, 'success');
                    modalTabungan.hide();
                    loadTable(state.currentPage);
                } else {
                    let msg = res.message;
                    if (res.errors) msg = Object.values(res.errors).flat().join('\n');
                    Swal.fire('Gagal', msg, 'error');
                }
            })
            .catch(err => Swal.fire('Error', 'Terjadi kesalahan sistem', 'error'));
        });
    }

    window.editTabungan = function(id) {
        fetch(`/pengurus/tabungan-qurban/${id}`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('id_tabungan_hewan_qurban').value = data.id_tabungan_hewan_qurban;
                document.getElementById('id_jamaah').value = data.id_jamaah;
                document.getElementById('saving_type').value = data.saving_type;
                document.getElementById('duration_months').value = data.duration_months || '';

                const inputEl = document.getElementById('total_harga_input');
                if(inputEl) inputEl.value = data.total_harga_hewan_qurban;

                toggleDuration();
                document.getElementById('modalTabunganTitle').innerText = 'Edit Tabungan';
                hewanContainer.innerHTML = '';
                if (data.details && data.details.length > 0) {
                    data.details.forEach(d => addHewanRow(d.id_hewan_qurban, d.jumlah_hewan));
                } else {
                    addHewanRow();
                }
                updateTotalDisplay();
                // Override lagi setelah update display agar harga deal tetap muncul
                if(inputEl) inputEl.value = data.total_harga_hewan_qurban; 
                modalTabungan.show();
            });
    };

    window.approveTabungan = function(id, status) {
        Swal.fire({
            title: `Konfirmasi ${status === 'disetujui' ? 'Menyetujui' : 'Menolak'}?`,
            text: "Status akan diperbarui.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/pengurus/tabungan-qurban/${id}/status`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
                    body: JSON.stringify({ status: status })
                })
                .then(res => res.json())
                .then(res => {
                    Swal.fire('Sukses', res.message, 'success');
                    loadTable(state.currentPage);
                });
            }
        });
    };

    window.deleteTabungan = function(id) {
        Swal.fire({
            title: 'Hapus Data?',
            text: "Data akan hilang permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Hapus'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/pengurus/tabungan-qurban/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' }
                })
                .then(res => res.json())
                .then(res => {
                    if (res.success) {
                        Swal.fire('Terhapus', res.message, 'success');
                        loadTable(state.currentPage);
                    }
                });
            }
        });
    };

    // ==========================================
    // 4. DETAIL & SETORAN
    // ==========================================
    window.openDetail = function(id) {
        currentDetailId = id;
        const inputSetorId = document.getElementById('setoran_id_tabungan');
        if (inputSetorId) inputSetorId.value = id;
        refreshDetailModal(id);
        if (modalDetail) modalDetail.show();
    };

    function refreshDetailModal(id) {
        fetch(`/pengurus/tabungan-qurban/${id}`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('detailModalTitle').innerText = `Detail: ${data.jamaah.name}`;
                document.getElementById('detailSavingType').innerText = data.saving_type;
                document.getElementById('detailStatusBadge').innerText = data.status.toUpperCase();

                const terkumpul = data.pemasukan_tabungan_qurban.reduce((a, b) => a + parseInt(b.nominal), 0);
                const sisa = data.total_harga_hewan_qurban - terkumpul;

                document.getElementById('detailTotalHarga').innerText = fmtMoney(data.total_harga_hewan_qurban);
                document.getElementById('detailTerkumpul').innerText = fmtMoney(terkumpul);
                document.getElementById('detailSisa').innerText = fmtMoney(sisa);

                const listUl = document.getElementById('detailListHewan');
                listUl.innerHTML = '';
                data.details.forEach(d => {
                    listUl.innerHTML += `<li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>${d.hewan.nama_hewan} (${d.hewan.kategori_hewan})</span>
                        <span class="badge bg-primary rounded-pill">${d.jumlah_hewan} Ekor</span>
                    </li>`;
                });

                const tbody = document.getElementById('tabelRiwayatSetoran');
                tbody.innerHTML = '';
                if (data.pemasukan_tabungan_qurban.length > 0) {
                    data.pemasukan_tabungan_qurban.forEach(p => {
                        tbody.innerHTML += `<tr>
                            <td>${formatDate(p.tanggal)}</td>
                            <td class="text-end fw-bold text-success">+ ${fmtMoney(p.nominal)}</td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-danger py-0" onclick="deleteSetoran('${p.id_pemasukan_tabungan_qurban}')">&times;</button>
                            </td>
                        </tr>`;
                    });
                } else {
                    tbody.innerHTML = '<tr><td colspan="3" class="text-center text-muted">Belum ada setoran.</td></tr>';
                }
            });
    }

    window.openModalSetor = function() {
        document.getElementById('formTambahSetoran').reset();
        document.querySelector('#formTambahSetoran input[name="tanggal"]').value = new Date().toISOString().split('T')[0];
        if (modalSetor) modalSetor.show();
    };

    if (formSetoran) {
        formSetoran.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            fetch("/pengurus/pemasukan-qurban", {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
                body: formData
            })
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    Swal.fire('Berhasil', 'Setoran tersimpan', 'success');
                    modalSetor.hide();
                    refreshDetailModal(currentDetailId);
                    loadTable(state.currentPage);
                } else {
                    Swal.fire('Gagal', 'Gagal menyimpan', 'error');
                }
            });
        });
    }

    window.deleteSetoran = function(idSetoran) {
        Swal.fire({
            title: 'Hapus Setoran?',
            text: "Saldo akan berkurang.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Hapus'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/pengurus/pemasukan-qurban/${idSetoran}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' }
                })
                .then(res => res.json())
                .then(res => {
                    if (res.success) {
                        refreshDetailModal(currentDetailId);
                        loadTable(state.currentPage);
                        Swal.fire('Terhapus', 'Setoran dihapus.', 'success');
                    }
                });
            }
        });
    };

    // ==========================================
    // 5. MASTER HARGA HEWAN
    // ==========================================
    if (modalHargaEl) {
        modalHargaEl.addEventListener('show.bs.modal', function (event) {
            resetFormHarga();
            document.getElementById('alertHargaContainer').innerHTML = ''; 
        });
    }

    function resetFormHarga() {
        if(formHarga) formHarga.reset();
        document.getElementById('input_id_hewan_qurban').value = ''; 
        const titleEl = document.getElementById('modalHargaTitle');
        if(titleEl) titleEl.innerText = 'Kelola Harga Hewan Qurban'; 
        const btnSimpan = document.getElementById('btnSimpanHarga');
        if(btnSimpan) btnSimpan.innerText = 'Simpan'; 
    }

    function showHargaNotif(message) {
        const container = document.getElementById('alertHargaContainer');
        if(container) {
            container.innerHTML = `<div class="alert alert-success py-2 mb-3 shadow-sm small fw-bold">${message}</div>`;
            setTimeout(() => { container.innerHTML = ''; }, 2000);
        }
    }

    window.loadListHarga = function() {
        const tbody = document.getElementById('listHargaBody');
        if (!tbody) return;
        tbody.innerHTML = '<tr><td colspan="4" class="text-center">Loading...</td></tr>';

        fetch("/pengurus/hewan-qurban")
            .then(res => res.json())
            .then(data => {
                tbody.innerHTML = '';
                if (data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="4" class="text-center">Belum ada data.</td></tr>';
                    return;
                }
                data.forEach(h => {
                    const dataJson = encodeURIComponent(JSON.stringify(h));
                    tbody.innerHTML += `<tr>
                        <td class="text-capitalize">${h.nama_hewan}</td>
                        <td class="text-capitalize">${h.kategori_hewan}</td>
                        <td class="text-end">${fmtMoney(h.harga_hewan)}</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-warning py-0 me-1" onclick="editHarga('${dataJson}')"><i class="bi bi-pencil"></i></button>
                            <button class="btn btn-sm btn-danger py-0" onclick="deleteHarga('${h.id_hewan_qurban}')">&times;</button>
                        </td>
                    </tr>`;
                });
            });
    };

    window.editHarga = function(jsonString) {
        const data = JSON.parse(decodeURIComponent(jsonString));
        document.getElementById('input_id_hewan_qurban').value = data.id_hewan_qurban;
        document.querySelector('select[name="nama_hewan"]').value = data.nama_hewan;
        document.querySelector('select[name="kategori_hewan"]').value = data.kategori_hewan;
        document.querySelector('input[name="harga_hewan"]').value = data.harga_hewan;
        document.getElementById('modalHargaTitle').innerText = 'Edit Harga Hewan';
        document.getElementById('btnSimpanHarga').innerText = 'Update';
    };

    if (formHarga) {
        formHarga.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const id = document.getElementById('input_id_hewan_qurban').value;
            let url = "/pengurus/hewan-qurban";
            if (id) {
                url = `/pengurus/hewan-qurban/${id}`;
                formData.append('_method', 'PUT'); 
            }
            fetch(url, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
                body: formData
            })
            .then(res => res.json())
            .then(res => {
                if (res.success || res.message) {
                    showHargaNotif(res.message || 'Data disimpan');
                    resetFormHarga(); 
                    loadListHarga(); 
                } else {
                    Swal.fire('Gagal', 'Gagal menyimpan', 'error');
                }
            })
            .catch(err => Swal.fire('Error', 'Kesalahan sistem', 'error'));
        });
    }

    window.deleteHarga = function(id) {
        Swal.fire({
            title: 'Hapus?',
            text: "Data akan hilang.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Hapus'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/pengurus/hewan-qurban/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': token } })
                .then(res => { loadListHarga(); showHargaNotif('Terhapus.'); })
                .catch(err => Swal.fire('Gagal', 'Gagal menghapus', 'error'));
            }
        });
    };

    window.togglePdfFilter = function() {
        const select = document.getElementById('filter-periode');
        if (!select) return;
        const val = select.value;
        const bulanan = document.getElementById('filter-bulanan');
        const tahunan = document.getElementById('filter-tahunan');
        const rentang = document.getElementById('filter-rentang');
        if (bulanan) bulanan.style.display = (val === 'per_bulan') ? 'block' : 'none';
        if (tahunan) tahunan.style.display = (val === 'per_tahun') ? 'block' : 'none';
        if (rentang) rentang.style.display = (val === 'rentang_waktu') ? 'block' : 'none';
    };

    // ==========================================
    // 6. FUNGSI MODAL KONTAK JAMAAH
    // ==========================================
    window.openContactModal = function(jsonString) {
        if (!modalContact) return;

        // Decode Data
        const jamaah = JSON.parse(decodeURIComponent(jsonString));

        document.getElementById('contactName').innerText = jamaah.name;
        document.getElementById('contactEmail').innerText = jamaah.email || '-';
        document.getElementById('contactPhone').innerText = jamaah.no_hp || '-';

        const avatarEl = document.getElementById('contactAvatar');
        if (jamaah.avatar) {
            avatarEl.src = `/storage/${jamaah.avatar}`; 
        } else {
            avatarEl.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(jamaah.name)}&background=198754&color=fff&size=128`;
        }
        avatarEl.onerror = function() { this.src = 'https://via.placeholder.com/100?text=User'; };

        const btnWa = document.getElementById('btnChatWA');
        if (jamaah.no_hp) {
            let phone = jamaah.no_hp.replace(/\D/g, ''); 
            if (phone.startsWith('0')) phone = '62' + phone.substring(1);
            const text = `Assalamu'alaikum ${jamaah.name}, saya pengurus Qurban ingin mendiskusikan tabungan Anda.`;
            btnWa.href = `https://wa.me/${phone}?text=${encodeURIComponent(text)}`;
            btnWa.classList.remove('disabled');
        } else {
            btnWa.href = '#';
            btnWa.classList.add('disabled');
        }

        modalContact.show();
    };

    // Jalankan loadTable pertama kali
    loadTable();
});