<?php

require '../config/config.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}


$username = $_SESSION['username'] ?? 'Pengguna';
$role = $_SESSION['role'] ?? 'user';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1"/>
    <title>Dashboard - Lost & Found Campus</title>
    <link rel="stylesheet" href="assets/css/style.css"> 
</head>
<body>

    <header>
        <nav style="background-color: #f8f8f8; padding: 15px; border-bottom: 1px solid #ddd; display: flex; justify-content: space-between; align-items: center;">
            <div style="font-weight: bold;">Lost & Found Campus</div>
            <div>
                <span style="margin-right: 15px;">Halo, <?php echo htmlspecialchars($username); ?> (Role: <?php echo htmlspecialchars($role); ?>)</span>
                <a href="logout.php" style="color: red; text-decoration: none;">Logout</a>
            </div>
        </nav>
    </header>

    <main style="padding: 20px; max-width: 1200px; margin: auto;">
        <h2>Dashboard Utama</h2>
        <p>Selamat datang di halaman utama aplikasi Lost & Found Campus.</p>

        <section style="margin-top: 30px;">
            <h3>Fitur Cepat</h3>
            <a href="report_create.php" style="padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px; margin-right: 10px;">Buat Laporan Baru</a>
            <a href="report_list.php" style="padding: 10px 20px; background-color: #6c757d; color: white; text-decoration: none; border-radius: 5px;">Lihat Semua Laporan</a>
        </section>
        
        <hr style="margin-top: 40px; margin-bottom: 40px;">

        <section>
            <h3>Laporan Terbaru</h3>
            <p>Di sini akan ditampilkan daftar laporan barang hilang dan ditemukan terbaru. (Akan diimplementasikan nanti)</p>
        </section>
    </main>

</body>
</html>