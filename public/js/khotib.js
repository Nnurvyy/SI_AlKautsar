document.addEventListener('DOMContentLoaded', () => {

    // --- Definisi Elemen ---
    const form = document.getElementById('formKhotib');
    const modalKhotib = document.getElementById('modalKhotib');
    const searchInput = document.getElementById('searchInput');
    const tbody = document.querySelector('#tabelKhotib tbody');
    const token = document.querySelector('meta[name="csrf-token"]').content;

    // BARU: Ambil tombol simpan & batal, dan simpan teks aslinya
    const submitButton = form ? form.querySelector('button[type="submit"]') : null;
    const cancelButton = form ? form.querySelector('button[data-bs-dismiss="modal"]') : null;
    const originalButtonText = submitButton ? submitButton.innerHTML : 'Simpan';

    // Elemen untuk input file kustom
    const fotoInput = document.getElementById('foto_khotib');
    const fotoLabel = document.getElementById('foto_khotib_label');
    const fotoLabelSpan = fotoLabel ? fotoLabel.querySelector('span') : null;
    const clearFileBtn = document.getElementById('clearFile');
    
    // Elemen untuk preview
    const preview = document.getElementById('previewFoto');
    const previewContainer = document.getElementById('previewContainer');

    // --- Event Listener Utama ---

    // 1. Submit form (tambah / edit)
    if (form) {
        form.addEventListener('submit', async e => {
            e.preventDefault();
            
            // BARU: Mulai animasi loading
            if (submitButton && cancelButton) {
                submitButton.disabled = true;
                cancelButton.disabled = true; // Nonaktifkan tombol batal juga
                submitButton.innerHTML = `
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    Menyimpan...
                `;
            }

            const id = document.getElementById('id_khutbah').value;
            const formData = new FormData(form);
            
            const url = id ? `/khotib-jumat/${id}` : '/khotib-jumat';
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
                    bootstrap.Modal.getInstance(modalKhotib).hide();
                    loadKhotib();
                } else {
                    if (res.status === 422 && data.errors) {
                        let errorMessages = Object.values(data.errors).map(err => err[0]).join('<br>');
                        throw new Error(errorMessages);
                    }
                    throw new Error(data.message || 'Terjadi kesalahan');
                }
            } catch (err) {
                // BARU: Kembalikan tombol jika gagal
                if (submitButton && cancelButton) {
                    submitButton.disabled = false;
                    cancelButton.disabled = false;
                    submitButton.innerHTML = originalButtonText;
                }
                Swal.fire('Gagal', err.message, 'error');
            }
        });
    }

    // 2. Script untuk Search Bar
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const filter = searchInput.value.toLowerCase();
            const rows = tbody.getElementsByTagName('tr');
            
            for (let i = 0; i < rows.length; i++) {
                if (rows[i].getElementsByTagName('td').length <= 1) continue;

                const namaKhotib = rows[i].cells[2].innerText.toLowerCase();
                const namaImam = rows[i].cells[3].innerText.toLowerCase();
                const tema = rows[i].cells[4].innerText.toLowerCase();

                if (namaKhotib.indexOf(filter) > -1 || namaImam.indexOf(filter) > -1 || tema.indexOf(filter) > -1) {
                    rows[i].style.display = "";
                } else {
                    rows[i].style.display = "none";
                }
            }
        });
    }

    // 3. Script untuk Preview Foto & Update Label
    if (fotoInput && fotoLabelSpan && clearFileBtn && previewContainer && preview) {
        fotoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];

            if (file) {
                fotoLabelSpan.textContent = file.name;
                fotoLabelSpan.classList.remove('text-muted');
                clearFileBtn.classList.remove('d-none');

                const reader = new FileReader();
                reader.onload = function(event) {
                    preview.src = event.target.result;
                    previewContainer.classList.remove('d-none');
                }
                reader.readAsDataURL(file);
            } else {
                fotoLabelSpan.textContent = "Choose file...";
                fotoLabelSpan.classList.add('text-muted');
                clearFileBtn.classList.add('d-none');
                preview.src = "";
                previewContainer.classList.add('d-none');
            }
        });
    }

    // 4. Script untuk Tombol 'x' di field
    if (clearFileBtn && fotoInput) {
        clearFileBtn.addEventListener('click', function(e) {
            e.stopPropagation(); 
            fotoInput.value = ""; 
            fotoInput.dispatchEvent(new Event('change'));
        });
    }

    // 5. Reset Modal saat ditutup
    if (modalKhotib && form && fotoInput) {
        modalKhotib.addEventListener('hidden.bs.modal', function () {
            form.reset();
            fotoInput.dispatchEvent(new Event('change'));
            document.getElementById('id_khutbah').value = '';

            // BARU: Pastikan tombol simpan kembali normal saat modal ditutup
            if (submitButton && cancelButton) {
                submitButton.disabled = false;
                cancelButton.disabled = false;
                submitButton.innerHTML = originalButtonText;
            }
        });
    }

    // --- Fungsi ---

    // Ambil data dari server
    async function loadKhotib() {
        if (!tbody) return;
        tbody.innerHTML = `<tr><td colspan="7" class="text-center"><div class="spinner-border text-primary"></div></td></tr>`;

        try {
            const res = await fetch('/khotib-jumat-data');
            if (!res.ok) throw new Error('Gagal memuat data');
            const data = await res.json();

            tbody.innerHTML = '';
            if (data.length === 0) {
                tbody.innerHTML = `<tr><td colspan="7" class="text-center">Belum ada data.</td></tr>`;
                return;
            }

            data.forEach((item, i) => {
                const row = `
                <tr>
                    <td class="text-center">${i + 1}</td>
                    <td class="text-center"><img src="${item.foto_url}" class="rounded" style="width:60px;height:60px;object-fit:cover;" alt="Foto ${item.nama_khotib}"></td>
                    <td>${item.nama_khotib}</td>
                    <td>${item.nama_imam}</td>
                    <td>${item.tema_khutbah}</td> 
                    <td class="text-center">${new Date(item.tanggal).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })}</td>
                    <td class="text-center">
                        <button class="btn btn-warning btn-sm" onclick="editKhotib('${item.id_khutbah}')">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="hapusKhotib('${item.id_khutbah}')">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>`;
                tbody.insertAdjacentHTML('beforeend', row);
            });
        } catch (err) {
            tbody.innerHTML = `<tr><td colspan="7" class="text-center text-danger">${err.message}</td></tr>`;
        }
    }

    // Edit data (dibuat global agar bisa di-panggil dari onclick)
    window.editKhotib = async function(id_khutbah) {
        try {
            const res = await fetch(`/khotib-jumat/${id_khutbah}`);
            if (!res.ok) throw new Error('Data tidak ditemukan');
            const data = await res.json();

            document.getElementById('id_khutbah').value = data.id_khutbah;
            document.getElementById('nama_khotib').value = data.nama_khotib;
            document.getElementById('nama_imam').value = data.nama_imam;
            document.getElementById('tema_khutbah').value = data.tema_khutbah;
            document.getElementById('tanggal').value = data.tanggal;

            if (data.foto_khotib) {
                fotoLabelSpan.textContent = data.foto_khotib.split('/').pop();
                fotoLabelSpan.classList.remove('text-muted');
                clearFileBtn.classList.remove('d-none');
                preview.src = data.foto_url;
                previewContainer.classList.remove('d-none');
            } else {
                fotoInput.dispatchEvent(new Event('change'));
            }

            new bootstrap.Modal(modalKhotib).show();
        } catch (err) {
            Swal.fire('Gagal', err.message, 'error');
        }
    }

    // Hapus data (dibuat global)
    window.hapusKhotib = async function(id_khutbah) {
        const confirm = await Swal.fire({
            title: 'Yakin ingin menghapus?',
            text: 'Data akan dihapus permanen!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal'
        });

        if (!confirm.isConfirmed) return;

        try {
            const formData = new FormData();
            formData.append('_method', 'DELETE');

            const res = await fetch(`/khotib-jumat/${id_khutbah}`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
                body: formData
            });

            const data = await res.json();
            if (res.ok) {
                Swal.fire('Terhapus!', data.message, 'success');
                loadKhotib();
            } else {
                throw new Error(data.message || 'Terjadi kesalahan');
            }
        } catch (err) {
            Swal.fire('Gagal', err.message, 'error');
        }
    }

    // --- Inisialisasi ---
    loadKhotib();
});

