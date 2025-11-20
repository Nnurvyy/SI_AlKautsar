document.addEventListener('DOMContentLoaded', function () {
    
    // --- 1. Definisi Variabel DOM Utama ---
    const editorContainer = document.getElementById('editor-container');
    const isiArtikelInput = document.getElementById('isiArtikelInput'); 
    const form = document.getElementById('artikelForm');
    const fotoInput = document.getElementById('foto_artikel');
    const fotoPreview = document.getElementById('fotoPreview');
    
    let quill; // Deklarasi Quill
    
    // --- 2. INISIALISASI QUILL.JS ---
    if (editorContainer && isiArtikelInput && form) {
        
        // Inisialisasi Quill
        quill = new Quill(editorContainer, {
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
        
        // **PENTING:** Karena Anda mengisi konten Quill menggunakan Blade ( {!! $artikel->isi_artikel !!} )
        // Quill mungkin memerlukan sedikit waktu setelah inisialisasi untuk memproses HTML tersebut.
        // Jika Anda ingin memvalidasi isi Quill saat start (untuk edit), lakukan di sini.

        // --- 3. LOGIKA SUBMIT FORM (ADD/EDIT) ---
        form.addEventListener('submit', function (e) {
            
            // Dapatkan konten HTML yang dihasilkan oleh editor Quill
            const htmlContent = editorContainer.querySelector('.ql-editor').innerHTML;
            
            // Pengecekan Validasi Minimal: apakah konten kosong atau hanya tag kosong
            // Paling aman: cek teks yang terlihat (innerText)
            const plainTextContent = editorContainer.querySelector('.ql-editor').innerText.trim();

            if (plainTextContent.length === 0 || htmlContent === '<p><br></p>' || htmlContent.trim() === '') {
                 // Ganti dengan notifikasi yang sesuai (misalnya menggunakan Swal.fire)
                 alert('Isi artikel tidak boleh kosong.');
                 e.preventDefault(); // Mencegah form disubmit
                 return;
            }
            
            // Masukkan konten HTML ke input hidden (isiArtikelInput)
            // Nilai inilah yang akan dikirim ke Controller
            isiArtikelInput.value = htmlContent;

            // Karena Form ACTION sudah menentukan apakah ini POST (Store) atau PUT (Update)
            // dan sudah termasuk @csrf dan @method('PUT'), kita biarkan browser melanjutkan submit.
        });
    }

    // --- 4. PHOTO PREVIEW LOGIC ---
    if (fotoInput && fotoPreview) {
        fotoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    fotoPreview.src = event.target.result;
                }
                reader.readAsDataURL(file);
            }
        });
    }
});