<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Laporan - Admin Panel</title>
    <link rel="stylesheet" href="../public/assets/css/style.css">
</head>
<body>

    <div class="app">
        <nav class="sidebar">
            <div class="s-top">
                <img src="../public/assets/img/logo.png" class="s-logo" alt="logo">
                <h3>Admin Panel</h3>
            </div>
            <ul class="menu">
                <li><a href="index.php">Dashboard Admin</a></li>
                <li class="active">Kelola Laporan</li>
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
                <h2>Kelola Semua Laporan</h2>
                <div class="head-actions">
                    <input class="search" placeholder="Filter/Cari laporan...">
                    <button class="btn-primary small" style="background:#1abc9c;">Generate PDF Report</button>
                </div>
            </header>
            
            <div class="cards-list">
              <div class="card">
                <h3>Filter Status: <span class="tag orange">Pending</span></h3>
                <table class="table">
                  <thead>
                    <tr><th>ID</th><th>Judul</th><th>Jenis</th><th>Pelapor</th><th>Tanggal</th><th>Lokasi</th><th>Status</th><th>Aksi Admin</th></tr>
                  </thead>
                  <tbody>
                    <tr><td>#108</td><td>Tas Ransel Biru</td><td>Lost</td><td>Rio</td><td>2025-11-30</td><td>Area Kantin</td><td><span class="tag orange">Pending</span></td><td><a href="#">Verifikasi</a></td></tr>
                    <tr><td>#109</td><td>Sepeda Lipat</td><td>Found</td><td>Dewi</td><td>2025-11-29</td><td>Pos Satpam</td><td><span class="tag green">Found</span></td><td><a href="#">Selesai</a></td></tr>
                    <tr><td>#107</td><td>Kartu Mahasiswa</td><td>Lost</td><td>Andi</td><td>2025-11-28</td><td>Perpustakaan</td><td><span class="tag red">Lost</span></td><td><a href="#">Tindak Lanjut</a></td></tr>
                  </tbody>
                </table>
              </div>
            </div>
        </main>
    </div>

</body>
</html>