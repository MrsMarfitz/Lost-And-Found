<?php
session_start();

require __DIR__ . '/../config/config.php';
require __DIR__ . '/../config/db_connect.php';

$status_message = '';
if (isset($_GET['status']) && $_GET['status'] == 'register_failed' && isset($_GET['msg'])) {
    $status_message = 'ERROR: ' . htmlspecialchars($_GET['msg']);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - Lost & Found Campus</title>
    <link rel="stylesheet" href="assets/css/auth.css">
</head>
<body>

<div class="container">
    <div class="form-box">
        <h2 class="title">Create Account</h2>
        <p>Buat akun agar bisa melaporkan barang hilang/temuan</p>

        <?php if (!empty($status_message)): ?>
            <div style="margin:10px 0; padding:10px; border-radius:8px; background:#ffecec; color:#b91c1c; font-size:14px;">
                <?php echo $status_message; ?>
            </div>
        <?php endif; ?>

        <form action="register.php" method="POST">
            <div class="input-box">
                <input type="text" name="full_name" placeholder="Nama Lengkap" required>
            </div>
            <div class="input-box">
                <input type="text" name="username" placeholder="Username" required>
            </div>
            <div class="input-box">
                <input type="email" name="email" placeholder="Email" required>
            </div>
            <div class="input-box">
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <div class="input-box">
                <input type="password" name="confirm_password" placeholder="Konfirmasi Password" required>
            </div>

            <button type="submit" class="btn">Create Account</button>

            <p style="margin-top:12px; font-size:14px;">
                Sudah punya akun? <a href="login.php">Login</a>
            </p>
        </form>
    </div>

    <div class="side-box">
        <div>
            <img src="assets/img/logo.png" alt="Logo" style="width:90px; margin-bottom:25px;">
            <h2>Welcome!</h2>
            <p>Let's help the campus community — report lost & found items quickly.</p>
            <a href="login.php" class="side-btn">Sign In</a>
        </div>
    </div>
</div>

</body>
</html>
