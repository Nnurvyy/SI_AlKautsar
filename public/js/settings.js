document.addEventListener('DOMContentLoaded', () => {

    
    const form = document.getElementById('formSettings');
    const token = document.querySelector('meta[name="csrf-token"]').content;
    
    const submitButton = form ? form.querySelector('button[type="submit"]') : null;
    const originalButtonText = submitButton ? submitButton.innerHTML : 'Simpan Pengaturan';

    
    const fotoInput = document.getElementById('foto_masjid');
    const fotoLabel = document.getElementById('foto_masjid_label');
    const fotoLabelSpan = fotoLabel ? fotoLabel.querySelector('span') : null;
    const clearFileBtn = document.getElementById('clearFotoMasjid');
    
    const preview = document.getElementById('previewFotoMasjid');
    const previewContainer = document.getElementById('previewFotoMasjidContainer');

    
    if (form) {
        form.addEventListener('submit', async e => {
            e.preventDefault();
            setLoading(true);

            const formData = new FormData(form);
            const url = form.getAttribute('action');

            try {
                const res = await fetch(url, {
                    method: 'POST', 
                    headers: { 
                        'X-CSRF-TOKEN': token, 
                        'Accept': 'application/json' 
                    },
                    body: formData
                });

                const data = await res.json();
                
                if (res.ok) {
                    Swal.fire('Berhasil!', data.message, 'success');
                    
                    if (data.foto_url) {
                         
                         preview.src = data.foto_url;
                         previewContainer.classList.remove('d-none');
                         clearFileBtn.classList.remove('d-none');
                         const fileName = data.foto_url.split('/').pop();
                         fotoLabelSpan.textContent = fileName;
                         fotoLabelSpan.classList.remove('text-muted');
                    } else {
                        
                        clearFileVisuals(); 
                    }
                    
                    
                    const deleteInput = document.getElementById('hapus_foto_masjid');
                    if (deleteInput) {
                        deleteInput.remove();
                    }
                    
                } else {
                    if (res.status === 422 && data.errors) {
                        let errorMessages = Object.values(data.errors).map(err => err[0]).join('<br>');
                        throw new Error(errorMessages);
                    }
                    throw new Error(data.message || 'Terjadi kesalahan');
                }
            } catch (err) {
                Swal.fire('Gagal', err.message, 'error');
            } finally {
                setLoading(false);
            }
        });
    }

    
    function setLoading(isLoading) {
        if (!submitButton) return;
        if (isLoading) {
            submitButton.disabled = true;
            submitButton.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...`;
        } else {
            submitButton.disabled = false;
            submitButton.innerHTML = originalButtonText;
        }
    }
    
    
    
    
    function clearFileVisuals() {
        fotoLabelSpan.textContent = "Choose file...";
        fotoLabelSpan.classList.add('text-muted');
        clearFileBtn.classList.add('d-none');
        preview.src = "";
        previewContainer.classList.add('d-none');
    }

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
                    preview.style.width = '200px';
                    preview.style.height = '200px';
                    preview.style.objectFit = 'cover';
                    previewContainer.classList.remove('d-none');
                }
                reader.readAsDataURL(file);
                
                
                const deleteInput = document.getElementById('hapus_foto_masjid');
                if (deleteInput) {
                    deleteInput.remove();
                }
                
            } else {
                
            }
        });
    }
    
    if (clearFileBtn && fotoInput) {
        clearFileBtn.addEventListener('click', function(e) {
            e.stopPropagation(); 
            e.preventDefault();
            
            fotoInput.value = ""; 
            clearFileVisuals(); 
            
            
            let deleteInput = document.getElementById('hapus_foto_masjid');
            if (!deleteInput) {
                deleteInput = document.createElement('input');
                deleteInput.type = 'hidden';
                deleteInput.name = 'hapus_foto_masjid';
                deleteInput.id = 'hapus_foto_masjid';
                form.appendChild(deleteInput);
            }
            deleteInput.value = '1'; 
        });
    }
});