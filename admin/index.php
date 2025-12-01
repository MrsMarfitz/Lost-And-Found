<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Admin Dashboard - Lost & Found Campus</title>
  <link rel="stylesheet" href="../public/assets/css/style.css"> </head>
<body>

  <div class="app">
    <nav class="sidebar">
      <div class="s-top">
        <img src="../public/assets/img/logo.png" class="s-logo" alt="logo">
        <h3>Admin Panel</h3>
      </div>

      <ul class="menu">
        <li class="active">Dashboard Admin</li>
        <li><a href="reports.php">Kelola Laporan</a></li>
        <li><a href="users.php">Kelola Pengguna</a></li>
        <li style="margin-top: 20px;"><a href="../public/dashboard.php">Kembali ke User Dashboard</a></li>
      </ul>

      <div class="s-bottom">
        <img src="../public/assets/img/user.jpg" class="avatar" alt="user">
        <div>
          <div class="small">Admin</div>
          <a href="../public/logout.php">Logout</a>
        </div>
      </div>
    </nav>

    <main class="main">
      <header class="main-head">
        <h2>Admin Dashboard</h2>
      </header>

      <section class="grid">
        <div class="card stat" style="grid-column: span 1;">
          <h3>Total Laporan</h3><div class="big">120</div>
        </div>
        <div class="card stat" style="grid-column: span 1;">
          <h3>Laporan Baru Hari Ini</h3><div class="big">7</div>
        </div>
        <div class="card stat" style="grid-column: span 1;">
          <h3>Pengguna Baru</h3><div class="big">15</div>
        </div>
        <div class="card stat" style="grid-column: span 1;">
          <h3>Laporan Perlu Aksi</h3><div class="big" style="color:#ef4444;">12</div>
        </div>
      </section>

      <section class="cards-list">
        <div class="card">
          <h3>Laporan Terbaru (Perlu Verifikasi)</h3>
          <table class="table">
            <thead><tr><th>ID</th><th>Judul</th><th>Jenis</th><th>Pelapor</th><th>Tanggal</th><th>Aksi</th></tr></thead>
            <tbody>
              <tr><td>#110</td><td>Smartphone Samsung</td><td>Lost</td><td>Jono</td><td>2025-12-01</td><td><a href="reports.php?id=110">Lihat/Verifikasi</a></td></tr>
              <tr><td>#111</td><td>Buku Paket Biologi</td><td>Found</td><td>Siti</td><td>2025-12-01</td><td><a href="reports.php?id=111">Lihat/Verifikasi</a></td></tr>
            </tbody>
          </table>
        </div>

        <div class="card">
          <h3>Aktivitas Pengguna Baru</h3>
          <table class="table">
            <thead><tr><th>ID</th><th>Nama</th><th>Email</th><th>Tanggal Daftar</th><th>Aktivasi</th></tr></thead>
            <tbody>
              <tr><td>#50</td><td>Budi</td><td>budi@mail.com</td><td>2025-12-01</td><td><span class="tag orange">Pending</span></td></tr>
              <tr><td>#49</td><td>Ani</td><td>ani@mail.com</td><td>2025-11-30</td><td><span class="tag green">Aktif</span></td></tr>
            </tbody>
          </table>
        </div>
      </section>
    </main>
  </div>

<script src="../public/assets/js/app.js"></script>
</body>
</html>