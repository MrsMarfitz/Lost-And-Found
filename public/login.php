<?php 
// Panggil config untuk session_start()
require '../config/config.php';

// Menampilkan pesan sukses setelah pendaftaran atau pesan error login
$status_message = "";
if (isset($_GET['status'])) {
    if ($_GET['status'] == 'register_success') {
        $status_message = "<p style='color:green; text-align:center;'>Pendaftaran berhasil! Silakan Login.</p>";
    } elseif ($_GET['status'] == 'login_failed' && isset($_GET['msg'])) {
        $status_message = "<p style='color:red; text-align:center; font-weight:bold; padding: 10px; border: 1px solid red; border-radius: 4px;'>ERROR: " . htmlspecialchars($_GET['msg']) . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1"/>
    <title>Login - Lost & Found Campus</title>
    <!-- Asumsi Anda memiliki file CSS di assets/css/style.css -->
    <link rel="stylesheet" href="assets/css/style.css"> 
</head>
<body class="bg-gradient">

    <main class="auth-wrap">
        <div class="auth-card">
            <div class="auth-left">
                <!-- Asumsi Anda memiliki logo di assets/img/logo.png -->
                <img src="assets/img/logo.png" alt="logo" class="brand">
                <h1>Sign In</h1>
                <p class="muted">Masuk menggunakan akun kamu</p>

                <?php echo $status_message; ?>

                <form action="../backend/login_process.php" method="POST" class="form">
                    <input name="username_email" type="text" placeholder="Username atau Email" required>
                    <input name="password" type="password" placeholder="Password" required>
                    
                    <button class="btn-primary" type="submit">Sign In</button>
                </form>

                <p class="muted center">Belum punya akun? <a href="register.php">Daftar</a></p>
            </div>

            <aside class="auth-right">
                <div class="auth-hero">
                    <h2>Hello, Friend!</h2>
                    <p>Register with your personal details to use all features of Lost & Found Campus.</p>
                    <a href="register.php" class="btn-outline">Sign Up</a>
                </div>
            </aside>
        </div>
    </main>

<script src="assets/js/app.js"></script>
</body>
</html>