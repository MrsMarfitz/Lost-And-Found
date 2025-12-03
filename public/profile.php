<?php
session_start();
require '../config/config.php';
require '../config/db_connect.php';

// Cek Login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Ambil data user terbaru
$query = "SELECT * FROM users WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - Lost & Found</title>
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
            <li><a href="report_list.php">Daftar Laporan</a></li>
            <li class="active"><a href="profile.php">Profil Saya</a></li>
            
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
            <h2>Profil Saya</h2>
        </header>

        <div style="background: white; padding: 30px; border-radius: 10px; max-width: 600px;">
            <div style="margin-bottom: 15px;">
                <label style="display:block; margin-bottom:5px; font-weight:bold;">Nama Lengkap</label>
                <input type="text" value="<?php echo htmlspecialchars($user['full_name']); ?>" readonly style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; background:#f9f9f9;">
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display:block; margin-bottom:5px; font-weight:bold;">Username</label>
                <input type="text" value="<?php echo htmlspecialchars($user['username']); ?>" readonly style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; background:#f9f9f9;">
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display:block; margin-bottom:5px; font-weight:bold;">Email</label>
                <input type="text" value="<?php echo htmlspecialchars($user['email']); ?>" readonly style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; background:#f9f9f9;">
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display:block; margin-bottom:5px; font-weight:bold;">No HP</label>
                <input type="text" value="<?php echo htmlspecialchars($user['phone']); ?>" readonly style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; background:#f9f9f9;">
            </div>
        </div>
    </main>
</div>

</body>
</html>