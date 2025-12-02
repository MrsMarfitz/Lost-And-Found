<?php 
// Panggil config untuk session_start() jika diperlukan
require '../config/config.php';

// Menampilkan pesan error dari proses registrasi yang gagal
$error_message = "";
if (isset($_GET['status']) && $_GET['status'] == 'register_failed' && isset($_GET['msg'])) {
    $error_message = "<p style='color:red; text-align:center; font-weight:bold; padding: 10px; border: 1px solid red; border-radius: 4px;'>ERROR: " . htmlspecialchars($_GET['msg']) . "</p>";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1"/>
    <title>Register - Lost & Found Campus</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-gradient">

    <main class="auth-wrap">
        <div class="auth-card">
            <div class="auth-left">
                <img src="assets/img/logo.png" alt="logo" class="brand">
                <h1>Create Account</h1>
                <p class="muted">Buat akun agar bisa melaporkan barang hilang/temuan</p>

                <?php echo $error_message; ?>

                <!-- PENTING: Action menggunakan URL absolut PORT 8081 untuk mengatasi error 404 -->
                <form action="..Lost-And-Found/backend/register_process.php" method="POST" class="form">
                    <input name="nama" type="text" placeholder="Nama Lengkap" required>
                    <input name="username" type="text" placeholder="Username" required>
                    <input name="email" type="email" placeholder="Email" required>
                    <input name="phone" type="text" placeholder="Nomor Telepon" required>
                    <input name="password" type="password" placeholder="Password" required>
                    <input name="password_confirm" type="password" placeholder="Konfirmasi Password" required>
                    
                    <button class="btn-primary" type="submit">Create Account</button>
                </form>

                <p class="muted center">Sudah punya akun? <a href="login.php">Login</a></p>
            </div>

            <aside class="auth-right">
                <div class="auth-hero">
                    <h2>Welcome!</h2>
                    <p>Let's help the campus community — report lost & found items quickly.</p>
                    <a href="login.php" class="btn-outline">Sign In</a>
                </div>
            </aside>
        </div>
    </main>

<script src="assets/js/app.js"></script>
</body>
</html>