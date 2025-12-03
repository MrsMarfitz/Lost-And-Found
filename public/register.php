<?php

require '../config/config.php';

$pesan_error = '';
$pesan_sukses = '';

// 2. Tangkap pesan dari URL (hasil lemparan dari backend)
if (isset($_GET['status'])) {
    if ($_GET['status'] == 'failed' && isset($_GET['msg'])) {
        $pesan_error = htmlspecialchars($_GET['msg']);
    } 
    else if ($_GET['status'] == 'success') {
        $pesan_sukses = "Pendaftaran berhasil! Silakan login.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - Lost & Found</title>
    <link rel="stylesheet" href="assets/css/auth.css">

    <style>
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 8px; font-weight: bold; text-align: center; }
        .alert-danger { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .alert-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    </style>
</head>
<body>

<div class="container">
    <div class="form-box">
        <h2 class="title">Create Account</h2>
        <p>Buat akun agar bisa melaporkan barang hilang/temuan</p>

        <?php if (!empty($pesan_error)): ?>
            <div class="alert alert-danger">
                ⚠️ <?php echo $pesan_error; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($pesan_sukses)): ?>
            <div class="alert alert-success">
                ✅ <?php echo $pesan_sukses; ?>
            </div>
        <?php endif; ?>

        <form action="../backend/register_process.php" method="POST">
            
            <div class="input-box">
                <input type="text" name="nama" placeholder="Nama Lengkap" required>
            </div>
            
            <div class="input-box">
                <input type="text" name="username" placeholder="Username" required>
            </div>
            
            <div class="input-box">
                <input type="email" name="email" placeholder="Email" required>
            </div>

            <div class="input-box">
                <input type="text" name="phone" placeholder="Nomor HP / WhatsApp" required>
            </div>

            <div class="input-box">
                <input type="password" name="password" placeholder="Password" required>
            </div>
            
            <div class="input-box">
                <input type="password" name="password_confirm" placeholder="Konfirmasi Password" required>
            </div>

            <button type="submit" class="btn">Buat Akun</button>

            <p style="margin-top:12px; font-size:14px; text-align:center;">
                Sudah punya akun? <a href="login.php">Login</a>
            </p>
        </form>
    </div>

    <div class="side-box">
        <div style="text-align: center;">
            <img src="assets/img/logo.png" alt="Logo" style="width:90px; margin-bottom:25px;">
            <h2>Welcome!</h2>
            <p>Jika sudah mempunyai akun silahkan Log in!.</p>

        </div>
    </div>
</div>

</body>
</html>