<?php
require_once '../includes/crud_barang.php';
require __DIR__ . '/../config/config.php';
require __DIR__ . '/../config/db_connect.php';

if (!isset($_GET['id'])) { header("Location: report_list.php"); exit; }
$id = $_GET['id'];
$data = ambilSatuBarang($id);

if (isset($_POST['update'])) {
    if (ubahBarang($_POST, $_FILES)) {
        echo "<script>alert('Berhasil Update!'); window.location='report_list.php';</script>";
    } else {
        echo "<script>alert('Gagal Update!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Edit Laporan</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .form-group{margin-bottom:15px}
        .form-group label{font-weight:600;display:block}
        .form-input{width:100%;padding:10px;border:1px solid #ddd;border-radius:8px}
        
        /* Style Galeri & Checkbox Hapus */
        .photo-gallery { display: flex; gap: 15px; flex-wrap: wrap; margin-top: 10px; }
        .photo-wrapper { position: relative; width: 120px; text-align: center; border: 1px solid #ddd; padding: 5px; border-radius: 8px; background: #fff; }
        .photo-item { width: 100%; height: 100px; object-fit: cover; border-radius: 5px; }
        .delete-check { margin-top: 5px; display: block; font-size: 13px; color: #c0392b; cursor: pointer; }
        .delete-check input { margin-right: 5px; cursor: pointer; }
        
        .no-photo { color: #888; font-style: italic; }
    </style>
</head>
<body>
    <div class="app">
        <nav class="sidebar">
            <div class="s-top"><img src="assets/img/logo.png" class="s-logo"><h3>LostFound</h3></div>
            <ul class="menu">
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="report_list.php">Kembali</a></li>
            </ul>
        </nav>
        <main class="main">
            <header class="main-head"><h2>Edit Laporan</h2></header>
            <div class="card">
              <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="report_id" value="<?= $data['report_id']; ?>">
                
                <div class="form-group">
                    <label>Jenis Laporan</label>
                    <select class="form-input" disabled style="background:#eee;">
                        <option value="lost" <?= ($data['type']=='lost')?'selected':''; ?>>Kehilangan</option>
                        <option value="found" <?= ($data['type']=='found')?'selected':''; ?>>Penemuan</option>
                    </select>
                </div>

                <div class="form-group"><label>Judul</label><input name="judul" type="text" value="<?= htmlspecialchars($data['title']); ?>" class="form-input" required></div>
                <div class="form-group"><label>Deskripsi</label><textarea name="deskripsi" class="form-input" required><?= htmlspecialchars($data['description']); ?></textarea></div>
                <div class="form-group"><label>Lokasi</label><input name="lokasi" type="text" value="<?= htmlspecialchars($data['location_text']); ?>" class="form-input" required></div>
                <div class="form-group"><label>Tanggal</label><input name="date_event" type="date" value="<?= $data['date_event']; ?>" class="form-input" required></div>
                
                <div class="form-group">
                    <label>Foto Saat Ini (Centang untuk menghapus):</label>
                    <div class="photo-gallery">
                        <?php if (!empty($data['photos'])): ?>
                            <?php foreach ($data['photos'] as $foto): ?>
                                <div class="photo-wrapper">
                                    <img src="uploads/<?= $foto['image_path']; ?>" class="photo-item">
                                    
                                    <label class="delete-check">
                                        <input type="checkbox" name="delete_photos[]" value="<?= $foto['photo_id']; ?>"> 
                                        Hapus
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <span class="no-photo">Belum ada foto.</span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-group">
                    <label>Tambah Foto Baru</label>
                    <input name="foto[]" type="file" class="form-input" accept="image/*" multiple>
                </div>
                
                <button type="submit" name="update" class="btn-primary" style="margin-top:20px;">Simpan Perubahan</button>
              </form>
            </div>
        </main>
    </div>
</body>
</html>
