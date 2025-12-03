<?php
session_start();
require_once "../config/db_connect.php";

// Kalau sudah login sebagai admin, langsung ke index admin
if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    header("Location: index.php");
    exit();
}

$error = isset($_GET['error']) ? $_GET['error'] : '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Lost & Found</title>
    <link rel="stylesheet" href="../public/assets/css/style.css">
    <style>
        body{
            min-height:100vh;
            display:flex;align-items:center;justify-content:center;
            background:radial-gradient(circle at top,#e0e7ff,#f9fafb);
            font-family:system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;
        }
        .auth-card{
            width:380px;max-width:90%;
            background:#fff;border-radius:24px;
            box-shadow:0 20px 60px rgba(15,23,42,.12);
            padding:32px 32px 28px;
        }
        .auth-title{
            font-size:22px;font-weight:700;color:#111827;margin-bottom:4px;
        }
        .auth-sub{
            font-size:13px;color:#6b7280;margin-bottom:18px;
        }
        .alert{
            padding:10px 12px;border-radius:10px;
            font-size:13px;margin-bottom:14px;
        }
        .alert.error{
            background:#fee2e2;color:#b91c1c;border:1px solid #fecaca;
        }
        .field-label{
            font-size:12px;font-weight:600;
            color:#4b5563;margin-bottom:4px;
        }
        .field-input{
            width:100%;border-radius:999px;border:1px solid #e5e7eb;
            padding:9px 14px;font-size:14px;outline:none;
        }
        .field-input:focus{
            border-color:#6366f1;
            box-shadow:0 0 0 2px rgba(99,102,241,.2);
        }
        .field-group{margin-bottom:14px;}
        .btn-primary{
            width:100%;border-radius:999px;border:none;
            padding:10px 0;margin-top:6px;
            background:linear-gradient(135deg,#6366f1,#8b5cf6);
            color:#fff;font-size:14px;font-weight:600;
            cursor:pointer;box-shadow:0 12px 30px rgba(79,70,229,.35);
        }
        .btn-primary:hover{filter:brightness(1.05);}
        .back-link{
            display:block;text-align:center;
            margin-top:14px;font-size:12px;color:#6b7280;
        }
        .back-link a{color:#4f46e5;text-decoration:none;}
    </style>
</head>
<body>

<div class="auth-card">
    <h1 class="auth-title">Admin Login</h1>
    <p class="auth-sub">Masuk sebagai admin untuk mengelola laporan.</p>

    <?php if ($error === 'invalid'): ?>
        <div class="alert error">Username atau password salah!</div>
    <?php elseif ($error === 'not_admin'): ?>
        <div class="alert error">Akun ini bukan admin.</div>
    <?php endif; ?>

    <form method="POST" action="login_process.php">
        <div class="field-group">
            <label class="field-label">Username</label>
            <input type="text" name="username" class="field-input" required autocomplete="username">
        </div>
        <div class="field-group">
            <label class="field-label">Password</label>
            <input type="password" name="password" class="field-input" required autocomplete="current-password">
        </div>
        <button type="submit" class="btn-primary">Sign In</button>
    </form>

    <span class="back-link">
        Kembali ke <a href="../public/login.php">login pengguna</a>
    </span>
</div>

</body>
</html>
