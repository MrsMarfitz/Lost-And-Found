<?php
require_once "auth.php";
require_once "../config/config.php";

$table = "reports";
$typeCol = "type";
$statusCol = "status";

// cek tabel ada atau belum
$check = $conn->query("SHOW TABLES LIKE '$table'");
if ($check && $check->num_rows > 0) {
  $total = $conn->query("SELECT COUNT(*) AS c FROM $table")->fetch_assoc()['c'];
  $lost  = $conn->query("SELECT COUNT(*) AS c FROM $table WHERE $typeCol='lost'")->fetch_assoc()['c'];
  $found = $conn->query("SELECT COUNT(*) AS c FROM $table WHERE $typeCol='found'")->fetch_assoc()['c'];
  $pending = $conn->query("SELECT COUNT(*) AS c FROM $table WHERE $statusCol='pending'")->fetch_assoc()['c'];
} else {
  // dummy buat presentasi kalau tabel belum ada
  $total = 120; $lost = 70; $found = 50; $pending = 12;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Admin Dashboard - Lost & Found Campus</title>
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
        <li class="active">Dashboard Admin</li>
        <li><a href="reports.php">Kelola Laporan</a></li>
        <li><a href="users.php">Kelola Pengguna</a></li>
        <li style="margin-top: 20px;"><a href="../public/dashboard.php">Kembali ke User Dashboard</a></li>
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
        <h2>Admin Dashboard</h2>
      </header>

      <section class="grid">
        <div class="card stat">
          <h3>Total Laporan</h3><div class="big"><?= $total ?></div>
        </div>
        <div class="card stat">
          <h3>Total Lost</h3><div class="big"><?= $lost ?></div>
        </div>
        <div class="card stat">
          <h3>Total Found</h3><div class="big"><?= $found ?></div>
        </div>
        <div class="card stat">
          <h3>Laporan Perlu Aksi</h3><div class="big" style="color:#ef4444;"><?= $pending ?></div>
        </div>
      </section>

    </main>
  </div>

<script src="../public/assets/js/app.js"></script>
</body>
</html>
