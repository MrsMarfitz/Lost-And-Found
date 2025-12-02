<?php
require_once '../includes/crud_barang.php';

if (isset($_POST['kirim'])) {
    if (tambahBarang($_POST, $_FILES)) {
        echo "<script>alert('Laporan Berhasil Dibuat!'); window.location='report_list.php';</script>";
    } else {
        echo "<script>alert('Gagal membuat laporan.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Laporan Baru - Lost & Found Campus</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
      .form-group { margin-bottom: 15px; }
      .form-group label { display: block; margin-bottom: 5px; font-weight: 600; color: #475569; }
      .form-group input, .form-group textarea, .form-group select { 
        width: 100%; background:#f6f7fb; border:1px solid #e6e9f1; padding:12px 14px; border-radius:10px; font-size:15px;
      }
      .form-group textarea { resize: vertical; min-height: 100px; }
    </style>
</head>
<body>

    <div class="app">
        <nav class="sidebar">
            <div class="s-top">
                <img src="assets/img/logo.png" class="s-logo" alt="logo">
                <h3>LostFound</h3>
            </div>
            <ul class="menu">
                <li><a href="dashboard.php">Dashboard</a></li>
                <li class="active"><a href="report_create.php">Buat Laporan</a></li>
                <li><a href="report_list.php">Daftar Laporan</a></li>
                <li><a href="profile.php">Profil Saya</a></li>
            </ul>
            <div class="s-bottom">
                <img src="assets/img/user.jpg" class="avatar" alt="user">
                <div>
                    <div class="small">Pengguna Aktif</div>
                    <a href="logout.php">Logout</a>
                </div>
            </div>
        </nav>
        
        <main class="main">
            <header class="main-head">
                <h2>Buat Laporan Baru</h2>
            </header>
            
            <div class="card">
              <form action="" method="POST" enctype="multipart/form-data">
                
                <div class="form-group">
                  <label for="jenis">Jenis Laporan</label>
                  <select name="jenis" id="jenis" required>
                    <option value="">Pilih...</option>
                    <option value="lost">Barang Hilang</option>
                    <option value="found">Barang Ditemukan</option>
                  </select>
                </div>

                <div class="form-group">
                  <label for="judul">Judul Laporan</label>
                  <input name="judul" id="judul" type="text" placeholder="Contoh: Dompet Kulit Warna Coklat" required>
                </div>
                
                <div class="form-group">
                  <label for="deskripsi">Deskripsi Detail</label>
                  <textarea name="deskripsi" id="deskripsi" placeholder="Sebutkan ciri-ciri, warna, merek, dan waktu kejadian." required></textarea>
                </div>
                
                <div class="form-group">
                  <label for="lokasi">Lokasi Kejadian/Penemuan</label>
                  <input name="lokasi" id="lokasi" type="text" placeholder="Contoh: Gedung B Lantai 3" required>
                </div>

                <div class="form-group">
                    <label>Tanggal Kejadian</label>
                    <input name="date_event" type="date" required>
                </div>
                
                <div class="form-group">
                  <label for="foto">Unggah Foto Barang (Opsional)</label>
                  <input name="foto[]" id="foto" type="file" accept="image/*" multiple> 
                </div>
                
                <button class="btn-primary" type="submit" name="kirim" style="margin-top: 20px;">Kirim Laporan</button>
              </form>
            </div>
        </main>
    </div>

</body>
</html>
