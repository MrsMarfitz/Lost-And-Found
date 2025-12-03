<?php
require_once "auth.php";
require_once "../config/config.php";
require_once "../config/db_connect.php";

$table="reports";
$idCol="id"; 
$typeCol="type"; 
$titleCol="title"; 
$catCol="category";
$locCol="location"; 
$statusCol="status";

// ==== AUTO DETECT kolom tanggal biar aman ====
$possibleDateCols = ["report_date","created_at","tanggal","date"];
$dateCol = null;

foreach($possibleDateCols as $col){
  $c = $conn->query("SHOW COLUMNS FROM $table LIKE '$col'");
  if($c && $c->num_rows > 0){
    $dateCol = $col;
    break;
  }
}

// cek tabel ada atau belum
$check = $conn->query("SHOW TABLES LIKE '$table'");
if ($check && $check->num_rows > 0) {

  if($dateCol){
    $result = $conn->query("SELECT * FROM $table ORDER BY $dateCol DESC");
  } else {
    // fallback kalau gak ada kolom tanggal sama sekali
    $result = $conn->query("SELECT * FROM $table ORDER BY $idCol DESC");
  }

  $useDummy = false;

} else {
  $useDummy = true;
  $dateCol = "report_date"; // biar dummy tetap tampil rapi

  $result = [
    ["id"=>1,"type"=>"lost","title"=>"Tas Ransel Biru","category"=>"Aksesoris","location"=>"Area Kantin","report_date"=>"2025-11-30","status"=>"pending","reporter"=>"Rio"],
    ["id"=>2,"type"=>"found","title"=>"Sepeda Lipat","category"=>"Kendaraan","location"=>"Pos Satpam","report_date"=>"2025-11-29","status"=>"approved","reporter"=>"Dewi"],
    ["id"=>3,"type"=>"lost","title"=>"Kartu Mahasiswa","category"=>"Dokumen","location"=>"Perpustakaan","report_date"=>"2025-11-28","status"=>"rejected","reporter"=>"Andi"],
  ];
}
?>

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
        <h2>Kelola Semua Laporan</h2>
        <div class="head-actions">
          <input class="search" placeholder="Filter/Cari laporan...">

          <a href="export_pdf.php" class="btn-primary small" style="text-decoration:none;">
            Generate PDF Report
          </a>
        </div>
      </header>

      <div class="cards-list">
        <div class="card">
          <table class="table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Judul</th>
                <th>Jenis</th>
                <th>Pelapor</th>
                <th>Tanggal</th>
                <th>Lokasi</th>
                <th>Status</th>
                <th>Aksi Admin</th>
              </tr>
            </thead>
            <tbody>

            <?php if($useDummy): ?>
              <?php foreach($result as $row): ?>
                <tr>
                  <td>#<?= $row[$idCol] ?></td>
                  <td><?= $row[$titleCol] ?></td>
                  <td><?= strtoupper($row[$typeCol]) ?></td>
                  <td><?= $row["reporter"] ?></td>
                  <td><?= $row[$dateCol] ?></td>
                  <td><?= $row[$locCol] ?></td>
                  <td><span class="tag orange"><?= $row[$statusCol] ?></span></td>
                  <td>
                    <a href="update_status.php?id=<?= $row[$idCol] ?>&status=approved">Approve</a> |
                    <a href="update_status.php?id=<?= $row[$idCol] ?>&status=rejected">Reject</a> |
                    <a href="delete_report.php?id=<?= $row[$idCol] ?>" onclick="return confirm('Hapus laporan ini?')">Delete</a>
                  </td>
                </tr>
              <?php endforeach; ?>

            <?php elseif($result && $result->num_rows > 0): ?>
              <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                  <td>#<?= $row[$idCol] ?></td>
                  <td><?= $row[$titleCol] ?></td>
                  <td><?= strtoupper($row[$typeCol]) ?></td>
                  <td><?= $row["reporter_name"] ?? "-" ?></td>
                  <td><?= $dateCol ? ($row[$dateCol] ?? "-") : "-" ?></td>
                  <td><?= $row[$locCol] ?></td>
                  <td><span class="tag orange"><?= $row[$statusCol] ?></span></td>
                  <td>
                    <a href="update_status.php?id=<?= $row[$idCol] ?>&status=approved">Approve</a> |
                    <a href="update_status.php?id=<?= $row[$idCol] ?>&status=rejected">Reject</a> |
                    <a href="delete_report.php?id=<?= $row[$idCol] ?>" onclick="return confirm('Hapus laporan ini?')">Delete</a>
                  </td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr><td colspan="8">Belum ada laporan.</td></tr>
            <?php endif; ?>

            </tbody>
          </table>
        </div>
      </div>

    </main>
  </div>
</body>
</html>