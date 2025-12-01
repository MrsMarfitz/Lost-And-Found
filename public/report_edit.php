<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Laporan #102 - Lost & Found Campus</title>
    <link rel="stylesheet" href="assets/css/style.css">
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
                <li class="active"><a href="report_list.php">Daftar Laporan</a></li>
                <li><a href="profile.php">Profil Saya</a></li>
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
                <h2>Edit Laporan #102 (Kunci Motor)</h2>
            </header>
            
            <div class="card">
              <form action="../backend/report_update.php?id=102" method="POST" enctype="multipart/form-data">
                
                <div class="form-group">
                  <label for="jenis">Jenis Laporan</label>
                  <select name="jenis" id="jenis" required class="form-input">
                    <option value="lost" selected>Barang Hilang</option>
                    <option value="found">Barang Ditemukan</option>
                  </select>
                </div>

                <div class="form-group">
                  <label for="judul">Judul Laporan</label>
                  <input name="judul" id="judul" type="text" value="Kunci Motor XMAX" required class="form-input">
                </div>
                
                <div class="form-group">
                  <label for="deskripsi">Deskripsi Detail</label>
                  <textarea name="deskripsi" id="deskripsi" required class="form-input">Kunci motor XMAX dengan gantungan kunci berwarna biru muda. Hilang sekitar jam 1 siang.</textarea>
                </div>
                
                <div class="form-group">
                  <label for="lokasi">Lokasi Kejadian/Penemuan</label>
                  <input name="lokasi" id="lokasi" type="text" value="Parkiran Gedung Utama" required class="form-input">
                </div>

                <div class="form-group">
                  <label>Foto Barang Saat Ini</label>
                  <img src="assets/img/kunci_motor.jpg" alt="Kunci Motor" class="current-photo">
                  <label for="foto_baru">Ganti Foto (Opsional)</label>
                  <input name="foto_baru" id="foto_baru" type="file" accept="image/*" class="form-input">
                </div>

                <h4 style="margin-top: 30px; margin-bottom: 10px;">Status Klaim</h4>
                
                <div class="form-group">
                  <label for="status">Status Laporan</label>
                  <select name="status" id="status" required class="form-input">
                    <option value="lost" selected>Lost (Hilang)</option>
                    <option value="pending">Pending (Menunggu Verifikasi)</option>
                    <option value="found">Found (Sudah Ditemukan/Klaim)</option>
                  </select>
                </div>
                
                <button class="btn-primary" type="submit" style="margin-top: 20px;">Simpan Perubahan</button>
                <button class="btn-primary" type="button" style="background:#ef4444;">Hapus Laporan</button>
              </form>
            </div>
        </main>
    </div>

</body>
</html>