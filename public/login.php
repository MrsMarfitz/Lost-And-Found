<?php
// Tangkap pesan error dari URL
$pesan_error = '';
$pesan_sukses = '';

if (isset($_GET['status'])) {
    if ($_GET['status'] == 'login_failed' && isset($_GET['msg'])) {
        $pesan_error = htmlspecialchars($_GET['msg']);
    } else if ($_GET['status'] == 'success') {
        $pesan_sukses = "Registrasi Berhasil! Silakan Login.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Lost & Found</title>
    <link rel="stylesheet" href="assets/css/auth.css">

    <style>
        .alert { padding: 12px; margin-bottom: 20px; border-radius: 8px; font-size: 14px; text-align: center; font-weight: bold; }
        .alert-danger { background-color: #fee2e2; color: #991b1b; border: 1px solid #f87171; }
        .alert-success { background-color: #d1fae5; color: #065f46; border: 1px solid #34d399; }
    </style>
</head>
<body>

<div class="container">
    <div class="form-box">
        <h2 class="title">Sign In</h2>
        <p>Masuk menggunakan akun kamu</p>

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

        <form action="../backend/login_process.php" method="POST">
            
            <div class="input-box">
                <input type="text" name="username_email" placeholder="Username atau Email" required>
            </div>

            <div class="input-box">
                <input type="password" name="password" placeholder="Password" required>
            </div>

            <div style="text-align: left; margin-top: 10px; font-size: 14px; color: #666;">
                <input type="checkbox" id="remember"> <label for="remember">Remember me</label>
            </div>

            <button type="submit" class="btn">Sign In</button>

            <p style="margin-top:15px; text-align:center;">
                Belum punya akun? <a href="register.php">Daftar</a>
            </p>
        </form>
    </div>

    <div class="side-box">
        <div style="text-align: center;">
            <img src="assets/img/logo.png" alt="Logo" style="width:90px; margin-bottom:25px;">
            <h2>Hello, Friend!</h2>
            <p>Buat akun disini jika belum mempunyai akun!!</p>
            <a href="register.php" class="side-btn">Buat Akun</a>
        </div>
    </div>
</div>

</body>
</html>