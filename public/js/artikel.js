const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

document.addEventListener("DOMContentLoaded", function () {

    const statusFilter = document.getElementById("statusFilter");
    const searchInput = document.getElementById("searchInput");
    const tableBody = document.querySelector("#tabelartikel tbody");
    const paginationLinks = document.getElementById("paginationLinks");
    const paginationInfo = document.getElementById("paginationInfo");

    loadArtikel();

    function loadArtikel(page = 1) {
        const status = statusFilter ? statusFilter.value : 'all';
        const search = searchInput ? searchInput.value : '';

        fetch(`/pengurus/artikel-data?page=${page}&status=${status}&search=${search}`)
            .then(res => res.json())
            .then(res => renderData(res))
            .catch(err => console.error(err));
    }

    function lihatDetailArtikel(id) {
        fetch(`/pengurus/artikel-data/${id}`)
            .then(res => res.json())
            .then(data => {
                document.getElementById("detailJudulArtikel").innerText = data.judul_artikel;
                document.getElementById("d_judul").innerText = data.judul_artikel;
                document.getElementById("d_penulis").innerText = data.penulis_artikel;
                document.getElementById("d_status_artikel").innerText = data.status_artikel;
                document.getElementById("d_tanggal_terbit").innerText =
                    new Date(data.tanggal_terbit_artikel).toLocaleDateString('id-ID');
                document.getElementById("d_isi").innerHTML = data.isi_artikel;

                document.getElementById("detailFotoArtikel").src =
                    data.foto_artikel ? `/storage/${data.foto_artikel}` : `/images/default_artikel.png`;

                const modal = new bootstrap.Modal(document.getElementById('modalDetailArtikel'));
                modal.show();
            });
    }

    window.lihatDetailArtikel = lihatDetailArtikel; // <-- FIX PENTING
    window.hapusArtikel = async function(id_artikel) {
        const confirm = await Swal.fire({
            title: 'Yakin ingin menghapus?',
            text: 'Data artikel akan dihapus permanen!',
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

            const res = await fetch(`/pengurus/artikel/${id_artikel}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,   // Pastikan variable token sudah ada (sama seperti di program)
                    'Accept': 'application/json'
                },
                body: formData
            });

            const data = await res.json();

            if (res.ok) {
                Swal.fire('Terhapus!', data.message, 'success');
                loadArtikel();  // ⬅️ muat ulang data tabel
            } else {
                throw new Error(data.message || 'Terjadi kesalahan.');
            }

        } catch (err) {
            Swal.fire('Gagal', err.message, 'error');
        }
    };




    function renderData(res) {
        tableBody.innerHTML = "";
        let no = res.from;

        res.data.forEach(item => {
            tableBody.innerHTML += `
                <tr>
                    <td class="text-center">${no++}</td>
                    <td class="text-center">${item.judul_artikel}</td>
                    <td class="text-center">${item.penulis_artikel}</td>
                    <td class="text-center">${item.status_artikel}</td>
                    <td class="text-center">${item.tanggal_terbit_artikel ?? '-'}</td>
                    <td class="text-center">           
                           <button onclick="lihatDetailArtikel('${item.id_artikel}')" 
                                class="btn btn-info btn-sm me-1">
                            <i class="bi bi-eye"></i>
                        </button>

                        <a href="/pengurus/artikel/${item.id_artikel}/edit" 
                        class="btn btn-warning btn-sm me-1">
                            <i class="bi bi-pencil-square"></i>
                        </a>

                        <button onclick="hapusArtikel('${item.id_artikel}')" 
                                class="btn btn-danger btn-sm">
                            <i class="bi bi-trash"></i>
                        </button>

                    </td>
                </tr>`;
        });

        paginationInfo.textContent = `Menampilkan ${res.data.length} dari ${res.total} data`;

        renderPagination(res);
    }




    function renderPagination(res) {
        let html = `<ul class="pagination">`;

        if (res.prev_page_url) {
            html += `<li class="page-item"><a class="page-link" data-page="${res.current_page - 1}">Sebelumnya</a></li>`;
        }

        if (res.next_page_url) {
            html += `<li class="page-item"><a class="page-link" data-page="${res.current_page + 1}">Berikutnya</a></li>`;
        }

        html += `</ul>`;
        paginationLinks.innerHTML = html;

        paginationLinks.querySelectorAll(".page-link").forEach(link => {
            link.addEventListener("click", function () {
                loadArtikel(this.getAttribute("data-page"));
            });
        });
    }

    statusFilter.addEventListener("change", () => loadArtikel());
    searchInput.addEventListener("keyup", () => loadArtikel());

});
