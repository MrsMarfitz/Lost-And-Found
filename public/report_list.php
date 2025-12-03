<?php
require_once '../includes/crud_barang.php';
require __DIR__ . '/../config/config.php';
require __DIR__ . '/../config/db_connect.php';
$data_laporan = tampilkanSemuaBarang();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Laporan</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
      .report-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 20px; padding: 20px 0; }
      .report-card-item { width: 100%; height: 260px; border-radius: 10px; overflow: hidden; position: relative; cursor: pointer; box-shadow: 0 4px 10px rgba(0,0,0,0.1); transition: transform 0.2s ease-in-out; background: #fff; border: 1px solid #eee; }
      .report-card-item:hover { transform: translateY(-5px); }
      .report-card-item img { width: 100%; height: 160px; object-fit: cover; }
      .report-card-content { padding: 10px; }
      .report-card-overlay { position: absolute; top: 10px; right: 10px; }
      .status-tag { padding: 4px 8px; border-radius: 5px; font-size: 0.75em; color: white; font-weight: bold; text-transform: uppercase; }
      .status-tag.lost { background: #e74c3c; } 
      .status-tag.found { background: #27ae60; } 

      /* MODAL STYLES */
      .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.8); }
      .modal-content { background-color: #fefefe; margin: 2% auto; border-radius: 10px; width: 90%; max-width: 700px; position: relative; animation: slideIn 0.3s; padding-bottom: 20px; }
      .modal-header { position: relative; width: 100%; background: #000; text-align: center; }
      
      /* Gambar Utama Besar */
      .modal-main-img { width: 100%; height: 300px; object-fit: contain; background: #222; }
      
      /* Galeri Kecil di Bawah */
      .modal-gallery { display: flex; gap: 10px; padding: 10px; overflow-x: auto; background: #f9f9f9; border-bottom: 1px solid #eee; justify-content: center; }
      .gallery-thumb { width: 60px; height: 60px; object-fit: cover; border-radius: 5px; cursor: pointer; border: 2px solid transparent; opacity: 0.6; transition: 0.2s; }
      .gallery-thumb:hover, .gallery-thumb.active { border-color: #3498db; opacity: 1; }

      .close-btn { position: absolute; top: 10px; right: 10px; color: white; font-size: 30px; cursor: pointer; background: rgba(0,0,0,0.5); width: 40px; height: 40px; text-align: center; border-radius: 50%; line-height: 40px; z-index: 10; }
      .modal-body { padding: 20px; }
      .modal-actions { margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee; display: flex; gap: 10px; justify-content: flex-end; }
      .btn { padding: 10px 20px; border-radius: 5px; text-decoration: none; color: white; font-weight: bold; font-size: 14px; }
      .btn-edit { background: #f39c12; }
      .btn-delete { background: #c0392b; }
      @keyframes slideIn { from {transform: translateY(-50px); opacity: 0;} to {transform: translateY(0); opacity: 1;} }
    </style>
</head>
<body>

    <div class="app">
        <nav class="sidebar">
            <div class="s-top"><img src="assets/img/logo.png" class="s-logo"><h3>LostFound</h3></div>
            <ul class="menu">
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="report_create.php">Buat Laporan</a></li>
                <li class="active"><a href="report_list.php">Daftar Laporan</a></li>
            </ul>
        </nav>
        
        <main class="main">
            <header class="main-head">
                <h2>Daftar Laporan</h2>
                <div class="head-actions">
                    <a href="report_create.php" class="btn-primary small">+ Lapor Baru</a>
                </div>
            </header>
            
            <div class="card">
                <?php if (empty($data_laporan)) : ?>
                    <p style="text-align:center; padding: 20px;">Belum ada laporan masuk.</p>
                <?php else : ?>
                    <div class="report-grid">
                        <?php foreach ($data_laporan as $row) : ?>
                            <?php 
                                $statusClass = ($row['type'] == 'lost') ? 'lost' : 'found';
                                // Ambil Thumbnail (Foto Pertama)
                                $thumb = !empty($row['thumbnail']) ? "uploads/" . $row['thumbnail'] : "assets/img/no-image.jpg";
                                // Siapkan JSON semua foto untuk dikirim ke JS
                                $allPhotosJson = htmlspecialchars(json_encode($row['photos_array']));
                            ?>
                            <div class="report-card-item" onclick='openModal(this, <?= $allPhotosJson; ?>)'
                                 data-id="<?= $row['report_id']; ?>"
                                 data-title="<?= htmlspecialchars($row['title']); ?>"
                                 data-desc="<?= htmlspecialchars($row['description']); ?>"
                                 data-loc="<?= htmlspecialchars($row['location_text']); ?>"
                                 data-date="<?= $row['date_event']; ?>"
                                 data-type="<?= $row['type']; ?>">
                                
                                <img src="<?= $thumb; ?>" alt="Thumbnail">
                                <div class="report-card-overlay">
                                    <span class="status-tag <?= $statusClass; ?>"><?= strtoupper($row['type']); ?></span>
                                </div>
                                <div class="report-card-content">
                                    <h4 style="margin:0; font-size:16px;"><?= htmlspecialchars($row['title']); ?></h4>
                                    <p style="color:#666; font-size:13px; margin-top:5px;"><?= $row['location_text']; ?></p>
                                    <small style="color:#999;"><?= $row['date_event']; ?></small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <div id="detailModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <span class="close-btn" onclick="closeModal()">&times;</span>
                <img id="mMainImg" class="modal-main-img" src="" alt="Detail">
            </div>
            
            <div id="mGallery" class="modal-gallery" style="display:none;"></div>

            <div class="modal-body">
                <h2 id="mTitle" style="margin-top:0;">Judul</h2>
                <span id="mType" class="status-tag" style="background:#333;">TYPE</span>
                <p style="margin-top:15px;"><strong>Lokasi:</strong> <span id="mLoc">-</span></p>
                <p><strong>Tanggal:</strong> <span id="mDate">-</span></p>
                <hr>
                <p id="mDesc" style="line-height:1.6;">Deskripsi...</p>
                
                <div class="modal-actions">
                    <a id="btnEdit" href="#" class="btn btn-edit">Edit</a>
                    <a id="btnDelete" href="#" class="btn btn-delete" onclick="return confirm('Hapus permanen?')">Hapus</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openModal(el, photos) {
            // 1. Isi Data Teks
            document.getElementById('mTitle').innerText = el.getAttribute('data-title');
            document.getElementById('mDesc').innerText = el.getAttribute('data-desc');
            document.getElementById('mLoc').innerText = el.getAttribute('data-loc');
            document.getElementById('mDate').innerText = el.getAttribute('data-date');
            
            var type = el.getAttribute('data-type');
            var badge = document.getElementById('mType');
            badge.innerText = type.toUpperCase();
            badge.className = "status-tag " + (type == 'lost' ? 'lost' : 'found');

            var id = el.getAttribute('data-id');
            document.getElementById('btnEdit').href = "report_edit.php?id=" + id;
            document.getElementById('btnDelete').href = "report_delete.php?id=" + id;

            // 2. Logic Foto Galeri
            var galleryContainer = document.getElementById('mGallery');
            var mainImg = document.getElementById('mMainImg');
            galleryContainer.innerHTML = ""; // Bersihkan galeri lama

            if (photos && photos.length > 0 && photos[0] !== "") {
                // Set gambar utama pakai foto pertama
                mainImg.src = "uploads/" + photos[0];
                
                // Jika foto lebih dari 1, tampilkan galeri kecil
                if (photos.length > 1) {
                    galleryContainer.style.display = "flex";
                    photos.forEach(function(photoName) {
                        var img = document.createElement("img");
                        img.src = "uploads/" + photoName;
                        img.className = "gallery-thumb";
                        // Klik thumb -> Ganti gambar utama
                        img.onclick = function() {
                            mainImg.src = this.src;
                            // Reset active class
                            document.querySelectorAll('.gallery-thumb').forEach(t => t.classList.remove('active'));
                            this.classList.add('active');
                        };
                        galleryContainer.appendChild(img);
                    });
                } else {
                    galleryContainer.style.display = "none";
                }
            } else {
                // Kalau gak ada foto sama sekali
                mainImg.src = "assets/img/no-image.jpg";
                galleryContainer.style.display = "none";
            }

            document.getElementById('detailModal').style.display = "block";
        }

        function closeModal() {
            document.getElementById('detailModal').style.display = "none";
        }
        window.onclick = function(e) {
            if(e.target == document.getElementById('detailModal')) closeModal();
        }
    </script>
</body>
</html>