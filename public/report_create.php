<?php
session_start();
require '../config/config.php';
require '../config/db_connect.php';

// Cek Login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
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
      /* CSS ASLI KAMU (TIDAK SAYA UBAH) */
      .form-group { margin-bottom: 15px; }
      .form-group label { display: block; margin-bottom: 5px; font-weight: 600; color: #475569; }
      .form-group input, .form-group textarea, .form-group select { 
        width: 100%; background:#f6f7fb; border:1px solid #e6e9f1; padding:12px 14px; border-radius:10px; font-size:15px;
      }
      .form-group textarea { resize: vertical; min-height: 100px; }
      
      /* Tambahan dikit buat pesan error biar rapi */
      .alert { padding: 15px; margin-bottom: 20px; border-radius: 10px; text-align: center; font-weight: bold;}
      .alert-red { background: #fee2e2; color: #991b1b; border: 1px solid #f87171; }
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
                
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') : ?>
                    <li><a href="../admin/index.php">Admin Panel</a></li>
                <?php endif; ?>
            </ul>
            <div class="s-bottom">
                <img src="assets/img/user.jpg" class="avatar" alt="user">
                <div>
                    <div class="small"><?php echo htmlspecialchars($_SESSION['full_name'] ?? 'Pengguna'); ?></div>
                    <a href="logout.php">Logout</a>
                </div>
            </div>
        </nav>
        
        <main class="main">
            <header class="main-head">
                <h2>Buat Laporan Baru</h2>
            </header>
            
            <?php if (isset($_GET['status']) && $_GET['status'] == 'failed'): ?>
                <div class="alert alert-red">
                    ⚠️ <?php echo htmlspecialchars($_GET['msg']); ?>
                </div>
            <?php endif; ?>

            <div class="card">
              <form action="../backend/create_report_process.php" method="POST" enctype="multipart/form-data">
                
                <div class="form-group">
                  <label for="jenis">Jenis Laporan</label>
                  <select name="type" id="jenis" required>
                    <option value="">Pilih...</option>
                    <option value="Hilang">Barang Hilang</option>
                    <option value="Ditemukan">Barang Ditemukan</option>
                  </select>
                </div>

                <div class="form-group">
                  <label for="judul">Judul Laporan</label>
                  <input name="title" id="judul" type="text" placeholder="Contoh: Dompet Kulit Warna Coklat" required>
                </div>
                
                <div class="form-group">
                  <label for="deskripsi">Deskripsi Detail</label>
                  <textarea name="description" id="deskripsi" placeholder="Sebutkan ciri-ciri, warna, merek, dan waktu kejadian." required></textarea>
                </div>
                
                <div class="form-group">
                  <label for="lokasi">Lokasi Kejadian/Penemuan</label>
                  <input name="location" id="lokasi" type="text" placeholder="Contoh: Gedung B Lantai 3" required>
                </div>

                <div class="form-group">
                    <label>Tanggal Kejadian</label>
                    <input name="date" type="date" required>
                </div>
                
                <div class="form-group">
                  <label for="foto">Unggah Foto Barang (Opsional)</label>
                  <input name="photo" id="foto" type="file" accept="image/*"> 
                  <small style="color: #666; font-size: 12px; margin-top:5px; display:block;">Format: JPG, PNG, WEBP. Maks 2MB.</small>
                </div>
                
                <button class="btn-primary" type="submit" style="margin-top: 20px;">Kirim Laporan</button>
              </form>
            </div>
        </main>
    </div>

</body>
</html>