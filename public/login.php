<?php
session_start();

require __DIR__ . '/../config/config.php';
require __DIR__ . '/../config/db_connect.php';

$status_message = '';
if (isset($_GET['status'])) {
    if ($_GET['status'] == 'registered') {
        $status_message = 'Pendaftaran berhasil! Silakan Login.';
    } elseif ($_GET['status'] == 'login_failed' && isset($_GET['msg'])) {
        $status_message = 'ERROR: ' . htmlspecialchars($_GET['msg']);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - Lost & Found Campus</title>
    <link rel="stylesheet" href="assets/css/auth.css">
</head>
<body>

<div class="container">
    <div class="form-box">
        <h2 class="title">Sign In</h2>
        <p>Masuk menggunakan akun kamu</p>

        <?php if (!empty($status_message)): ?>
            <div style="margin:10px 0; padding:10px; border-radius:8px; background:#ffecec; color:#b91c1c; font-size:14px;">
                <?php echo $status_message; ?>
            </div>
        <?php endif; ?>

        <form action="check_user.php" method="POST">
            <div class="input-box">
                <input type="text" name="username_email" placeholder="Username atau Email" required>
            </div>
            <div class="input-box">
                <input type="password" name="password" placeholder="Password" required>
            </div>

            <div style="margin-bottom:10px; font-size:12px;">
                <label>
                    <input type="checkbox" name="remember"> Remember me
                </label>
            </div>

            <button type="submit" class="btn">Sign In</button>

            <p style="margin-top:12px; font-size:14px;">
                Belum punya akun? <a href="register.php">Daftar</a>
            </p>
        </form>
    </div>

    <div class="side-box">
        <div>
            <img src="assets/img/logo.png" alt="Logo" style="width:90px; margin-bottom:25px;">
            <h2>Hello, Friend!</h2>
            <p>Buat akun disini jika belum mempunyai akun!!</p>
            <a href="register.php" class="side-btn">Buat Akun</a>
        </div>
    </div>
</div>

</body>
</html>
