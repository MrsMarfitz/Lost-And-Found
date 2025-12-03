<?php
require_once "../config/config.php";
require_once "../config/db_connect.php";

$table="reports";

// kolom sesuai DB temanmu
$idCol="report_id";
$typeCol="type";
$titleCol="title";
$locCol="location";        // <- DB temanmu pakai location
$dateCol="incident_date"; // <- DB temanmu pakai incident_date
$statusCol="status";

// ambil data reports + nama pelapor
$sql = "SELECT r.*, COALESCE(u.full_name, u.username) AS reporter_name
        FROM reports r
        LEFT JOIN users u ON r.user_id = u.user_id
        ORDER BY r.created_at DESC";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kelola Laporan</title>
  <link rel="stylesheet" href="../public/assets/css/style.css">

  <style>
    .card {
      background: #fff;
      border-radius: 14px;
      padding: 18px;
      box-shadow: 0 6px 20px rgba(0,0,0,0.06);
      overflow-x: auto;
    }
    .table {
      width: 100%;
      border-collapse: collapse;
      font-size: 14px;
      min-width: 900px;
    }
    .table thead th {
      background: #f5f7fb;
      color: #111827;
      font-weight: 700;
      text-align: left;
      padding: 12px 10px;
      border-bottom: 2px solid #e5e7eb;
      white-space: nowrap;
    }
    .table tbody td {
      padding: 12px 10px;
      border-bottom: 1px solid #eef2f7;
      vertical-align: middle;
      white-space: nowrap;
    }
    .table tbody tr:nth-child(even){ background: #fafbff; }
    .table tbody tr:hover{ background: #f0f7ff; }

    .tag {
      display: inline-block; padding: 4px 10px; border-radius: 999px;
      font-size: 12px; font-weight: 700; text-transform: capitalize;
    }
    .tag.orange { background:#fff7ed; color:#c2410c; }
    .tag.green  { background:#ecfdf3; color:#16a34a; }
    .tag.red    { background:#fef2f2; color:#dc2626; }
    .tag.gray   { background:#f3f4f6; color:#374151; }

    .aksi-admin a{
      display:inline-block; padding:6px 10px; font-size:12px; font-weight:700;
      border-radius:8px; text-decoration:none; margin-right:6px; transition:.15s ease;
    }
    .aksi-admin a.approve{ background:#e8f5ff; color:#0369a1; }
    .aksi-admin a.reject{ background:#fff1f2; color:#be123c; }
    .aksi-admin a.delete{ background:#f3f4f6; color:#111827; }
  </style>
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
        <div class="head-actions" style="display:flex; gap:10px; align-items:center;">
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

            <?php if($result && $result->num_rows > 0): ?>
              <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                  <td>#<?= $row[$idCol] ?></td>
                  <td><?= $row[$titleCol] ?? "-" ?></td>
                  <td><?= strtoupper($row[$typeCol] ?? "-") ?></td>
                  <td><?= $row["reporter_name"] ?? "-" ?></td>

                  <td>
                    <?php
                      $tgl = $row[$dateCol] ?? "-";
                      if($tgl !== "-" && strtotime($tgl)){
                        $tgl = date("d-m-Y", strtotime($tgl));
                      }
                      echo $tgl;
                    ?>
                  </td>

                  <td><?= $row[$locCol] ?? "-" ?></td>

                  <td>
                    <?php
                      $st = $row[$statusCol] ?? "pending";
                      $cls="gray";
                      if($st=="approved"||$st=="resolved") $cls="green";
                      if($st=="rejected") $cls="red";
                      if($st=="active"||$st=="pending") $cls="orange";
                    ?>
                    <span class="tag <?= $cls ?>"><?= $st ?></span>
                  </td>

                  <td class="aksi-admin">
                    <a class="approve" href="update_status.php?id=<?= $row[$idCol] ?>&status=approved">Approve</a>
                    <a class="reject"  href="update_status.php?id=<?= $row[$idCol] ?>&status=rejected">Reject</a>
                    <a class="delete"  href="delete_report.php?id=<?= $row[$idCol] ?>" onclick="return confirm('Hapus laporan ini?')">Delete</a>
                  </td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr><td colspan="8" style="text-align:center;">Belum ada laporan.</td></tr>
            <?php endif; ?>

            </tbody>
          </table>
        </div>
      </div>
    </main>
  </div>
</body>
</html>