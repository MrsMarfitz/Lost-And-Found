document.addEventListener("DOMContentLoaded", function() {
    
    // --- 1. FITUR PREVIEW FOTO ---
    const fileInput = document.getElementById('fileInput');
    const profilePreview = document.getElementById('profilePreview');
    const smallProfilePreview = document.querySelector('.user-img-small'); 

    // Saat user memilih file
    if(fileInput) {
        fileInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // Update tampilan foto besar dan kecil
                    if(profilePreview) profilePreview.src = e.target.result;
                    if(smallProfilePreview) smallProfilePreview.src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        });
    }

    // --- 2. FITUR SAVE DATA (AJAX) ---
    const form = document.getElementById('profileForm');
    const saveBtn = document.querySelector('.btn-save');

    if(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault(); 

            // Efek Loading pada tombol
            const originalBtnText = saveBtn.innerHTML;
            saveBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Saving...';
            saveBtn.disabled = true;

            const formData = new FormData(form);
            
            // Masukkan file foto ke paket pengiriman
            if(fileInput && fileInput.files[0]) {
                formData.append("photo", fileInput.files[0]);
            }

            // Kirim ke Java Servlet
            fetch('UpdateProfileServlet', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text()) 
            .then(data => {
                alert("Berhasil!\n" + data);
                saveBtn.innerHTML = originalBtnText;
                saveBtn.disabled = false;
            })
            .catch(error => {
                console.error('Error:', error);
                alert("Gagal menyimpan data.");
                saveBtn.innerHTML = originalBtnText;
                saveBtn.disabled = false;
            });
        });
    }
});