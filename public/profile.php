<?php
session_start();

require __DIR__ . '/../config/config.php';
require __DIR__ . '/../config/db_connect.php';

// cek sudah login atau belum
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
    <title>Profil Saya - Lost & Found Campus</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
      /* Tambahan CSS sederhana untuk tata letak profil */
      .profile-info { display:flex; gap: 30px; align-items:flex-start; }
      .profile-photo-area { text-align:center; padding:20px; border-right: 1px solid #eef2f7; }
      .profile-photo { width:150px; height:150px; border-radius:50%; object-fit: cover; margin-bottom:15px; border: 4px solid #fff; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
      .profile-form-area { flex:1; padding:20px; }
      .form-group { margin-bottom: 15px; }
      .form-group label { display: block; margin-bottom: 5px; font-weight: 600; color: #475569; }
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
                <li><a href="report_create.php">Buat Laporan</a></li>
                <li><a href="report_list.php">Daftar Laporan</a></li>
                <li class="active">Profil Saya</li>
                <li><a href="../admin/index.php">Admin Panel</a></li>
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
                <h2>Profil Pengguna</h2>
            </header>
            
            <div class="card">
              <h3>Informasi Akun</h3>
              <div class="profile-info">

                <div class="profile-photo-area">
                  <img src="assets/img/user.jpg" class="profile-photo" alt="Photo Profil">
                  <form action="../backend/upload_photo.php" method="POST" enctype="multipart/form-data">
                    <input type="file" name="photo" id="photo" style="display:none;">
                    <label for="photo" class="btn-primary small" style="display:block; margin-bottom:10px;">Ganti Foto</label>
                    <button type="submit" class="btn-primary small" style="background:#1abc9c;">Upload Foto</button>
                  </form>
                </div>
                
                <div class="profile-form-area">
                  <form action="../backend/update_profile.php" method="POST">
                    
                    <div class="form-group">
                      <label for="nama">Nama Lengkap</label>
                      <input name="nama" id="nama" type="text" placeholder="Nama Lengkap" value="Nama Pengguna" required class="form-input">
                    </div>
                    
                    <div class="form-group">
                      <label for="username">Username</label>
                      <input name="username" id="username" type="text" placeholder="Username" value="user.aktif" required class="form-input">
                    </div>
                    
                    <div class="form-group">
                      <label for="email">Email</label>
                      <input name="email" id="email" type="email" placeholder="Email" value="user@kampus.ac.id" required class="form-input">
                    </div>

                    <h4 style="margin-top: 30px; margin-bottom: 10px;">Ganti Password (Opsional)</h4>
                    
                    <div class="form-group">
                      <label for="password_lama">Password Lama</label>
                      <input name="password_lama" id="password_lama" type="password" placeholder="Masukkan password lama" class="form-input">
                    </div>
                    
                    <div class="form-group">
                      <label for="password_baru">Password Baru</label>
                      <input name="password_baru" id="password_baru" type="password" placeholder="Masukkan password baru" class="form-input">
                    </div>
                    
                    <button class="btn-primary" type="submit" style="margin-top: 20px;">Update Profil</button>
                  </form>
                </div>

              </div>
            </div>
        </main>
    </div>

</body>
</html>