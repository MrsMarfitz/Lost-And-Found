<?php
session_start();
require '../config/config.php';
require '../config/db_connect.php';

// Cek Login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Ambil semua laporan (bisa difilter statusnya nanti)
$query = "SELECT * FROM reports ORDER BY created_at DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Laporan - Lost & Found</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* CSS KHUSUS HALAMAN INI */
        .table-container { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f8f9fc; color: #555; font-weight: 600; }
        tr:hover { background-color: #f1f1f1; }
        .badge { padding: 5px 10px; border-radius: 20px; font-size: 12px; color: white; }
        .bg-hilang { background: #e74a3b; }
        .bg-ditemukan { background: #1cc88a; }
        .bg-selesai { background: #858796; }
        .btn-sm { padding: 5px 10px; background: #4e73df; color: white; text-decoration: none; border-radius: 4px; font-size: 12px; }
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
            <li class="active"><a href="report_list.php">Daftar Laporan</a></li>
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
            <h2>Daftar Semua Laporan</h2>
            <a href="report_create.php" class="btn-primary small">+ Buat Laporan</a>
        </header>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Foto</th>
                        <th>Judul</th>
                        <th>Lokasi</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <?php $foto = !empty($row['photo']) ? "uploads/".$row['photo'] : "assets/img/placeholder.jpg"; ?>
                                    <img src="<?php echo $foto; ?>" width="50" height="50" style="object-fit:cover; border-radius:5px;">
                                </td>
                                <td><?php echo htmlspecialchars($row['title']); ?></td>
                                <td><?php echo htmlspecialchars($row['location']); ?></td>
                                <td><?php echo date('d M Y', strtotime($row['created_at'])); ?></td>
                                <td>
                                    <?php 
                                        $badge = 'bg-selesai';
                                        if($row['status'] == 'Hilang') $badge = 'bg-hilang';
                                        if($row['status'] == 'Ditemukan') $badge = 'bg-ditemukan';
                                    ?>
                                    <span class="badge <?php echo $badge; ?>"><?php echo $row['status']; ?></span>
                                </td>
                                <td>
                                    <a href="#" class="btn-sm">Detail</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align:center;">Belum ada laporan.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

</body>
</html>