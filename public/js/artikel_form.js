// File: public/js/artikel_form.js

document.addEventListener('DOMContentLoaded', function () {
    
    // --- 1. Definisi Variabel DOM ---
    const editorContainer = document.getElementById('editor-container');
    const isiArtikelInput = document.getElementById('isiArtikelInput'); 
    const form = document.getElementById('artikelForm');
    
    // Variabel Foto
    const fotoInput = document.getElementById('foto_artikel');
    const fotoLabelSpan = document.querySelector('#foto_artikel_label span');
    const clearFileBtn = document.getElementById('clearFile');
    const previewContainer = document.getElementById('previewContainer');
    const fotoPreview = document.getElementById('fotoPreview');
    const hapusFotoInput = document.getElementById('hapus_foto_input'); // Input hidden baru
    
    // --- 2. Inisialisasi Quill ---
    if (editorContainer && isiArtikelInput && form) {
        // ... (kode inisialisasi Quill sama seperti sebelumnya, tidak perlu diubah) ...
        var quill = new Quill(editorContainer, {
            theme: 'snow',
            placeholder: 'Tulis konten artikel di sini...',
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline', 'strike'], 
                    [{ 'header': [1, 2, 3, 4, false] }],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    [{ 'align': [] }],
                    ['link', 'image', 'code-block'],
                    ['clean']
                ]
            }
        });

        // Logika Submit untuk validasi isi quill
        form.addEventListener('submit', function (e) {
            const htmlContent = editorContainer.querySelector('.ql-editor').innerHTML;
            // Cek apakah hanya berisi tag kosong atau spasi
            const isReallyEmpty = htmlContent.replace(/<(.|\n)*?>/g, '').trim().length === 0 && !htmlContent.includes('<img');

            if (isReallyEmpty) {
                 alert('Isi artikel tidak boleh kosong.');
                 e.preventDefault(); 
                 return;
            }
            isiArtikelInput.value = htmlContent;
        });
    }

    // --- 3. Logika Preview Foto & Tombol X (DIPERBARUI) ---
    if (fotoInput && fotoPreview && clearFileBtn) {
        
        // Fungsi untuk update UI berdasarkan state
        function updateFotoUI(filename, imgSrc, showPreview, showClearBtn) {
            fotoLabelSpan.textContent = filename;
            fotoLabelSpan.className = filename === 'Pilih foto...' ? 'text-muted' : 'text-dark fw-bold';
            
            if (imgSrc) fotoPreview.src = imgSrc;
            
            showPreview ? previewContainer.classList.remove('d-none') : previewContainer.classList.add('d-none');
            showClearBtn ? clearFileBtn.classList.remove('d-none') : clearFileBtn.classList.add('d-none');
        }

        // Cek kondisi awal (Mode Edit dengan gambar existing)
        const hasExistingImage = previewContainer.dataset.hasImage === 'true';
        const originalSrc = previewContainer.dataset.originalSrc;

        if (hasExistingImage) {
            updateFotoUI("Foto tersimpan (Klik X untuk hapus)", originalSrc, true, true);
        }

        // Event saat file dipilih dari komputer
        fotoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Reset flag hapus foto jika user memilih file baru
                hapusFotoInput.value = "0"; 

                const reader = new FileReader();
                reader.onload = function(event) {
                    updateFotoUI(file.name, event.target.result, true, true);
                }
                reader.readAsDataURL(file);
            }
        });

        // Event saat tombol X diklik
        clearFileBtn.addEventListener('click', function(e) {
            e.preventDefault(); 
            e.stopPropagation(); 
            
            // 1. Kosongkan input file
            fotoInput.value = ""; 
            
            // 2. Cek apakah kita sedang menghapus gambar existing di mode edit
            if (hasExistingImage) {
                // Set flag agar controller tahu untuk menghapus gambar di DB
                hapusFotoInput.value = "1";
                updateFotoUI("Pilih foto...", "", false, false); // Sembunyikan preview
                // Opsional: Beri feedback visual
                 fotoLabelSpan.textContent = "Foto akan dihapus saat disimpan.";
                 fotoLabelSpan.classList.add('text-danger');
            } else {
                // Hanya mereset upload file baru yg belum disimpan
                hapusFotoInput.value = "0";
                updateFotoUI("Pilih foto...", "", false, false);
            }
        });
    }
});