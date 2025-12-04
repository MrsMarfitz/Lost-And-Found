<?php
session_start();
require '../config/db_connect.php';

// Cek Login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Ambil data user terbaru dari database
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Tentukan foto profil (Gunakan default jika kosong)
$foto_profil = !empty($user['profile_image']) ? "uploads/" . $user['profile_image'] : "assets/images/default-avatar.png"; 
// Catatan: Pastikan Anda punya gambar default di folder assets/images/ atau ganti linknya ke gambar online.
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya</title>
    <style>
        body { font-family: sans-serif; background: #f3f4f6; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .profile-header { text-align: center; margin-bottom: 30px; }
        .profile-img { width: 150px; height: 150px; object-fit: cover; border-radius: 50%; border: 4px solid #3b82f6; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="email"], input[type="password"] { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; }
        .btn-save { background: #3b82f6; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-size: 16px; width: 100%; }
        .btn-save:hover { background: #2563eb; }
        .section-title { border-bottom: 2px solid #eee; padding-bottom: 10px; margin-bottom: 20px; color: #333; }
        .alert { padding: 10px; background: #d1fae5; color: #065f46; border-radius: 5px; margin-bottom: 20px; }
    </style>
</head>
<body>

<div class="container">
    <a href="dashboard.php" style="text-decoration: none; color: #666;">&larr; Kembali ke Dashboard</a>
    
    <div class="profile-header">
        <img src="<?php echo htmlspecialchars($foto_profil); ?>" alt="Foto Profil" class="profile-img">
        <h2>Halo, <?php echo htmlspecialchars($user['full_name']); ?>!</h2>
        <p>@<?php echo htmlspecialchars($user['username']); ?></p>
    </div>

    <form action="../backend/update_profile.php" method="POST" enctype="multipart/form-data">
        
        <h3 class="section-title">Edit Data Diri</h3>

        <div class="form-group">
            <label>Ganti Foto Profil</label>
            <input type="file" name="foto_profil" accept="image/*">
            <small style="color: gray;">Format: JPG, PNG. Maks 2MB.</small>
        </div>

        <div class="form-group">
            <label>Nama Lengkap</label>
            <input type="text" name="nama" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
        </div>

        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
        </div>

        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        </div>

        <h3 class="section-title" style="margin-top: 40px;">Ganti Password (Opsional)</h3>
        <p style="font-size: 14px; color: #666; margin-bottom: 15px;">Kosongkan jika tidak ingin mengganti password.</p>

        <div class="form-group">
            <label>Password Lama</label>
            <input type="password" name="password_lama" placeholder="Masukkan password saat ini">
        </div>

        <div class="form-group">
            <label>Password Baru</label>
            <input type="password" name="password_baru" placeholder="Masukkan password baru">
        </div>

        <button type="submit" class="btn-save">Simpan Perubahan</button>
    </form>
</div>

</body>
</html>