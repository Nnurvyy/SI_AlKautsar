document.addEventListener('DOMContentLoaded', () => {

    // ==========================================
    // 1. SETUP VARIABEL & ELEMENT GLOBAL
    // ==========================================
    const tokenMeta = document.querySelector('meta[name="csrf-token"]');
    const token = tokenMeta ? tokenMeta.content : '';

    // Filters
    const filterTabunganEl = document.getElementById('filterStatusTabungan');
    const filterSetoranEl = document.getElementById('filterStatusSetoran');
    const searchNamaEl = document.getElementById('searchNama');
    
    // Modal & Form Tabungan (CREATE/EDIT)
    const modalTabunganEl = document.getElementById('modalTabungan');
    const modalTabungan = modalTabunganEl ? new bootstrap.Modal(modalTabunganEl) : null;
    const formTabungan = document.getElementById('formTabungan');
    const hewanContainer = document.getElementById('hewanContainer');
    const displayTotalHarga = document.getElementById('display_total_harga'); // Visual Input
    const realTotalHarga = document.getElementById('total_harga_input');     // Hidden Input
    
    // Modal & Form Detail
    const modalDetailEl = document.getElementById('modalDetailTabungan');
    const modalDetail = modalDetailEl ? new bootstrap.Modal(modalDetailEl) : null;
    
    // Modal & Form Setoran (Manual)
    const modalSetorEl = document.getElementById('modalTambahSetoran');
    const modalSetor = modalSetorEl ? new bootstrap.Modal(modalSetorEl) : null;
    const formSetoran = document.getElementById('formTambahSetoran');
    const displayNominalSetor = document.getElementById('display_nominal_setor'); // Visual Input
    const realNominalSetor = document.getElementById('real_nominal_setor');       // Hidden Input

    // Modal & Form Harga
    const modalHargaEl = document.getElementById('modalHargaHewan');
    const formHarga = document.getElementById('formHargaHewan');

    const displayHarga = document.getElementById('display_harga_hewan');
    const realHarga = document.getElementById('real_harga_hewan');

    // Modal Kontak
    const modalContactEl = document.getElementById('modalContactJamaah');
    const modalContact = modalContactEl ? new bootstrap.Modal(modalContactEl) : null;

    // Data Master
    let hewanListMaster = [];
    try {
        const jsonEl = document.getElementById('hewanListJson');
        if (jsonEl && jsonEl.value) hewanListMaster = JSON.parse(jsonEl.value);
    } catch (e) { console.error(e); }

    let state = { currentPage: 1, statusTabungan: 'semua', statusSetoran: 'semua', searchNama: '' };
    let currentDetailData = null; 
    let searchTimeout = null;

    // --- Helper Formatter ---
    const fmtMoney = (n) => 'Rp ' + new Intl.NumberFormat('id-ID').format(n);
    const formatDate = (str) => {
        if (!str) return '-';
        return new Date(str).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
    };

    // ==========================================
    // 2. LOGIC FORMAT RUPIAH (INPUT)
    // ==========================================
    function setupCurrencyInput(displayEl, realEl) {
        if (!displayEl || !realEl) return;
        displayEl.addEventListener('keyup', function(e) {
            let raw = this.value.replace(/[^0-9]/g, '');
            realEl.value = raw;
            this.value = raw ? new Intl.NumberFormat('id-ID').format(raw) : '';
            // Trigger event change manual jika perlu update total lain
            if(displayEl.id === 'display_total_harga') updateTotalDisplay(); 
        });
    }
    setupCurrencyInput(displayTotalHarga, realTotalHarga);
    setupCurrencyInput(displayNominalSetor, realNominalSetor);
    setupCurrencyInput(displayHarga, realHarga);

    // ==========================================
    // 3. FUNGSI UTAMA: LOAD TABLE
    // ==========================================
    window.loadTableDelay = function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => { loadTable(1); }, 500); 
    };

    window.loadTable = function(page = 1) {
        state.currentPage = page;
        const tbody = document.getElementById('tableBody');
        if (!tbody) return;

        state.statusTabungan = filterTabunganEl.value;
        state.statusSetoran = filterSetoranEl.value;
        state.searchNama = searchNamaEl.value;

        tbody.innerHTML = `<tr><td colspan="8" class="text-center py-5"><div class="spinner-border text-success"></div></td></tr>`;

        const params = new URLSearchParams({
            page: page, status_tabungan: state.statusTabungan, status_setoran: state.statusSetoran,
            search_nama: state.searchNama, perPage: 10, sortBy: 'created_at', sortDir: 'desc'
        });

        fetch(`/pengurus/tabungan-qurban-data?${params.toString()}`)
            .then(res => res.json())
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
                    
                    // --- REVISI 1: JAMAAH COLUMN (HANYA NAMA, KLIKABLE) ---
                    let jamaahHtml = '<span class="text-muted">Unknown</span>';
                    if (item.jamaah) {
                        const jamaahJson = encodeURIComponent(JSON.stringify(item.jamaah));
                        // Tampilan simpel: Nama tebal, cursor pointer, saat diklik buka modal contact
                        jamaahHtml = `
                            <a href="javascript:void(0)" 
                               onclick="openContactModal('${jamaahJson}')" 
                               class="fw-bold text-dark text-decoration-none" 
                               title="Lihat Detail Kontak">
                               ${item.jamaah.name}
                            </a>
                        `;
                    }

                    // Hewan HTML (Tetap)
                    let hewanHtml = '<ul class="mb-0 ps-3 small text-muted" style="list-style-type:circle;">';
                    if (item.details && item.details.length > 0) {
                        item.details.forEach(d => {
                            const nama = d.hewan ? d.hewan.nama_hewan : 'Unknown';
                            const kat = d.hewan ? d.hewan.kategori_hewan : '';
                            hewanHtml += `<li><b>${d.jumlah_hewan}</b> ${nama} <span class="text-secondary">(${kat})</span></li>`;
                        });
                    } else { hewanHtml += '<li>-</li>'; }
                    hewanHtml += '</ul>';

                    // --- REVISI 2: TOMBOL AKSI (HORIZONTAL / KE SAMPING) ---
                    let badgeApproval = '';
                    let actionBtns = '';

                    // Gunakan d-flex gap-1 agar tombol berjajar ke samping
                    if (item.status === 'menunggu') {
                        badgeApproval = '<span class="badge bg-warning text-dark rounded-pill">⏳ Menunggu</span>';
                        actionBtns = `
                            <div class="d-flex gap-1 justify-content-center">
                                <button class="btn btn-sm btn-success rounded-3 shadow-sm" title="Setujui" onclick="approveTabungan('${item.id_tabungan_hewan_qurban}', 'disetujui')"><i class="bi bi-check-lg"></i></button>
                                <button class="btn btn-sm btn-danger rounded-3 shadow-sm" title="Tolak" onclick="approveTabungan('${item.id_tabungan_hewan_qurban}', 'ditolak')"><i class="bi bi-x-lg"></i></button>
                                <button class="btn btn-sm btn-secondary rounded-3 shadow-sm" title="Hapus" onclick="deleteTabungan('${item.id_tabungan_hewan_qurban}')"><i class="bi bi-trash"></i></button>
                            </div>
                        `;
                    } else if (item.status === 'disetujui') {
                        badgeApproval = '<span class="badge bg-success rounded-pill">✅ Disetujui</span>';
                        actionBtns = `
                            <div class="d-flex gap-1 justify-content-center">
                                <button class="btn btn-sm btn-info text-white rounded-3 shadow-sm" title="Detail & Setoran" onclick="openDetail('${item.id_tabungan_hewan_qurban}')"><i class="bi bi-eye"></i></button>
                                <button class="btn btn-sm btn-warning text-white rounded-3 shadow-sm" title="Edit" onclick="editTabungan('${item.id_tabungan_hewan_qurban}')"><i class="bi bi-pencil"></i></button>
                            </div>
                        `;
                    } else {
                        badgeApproval = '<span class="badge bg-secondary rounded-pill">❌ Ditolak</span>';
                        actionBtns = `
                            <div class="d-flex gap-1 justify-content-center">
                                <button class="btn btn-sm btn-outline-danger rounded-circle" title="Hapus" onclick="deleteTabungan('${item.id_tabungan_hewan_qurban}')"><i class="bi bi-trash"></i></button>
                            </div>
                        `;
                    }

                    let badgeFinance = '';
                    if (item.status === 'disetujui') {
                        if (item.finance_status === 'lunas') badgeFinance = '<span class="badge bg-success rounded-pill">Lunas</span>';
                        else if (item.finance_status === 'menunggak') badgeFinance = '<span class="badge bg-danger rounded-pill">Menunggak</span>';
                        else badgeFinance = `<span class="badge bg-primary rounded-pill">${item.finance_label || 'Aktif'}</span>`;
                    } else { badgeFinance = '-'; }

                    const row = `
                        <tr>
                            <td class="text-center text-muted">${no}</td>
                            <td>${jamaahHtml}</td> <td>${hewanHtml}</td>
                            <td class="text-end">
                                <small class="d-block text-muted">Target: ${fmtMoney(item.total_harga_hewan_qurban)}</small>
                                <strong class="text-success">${fmtMoney(item.terkumpul)}</strong>
                            </td>
                            <td class="text-center text-capitalize small text-secondary">
                                ${item.saving_type} <br> 
                                ${item.saving_type == 'cicilan' ? '<span class="badge bg-light text-dark border">' + (item.duration_months || '-') + ' bln</span>' : ''}
                            </td>
                            <td class="text-center">${badgeApproval}</td>
                            <td class="text-center">${badgeFinance}</td>
                            <td class="text-center">${actionBtns}</td> </tr>
                    `;
                    tbody.insertAdjacentHTML('beforeend', row);
                });
                renderPagination(response);
            });
    };

    function renderPagination(response) {
        const container = document.getElementById('paginationLinks');
        const info = document.getElementById('paginationInfo');
        if (!container || !info) return;

        info.innerText = response.total === 0 ? '0 Data' : `Menampilkan ${response.from} - ${response.to} dari ${response.total} data`;
        if (response.total === 0) { container.innerHTML = ''; return; }

        let nav = '<ul class="pagination pagination-sm m-0">';
        response.links.forEach(link => {
            const active = link.active ? 'active' : '';
            const disabled = link.url ? '' : 'disabled';
            let label = link.label.replace('&laquo;', '<').replace('&raquo;', '>');
            let pageNum = link.url ? new URL(link.url).searchParams.get('page') : 1;
            nav += `<li class="page-item ${active} ${disabled}"><button class="page-link" onclick="loadTable(${pageNum})">${label}</button></li>`;
        });
        nav += '</ul>';
        container.innerHTML = nav;
    }

    // ==========================================
    // 4. MODAL CREATE / EDIT TABUNGAN
    // ==========================================
    window.openModalCreate = function() {
        if (!formTabungan) return;
        formTabungan.reset();
        document.getElementById('id_tabungan_hewan_qurban').value = '';
        realTotalHarga.value = ''; // Reset Hidden
        document.getElementById('modalTabunganTitle').innerText = 'Buka Tabungan Baru';
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
                <select name="hewan_items[][id_hewan]" class="form-select select-hewan border-0 bg-light" onchange="window.updateTotalDisplay()" required>${options}</select>
            </div>
            <div class="col-3">
                <input type="number" name="hewan_items[][jumlah]" class="form-control input-qty border-0 bg-light text-center" value="${selectedQty}" min="1" oninput="window.updateTotalDisplay()" required placeholder="Qty">
            </div>
            <div class="col-2 text-end">
                <button type="button" class="btn btn-sm btn-outline-danger border-0" onclick="window.removeRow(this)"><i class="bi bi-trash-fill"></i></button>
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
            Swal.fire('Info', 'Minimal 1 item.', 'info');
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

        // Update Estimasi Text
        const displayEl = document.getElementById('displayTotalTarget');
        if (displayEl) displayEl.innerText = "Estimasi Sistem: " + fmtMoney(total);

        // Jika user belum manual input, atau manual input kosong, isi otomatis
        // Tapi logic donasi biasanya input manual lebih prioritas jika deal beda.
        // Disini kita buat: Jika user mengetik manual, pakai manual. Jika tidak, pakai sistem.
        // Namun sederhananya, kita update Display Visual & Hidden Value jika kosong/reset
        if (!displayTotalHarga.value) {
            realTotalHarga.value = total;
            displayTotalHarga.value = new Intl.NumberFormat('id-ID').format(total);
        }

        // Hitung estimasi bulanan berdasarkan realTotalHarga (yg mungkin diedit user)
        const durationInput = document.getElementById('duration_months');
        const estEl = document.getElementById('estBulan');
        if (durationInput && estEl) {
            const dur = durationInput.value;
            const deal = realTotalHarga.value || total; // Prioritas input manual
            if (dur > 0) {
                const perBulan = Math.ceil(deal / dur / 100) * 100;
                estEl.innerText = fmtMoney(perBulan);
            } else { estEl.innerText = '-'; }
        }
    };

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
            // Pastikan Real Value terisi dari Display Value sebelum submit
            if (!realTotalHarga.value && displayTotalHarga.value) {
                realTotalHarga.value = displayTotalHarga.value.replace(/[^0-9]/g, '');
            }

            const id = document.getElementById('id_tabungan_hewan_qurban').value;
            const formData = new FormData(this);
            // Re-map hewan items clean logic
            formData.delete('hewan_items[][id_hewan]');
            formData.delete('hewan_items[][jumlah]');
            document.querySelectorAll('.hewan-row').forEach((row, index) => {
                formData.append(`hewan_items[${index}][id_hewan]`, row.querySelector('.select-hewan').value);
                formData.append(`hewan_items[${index}][jumlah]`, row.querySelector('.input-qty').value);
            });

            let url = id ? `/pengurus/tabungan-qurban/${id}` : "/pengurus/tabungan-qurban";
            if (id) formData.append('_method', 'PUT');

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
                    Swal.fire('Gagal', res.message || 'Cek kembali data', 'error');
                }
            });
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

                // Set Nilai Rupiah
                realTotalHarga.value = data.total_harga_hewan_qurban;
                displayTotalHarga.value = new Intl.NumberFormat('id-ID').format(data.total_harga_hewan_qurban);

                toggleDuration();
                document.getElementById('modalTabunganTitle').innerText = 'Edit Tabungan';
                hewanContainer.innerHTML = '';
                if (data.details && data.details.length > 0) {
                    data.details.forEach(d => addHewanRow(d.id_hewan_qurban, d.jumlah_hewan));
                } else { addHewanRow(); }
                
                updateTotalDisplay();
                modalTabungan.show();
            });
    };

    window.approveTabungan = function(id, status) {
        Swal.fire({
            title: `Konfirmasi ${status}?`, icon: 'question', showCancelButton: true, confirmButtonText: 'Ya', confirmButtonColor: '#198754'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/pengurus/tabungan-qurban/${id}/status`, {
                    method: 'PUT', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
                    body: JSON.stringify({ status: status })
                }).then(res => res.json()).then(res => {
                    Swal.fire('Sukses', res.message, 'success');
                    loadTable(state.currentPage);
                });
            }
        });
    };

    window.deleteTabungan = function(id) {
        Swal.fire({ title: 'Hapus Data?', text: "Permanen!", icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33' })
            .then((result) => {
                if (result.isConfirmed) {
                    fetch(`/pengurus/tabungan-qurban/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' } })
                    .then(res => res.json()).then(res => {
                        if (res.success) { Swal.fire('Terhapus', res.message, 'success'); loadTable(state.currentPage); }
                    });
                }
            });
    };

    // ==========================================
    // 5. DETAIL & SETORAN
    // ==========================================
    window.openDetail = function(id) {
        currentDetailData = null;
        document.getElementById('setoran_id_tabungan').value = id;
        refreshDetailModal(id);
        if (modalDetail) modalDetail.show();
    };

    function refreshDetailModal(id) {
        fetch(`/pengurus/tabungan-qurban/${id}`)
            .then(res => res.json())
            .then(data => {
                currentDetailData = data;
                document.getElementById('detailSavingType').innerText = data.saving_type;
                document.getElementById('detailStatusBadge').innerText = data.status.toUpperCase();
                document.getElementById('detailStatusBadge').className = `badge rounded-pill ${data.status==='disetujui'?'bg-success':(data.status==='menunggu'?'bg-warning text-dark':'bg-secondary')}`;

                const terkumpul = data.pemasukan_tabungan_qurban.reduce((a, b) => (b.status === 'success' ? a + parseInt(b.nominal) : a), 0);
                const sisa = data.total_harga_hewan_qurban - terkumpul;

                document.getElementById('detailTotalHarga').innerText = fmtMoney(data.total_harga_hewan_qurban);
                document.getElementById('detailTerkumpul').innerText = fmtMoney(terkumpul);
                document.getElementById('detailSisa').innerText = fmtMoney(sisa);

                const listUl = document.getElementById('detailListHewan');
                listUl.innerHTML = '';
                data.details.forEach(d => {
                    listUl.innerHTML += `<li class="list-group-item d-flex justify-content-between align-items-center border-0 bg-light mb-1 rounded">
                        <span>${d.hewan.nama_hewan} (${d.hewan.kategori_hewan})</span>
                        <span class="badge bg-success rounded-pill">${d.jumlah_hewan} Ekor</span>
                    </li>`;
                });

                const tbody = document.getElementById('tabelRiwayatSetoran');
                tbody.innerHTML = '';
                if (data.pemasukan_tabungan_qurban.length > 0) {
                    data.pemasukan_tabungan_qurban.forEach(p => {
                        let statusBadge = p.status === 'success' ? '<span class="badge bg-success rounded-pill">Berhasil</span>' : '<span class="badge bg-warning rounded-pill">Pending</span>';
                        let metode = p.metode_pembayaran ? p.metode_pembayaran.toUpperCase() : 'MANUAL';
                        tbody.innerHTML += `<tr>
                            <td>${formatDate(p.tanggal)}</td>
                            <td class="text-center"><span class="small fw-bold text-secondary">${metode}</span></td>
                            <td class="text-center">${statusBadge}</td>
                            <td class="text-end fw-bold text-success">+ ${fmtMoney(p.nominal)}</td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-danger border-0" onclick="deleteSetoran('${p.id_pemasukan_tabungan_qurban}')"><i class="bi bi-trash-fill"></i></button>
                            </td>
                        </tr>`;
                    });
                } else {
                    tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">Belum ada setoran.</td></tr>';
                }
            });
    }

    window.openModalSetor = function() {
        document.getElementById('formTambahSetoran').reset();
        displayNominalSetor.value = '';
        realNominalSetor.value = '';
        
        // Auto-Fill Cicilan
        if(currentDetailData && currentDetailData.saving_type === 'cicilan' && currentDetailData.duration_months > 0) {
            const target = currentDetailData.total_harga_hewan_qurban;
            const durasi = currentDetailData.duration_months;
            const cicilan = Math.ceil(target / durasi / 100) * 100;
            
            realNominalSetor.value = cicilan;
            displayNominalSetor.value = new Intl.NumberFormat('id-ID').format(cicilan);
        }
        modalSetor.show();
    };

    if (formSetoran) {
        formSetoran.addEventListener('submit', function(e) {
            e.preventDefault();
            if (!realNominalSetor.value && displayNominalSetor.value) {
                realNominalSetor.value = displayNominalSetor.value.replace(/[^0-9]/g, '');
            }

            const formData = new FormData(this);
            fetch("/pengurus/pemasukan-qurban", {
                method: 'POST', headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' }, body: formData
            }).then(res => res.json()).then(res => {
                if (res.success) {
                    Swal.fire({ icon: 'success', title: 'Berhasil', timer: 1500, showConfirmButton: false });
                    modalSetor.hide();
                    refreshDetailModal(document.getElementById('setoran_id_tabungan').value);
                    loadTable(state.currentPage);
                } else { Swal.fire('Gagal', 'Error simpan', 'error'); }
            });
        });
    }

    window.deleteSetoran = function(id) {
        Swal.fire({ title: 'Hapus Setoran?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33' }).then((result) => {
            if (result.isConfirmed) {
                const idTabungan = document.getElementById('setoran_id_tabungan').value;
                fetch(`/pengurus/pemasukan-qurban/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': token } })
                .then(res => res.json()).then(res => {
                    if (res.success) { refreshDetailModal(idTabungan); loadTable(state.currentPage); }
                });
            }
        });
    };

    // ==========================================
    // 6. HARGA & KONTAK (Standard Logic)
    // ==========================================
    window.loadListHarga = function() {
        const tbody = document.getElementById('listHargaBody');
        tbody.innerHTML = '<tr><td colspan="4" class="text-center"><div class="spinner-border spinner-border-sm"></div></td></tr>';
        fetch("/pengurus/hewan-qurban").then(res => res.json()).then(data => {
            tbody.innerHTML = '';
            data.forEach(h => {
                const dataJson = encodeURIComponent(JSON.stringify(h));
                tbody.innerHTML += `<tr>
                    <td class="text-capitalize fw-bold">${h.nama_hewan}</td>
                    <td class="text-capitalize"><span class="badge bg-light text-dark border">${h.kategori_hewan}</span></td>
                    <td>${fmtMoney(h.harga_hewan)}</td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-link text-warning p-0 me-2" onclick="editHarga('${dataJson}')"><i class="bi bi-pencil-square fs-5"></i></button>
                        <button class="btn btn-sm btn-link text-danger p-0" onclick="deleteHarga('${h.id_hewan_qurban}')"><i class="bi bi-trash-fill fs-5"></i></button>
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
        
        // --- PERBAIKAN DI SINI (Set Visual & Real) ---
        realHarga.value = data.harga_hewan;
        displayHarga.value = new Intl.NumberFormat('id-ID').format(data.harga_hewan);
        // ---------------------------------------------

        document.getElementById('btnSimpanHarga').innerText = 'Update';
    };

    // UPDATE FUNGSI SUBMIT HARGA
    if(formHarga) {
        formHarga.addEventListener('submit', function(e) {
            e.preventDefault();

            // Pastikan input hidden terisi jika user copas angka
            if (!realHarga.value && displayHarga.value) {
                realHarga.value = displayHarga.value.replace(/[^0-9]/g, '');
            }

            const formData = new FormData(this);
            const id = document.getElementById('input_id_hewan_qurban').value;
            let url = id ? `/pengurus/hewan-qurban/${id}` : "/pengurus/hewan-qurban";
            if(id) formData.append('_method', 'PUT');

            fetch(url, { method: 'POST', headers: { 'X-CSRF-TOKEN': token }, body: formData })
            .then(res => res.json()).then(res => {
                document.getElementById('alertHargaContainer').innerHTML = `<div class="alert alert-success py-1 small">Berhasil disimpan</div>`;
                
                // Reset Form & Input Visual
                formHarga.reset(); 
                document.getElementById('input_id_hewan_qurban').value = '';
                displayHarga.value = ''; // Reset visual manual
                realHarga.value = '';    // Reset real manual

                document.getElementById('btnSimpanHarga').innerText = 'Simpan';
                loadListHarga();
                setTimeout(() => document.getElementById('alertHargaContainer').innerHTML = '', 2000);
            });
        });
    }

    window.deleteHarga = function(id) {
        if(confirm('Hapus harga ini?')) {
            fetch(`/pengurus/hewan-qurban/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': token } })
            .then(() => loadListHarga());
        }
    };

    window.openContactModal = function(jsonString) {
        const jamaah = JSON.parse(decodeURIComponent(jsonString));
        document.getElementById('contactName').innerText = jamaah.name;
        document.getElementById('contactEmail').innerText = jamaah.email || '-';
        document.getElementById('contactPhone').innerText = jamaah.no_hp || '-';
        const avatarEl = document.getElementById('contactAvatar');
        avatarEl.src = jamaah.avatar ? `/storage/${jamaah.avatar}` : `https://ui-avatars.com/api/?name=${encodeURIComponent(jamaah.name)}&background=198754&color=fff`;
        
        const btnWa = document.getElementById('btnChatWA');
        if (jamaah.no_hp) {
            let phone = jamaah.no_hp.replace(/\D/g, '');
            if (phone.startsWith('0')) phone = '62' + phone.substring(1);
            btnWa.href = `https://wa.me/${phone}`;
            btnWa.classList.remove('disabled');
        } else { btnWa.classList.add('disabled'); }
        modalContact.show();
    };

    window.togglePdfFilter = function() {
        const val = document.getElementById('filter-periode').value;
        document.getElementById('filter-bulanan').style.display = (val === 'per_bulan') ? 'block' : 'none';
        document.getElementById('filter-tahunan').style.display = (val === 'per_tahun') ? 'block' : 'none';
        document.getElementById('filter-rentang').style.display = (val === 'rentang_waktu') ? 'block' : 'none';
    };

    loadTable();
});