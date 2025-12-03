
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pengguna - Admin Panel</title>
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
                <li><a href="reports.php">Kelola Laporan</a></li>
                <li class="active">Kelola Pengguna</li>
                </ul>
            <div class="s-bottom">
                <img src="../public/assets/img/user.jpg" class="avatar" alt="user">
                <div>
                    <div class="small">Admin</div>
                    <a href="logout.php">Logout</a>
                </div>
            </div>
        </nav>
        
        <main class="main">
            <header class="main-head">
                <h2>Kelola Semua Pengguna</h2>
                <div class="head-actions">
                    <input class="search" placeholder="Cari pengguna...">
                    <button class="btn-primary small">Tambah Admin Baru</button>
                </div>
            </header>
            
            <div class="cards-list">
              <div class="card">
                <table class="table">
                  <thead>
                    <tr><th>ID</th><th>Nama</th><th>Username</th><th>Email</th><th>Tipe</th><th>Aktivasi</th><th>Aksi</th></tr>
                  </thead>
                  <tbody>
                    <tr><td>#1</td><td>Super Admin</td><td>admin</td><td>admin@mail.com</td><td><span class="tag red">Admin</span></td><td><span class="tag green">Aktif</span></td><td><a href="#">Edit/Blokir</a></td></tr>
                    <tr><td>#2</td><td>Andi Santoso</td><td>andi_s</td><td>andi@mail.com</td><td><span class="tag orange">User</span></td><td><span class="tag green">Aktif</span></td><td><a href="#">Edit/Blokir</a></td></tr>
                    <tr><td>#3</td><td>Budi Wijaya</td><td>budi_w</td><td>budi@mail.com</td><td><span class="tag orange">User</span></td><td><span class="tag red">Pending</span></td><td><a href="#">Aktivasi Manual</a></td></tr>
                  </tbody>
                </table>
              </div>
            </div>
        </main>
    </div>

</body>
</html>